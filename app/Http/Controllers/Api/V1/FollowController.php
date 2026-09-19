<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Follow;
use App\Models\User;
use App\Notifications\UserFollowedNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    use ApiResponse;

    /**
     * List a user's followers (paginated).
     */
    public function followers(Request $request, User $user): JsonResponse
    {
        $followers = $user->followers()
            ->with('profile.country')
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')->whereNull('deleted_at')])
            ->orderBy('follows.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            UserResource::collection($followers->items())->response()->getData(true),
            'Followers retrieved successfully.',
            200,
            ['pagination' => $this->paginationMeta($followers)]
        );
    }

    /**
     * List a user's following (paginated).
     */
    public function following(Request $request, User $user): JsonResponse
    {
        $following = $user->following()
            ->with('profile.country')
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')->whereNull('deleted_at')])
            ->orderBy('follows.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            UserResource::collection($following->items())->response()->getData(true),
            'Following list retrieved successfully.',
            200,
            ['pagination' => $this->paginationMeta($following)]
        );
    }

    /**
     * Current user's follow status + counts for a target user.
     */
    public function status(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        $isFollowing = $currentUser
            ? Follow::where('follower_id', $currentUser->id)
                ->where('following_id', $user->id)
                ->exists()
            : false;

        return $this->successResponse([
            'is_following'    => $isFollowing,
            'followers_count' => (int) Follow::where('following_id', $user->id)->count(),
            'following_count' => (int) Follow::where('follower_id', $user->id)->count(),
        ], 'Follow status retrieved.');
    }

    /**
     * Follow a user (creates follow + fires notification).
     */
    public function store(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ((int) $currentUser->id === (int) $user->id) {
            return $this->errorResponse('You cannot follow yourself.', 422);
        }

        $follow = Follow::firstOrCreate([
            'follower_id'  => $currentUser->id,
            'following_id' => $user->id,
        ]);

        if ($follow->wasRecentlyCreated) {
            $user->notify(new UserFollowedNotification($currentUser));
        }

        return $this->successResponse([
            'is_following' => true,
            'user_id'      => $user->id,
        ], $follow->wasRecentlyCreated
            ? 'User followed successfully.'
            : 'You are already following this user.');
    }

    /**
     * Unfollow a user.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        $deleted = Follow::where('follower_id', $currentUser->id)
            ->where('following_id', $user->id)
            ->delete();

        return $this->successResponse([
            'is_following' => false,
            'user_id'      => $user->id,
        ], $deleted
            ? 'User unfollowed successfully.'
            : 'You are not following this user.');
    }

    /**
     * Remove a follower from your account (owner only).
     */
    public function removeFollower(Request $request, User $follower): JsonResponse
    {
        $currentUser = $request->user();

        if ((int) $follower->id === (int) $currentUser->id) {
            return $this->errorResponse('Cannot remove yourself as a follower.', 422);
        }

        $deleted = Follow::where('follower_id', $follower->id)
            ->where('following_id', $currentUser->id)
            ->delete();

        return $this->successResponse(
            ['removed' => true, 'follower_id' => $follower->id],
            $deleted
                ? "{$follower->name} has been removed from your followers."
                : 'This user is not following you.'
        );
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
