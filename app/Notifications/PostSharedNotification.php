<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostSharedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public Post $post
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_shared',
            'message' => "{$this->user->name} shared your post.",
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
        ];
    }
}