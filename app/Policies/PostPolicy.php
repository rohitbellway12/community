<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
   public function view(?User $user, Post $post): bool
    {
        // Public posts are viewable by everyone
        if (is_null($post->group_id)) {
            return true;
        }

        // Group posts require active membership
        if (!$user) {
            return false;
        }

        return $post->group->users()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'active')
            ->exists();
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}