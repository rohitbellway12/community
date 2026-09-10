<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Group;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use App\Notifications\GroupJoinRequestNotification;
use App\Notifications\GroupJoinRequestAccepted;
use App\Notifications\GroupJoinRequestRejected;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    use AuthorizesRequests;

    /**
     * Group Management Page
     */
    public function index(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Joined / Owned Groups
        |--------------------------------------------------------------------------
        |
        | Only active memberships are shown here.
        |
        */
        $groups = $user->groups()
            ->wherePivot('status', 'active')
            ->withCount('users')
            ->with([
                'users' => function ($q) {
                    $q->select(['users.id', 'users.name'])->withPivot(['role', 'status']);
                },
                'posts' => function ($query) {
                    $query
                        ->with([
                            'user',
                            'group',
                            'media',
                            'likes',
                            'comments',
                        ])
                        ->latest()
                        ->limit(10);
                },
            ])
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $query->where(
                        'groups.name',
                        'like',
                        '%' . trim($request->search) . '%'
                    );
                }
            )
            ->latest('groups.created_at')
            ->get();

        $groups->each(function ($group) use ($user) {
            $group->is_owner =
                (int) $group->owner_id === (int) $user->id
                || optional($group->pivot)->role === 'owner'
                || optional($group->pivot)->role == 2;

            $group->membership_role = optional($group->pivot)->role;
        });

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        |
        | Used by the Create Group modal.
        |
        */
        $users = User::query()
            ->where('id', '!=', $user->id)
            ->with('profile:id,user_id,username,avatar')
            ->select([
                'id',
                'name',
                'email',
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Followers (Used by Create & Edit Group modals)
        |--------------------------------------------------------------------------
        */
        $followers = $user->followers()
            ->with('profile:id,user_id,username,avatar')
            ->select([
                'users.id',
                'users.name',
                'users.email',
            ])
            ->orderBy('users.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Community Sidebar Data
        |--------------------------------------------------------------------------
        */
        $notificationsCount = $user && method_exists($user, 'unreadNotifications')
            ? $user->unreadNotifications()->count()
            : 0;

        $topContributors = User::query()
            ->with('profile')
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Existing Sidebar Compatibility
        |--------------------------------------------------------------------------
        */
        $categories = collect();
        $tags = collect();

        return view('community.groups.index', [
            'groups' => $groups,
            'users' => $users,
            'followers' => $followers,
            'user' => $user,
            'notificationsCount' => $notificationsCount,
            'topContributors' => $topContributors,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Create Group
     *
     * Selected users are NOT made active members immediately.
     * They receive a group invitation and remain pending until
     * they accept it.
     */
    public function store(StoreGroupRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        $data['owner_id'] = $user->id;

        /*
        |--------------------------------------------------------------------------
        | Cover Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('groups/covers', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Group
        |--------------------------------------------------------------------------
        */
        $group = Group::create($data);

        /*
        |--------------------------------------------------------------------------
        | Owner = Active Member
        |--------------------------------------------------------------------------
        */
        $group->users()->syncWithoutDetaching([
            $user->id => [
                'role' => 'owner',
                'status' => 'active',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Selected Users = Pending Invitations
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | Do NOT set selected users to active.
        | They must accept the invitation first.
        |
        */
        $memberIds = $request->input('members', []);

        if (is_array($memberIds) && !empty($memberIds)) {
            $memberIds = collect($memberIds)
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->reject(fn ($id) => $id === (int) $user->id)
                ->values();

            foreach ($memberIds as $memberId) {
                $invitedUser = User::find($memberId);

                if (!$invitedUser) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Do not overwrite an already-active membership
                |--------------------------------------------------------------------------
                */
                $existingMembership = $group->users()
                    ->where('user_id', $invitedUser->id)
                    ->first();

                if ($existingMembership) {
                    $existingStatus = $existingMembership->pivot->status ?? null;

                    if ($existingStatus === 'active') {
                        continue;
                    }

                    if ($existingStatus === 'pending' || $existingStatus === 'invited') {
                        continue;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Create Invited Membership
                |--------------------------------------------------------------------------
                */
                $group->users()->syncWithoutDetaching([
                    $invitedUser->id => [
                        'role' => 'member',
                        'status' => 'invited',
                    ],
                ]);

                /*
                |--------------------------------------------------------------------------
                | Send Invitation Notification
                |--------------------------------------------------------------------------
                */
                $invitedUser->notify(
                    new GroupInvitationNotification($group, $user)
                );
            }
        }

        return redirect()
            ->route('community.groups.index')
            ->with('success', 'Group created successfully.');
    }

    /**
     * Show Group
     */
    public function show(Group $group)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Group Stats & Owner
        |--------------------------------------------------------------------------
        */
        $group->loadCount('users');

        $owner = User::with(['profile.country'])->find($group->owner_id);

        /*
        |--------------------------------------------------------------------------
        | Current User Membership
        |--------------------------------------------------------------------------
        */
        $membership = $user
            ? $group->users()
                ->where('user_id', $user->id)
                ->first()?->pivot
            : null;

        $isMember = $membership
            && $membership->status === 'active';

        /*
        |--------------------------------------------------------------------------
        | Group Members (Active)
        |--------------------------------------------------------------------------
        */
        $members = $group->users()
            ->withPivot(['role', 'status', 'created_at'])
            ->with(['profile.country'])
            ->wherePivot('status', 'active')
            ->orderByRaw("CASE WHEN group_user.role = 'owner' THEN 0 ELSE 1 END")
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pending Join Requests (Visible to Owner)
        |--------------------------------------------------------------------------
        | Only actual join requests sent by users to join this group.
        */
        $pendingMembers = ($user && (int) $group->owner_id === (int) $user->id)
            ? $group->users()
                ->withPivot(['role', 'status', 'created_at'])
                ->with(['profile.country'])
                ->wherePivot('status', 'pending')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Pending Invitations (Sent by Owner)
        |--------------------------------------------------------------------------
        */
        $invitedMembers = ($user && (int) $group->owner_id === (int) $user->id)
            ? $group->users()
                ->withPivot(['role', 'status', 'created_at'])
                ->with(['profile.country'])
                ->wherePivot('status', 'invited')
                ->get()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Check if Current User has a Pending Invitation to this Group
        |--------------------------------------------------------------------------
        */
        $hasPendingInvitation = false;
        if ($user && !$isMember) {
            $hasPendingInvitation = ($membership && $membership->status === 'invited')
                || $this->hasPendingInvitation($user, $group);
        }

        /*
        |--------------------------------------------------------------------------
        | Group Posts
        |--------------------------------------------------------------------------
        |
        | Only active members can see group posts.
        |
        */
        $posts = $isMember
            ? $group->posts()
                ->with([
                    'user',
                    'user.profile.country',
                    'group',
                    'likes',
                    'comments' => function ($query) {
                        $query->whereNull('parent_id')
                            ->latest()
                            ->take(3)
                            ->with([
                                'user.profile',
                                'replies' => function ($rq) {
                                    $rq->oldest()->with('user.profile');
                                },
                            ]);
                    },
                    'media',
                ])
                ->latest()
                ->paginate(10)
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Notification Count
        |--------------------------------------------------------------------------
        */
        $notificationsCount = $user
            ? $user->unreadNotifications()->count()
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Right Sidebar
        |--------------------------------------------------------------------------
        */
        $topContributors = User::query()
            ->with('profile')
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();

        $categories = collect();
        $tags = collect();

        return view('community.groups.show', [
            'group' => $group,
            'owner' => $owner,
            'members' => $members,
            'pendingMembers' => $pendingMembers,
            'invitedMembers' => $invitedMembers,
            'hasPendingInvitation' => $hasPendingInvitation,
            'isMember' => $isMember,
            'membership' => $membership,
            'posts' => $posts,
            'user' => $user,
            'notificationsCount' => $notificationsCount,
            'topContributors' => $topContributors,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function join(Request $request, Group $group)
    {
        $user = $request->user();
        $userId = $user->id;

        if ((int) $group->owner_id === (int) $userId) {
            return back()->with(
                'error',
                'You are already the owner of this group.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Existing Membership
        |--------------------------------------------------------------------------
        */
        $existingMembership = $group->users()
            ->where('user_id', $userId)
            ->first();

        if ($existingMembership) {
            $status = $existingMembership->pivot->status ?? null;

            if ($status === 'active') {
                return back()->with(
                    'error',
                    'You are already a member of this group.'
                );
            }

            if ($status === 'pending') {
                return back()->with(
                    'error',
                    'Your join request is already pending owner approval.'
                );
            }

            if ($status === 'invited') {
                return back()->with(
                    'info',
                    'You have an invitation to join this group. Please accept the invitation.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create Pending Join Request
        |--------------------------------------------------------------------------
        */
        $group->users()->syncWithoutDetaching([
            $userId => [
                'role' => 'member',
                'status' => 'pending',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Notify Owner
        |--------------------------------------------------------------------------
        */
        $owner = User::find($group->owner_id);

        if ($owner && (int) $owner->id !== (int) $userId) {
            $owner->notify(
                new GroupJoinRequestNotification($group, $user)
            );
        }

        return back()->with(
            'success',
            'Join request sent successfully. Waiting for owner approval.'
        );
    }

    /**
     * Leave Group
     */
    public function leave(Request $request, Group $group)
    {
        $userId = $request->user()->id;

        if ((int) $group->owner_id === (int) $userId) {
            return back()->with(
                'error',
                'Group owners cannot leave their own group.'
            );
        }

        $group->users()->detach($userId);

        return redirect()
            ->route('community.groups.index')
            ->with(
                'success',
                'You have left the group.'
            );
    }

    /**
     * Group Members
     */
    public function members(Group $group)
    {
        return redirect()->route('community.groups.show', [
            'group' => $group->slug,
            'tab' => 'members',
        ]);
    }

    /**
     * Update Group
     */
    public function update(
        UpdateGroupRequest $request,
        Group $group
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Owner Check
        |--------------------------------------------------------------------------
        */
        if ((int) $group->owner_id !== (int) $user->id) {
            return back()->with(
                'error',
                'Unauthorized action.'
            );
        }

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Cover Image
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('cover_image')) {
            if ($group->cover_image) {
                Storage::disk('public')->delete(
                    $group->cover_image
                );
            }

            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('groups/covers', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Update Group Details
        |--------------------------------------------------------------------------
        */
        $memberIds = $request->input('members', []);
        unset($data['members']);

        $group->update($data);

        /*
        |--------------------------------------------------------------------------
        | Add / Invite New Members
        |--------------------------------------------------------------------------
        */
        if (is_array($memberIds) && !empty($memberIds)) {
            $memberIds = collect($memberIds)
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->reject(fn ($id) => $id === (int) $user->id)
                ->values();

            foreach ($memberIds as $memberId) {
                $invitedUser = User::find($memberId);

                if (!$invitedUser) {
                    continue;
                }

                $existingMembership = $group->users()
                    ->where('user_id', $invitedUser->id)
                    ->first();

                if ($existingMembership) {
                    continue;
                }

                $group->users()->syncWithoutDetaching([
                    $invitedUser->id => [
                        'role' => 'member',
                        'status' => 'invited',
                    ],
                ]);

                $invitedUser->notify(
                    new GroupInvitationNotification($group, $user)
                );
            }
        }

        return redirect()
            ->route('community.groups.index')
            ->with(
                'success',
                'Group updated successfully.'
            );
    }

    /**
     * Delete Group
     */
    public function destroy(
        Request $request,
        Group $group
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Owner Check
        |--------------------------------------------------------------------------
        */
        if ((int) $group->owner_id !== (int) $user->id) {
            return back()->with(
                'error',
                'Unauthorized action.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Load Posts + Media
        |--------------------------------------------------------------------------
        */
        $group->load([
            'posts.media',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delete Posts + Media
        |--------------------------------------------------------------------------
        */
        foreach ($group->posts as $post) {
            foreach ($post->media as $media) {
                if ($media->file_path) {
                    Storage::disk('public')->delete(
                        $media->file_path
                    );
                }

                $media->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Backward compatibility for old image column
            |--------------------------------------------------------------------------
            */
            if (!empty($post->image)) {
                Storage::disk('public')->delete(
                    $post->image
                );
            }

            $post->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Group Cover
        |--------------------------------------------------------------------------
        */
        if ($group->cover_image) {
            Storage::disk('public')->delete(
                $group->cover_image
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Members
        |--------------------------------------------------------------------------
        */
        $group->users()->detach();

        /*
        |--------------------------------------------------------------------------
        | Delete Group
        |--------------------------------------------------------------------------
        */
        $group->delete();

        return redirect()
            ->route('community.groups.index')
            ->with(
                'success',
                'Group and its posts deleted successfully.'
            );
    }

    /**
     * Accept a join request submitted by a user.
     *
     * Only the group owner can approve a normal join request.
     */
    public function acceptRequest(
        Request $request,
        Group $group,
        User $userToAccept
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Owner Authorization
        |--------------------------------------------------------------------------
        */
        if ((int) $group->owner_id !== (int) $user->id) {
            return $this->requestResponse(
                $request,
                false,
                'Unauthorized action.',
                403
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check Pending Membership
        |--------------------------------------------------------------------------
        */
        $membership = $group->users()
            ->where('user_id', $userToAccept->id)
            ->first();

        if (
            !$membership
            || ($membership->pivot->status ?? null) !== 'pending'
        ) {
            return $this->requestResponse(
                $request,
                false,
                'No pending join request found.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Activate Member
        |--------------------------------------------------------------------------
        */
        $group->users()->updateExistingPivot(
            $userToAccept->id,
            [
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Update Owner's Notification
        |--------------------------------------------------------------------------
        */
        $this->updateJoinRequestNotification(
            $user,
            $group,
            $userToAccept,
            'accepted'
        );

        /*
        |--------------------------------------------------------------------------
        | Notify Requester
        |--------------------------------------------------------------------------
        */
        $userToAccept->notify(
            new GroupJoinRequestAccepted($group)
        );

        return $this->requestResponse(
            $request,
            true,
            "{$userToAccept->name}'s join request has been accepted.",
            200,
            [
                'status' => 'accepted',
            ]
        );
    }

    /**
     * Reject a join request submitted by a user.
     *
     * Only the group owner can reject a normal join request.
     */
    public function rejectRequest(
        Request $request,
        Group $group,
        User $userToReject
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Owner Authorization
        |--------------------------------------------------------------------------
        */
        if ((int) $group->owner_id !== (int) $user->id) {
            return $this->requestResponse(
                $request,
                false,
                'Unauthorized action.',
                403
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check Pending Membership
        |--------------------------------------------------------------------------
        */
        $membership = $group->users()
            ->where('user_id', $userToReject->id)
            ->first();

        if (
            !$membership
            || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])
        ) {
            return $this->requestResponse(
                $request,
                false,
                'No pending join request or invitation found.',
                422
            );
        }

        $wasInvited = (($membership->pivot->status ?? null) === 'invited');

        /*
        |--------------------------------------------------------------------------
        | Remove Request / Cancel Invitation
        |--------------------------------------------------------------------------
        */
        $group->users()->detach($userToReject->id);

        if ($wasInvited) {
            $this->updateInvitationNotification(
                $userToReject,
                $group,
                'cancelled'
            );

            return $this->requestResponse(
                $request,
                true,
                "Invitation to {$userToReject->name} has been cancelled.",
                200,
                [
                    'status' => 'cancelled',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Owner's Notification
        |--------------------------------------------------------------------------
        */
        $this->updateJoinRequestNotification(
            $user,
            $group,
            $userToReject,
            'rejected'
        );

        /*
        |--------------------------------------------------------------------------
        | Notify Requester
        |--------------------------------------------------------------------------
        */
        $userToReject->notify(
            new GroupJoinRequestRejected($group)
        );

        return $this->requestResponse(
            $request,
            true,
            "{$userToReject->name}'s join request has been rejected.",
            200,
            [
                'status' => 'rejected',
            ]
        );
    }

    /**
     * Accept an invitation received when the group was created.
     *
     * Only the invited authenticated user can accept their own invitation.
     */
    public function acceptInvitation(
        Request $request,
        Group $group
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Find Pending Membership
        |--------------------------------------------------------------------------
        */
        $membership = $group->users()
            ->where('user_id', $user->id)
            ->first();

        if (
            !$membership
            || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])
        ) {
            return $this->requestResponse(
                $request,
                false,
                'No pending invitation found.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify This Pending Membership Came From An Invitation
        |--------------------------------------------------------------------------
        |
        | A normal "Join Group" request also uses status=pending.
        | Therefore we additionally check for the group_invitation
        | notification before allowing invitation acceptance.
        |
        */
        if (!$this->hasPendingInvitation($user, $group)) {
            return $this->requestResponse(
                $request,
                false,
                'No valid group invitation found.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Activate Membership
        |--------------------------------------------------------------------------
        */
        $group->users()->updateExistingPivot(
            $user->id,
            [
                'status' => 'active',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Update Invitation Notification
        |--------------------------------------------------------------------------
        */
        $this->updateInvitationNotification(
            $user,
            $group,
            'accepted'
        );

        return $this->requestResponse(
            $request,
            true,
            "You joined {$group->name} successfully.",
            200,
            [
                'status' => 'accepted',
                'group_url' => route(
                    'community.groups.show',
                    $group
                ),
            ]
        );
    }

    /**
     * Reject an invitation received when the group was created.
     *
     * Rejection removes the pending membership.
     */
    public function rejectInvitation(
        Request $request,
        Group $group
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Find Pending Membership
        |--------------------------------------------------------------------------
        */
        $membership = $group->users()
            ->where('user_id', $user->id)
            ->first();

        if (
            !$membership
            || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])
        ) {
            return $this->requestResponse(
                $request,
                false,
                'No pending invitation found.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Invitation
        |--------------------------------------------------------------------------
        */
        if (!$this->hasPendingInvitation($user, $group)) {
            return $this->requestResponse(
                $request,
                false,
                'No valid group invitation found.',
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Pending Membership
        |--------------------------------------------------------------------------
        */
        $group->users()->detach($user->id);

        /*
        |--------------------------------------------------------------------------
        | Update Invitation Notification
        |--------------------------------------------------------------------------
        */
        $this->updateInvitationNotification(
            $user,
            $group,
            'rejected'
        );

        return $this->requestResponse(
            $request,
            true,
            "You rejected the invitation to {$group->name}.",
            200,
            [
                'status' => 'rejected',
            ]
        );
    }

    /**
     * Check whether the authenticated user has a group invitation
     * for the specified group.
     */
    private function hasPendingInvitation(
        User $user,
        Group $group
    ): bool {
        return $user->notifications()
            ->get()
            ->contains(function ($notification) use ($group, $user) {
                $data = is_array($notification->data)
                    ? $notification->data
                    : [];

                return ($data['type'] ?? null) === 'group_invitation'
                    && (int) ($data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($data['user_id'] ?? 0) === (int) $user->id
                    && ($data['status'] ?? 'pending') === 'pending';
            });
    }

    /**
     * Update the invitation notification after accept/reject.
     */
    private function updateInvitationNotification(
        User $user,
        Group $group,
        string $status
    ): void {
        $notification = $user->notifications()
            ->get()
            ->first(function ($notification) use ($group, $user) {
                $data = is_array($notification->data)
                    ? $notification->data
                    : [];

                return ($data['type'] ?? null) === 'group_invitation'
                    && (int) ($data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($data['user_id'] ?? 0) === (int) $user->id;
            });

        if (!$notification) {
            return;
        }

        $data = is_array($notification->data)
            ? $notification->data
            : [];

        $data['status'] = $status;

        $notification->update([
            'data' => $data,
            'read_at' => now(),
        ]);
    }

    /**
     * Update the owner's join-request notification after accept/reject.
     */
    private function updateJoinRequestNotification(
        User $owner,
        Group $group,
        User $requester,
        string $status
    ): void {
        $notifications = $owner->notifications()
            ->get()
            ->filter(function ($n) use ($group, $requester) {
                $data = is_array($n->data) ? $n->data : [];
                return ($data['type'] ?? null) === 'group_join_requested'
                    && (int) ($data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($data['user_id'] ?? 0) === (int) $requester->id;
            });

        foreach ($notifications as $notification) {
            $data = is_array($notification->data) ? $notification->data : [];
            $data['status'] = $status;

            $notification->update([
                'data' => $data,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Return JSON for AJAX requests and normal redirects for
     * regular form requests.
     */
    private function requestResponse(
        Request $request,
        bool $success,
        string $message,
        int $status = 200,
        array $extra = []
    ) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(
                array_merge(
                    [
                        'success' => $success,
                        'message' => $message,
                    ],
                    $extra
                ),
                $status
            );
        }

        return $success
            ? back()->with('success', $message)
            : back()->with('error', $message);
    }
}
