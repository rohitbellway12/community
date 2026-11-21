<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\BlockedUser;
use App\Models\Follow;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    use ApiResponse;

    /**
     * List all users blocked by the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?? $request->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        $blockedUsers = $user->blockedUsers()
            ->with('profile.country')
            ->orderBy('blocked_users.created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return $this->successResponse(
            UserResource::collection($blockedUsers->items())->resolve(),
            'Blocked users retrieved successfully.',
            200,
            ['pagination' => $this->paginationMeta($blockedUsers)]
        );
    }

    /**
     * Block a target user.
     */
    public function block(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user('sanctum') ?? $request->user();
        if (!$currentUser) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        if ($currentUser->id === $user->id) {
            return $this->errorResponse('You cannot block yourself.', 422);
        }

        // Create or ensure block record exists
        BlockedUser::firstOrCreate([
            'blocker_id' => $currentUser->id,
            'blocked_id' => $user->id,
        ]);

        // Break any existing follow relationships in either direction
        Follow::where(function ($q) use ($currentUser, $user) {
            $q->where('follower_id', $currentUser->id)->where('following_id', $user->id);
        })->orWhere(function ($q) use ($currentUser, $user) {
            $q->where('follower_id', $user->id)->where('following_id', $currentUser->id);
        })->delete();

        return $this->successResponse(
            [
                'blocked' => true,
                'user_id' => $user->id,
            ],
            'User has been blocked successfully.'
        );
    }

    /**
     * Unblock a target user.
     */
    public function unblock(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user('sanctum') ?? $request->user();
        if (!$currentUser) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        $deleted = BlockedUser::where('blocker_id', $currentUser->id)
            ->where('blocked_id', $user->id)
            ->delete();

        if (!$deleted) {
            return $this->errorResponse('User is not blocked.', 404);
        }

        return $this->successResponse(
            [
                'blocked' => false,
                'user_id' => $user->id,
            ],
            'User has been unblocked successfully.'
        );
    }

    /**
     * Check block status between current user and target user.
     */
    public function status(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user('sanctum') ?? $request->user();
        if (!$currentUser) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        $isBlocking = BlockedUser::where('blocker_id', $currentUser->id)
            ->where('blocked_id', $user->id)
            ->exists();

        $isBlockedBy = BlockedUser::where('blocker_id', $user->id)
            ->where('blocked_id', $currentUser->id)
            ->exists();

        return $this->successResponse([
            'is_blocking'   => $isBlocking,
            'is_blocked_by' => $isBlockedBy,
        ]);
    }

    /**
     * Pagination metadata helper.
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
