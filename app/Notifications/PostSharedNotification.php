<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Traits\ResolvesNotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostSharedNotification extends Notification
{
    use Queueable, ResolvesNotificationMessages;

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
        $message = "{$this->user->name} shared your post.";

        [$title, $message] = $this->resolveMessage('post_shared', 'Post Shared', $message, [
            'user_name'  => $this->user->name,
            'post_title' => $this->post->title,
        ]);

        return [
            'type' => 'post_shared',
            'title' => $title,
            'message' => $message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
        ];
    }
}