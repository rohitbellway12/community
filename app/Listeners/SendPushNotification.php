<?php

namespace App\Listeners;

use App\Jobs\SendPushNotificationJob;
use App\Models\NotificationSetting;
use Illuminate\Notifications\Events\NotificationSending;

class SendPushNotification
{
    public function handle(NotificationSending $event): void
    {
        $notifiable   = $event->notifiable;
        $notification = $event->notification;

        if (!($notifiable instanceof \App\Models\User)) {
            return;
        }

        $data = $notification->toArray($notifiable);
        $type = $data['type'] ?? null;

        if (!$type) {
            return;
        }

        if (!NotificationSetting::isPushEnabled($type)) {
            return;
        }

        $tokens = $notifiable->deviceTokens()
            ->active()
            ->pluck('token')
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return;
        }

        $title = $data['title'] ?? config('fcm.default_title', 'Community App');
        $body  = $data['message'] ?? '';

        $pushData = $this->buildPushData($data);

        SendPushNotificationJob::dispatchAfterResponse(
            $notifiable->id,
            $tokens,
            $title,
            $body,
            $pushData
        );
    }

    /**
     * Build the FCM data payload (all values must be strings).
     */
    protected function buildPushData(array $notificationData): array
    {
        $type = $notificationData['type'] ?? 'default';

        // Resolve standard navigation target details matching Flutter app expectations
        $targetType = 'home';
        $targetId = '';
        $actionLink = 'home';

        if (!empty($notificationData['post_id'])) {
            $targetType = 'post';
            $targetId = (string) $notificationData['post_id'];
            $actionLink = 'post:' . $targetId;
        } elseif (!empty($notificationData['group_id'])) {
            $targetType = 'group';
            $targetId = (string) $notificationData['group_id'];
            $actionLink = 'group:' . $targetId;
        } elseif (!empty($notificationData['test_id'])) {
            $targetType = 'test';
            $targetId = (string) $notificationData['test_id'];
            $actionLink = 'test:' . $targetId;
        } elseif (!empty($notificationData['user_id']) && $type === 'user_followed') {
            $targetType = 'user';
            $targetId = (string) $notificationData['user_id'];
            $actionLink = 'user:' . $targetId;
        }

        $payload = [
            'type'              => (string) $type,
            'notification_type' => (string) $type,
            'target_type'       => (string) $targetType,
            'target_id'         => (string) $targetId,
            'action_link'       => (string) $actionLink,
            'click_action'      => 'FLUTTER_NOTIFICATION_CLICK',
        ];

        $keys = [
            'title', 'message', 'url', 'post_id', 'post_title',
            'comment', 'comment_id', 'reply', 'group_id', 'group_name',
            'group_slug', 'user_id', 'user_name', 'test_id', 'test_title'
        ];

        foreach ($keys as $key) {
            if (isset($notificationData[$key]) && $notificationData[$key] !== null) {
                $payload[$key] = (string) $notificationData[$key];
            }
        }

        return $payload;
    }
}
