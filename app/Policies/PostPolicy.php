<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function view(?User $user, Post $post): bool
    {
        $post->loadMissing('group');
        $isPrivate = $post->visibility === 'private'
            || ($post->group && $post->group->visibility === 'private');

        if (!$isPrivate) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ((int) $post->user_id === (int) $user->id) {
            return true;
        }

        if (in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true)) {
            return true;
        }

        if ($post->group) {
            if ((int) $post->group->owner_id === (int) $user->id) {
                return true;
            }

            return $post->group->users()
                ->where('users.id', $user->id)
                ->wherePivot('status', 'active')
                ->exists();
        }

        return false;
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