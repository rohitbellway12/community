<?php

namespace App\Notifications;

use App\Models\Group;
use App\Traits\ResolvesNotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupJoinRequestAccepted extends Notification
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
        $message = "Your request to join {$this->group->name} has been accepted.";

        [$title, $message] = $this->resolveMessage('group_join_request_accepted', 'Join Request Accepted', $message, [
            'group_name' => $this->group->name,
        ]);

        return [
            'type' => 'group_join_request_accepted',
            'title' => $title,
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => $message,
        ];
    }
}