<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public int $postId,
        public int $commentId,
        public string $reply
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'comment_replied',
            'message' => "{$this->user->name} replied to your comment.",
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'post_id' => $this->postId,
            'comment_id' => $this->commentId,
            'reply' => $this->reply,
        ];
    }
}