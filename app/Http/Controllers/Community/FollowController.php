<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function store(User $user): JsonResponse|RedirectResponse
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return $this->response(
                request: request(),
                success: false,
                message: 'You cannot follow yourself.',
                status: 422
            );
        }

        $follow = Follow::firstOrCreate([
            'follower_id' => $authUser->id,
            'following_id' => $user->id,
        ]);

        return $this->response(
            request: request(),
            success: true,
            message: $follow->wasRecentlyCreated
                ? 'User followed successfully.'
                : 'You are already following this user.',
            data: [
                'following' => true,
                'user_id' => $user->id,
            ]
        );
    }

    public function destroy(User $user): JsonResponse|RedirectResponse
    {
        $deleted = Follow::where('follower_id', Auth::id())
            ->where('following_id', $user->id)
            ->delete();

        return $this->response(
            request: request(),
            success: true,
            message: $deleted
                ? 'User unfollowed successfully.'
                : 'You are not following this user.',
            data: [
                'following' => false,
                'user_id' => $user->id,
            ]
        );
    }

    public function toggle(User $user): JsonResponse|RedirectResponse
    {
        $authUser = Auth::user();

        if ($authUser->id === $user->id) {
            return $this->response(
                request: request(),
                success: false,
                message: 'You cannot follow yourself.',
                status: 422
            );
        }

        $follow = Follow::where('follower_id', $authUser->id)
            ->where('following_id', $user->id)
            ->first();

        if ($follow) {
            $follow->delete();

            return $this->response(
                request: request(),
                success: true,
                message: 'User unfollowed successfully.',
                data: [
                    'following' => false,
                    'user_id' => $user->id,
                ]
            );
        }

        Follow::create([
            'follower_id' => $authUser->id,
            'following_id' => $user->id,
        ]);

        return $this->response(
            request: request(),
            success: true,
            message: 'User followed successfully.',
            data: [
                'following' => true,
                'user_id' => $user->id,
            ]
        );
    }

    public function status(User $user): JsonResponse
    {
        $following = Follow::where('follower_id', Auth::id())
            ->where('following_id', $user->id)
            ->exists();

        return response()->json([
            'success' => true,
            'following' => $following,
            'followers_count' => Follow::where(
                'following_id',
                $user->id
            )->count(),
        ]);
    }

    public function followers(User $user)
    {
        $followers = Follow::with('follower')
            ->where('following_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $followers,
        ]);
    }
    public function removeFollower(User $user)
{
    $currentUser = Auth::user();

    Follow::where('follower_id', $user->id)
        ->where('following_id', $currentUser->id)
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Follower removed successfully.',
    ]);
}

    public function following(User $user)
    {
        $following = Follow::with('following')
            ->where('follower_id', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $following,
        ]);
    }

    private function response(
        Request $request,
        bool $success,
        string $message,
        array $data = [],
        int $status = 200
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data' => $data,
            ], $status);
        }

        return back()->with(
            $success ? 'success' : 'error',
            $message
        );
    }
}