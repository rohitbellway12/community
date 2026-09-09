<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupJoinRequestRejected extends Notification
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
            'type' => 'group_join_request_rejected',
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'message' => "Your request to join {$this->group->name} has been rejected.",
        ];
    }
}