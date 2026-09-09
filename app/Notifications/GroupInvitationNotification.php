<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Group $group,
        public User $invitedBy
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'group_invitation',
            'title' => 'Group Invitation',
            'message' => "{$this->invitedBy->name} invited you to join {$this->group->name}.",
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'user_id' => $notifiable->id,
            'invited_by' => $this->invitedBy->id,
            'invited_by_name' => $this->invitedBy->name,
            'status' => 'pending',
            'url' => route('community.groups.show', $this->group),
        ];
    }
}