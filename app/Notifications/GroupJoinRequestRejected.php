<?php

namespace App\Notifications;

use App\Models\Group;
use App\Traits\ResolvesNotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupJoinRequestRejected extends Notification
{
    use Queueable, ResolvesNotificationMessages;

    public function __construct(
        public Group $group
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = "Your request to join {$this->group->name} has been rejected.";

        [$title, $message] = $this->resolveMessage('group_join_request_rejected', 'Join Request Rejected', $message, [
            'group_name' => $this->group->name,
        ]);

        return [
            'type' => 'group_join_request_rejected',
            'title' => $title,
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => $message,
        ];
    }
}