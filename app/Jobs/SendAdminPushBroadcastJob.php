<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Models\PushNotification;
use App\Models\PushNotificationLog;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendAdminPushBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutes timeout for big broadcasts
    public int $tries = 3;

    public function __construct(
        public PushNotification $notification
    ) {}

    public function handle(PushNotificationService $pushService): void
    {
        $notification = $this->notification->fresh(['targets']);
        if (!$notification) {
            return;
        }

        // 1. Resolve Target Users
        $targetUserIds = $this->resolveTargetUserIds($notification);

        // Deduplicate user IDs
        $targetUserIds = array_values(array_unique($targetUserIds));
        $totalRecipients = count($targetUserIds);

        $notification->update([
            'total_recipients' => $totalRecipients,
        ]);

        if ($totalRecipients === 0) {
            $notification->update([
                'status'        => 'sent',
                'sent_at'       => now(),
                'success_count' => 0,
                'failed_count'  => 0,
            ]);
            return;
        }

        // 2. Fetch Active Device Tokens mapped by User (Filtered by Target Platform)
        $targetPlatform = $notification->target_platform ?? 'all';
        $tokenQuery = DeviceToken::query()
            ->whereIn('user_id', $targetUserIds)
            ->where('is_active', true);

        if ($targetPlatform === 'android') {
            $tokenQuery->where('device_type', 'android');
        } elseif ($targetPlatform === 'ios') {
            $tokenQuery->where('device_type', 'ios');
        }

        $tokensByUser = $tokenQuery->get()->groupBy('user_id');

        $successCount = 0;
        $failedCount = 0;

        // Resolve deep-linking keys for Mobile Apps (Flutter, Android, iOS)
        $rawAction = $notification->action_link ?? 'home';
        $targetType = 'home';
        $targetId = '';
        $url = '';
        $actionLink = 'home';

        if (str_starts_with($rawAction, 'test:') || $rawAction === 'test') {
            $targetType = 'test';
            $actionLink = 'test';
        } elseif (str_starts_with($rawAction, 'group:') || $rawAction === 'group') {
            $targetType = 'group';
            $actionLink = 'group';
        } elseif (str_starts_with($rawAction, 'event:') || $rawAction === 'event') {
            $targetType = 'event';
            $actionLink = 'event';
        } elseif (str_starts_with($rawAction, 'http://') || str_starts_with($rawAction, 'https://')) {
            $targetType = 'web_url';
            $url = $rawAction;
            $actionLink = $rawAction;
        } else {
            $targetType = 'home';
            $actionLink = 'home';
        }

        $customData = array_filter([
            'notification_id'   => (string) $notification->id,
            'notification_type' => 'admin_broadcast',
            'type'              => 'admin_broadcast',
            'target_type'       => $targetType,
            'target_id'         => (string) $targetId,
            'url'               => (string) $url,
            'action_link'       => (string) $actionLink,
            'image'             => $notification->image_url,
            'banner'            => $notification->banner_url,
            'click_action'      => 'FLUTTER_NOTIFICATION_CLICK',
        ]);

        $imageUrlToSend = $notification->banner_url ?: $notification->image_url;

        // 3. Process each targeted user
        $inAppNotifications = [];
        $pushLogs = [];
        $usedTokenIds = [];

        foreach ($targetUserIds as $userId) {
            $userTokens = $tokensByUser->get($userId);

            // In-app Notification creation (FR-14)
            $inAppNotifications[] = [
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AdminBroadcastNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $userId,
                'data'            => json_encode([
                    'type'            => 'admin_broadcast',
                    'title'           => $notification->title,
                    'message'         => $notification->description,
                    'body'            => $notification->description,
                    'image'           => $notification->image_url,
                    'banner'          => $notification->banner_url,
                    'action_link'     => $actionLink,
                    'target_type'     => $targetType,
                    'target_id'       => (string) $targetId,
                    'url'             => (string) $url,
                    'notification_id' => $notification->id,
                ]),
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            if (!$userTokens || $userTokens->isEmpty()) {
                $failedCount++;
                $pushLogs[] = [
                    'notification_id' => $notification->id,
                    'user_id'         => $userId,
                    'device_token'    => null,
                    'status'          => 'failed',
                    'error_message'   => 'No active device token registered on device.',
                    'delivered_at'    => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
                continue;
            }

            // User has 1 or more device tokens (e.g. tablet + phone)
            $userHadSuccess = false;
            foreach ($userTokens as $tokenModel) {
                $tokenString = $tokenModel->token;

                $result = $pushService->sendToDeviceDetailed(
                    $tokenString,
                    $notification->title,
                    $notification->description,
                    $customData,
                    $imageUrlToSend
                );

                if ($result['success']) {
                    $userHadSuccess = true;
                    $usedTokenIds[] = $tokenModel->id;

                    $pushLogs[] = [
                        'notification_id' => $notification->id,
                        'user_id'         => $userId,
                        'device_token'    => $tokenString,
                        'status'          => 'sent',
                        'error_message'   => null,
                        'delivered_at'    => now(),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                } else {
                    $pushLogs[] = [
                        'notification_id' => $notification->id,
                        'user_id'         => $userId,
                        'device_token'    => $tokenString,
                        'status'          => 'failed',
                        'error_message'   => $result['error'] ?? 'FCM send failed',
                        'delivered_at'    => null,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }

            if ($userHadSuccess) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        // 1. Bulk update used tokens in 1 query
        if (!empty($usedTokenIds)) {
            DeviceToken::whereIn('id', $usedTokenIds)->update(['last_used_at' => now()]);
        }

        // 2. Bulk insert in-app notifications in chunks of 100
        if (!empty($inAppNotifications)) {
            foreach (array_chunk($inAppNotifications, 100) as $chunk) {
                DB::table('notifications')->insert($chunk);
            }
        }

        // 3. Bulk insert delivery logs in chunks of 100
        if (!empty($pushLogs)) {
            foreach (array_chunk($pushLogs, 100) as $chunk) {
                PushNotificationLog::insert($chunk);
            }
        }

        // 4. Update Final Notification Status & Stats
        $finalStatus = 'sent';
        if ($successCount === 0 && $failedCount > 0) {
            $finalStatus = 'failed';
        } elseif ($successCount > 0 && $failedCount > 0) {
            $finalStatus = 'partially_failed';
        }

        $notification->update([
            'status'        => $finalStatus,
            'sent_at'       => now(),
            'success_count' => $successCount,
            'failed_count'  => $failedCount,
        ]);
    }

    /**
     * Resolve target user IDs based on audience_type and target_platform
     */
    protected function resolveTargetUserIds(PushNotification $notification): array
    {
        $type = $notification->audience_type;
        $targetPlatform = $notification->target_platform ?? 'all';

        $applyPlatformFilter = function ($query) use ($targetPlatform) {
            if ($targetPlatform === 'android') {
                $query->where('device_tokens.device_type', 'android');
            } elseif ($targetPlatform === 'ios') {
                $query->where('device_tokens.device_type', 'ios');
            }
        };

        if ($type === 'all_students') {
            $q = DB::table('device_tokens')
                ->join('users', 'device_tokens.user_id', '=', 'users.id')
                ->where('device_tokens.is_active', true)
                ->where('users.status', 'active');
            $applyPlatformFilter($q);

            return $q->pluck('users.id')
                ->unique()
                ->values()
                ->all();
        }

        if ($type === 'country') {
            $countryIds = $notification->targets()
                ->where('target_type', 'country')
                ->pluck('target_id')
                ->all();

            if (empty($countryIds)) {
                return [];
            }

            $q = DB::table('profiles')
                ->join('users', 'profiles.user_id', '=', 'users.id')
                ->join('device_tokens', 'device_tokens.user_id', '=', 'users.id')
                ->whereIn('profiles.country_id', $countryIds)
                ->where('users.status', 'active')
                ->where('device_tokens.is_active', true);
            $applyPlatformFilter($q);

            return $q->pluck('users.id')
                ->unique()
                ->values()
                ->all();
        }

        if ($type === 'group') {
            $groupIds = $notification->targets()
                ->where('target_type', 'group')
                ->pluck('target_id')
                ->all();

            if (empty($groupIds)) {
                return [];
            }

            $q = DB::table('group_user')
                ->join('users', 'group_user.user_id', '=', 'users.id')
                ->join('device_tokens', 'device_tokens.user_id', '=', 'users.id')
                ->whereIn('group_user.group_id', $groupIds)
                ->where('group_user.status', 'active')
                ->where('users.status', 'active')
                ->where('device_tokens.is_active', true);
            $applyPlatformFilter($q);

            return $q->pluck('users.id')
                ->unique()
                ->values()
                ->all();
        }

        if ($type === 'individual') {
            $userIds = $notification->targets()
                ->where('target_type', 'user')
                ->pluck('target_id')
                ->all();

            if (empty($userIds)) {
                return [];
            }

            $q = DB::table('device_tokens')
                ->join('users', 'device_tokens.user_id', '=', 'users.id')
                ->whereIn('users.id', $userIds)
                ->where('users.status', 'active')
                ->where('device_tokens.is_active', true);
            $applyPlatformFilter($q);

            return $q->pluck('users.id')
                ->unique()
                ->values()
                ->all();
        }

        return [];
    }

    /**
     * Store in-app notification in standard Laravel notifications table
     */
    protected function createInAppNotification(int $userId, PushNotification $notification, string $targetType = 'home', string $targetId = '', string $url = '', string $actionLink = 'home'): void
    {
        try {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AdminBroadcastNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $userId,
                'data'            => json_encode([
                    'type'            => 'admin_broadcast',
                    'title'           => $notification->title,
                    'message'         => $notification->description,
                    'body'            => $notification->description,
                    'image'           => $notification->image_url,
                    'banner'          => $notification->banner_url,
                    'target_type'     => $targetType,
                    'target_id'       => $targetId ?: null,
                    'url'             => $url ?: null,
                    'action_link'     => $actionLink,
                    'notification_id' => $notification->id,
                    'created_at'      => now('Asia/Seoul')->toIso8601String(),
                ]),
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Could not create in-app notification: ' . $e->getMessage());
        }
    }
}
