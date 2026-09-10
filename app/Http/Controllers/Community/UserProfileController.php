<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Country;
use App\Models\Profile;
use App\Models\Like;
use App\Models\Share;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a user's public profile.
     *
     * Note:
     * /community/profile/{username} can also match
     * /community/profile/edit because of the dynamic {username}
     * parameter. We handle "edit" here so the existing route
     * structure does not need to be changed.
     */
    public function show(string $username): View|RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Handle Profile Edit URL
        |--------------------------------------------------------------------------
        |
        | Because /community/profile/{username} is a dynamic public route,
        | Laravel may reach this method when the URL is:
        |
        | /community/profile/edit
        |
        | In that case, send the authenticated user to the existing edit()
        | method instead of looking for a profile with username "edit".
        |
        */

        if ($username === 'edit') {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            return $this->edit();
        }

        /*
        |--------------------------------------------------------------------------
        | Public Profile
        |--------------------------------------------------------------------------
        */

        $profile = Profile::with([
            'user.stats',
            'country',
        ])
            ->where('username', $username)
            ->firstOrFail();

        $user = $profile->user;

        /*
        |--------------------------------------------------------------------------
        | User Posts
        |--------------------------------------------------------------------------
        */

        $posts = $user->posts()
            ->with([
                'category',
                'media',
                'tags',
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
            ])
            ->withCount([
                'likes',
                'comments',
                'shares',
            ])
            ->latest()
            ->paginate(5, ['*'], 'posts_page');

        /*
        |--------------------------------------------------------------------------
        | User Comments
        |--------------------------------------------------------------------------
        */

        $comments = $user->comments()
            ->with([
                'post.category',
                'post.user.profile',
                'parent.user.profile',
            ])
            ->whereNull('comments.deleted_at')
            ->whereHas('post')
            ->latest()
            ->paginate(10, ['*'], 'comments_page');

        /*
        |--------------------------------------------------------------------------
        | User Activities
        |--------------------------------------------------------------------------
        */

        $activities = $user->activityLogs()
            ->latest()
            ->paginate(10, ['*'], 'activity_page');

        /*
        |--------------------------------------------------------------------------
        | Profile Statistics
        |--------------------------------------------------------------------------
        */

        $profileStats = [
            'posts' => $user->posts()
                ->whereNull('posts.deleted_at')
                ->count(),

            'comments' => $user->comments()
                ->whereNull('comments.deleted_at')
                ->whereHas('post')
                ->count(),

            'likes_received' => Like::whereHas('post', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->whereNull('posts.deleted_at');
            })->count(),

            'shares' => Share::whereHas('post', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->whereNull('posts.deleted_at');
            })->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Top Contributors
        |--------------------------------------------------------------------------
        */

        $topContributors = User::getTopContributors(5);

        /*
        |--------------------------------------------------------------------------
        | Public Profile View
        |--------------------------------------------------------------------------
        */

        return view(
            'community.profile.show',
            compact(
                'profile',
                'user',
                'posts',
                'comments',
                'activities',
                'profileStats',
                'topContributors'
            )
        );
    }

    /**
     * Show the authenticated user's profile edit page.
     */
    public function edit(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)
            ->with([
                'user.stats',
                'country',
            ])
            ->firstOrFail();

        $this->authorize('update', $profile);

        $countries = Country::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | User Posts
        |--------------------------------------------------------------------------
        */

        $posts = $user->posts()
            ->latest()
            ->paginate(5, ['*'], 'posts_page');

        /*
        |--------------------------------------------------------------------------
        | User Comments
        |--------------------------------------------------------------------------
        */

        $comments = $user->comments()
            ->latest()
            ->paginate(5, ['*'], 'comments_page');

        /*
        |--------------------------------------------------------------------------
        | User Activities
        |--------------------------------------------------------------------------
        */

        $activities = $user->activityLogs()
            ->latest()
            ->paginate(10, ['*'], 'activity_page');

        /*
        |--------------------------------------------------------------------------
        | Profile Statistics
        |--------------------------------------------------------------------------
        */

        $profileStats = [
            'posts' => $user->posts()
                ->whereNull('posts.deleted_at')
                ->count(),

            'comments' => $user->comments()
                ->whereNull('comments.deleted_at')
                ->count(),

            'likes_received' => Like::whereHas('post', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->whereNull('posts.deleted_at');
            })->count(),

            'shares' => Share::whereHas('post', function ($query) use ($user) {
                $query
                    ->where('user_id', $user->id)
                    ->whereNull('posts.deleted_at');
            })->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Top Contributors
        |--------------------------------------------------------------------------
        */

        $topContributors = User::getTopContributors(5);

        /*
        |--------------------------------------------------------------------------
        | Edit Profile View
        |--------------------------------------------------------------------------
        */

        return view(
            'community.profile.edit',
            compact(
                'profile',
                'user',
                'countries',
                'posts',
                'comments',
                'activities',
                'profileStats',
                'topContributors'
            )
        );
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)
            ->firstOrFail();

        $this->authorize('update', $profile);

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Avatar
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('avatar')) {
            if (
                $profile->avatar &&
                Storage::disk('public')->exists($profile->avatar)
            ) {
                Storage::disk('public')->delete($profile->avatar);
            }

            $data['avatar'] = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Cover Image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cover_image')) {
            if (
                $profile->cover_image &&
                Storage::disk('public')->exists($profile->cover_image)
            ) {
                Storage::disk('public')->delete($profile->cover_image);
            }

            $data['cover_image'] = $request
                ->file('cover_image')
                ->store('covers', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Save Profile
        |--------------------------------------------------------------------------
        */

        $profile->update($data);

        return redirect()
            ->route('community.profile', [
                'username' => $profile->username,
            ])
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Display a user's followers.
     */
    public function followers(string $username): View
    {
        $profile = Profile::with('user')
            ->where('username', $username)
            ->firstOrFail();

        $user = $profile->user;

        $followers = $user->followers()
            ->with('profile')
            ->paginate(15);

        return view(
            'community.profile.followers',
            compact(
                'profile',
                'user',
                'followers'
            )
        );
    }

    /**
     * Display users that this user follows.
     */
    public function following(string $username): View
    {
        $profile = Profile::with('user')
            ->where('username', $username)
            ->firstOrFail();

        $user = $profile->user;

        $following = $user->following()
            ->with('profile')
            ->paginate(15);

        return view(
            'community.profile.following',
            compact(
                'profile',
                'user',
                'following'
            )
        );
    }

    /**
     * Display the authenticated user's profile directly via their username.
     */
    public function myProfile(): RedirectResponse
    {
        $user = Auth::user();

        $profile = Profile::where('user_id', $user->id)
            ->firstOrFail();

        return redirect()->route(
            'community.profile',
            [
                'username' => $profile->username,
            ]
        );
    }
}