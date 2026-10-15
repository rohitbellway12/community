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

        // 2. Fetch Active Device Tokens mapped by User
        $tokensByUser = DeviceToken::query()
            ->whereIn('user_id', $targetUserIds)
            ->where('is_active', true)
            ->get()
            ->groupBy('user_id');

        $successCount = 0;
        $failedCount = 0;

        // Resolve deep-linking keys for Mobile Apps (Flutter, Android, iOS)
        $actionLink = $notification->action_link ?? 'home';
        $targetType = 'home';
        $targetId = '';
        $url = '';

        if (str_starts_with($actionLink, 'test:')) {
            $targetType = 'test';
            $targetId = substr($actionLink, 5);
        } elseif (str_starts_with($actionLink, 'group:')) {
            $targetType = 'group';
            $targetId = substr($actionLink, 6);
        } elseif (str_starts_with($actionLink, 'event:')) {
            $targetType = 'event';
            $targetId = substr($actionLink, 6);
        } elseif (str_starts_with($actionLink, 'http://') || str_starts_with($actionLink, 'https://')) {
            $targetType = 'web_url';
            $url = $actionLink;
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
        foreach ($targetUserIds as $userId) {
            $userTokens = $tokensByUser->get($userId);

            // In-app Notification creation (FR-14: Student can see past notifications in an in-app notification list)
            $this->createInAppNotification($userId, $notification);

            if (!$userTokens || $userTokens->isEmpty()) {
                // User has no registered/active device token
                $failedCount++;
                PushNotificationLog::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $userId,
                    'device_token'    => null,
                    'status'          => 'failed',
                    'error_message'   => 'No active device token registered on device.',
                    'delivered_at'    => null,
                ]);
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
                    $tokenModel->update(['last_used_at' => now()]);

                    PushNotificationLog::create([
                        'notification_id' => $notification->id,
                        'user_id'         => $userId,
                        'device_token'    => $tokenString,
                        'status'          => 'sent',
                        'error_message'   => null,
                        'delivered_at'    => now(),
                    ]);
                } else {
                    PushNotificationLog::create([
                        'notification_id' => $notification->id,
                        'user_id'         => $userId,
                        'device_token'    => $tokenString,
                        'status'          => 'failed',
                        'error_message'   => $result['error'] ?? 'FCM send failed',
                        'delivered_at'    => null,
                    ]);
                }
            }

            if ($userHadSuccess) {
                $successCount++;
            } else {
                $failedCount++;
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
     * Resolve target user IDs based on audience_type
     */
    protected function resolveTargetUserIds(PushNotification $notification): array
    {
        $type = $notification->audience_type;

        if ($type === 'all_students') {
            return User::query()
                ->where('status', 'active')
                ->where('role', 'user')
                ->pluck('id')
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

            return DB::table('group_user')
                ->join('users', 'group_user.user_id', '=', 'users.id')
                ->whereIn('group_user.group_id', $groupIds)
                ->where('group_user.status', 'active')
                ->where('users.status', 'active')
                ->pluck('users.id')
                ->unique()
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

            return User::query()
                ->whereIn('id', $userIds)
                ->where('status', 'active')
                ->pluck('id')
                ->all();
        }

        return [];
    }

    /**
     * Store in-app notification in standard Laravel notifications table
     */
    protected function createInAppNotification(int $userId, PushNotification $notification): void
    {
        try {
            DB::table('notifications')->insert([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\\Notifications\\AdminBroadcastNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id'   => $userId,
                'data'            => json_encode([
                    'title'           => $notification->title,
                    'body'            => $notification->description,
                    'image'           => $notification->image_url,
                    'banner'          => $notification->banner_url,
                    'action_link'     => $notification->action_link,
                    'notification_id' => $notification->id,
                    'created_at'      => now()->toIso8601String(),
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
