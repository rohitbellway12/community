<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserFollowedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $follower
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $profile = $this->follower->profile;
        $profileUrl = $profile?->username
            ? route('community.profile', $profile->username)
            : route('community.profile', $this->follower->id);

        return [
            'type' => 'user_followed',
            'title' => 'New Follower',
            'message' => "{$this->follower->name} started following you.",
            'user_id' => $this->follower->id,
            'user_name' => $this->follower->name,
            'user_avatar' => $profile?->avatar,
            'username' => $profile?->username,
            'url' => $profileUrl,
        ];
    }
}
