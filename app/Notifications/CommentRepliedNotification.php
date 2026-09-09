<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $user,
        public Post|int $post,
        public int $commentId,
        public string $reply
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $postId = $this->post instanceof Post ? $this->post->id : (int) $this->post;
        $postModel = $this->post instanceof Post ? $this->post : Post::with('group')->find($postId);
        $group = $postModel?->group;

        $message = $group
            ? "{$this->user->name} replied to your comment in '{$group->name}'."
            : "{$this->user->name} replied to your comment.";

        return [
            'type' => 'comment_replied',
            'title' => 'New Reply',
            'message' => $message,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'post_id' => $postId,
            'post_title' => $postModel?->title,
            'comment_id' => $this->commentId,
            'reply' => $this->reply,
            'group_id' => $group?->id,
            'group_name' => $group?->name,
            'group_slug' => $group?->slug,
            'url' => route('community.posts.show', $postId),
        ];
    }
}