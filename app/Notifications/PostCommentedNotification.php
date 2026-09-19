<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Traits\ResolvesNotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostCommentedNotification extends Notification
{
    use Queueable, ResolvesNotificationMessages;

    public function __construct(
        public User $user,
        public Post $post,
        public string $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $group = $this->post->group;
        $message = $group
            ? "{$this->user->name} commented on your post in '{$group->name}'."
            : "{$this->user->name} commented on your post.";

        [$title, $message] = $this->resolveMessage('post_commented', 'New Comment', $message, [
            'user_name'  => $this->user->name,
            'post_title' => $this->post->title,
            'group_name' => $group?->name,
            'comment'    => $this->comment,
        ]);

        return [
            'type' => 'post_commented',
            'title' => $title,
            'message' => $message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'comment' => $this->comment,
            'group_id' => $group?->id,
            'group_name' => $group?->name,
            'group_slug' => $group?->slug,
            'url' => route('community.posts.show', $this->post),
        ];
    }
}