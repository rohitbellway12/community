<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use App\Traits\ResolvesNotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupInvitationNotification extends Notification
{
    use Queueable, ResolvesNotificationMessages;

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
        $message = "{$this->invitedBy->name} invited you to join {$this->group->name}.";

        [$title, $message] = $this->resolveMessage('group_invitation', 'Group Invitation', $message, [
            'group_name'     => $this->group->name,
            'invited_by_name' => $this->invitedBy->name,
        ]);

        return [
            'type' => 'group_invitation',
            'title' => $title,
            'message' => $message,
            'group_id' => $this->group->id,
            'group_slug' => $this->group->slug,
            'group_name' => $this->group->name,
            'user_id' => $notifiable->id,
            'invited_by' => $this->invitedBy->id,
            'invited_by_name' => $this->invitedBy->name,
            'status' => 'pending',
            'url' => route('community.groups.show', $this->group),
        ];
    }
}