<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use App\Notifications\GroupJoinRequestAccepted;
use App\Notifications\GroupJoinRequestNotification;
use App\Notifications\GroupJoinRequestRejected;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    use ApiResponse;

    /**
     * List groups for discovery + current user membership context.
     */
    public function index(Request $request): JsonResponse
    {
        $user   = $request->user();
        $search = $request->string('search')->trim();

        $groups = Group::query()
            ->withCount('users')
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $userId = $user?->id;

        $groups->getCollection()->transform(function ($group) use ($userId) {
            $membership = $userId
                ? $group->users()
                    ->where('user_id', $userId)
                    ->wherePivot('status', 'active')
                    ->first()
                : null;

            $group->is_owner          = (int) $group->owner_id === (int) $userId;
            $group->is_member         = $membership !== null;
            $group->membership_role   = $membership?->pivot->role ?? null;
            $group->membership_status = $membership?->pivot->status ?? null;

            $group->load('owner:id,name,email');

            return $group;
        });

        return $this->successResponse(
            GroupResource::collection($groups->items())
                ->response()
                ->getData(true),
            'Groups retrieved successfully.',
            200,
            [
                'pagination' => [
                    'current_page' => $groups->currentPage(),
                    'last_page'    => $groups->lastPage(),
                    'per_page'     => $groups->perPage(),
                    'total'        => $groups->total(),
                ],
            ]
        );
    }

    /**
     * Show group details with owner, members, and current user membership.
     */
    public function show(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        $group->loadCount('users');
        $group->load('owner.profile.country', 'posts:id,group_id,title,slug');

        $membership = $user
            ? $group->users()
                ->where('user_id', $user->id)
                ->first()
            : null;

        $isMember = $membership && $membership->pivot->status === 'active';

        $data = $group->toArray() + [
            'owner'             => $group->owner ? [
                'id'        => $group->owner->id,
                'name'      => $group->owner->name,
                'email'     => $group->owner->email,
                'avatar'    => $group->owner->profile?->avatar
                    ? asset('storage/' . ltrim($group->owner->profile->avatar, ''))
                    : 'https://ui-avatars.com/api/?name=' . urlencode($group->owner->name) . '&background=0D8ABC&color=fff',
                'country'   => $group->owner->profile?->country ? [
                    'id'   => $group->owner->profile->country->id,
                    'name' => $group->owner->profile->country->name,
                    'code' => $group->owner->profile->country->code,
                ] : null,
            ] : null,
            'is_owner'          => (int) $group->owner_id === (int) ($user ? $user->id : 0),
            'is_member'         => $isMember,
            'membership_role'   => $membership?->pivot->role ?? null,
            'membership_status' => $membership?->pivot->status ?? null,
            'pending_members_count'    => $pendingCount = ($user && (int) $group->owner_id === (int) $user->id)
                ? $group->users()->wherePivot('status', 'pending')->count()
                : 0,
            'invited_members_count'    => ($user && (int) $group->owner_id === (int) $user->id)
                ? $group->users()->wherePivot('status', 'invited')->count()
                : 0,
        ];

        return $this->successResponse($data, 'Group retrieved successfully.');
    }

    /**
     * Create a new group. Selected members are invited (status = invited),
     * never made active members directly.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility'  => ['nullable', 'in:public,private'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['integer', 'exists:users,id'],
        ]);

        $user = $request->user();

        $data = $validated;
        $data['owner_id'] = $user->id;
        $data['visibility'] = $validated['visibility'] ?? 'public';

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('groups/covers', 'public');
        }

        $group = DB::transaction(function () use ($data, $user, $request, $validated) {
            $group = Group::create([
                'owner_id'    => $data['owner_id'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'visibility'  => $data['visibility'],
                'cover_image' => $data['cover_image'] ?? null,
            ]);

            $group->users()->syncWithoutDetaching([
                $user->id => ['role' => 'owner', 'status' => 'active'],
            ]);

            $memberIds = collect($validated['members'] ?? [])
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

                $existing = $group->users()->where('user_id', $invitedUser->id)->first();
                if ($existing) {
                    $status = $existing->pivot->status ?? null;
                    if (in_array($status, ['active', 'pending', 'invited'])) {
                        continue;
                    }
                }

                $group->users()->syncWithoutDetaching([
                    $invitedUser->id => ['role' => 'member', 'status' => 'invited'],
                ]);

                $invitedUser->notify(new GroupInvitationNotification($group, $user));
            }

            return $group;
        });

        return $this->successResponse(
            new GroupResource($group->load(['owner.profile.country'])),
            'Group created successfully.',
            201,
            ['members_count' => $group->users()->count()]
        );
    }

    /**
     * Update group (owner / admin only). Optionally invite new members.
     */
    public function update(Request $request, Group $group): JsonResponse
    {
        Gate::authorize('update', $group);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility'  => ['nullable', 'in:public,private'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['integer', 'exists:users,id'],
        ]);

        return DB::transaction(function () use ($request, $group, $validated) {
            $group->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'visibility'  => $validated['visibility'] ?? $group->visibility,
            ]);

            if ($request->hasFile('cover_image')) {
                if ($group->cover_image) {
                    Storage::disk('public')->delete($group->cover_image);
                }
                $group->update([
                    'cover_image' => $request->file('cover_image')->store('groups/covers', 'public'),
                ]);
            }

            $invitedCount = 0;
            $memberIds = collect($validated['members'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->reject(fn ($id) => $id === (int) $request->user()->id)
                ->values();

            foreach ($memberIds as $memberId) {
                $invitedUser = User::find($memberId);
                if (!$invitedUser) {
                    continue;
                }

                $existing = $group->users()->where('user_id', $invitedUser->id)->first();
                if ($existing) {
                    continue;
                }

                $group->users()->syncWithoutDetaching([
                    $invitedUser->id => ['role' => 'member', 'status' => 'invited'],
                ]);

                $invitedUser->notify(new GroupInvitationNotification($group, $request->user()));
                $invitedCount++;
            }

            return $this->successResponse(
                new GroupResource($group),
                'Group updated successfully.' . ($invitedCount ? " {$invitedCount} invitation(s) sent." : ''),
                200,
                ['invited_count' => $invitedCount]
            );
        });
    }

    /**
     * Delete a group (owner only). Cascades to posts + media + cover.
     */
    public function destroy(Request $request, Group $group): JsonResponse
    {
        Gate::authorize('delete', $group);

        DB::transaction(function () use ($group) {
            $group->load('posts.media');

            foreach ($group->posts as $post) {
                foreach ($post->media as $media) {
                    if ($media->file_path) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                    $media->delete();
                }
                if (!empty($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }
                $post->tags()->detach();
                $post->delete();
            }

            if ($group->cover_image) {
                Storage::disk('public')->delete($group->cover_image);
            }

            $group->users()->detach();
            $group->delete();
        });

        return $this->successResponse(
            ['deleted' => true],
            'Group and its posts deleted successfully.'
        );
    }

    /**
     * Request to join a group (becomes pending until owner approves).
     */
    public function join(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        if ((int) $group->owner_id === (int) $user->id) {
            return $this->errorResponse('You are already the owner of this group.', 409);
        }

        $existing = $group->users()->where('user_id', $user->id)->first();

        if ($existing) {
            $status = $existing->pivot->status ?? null;

            if ($status === 'active') {
                return $this->errorResponse('You are already a member of this group.', 409);
            }

            if ($status === 'pending') {
                return $this->errorResponse('Your join request is already pending owner approval.', 422);
            }

            if ($status === 'invited') {
                return $this->errorResponse(
                    'You have an invitation to join this group. Accept the invitation instead.',
                    422,
                    ['action' => 'accept_invitation']
                );
            }
        }

        $group->users()->syncWithoutDetaching([
            $user->id => ['role' => 'member', 'status' => 'pending'],
        ]);

        $owner = User::find($group->owner_id);
        if ($owner && (int) $owner->id !== (int) $user->id) {
            $owner->notify(new GroupJoinRequestNotification($group, $user));
        }

        return $this->successResponse(
            ['status' => 'pending'],
            'Join request sent successfully. Waiting for owner approval.'
        );
    }

    /**
     * Leave a group (owner cannot leave).
     */
    public function leave(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        if ((int) $group->owner_id === (int) $user->id) {
            return $this->errorResponse('Group owners cannot leave their own group.', 403);
        }

        $detached = DB::table('group_user')
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->delete();

        if (!$detached) {
            return $this->errorResponse('You are not a member of this group.', 404);
        }

        return $this->successResponse(['left' => true], 'You have left the group.');
    }

    /**
     * Invite new members (owner / admin only).
     */
    public function invite(Request $request, Group $group): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        $validated = $request->validate([
            'members'   => ['required', 'array', 'min:1'],
            'members.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $invitedCount = 0;

        foreach ($validated['members'] as $memberId) {
            $invitedUser = User::find($memberId);
            if (!$invitedUser) {
                continue;
            }

            $existing = $group->users()->where('user_id', $invitedUser->id)->first();
            if ($existing) {
                continue;
            }

            $group->users()->syncWithoutDetaching([
                $invitedUser->id => ['role' => 'member', 'status' => 'invited'],
            ]);

            $invitedUser->notify(new GroupInvitationNotification($group, $request->user()));
            $invitedCount++;
        }

        return $this->successResponse(
            ['invited_count' => $invitedCount],
            "{$invitedCount} member(s) invited successfully."
        );
    }

    /**
     * Remove a member (owner / admin only).
     */
    public function removeMember(Request $request, Group $group, User $userToRemove): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        if ((int) $userToRemove->id === (int) $group->owner_id) {
            return $this->errorResponse('The group owner cannot be removed.', 422);
        }

        $membership = $group->users()->where('user_id', $userToRemove->id)->first();

        if (!$membership) {
            return $this->errorResponse('This user is not a member of the group.', 404);
        }

        $group->users()->detach($userToRemove->id);

        return $this->successResponse(
            ['removed' => true, 'user_id' => $userToRemove->id],
            "{$userToRemove->name} has been removed from the group."
        );
    }

    /**
     * List group members.
     */
    public function members(Request $request, Group $group): JsonResponse
    {
        $this->authorizeView($request, $group);

        $members = $group->users()
            ->withPivot(['role', 'status', 'created_at'])
            ->with(['profile.country'])
            ->wherePivot('status', 'active')
            ->orderByRaw("CASE WHEN group_user.role = 'owner' THEN 0 ELSE 1 END")
            ->orderBy('group_user.created_at')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            GroupMemberResource::collection($members->items())->response()->getData(true),
            'Members retrieved successfully.',
            200,
            ['pagination' => $this->paginationMeta($members)]
        );
    }

    /**
     * List pending join requests (owner / admin only).
     */
    public function requests(Request $request, Group $group): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        $pending = $group->users()
            ->withPivot(['role', 'status', 'created_at'])
            ->with(['profile.country'])
            ->wherePivot('status', 'pending')
            ->orderBy('group_user.created_at')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            GroupMemberResource::collection($pending->items())->response()->getData(true),
            'Pending join requests retrieved.',
            200,
            ['pagination' => $this->paginationMeta($pending)]
        );
    }

    /**
     * List invited members (owner / admin only).
     */
    public function invitations(Request $request, Group $group): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        $invited = $group->users()
            ->withPivot(['role', 'status', 'created_at'])
            ->with(['profile.country'])
            ->wherePivot('status', 'invited')
            ->orderBy('group_user.created_at')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            GroupMemberResource::collection($invited->items())->response()->getData(true),
            'Invitations retrieved.',
            200,
            ['pagination' => $this->paginationMeta($invited)]
        );
    }

    /**
     * Accept a pending join request (owner / admin).
     */
    public function acceptRequest(Request $request, Group $group, User $targetUser): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        $membership = $group->users()->where('user_id', $targetUser->id)->first();

        if (!$membership || ($membership->pivot->status ?? null) !== 'pending') {
            return $this->errorResponse('No pending join request found.', 422);
        }

        $group->users()->updateExistingPivot($targetUser->id, ['status' => 'active']);

        $targetUser->notify(new GroupJoinRequestAccepted($group));

        $this->updateJoinRequestNotification($request->user(), $group, $targetUser, 'accepted');

        return $this->successResponse(
            ['status' => 'accepted', 'user_id' => $targetUser->id],
            "{$targetUser->name}'s join request has been accepted."
        );
    }

    /**
     * Reject a pending join request or cancel an invitation (owner / admin).
     */
    public function rejectRequest(Request $request, Group $group, User $targetUser): JsonResponse
    {
        Gate::authorize('manageMembers', $group);

        $membership = $group->users()->where('user_id', $targetUser->id)->first();

        if (!$membership || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])) {
            return $this->errorResponse('No pending join request or invitation found.', 422);
        }

        $wasInvited = (($membership->pivot->status ?? null) === 'invited');

        $group->users()->detach($targetUser->id);

        if ($wasInvited) {
            $this->updateInvitationNotification($targetUser, $group, 'cancelled');

            return $this->successResponse(
                ['status' => 'cancelled', 'user_id' => $targetUser->id],
                "Invitation to {$targetUser->name} has been cancelled."
            );
        }

        $targetUser->notify(new GroupJoinRequestRejected($group));

        $this->updateJoinRequestNotification($request->user(), $group, $targetUser, 'rejected');

        return $this->successResponse(
            ['status' => 'rejected', 'user_id' => $targetUser->id],
            "{$targetUser->name}'s join request has been rejected."
        );
    }

    /**
     * Accept a received group invitation (the invited user only).
     */
    public function acceptInvitation(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        $membership = $group->users()->where('user_id', $user->id)->first();

        if (!$membership || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])) {
            return $this->errorResponse('No pending invitation found.', 422);
        }

        if (!$this->hasPendingInvitation($user, $group)) {
            return $this->errorResponse('No valid group invitation found.', 422);
        }

        $group->users()->updateExistingPivot($user->id, ['status' => 'active']);

        $this->updateInvitationNotification($user, $group, 'accepted');

        return $this->successResponse(
            ['status' => 'accepted'],
            "You joined {$group->name} successfully."
        );
    }

    /**
     * Reject a received group invitation (the invited user only).
     */
    public function rejectInvitation(Request $request, Group $group): JsonResponse
    {
        $user = $request->user();

        $membership = $group->users()->where('user_id', $user->id)->first();

        if (!$membership || !in_array($membership->pivot->status ?? null, ['pending', 'invited'])) {
            return $this->errorResponse('No pending invitation found.', 422);
        }

        if (!$this->hasPendingInvitation($user, $group)) {
            return $this->errorResponse('No valid group invitation found.', 422);
        }

        $group->users()->detach($user->id);

        $this->updateInvitationNotification($user, $group, 'rejected');

        return $this->successResponse(
            ['status' => 'rejected'],
            "You rejected the invitation to {$group->name}."
        );
    }

    /**
     * List posts within a group (active members only).
     */
    public function posts(Request $request, Group $group): JsonResponse
    {
        $this->authorizeView($request, $group);

        $user    = $request->user();
        $isOwner = (int) $group->owner_id === (int) ($user ? $user->id : 0);
        $isMember = $user
            ? $group->users()->where('user_id', $user->id)->wherePivot('status', 'active')->exists()
            : false;

        if (!$isMember) {
            return $this->errorResponse('Only active group members can view group posts.', 403);
        }

        $posts = $group->posts()
            ->where('status', PostStatus::PUBLISHED->value)
            ->with([
                'user.profile.country',
                'category',
                'media',
                'tags',
                'likes'   => fn ($q) => $q->when($user, fn ($qq) => $qq->where('user_id', $user->id)),
                'savedBy' => fn ($q) => $q->when($user, fn ($qq) => $qq->where('user_id', $user->id)),
            ])
            ->withCount(['likes', 'comments', 'shares'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return $this->successResponse(
            PostResource::collection($posts->items())->response()->getData(true),
            'Group posts retrieved successfully.',
            200,
            ['pagination' => $this->paginationMeta($posts)]
        );
    }

    /**
     * Check group view access (private groups require active membership).
     */
    protected function authorizeView(Request $request, Group $group): void
    {
        if ($group->visibility === 'private') {
            $user = $request->user();

            if (!$user) {
                abort(403, 'This group is private.');
            }

            $isMember = (int) $group->owner_id === (int) $user->id
                || $group->users()->where('user_id', $user->id)->wherePivot('status', 'active')->exists()
                || in_array($user->role?->value ?? (string) $user->role, ['admin', 'super_admin'], true);

            if (!$isMember) {
                abort(403, 'Only active members can view this private group.');
            }
        }
    }

    /**
     * Check if a user has a group invitation notification.
     */
    protected function hasPendingInvitation(User $user, Group $group): bool
    {
        return $user->notifications()
            ->get()
            ->contains(function ($notification) use ($group, $user) {
                $data = is_array($notification->data) ? $notification->data : [];

                return ($data['type'] ?? null) === 'group_invitation'
                    && (int) ($data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($data['user_id'] ?? 0) === (int) $user->id
                    && ($data['status'] ?? 'pending') === 'pending';
            });
    }

    /**
     * Mark an invitation notification as read after accept/reject.
     */
    protected function updateInvitationNotification(User $user, Group $group, string $status): void
    {
        $notification = $user->notifications()
            ->get()
            ->first(function ($notification) use ($group, $user) {
                $data = is_array($notification->data) ? $notification->data : [];

                return ($data['type'] ?? null) === 'group_invitation'
                    && (int) ($data['group_id'] ?? 0) === (int) $group->id
                    && (int) ($data['user_id'] ?? 0) === (int) $user->id;
            });

        if (!$notification) {
            return;
        }

        $data = is_array($notification->data) ? $notification->data : [];
        $data['status'] = $status;

        $notification->update([
            'data'    => $data,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the owner's join-request notification after accept/reject.
     */
    protected function updateJoinRequestNotification(User $owner, Group $group, User $requester, string $status): void
    {
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
                'data'    => $data,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Pagination meta helper.
     */
    protected function paginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ];
    }
}
