<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    use ApiResponse;

    /**
     * Resolve a user by numeric ID or username.
     */
    protected function findUser(string $idOrUsername): ?User
    {
        if (is_numeric($idOrUsername)) {
            $user = User::with(['profile.country'])->find($idOrUsername);
            if ($user) {
                return $user;
            }
        }

        return User::whereHas('profile', function ($q) use ($idOrUsername) {
            $q->where('username', $idOrUsername);
        })->with(['profile.country'])->first();
    }

    /**
     * Display a user's public profile with statistics and follow status.
     *
     * GET /api/v1/users/{id_or_username}
     */
    public function show(Request $request, string $idOrUsername): JsonResponse
    {
        $user = $this->findUser($idOrUsername);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        $viewer = $request->user('sanctum');
        $isSelf = $viewer && (int) $viewer->id === (int) $user->id;

        $postsCount = $user->posts()
            ->where('status', PostStatus::PUBLISHED->value)
            ->whereNull('deleted_at')
            ->count();

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        $likesReceived = Like::whereHas('post', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNull('deleted_at');
        })->count();

        $isFollowing = false;
        if ($viewer && !$isSelf) {
            $isFollowing = $viewer->following()
                ->where('following_id', $user->id)
                ->exists();
        }

        $profile = $user->profile;
        $avatarUrl = null;
        if ($profile && $profile->avatar) {
            $avatarUrl = str_starts_with($profile->avatar, 'http')
                ? $profile->avatar
                : asset('storage/' . $profile->avatar);
        } else {
            $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff';
        }

        $coverUrl = null;
        if ($profile && $profile->cover_image) {
            $coverUrl = str_starts_with($profile->cover_image, 'http')
                ? $profile->cover_image
                : asset('storage/' . $profile->cover_image);
        }

        $data = [
            'id'             => $user->id,
            'name'           => $user->name,
            'email'          => $isSelf ? $user->email : null,
            'role'           => $user->role?->value ?? (string) $user->role,
            'is_self'        => $isSelf,
            'is_following'   => $isFollowing,
            'profile'        => [
                'id'          => $profile?->id,
                'username'    => $profile?->username,
                'bio'         => $profile?->bio,
                'location'    => $profile?->location,
                'avatar'      => $avatarUrl,
                'cover_image' => $coverUrl,
                'country'     => $profile?->country ? [
                    'id'   => $profile->country->id,
                    'name' => $profile->country->name,
                    'code' => $profile->country->code ?? null,
                ] : null,
                'joined_at'   => $profile?->joined_at?->toIso8601String() ?? $user->created_at?->toIso8601String(),
            ],
            'stats'          => [
                'posts_count'     => $postsCount,
                'followers_count' => $followersCount,
                'following_count' => $followingCount,
                'likes_received'  => $likesReceived,
                'referred_count'  => (int) $user->referred_count,
            ],
            'created_at'        => $user->created_at?->toIso8601String(),
            'activity_progress' => app(\App\Services\ProfileActivityService::class)->calculateProgress($user),
        ];

        return $this->successResponse($data, 'User profile retrieved successfully.');
    }

    /**
     * Get posts created by a specific user.
     *
     * GET /api/v1/users/{id_or_username}/posts
     */
    public function posts(Request $request, string $idOrUsername): JsonResponse
    {
        $user = $this->findUser($idOrUsername);

        if (!$user) {
            return $this->errorResponse('User not found.', 404);
        }

        $viewer = $request->user('sanctum');
        $isSelf = $viewer && (int) $viewer->id === (int) $user->id;

        $postsQuery = Post::query()
            ->where('user_id', $user->id)
            ->with([
                'user.profile.country',
                'category',
                'media',
                'tags',
                'likes' => fn ($q) => $viewer ? $q->where('user_id', $viewer->id) : $q->whereRaw('1 = 0'),
                'savedBy' => fn ($q) => $viewer ? $q->where('user_id', $viewer->id) : $q->whereRaw('1 = 0'),
            ])
            ->withCount(['likes', 'comments', 'shares'])
            ->latest();

        // If not viewing own posts, restrict to published public posts
        if (!$isSelf) {
            $postsQuery->where('status', PostStatus::PUBLISHED->value)
                ->where('visibility', 'public')
                ->where(function ($q) {
                    $q->whereNull('group_id')
                      ->orWhereHas('group', function ($g) {
                          $g->where('visibility', 'public');
                      });
                });
        }

        $perPage = min($request->integer('per_page', 15), 50);
        $posts = $postsQuery->paginate($perPage);

        return $this->successResponse(
            PostResource::collection($posts)->response()->getData(true),
            'User posts retrieved successfully.'
        );
    }

    /**
     * Update the authenticated user's profile.
     *
     * POST /api/v1/profile/update
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile ?? Profile::firstOrCreate(['user_id' => $user->id], [
            'username' => 'user_' . $user->id . '_' . uniqid(),
            'joined_at' => now(),
        ]);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'username'    => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('profiles', 'username')->ignore($profile->id),
            ],
            'bio'         => ['nullable', 'string', 'max:500'],
            'location'    => ['nullable', 'string', 'max:100'],
            'country_id'  => ['nullable', 'integer', 'exists:countries,id'],
            'avatar'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        // Handle Avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar && !str_starts_with($profile->avatar, 'http') && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle Cover image upload
        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image && !str_starts_with($profile->cover_image, 'http') && Storage::disk('public')->exists($profile->cover_image)) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        // Update user name
        $user->update(['name' => trim($validated['name'])]);

        // Update profile
        unset($validated['name']);
        $profile->update($validated);

        $user->load(['profile.country']);

        return $this->successResponse(
            new UserResource($user),
            'Profile updated successfully.'
        );
    }

    /**
     * Get recent activity logs for the authenticated user.
     *
     * GET /api/v1/user/activity
     */
    public function activity(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min($request->integer('per_page', 15), 50);

        $activities = $user->activityLogs()
            ->with('subject')
            ->latest()
            ->paginate($perPage);

        return $this->successResponse(
            $activities,
            'User activity retrieved successfully.'
        );
    }

    /**
     * Get top community contributors / leaderboard.
     *
     * GET /api/v1/leaderboard
     */
    public function topContributors(Request $request): JsonResponse
    {
        $limit = min($request->integer('limit', 10), 50);
        $contributors = User::getTopContributors($limit);

        return $this->successResponse(
            UserResource::collection($contributors),
            'Top contributors retrieved successfully.'
        );
    }

    /**
     * Get authenticated user's activity progress checklist.
     *
     * GET /api/v1/profile/activities
     */
    public function activityProgress(Request $request): JsonResponse
    {
        $user = $request->user('sanctum') ?: auth()->user();
        if (!$user) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        $progress = app(\App\Services\ProfileActivityService::class)->calculateProgress($user);

        return $this->successResponse($progress, 'Profile activity progress retrieved successfully.');
    }
}
