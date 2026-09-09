<?php namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Group $group): bool
    {
        return true; // Group details pages are publicly accessible; posts are filtered separately.
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Group $group): bool
    {
        $membership = $group->users()->where('user_id', $user->id)->wherePivot('status', 'active')->first();
        return $group->owner_id === $user->id || ($membership && in_array($membership->pivot->role, ['owner', 'admin']));
    }

    public function delete(User $user, Group $group): bool
    {
        return $group->owner_id === $user->id;
    }

    public function manageMembers(User $user, Group $group): bool
    {
        return $this->update($user, $group);
    }

    public function createPost(User $user, Group $group): bool
    {
        return $group->users()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'active')
            ->exists();
    }
}