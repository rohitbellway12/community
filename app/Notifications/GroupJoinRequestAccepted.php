<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupJoinRequestAccepted extends Notification
{
    use Queueable;

    public function __construct(
        public Group $group
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_join_request_accepted',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => "Your request to join {$this->group->name} has been accepted.",
        ];
    }
}