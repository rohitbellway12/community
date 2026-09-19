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
            ->all();

        if (empty($tokens)) {
            return;
        }

        $title = $data['title'] ?? config('fcm.default_title', 'Community App');
        $body  = $data['message'] ?? '';

        $pushData = $this->buildPushData($data);

        SendPushNotificationJob::dispatch(
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
        $payload = [
            'notification_type' => $notificationData['type'] ?? 'default',
        ];

        foreach (['title', 'message', 'url', 'post_id', 'post_title', 'group_id', 'user_id', 'user_name'] as $key) {
            if (isset($notificationData[$key])) {
                $payload[$key] = $notificationData[$key];
            }
        }

        $payload['click_action'] = $notificationData['url'] ?? 'FLUTTER_NOTIFICATION_CLICK';

        return $payload;
    }
}
