<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Community\CommunityController;
use App\Http\Controllers\Community\GroupController;
use App\Http\Controllers\Community\NotificationController;
use App\Http\Controllers\Community\PostController;
use App\Http\Controllers\Community\UserProfileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/__reaic-test', function () {
    return response('REAIC CURRENT WEB.PHP', 200)
        ->header('Content-Type', 'text/plain');
});
Route::get('/check-auth', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'user' => Auth::user()?->email,
        'guard' => Auth::getDefaultDriver(),
    ]);
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Public Community
|--------------------------------------------------------------------------
*/

Route::get('/', [PostController::class, 'index'])
    ->name('community.index');

Route::get('/community/posts/{post}', [PostController::class, 'show'])
    ->whereNumber('post')
    ->name('community.posts.show');

Route::get(
    '/community/{post}/comments',
    [CommunityController::class, 'loadMoreComments']
)
    ->whereNumber('post')
    ->name('community.posts.comments');

Route::get(
    '/community/profile/{username}',
    [UserProfileController::class, 'show']
)->name('community.profile');

Route::get(
    '/community/profile/{username}/followers',
    [UserProfileController::class, 'followers']
)->name('community.profile.followers');

Route::get(
    '/community/profile/{username}/following',
    [UserProfileController::class, 'following']
)->name('community.profile.following');

/*
|--------------------------------------------------------------------------
| Authenticated Community
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('community')
    ->name('community.')
    ->group(function () {

        Route::get('/test-auth', function () {
            return response()->json([
                'message' => 'AUTH ROUTE WORKING',
                'authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'user' => Auth::user()?->email,
            ]);
        });

        // Posts

        Route::get('/posts/create', [PostController::class, 'create'])
            ->name('posts.create');

        Route::post('/posts', [PostController::class, 'store'])
            ->name('posts.store');

        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
            ->whereNumber('post')
            ->name('posts.edit');

        Route::put('/posts/{post}', [PostController::class, 'update'])
            ->whereNumber('post')
            ->name('posts.update');

        Route::delete('/posts/{post}', [PostController::class, 'destroy'])
            ->whereNumber('post')
            ->name('posts.destroy');

        // Post interactions

        Route::post('/posts/{post}/like', [CommunityController::class, 'like'])
            ->whereNumber('post')
            ->name('posts.like');

        Route::post('/posts/{post}/share', [CommunityController::class, 'share'])
            ->whereNumber('post')
            ->name('posts.share');

        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
            ->whereNumber('post')
            ->name('posts.comment.store');

        Route::post('/posts/{post}/save', [PostController::class, 'toggleSave'])
            ->whereNumber('post')
            ->name('posts.save');

        // Comments

        Route::put('/comments/{comment}', [CommentController::class, 'update'])
            ->name('comments.update');

        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
            ->name('comments.destroy');

        // Groups

        Route::resource('groups', GroupController::class)
            ->parameters([
                'groups' => 'group:slug',
            ]);

        Route::post(
            '/groups/{group:slug}/join',
            [GroupController::class, 'join']
        )->name('groups.join');

        Route::delete(
            '/groups/{group:slug}/leave',
            [GroupController::class, 'leave']
        )->name('groups.leave');

        Route::get(
            '/groups/{group:slug}/members',
            [GroupController::class, 'members']
        )->name('groups.members');

        Route::post('/groups/{group:slug}/requests/{userToAccept}/accept', [GroupController::class, 'acceptRequest'])
            ->name('groups.requests.accept');

        Route::post('/groups/{group:slug}/requests/{userToReject}/reject', [GroupController::class, 'rejectRequest'])
    ->name('groups.requests.reject');

        // Community pages

        Route::get('/saved', [CommunityController::class, 'saved'])
            ->name('saved');

     


        Route::get(
            '/notifications/unread-count',
            [NotificationController::class, 'unreadCount']
        )->name('notifications.unread-count');

        Route::patch(
            '/notifications/{id}/read',
            [NotificationController::class, 'markAsRead']
        )->name('notifications.read');

        Route::patch(
            '/notifications/read-all',
            [NotificationController::class, 'markAllAsRead']
        )->name('notifications.read-all');

        // My profile

        Route::get(
            '/profile',
            [UserProfileController::class, 'myProfile']
        )->name('profile.me');

        Route::get(
            '/profile/edit',
            [UserProfileController::class, 'edit']
        )->name('profile.edit');

        Route::patch(
            '/profile',
            [UserProfileController::class, 'update']
        )->name('profile.update');

        // Activity

        Route::get(
            '/activity',
            [CommunityController::class, 'activity']
        )->name('activity');
    });

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        // Users

        Route::get(
            '/users',
            [AdminUserController::class, 'index']
        )->name('users');

        Route::put(
            '/users/{user}',
            [AdminUserController::class, 'update']
        )->name('users.update');

        Route::patch(
            '/users/{user}/status',
            [AdminUserController::class, 'updateStatus']
        )->name('users.updateStatus');

        // Posts

        Route::get(
            '/posts',
            [PostController::class, 'adminIndex']
        )->name('posts');

        Route::patch(
            '/posts/{post}',
            [PostController::class, 'adminUpdate']
        )->name('posts.adminUpdate');

        Route::patch(
            '/posts/{post}/status',
            [PostController::class, 'updateStatus']
        )->name('posts.updateStatus');

/*
|--------------------------------------------------------------------------
| Comments
|--------------------------------------------------------------------------
*/

Route::get(
    '/comments',
    [\App\Http\Controllers\Admin\CommunityCommentController::class, 'index']
)->name('comments');


Route::put(
    '/comments/{comment}',
    [\App\Http\Controllers\Admin\CommunityCommentController::class, 'update']
)->name('comments.update');


Route::patch(
    '/comments/{comment}/toggle-hide',
    [\App\Http\Controllers\Admin\CommunityCommentController::class, 'toggleHide']
)->name('comments.toggleHide');


Route::delete(
    '/comments/{comment}',
    [\App\Http\Controllers\Admin\CommunityCommentController::class, 'destroy']
)->name('comments.destroy');


Route::post(
    '/comments/bulk-action',
    [\App\Http\Controllers\Admin\CommunityCommentController::class, 'bulkAction']
)->name('comments.bulkAction');

        // Countries

        Route::resource('countries', CountryController::class);

        // Reports

        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
    });

Route::middleware('auth')->group(function () {
    Route::post('/users/{user}/follow', ['App\\Http\\Controllers\\Community\\FollowController', 'store'])
        ->name('users.follow');

    Route::delete('/users/{user}/follow', ['App\\Http\\Controllers\\Community\\FollowController', 'destroy'])
        ->name('users.unfollow');

    Route::post('/users/{user}/follow/toggle', ['App\\Http\\Controllers\\Community\\FollowController', 'toggle'])
        ->name('users.follow.toggle');

    Route::get('/users/{user}/follow/status', ['App\\Http\\Controllers\\Community\\FollowController', 'status'])
        ->name('users.follow.status');

    Route::get('/users/{user}/followers', ['App\\Http\\Controllers\\Community\\FollowController', 'followers'])
        ->name('users.followers');

    Route::post('/community/followers/{user}/remove', ['App\\Http\\Controllers\\Community\\FollowController', 'removeFollower'])
        ->name('users.followers.remove');

    Route::get('/users/{user}/following', ['App\\Http\\Controllers\\Community\\FollowController', 'following'])
        ->name('users.following');
});
Route::delete('/community/notifications/{id}', [NotificationController::class, 'destroy'])->name('community.notifications.destroy');


Route::middleware(['auth'])->group(function () {

    Route::resource('admin/tags', TagController::class)
        ->except(['show', 'create', 'edit']);

});


Route::middleware('auth')->group(function () {

    // Notification page
    Route::get('/community/notifications', [NotificationController::class, 'page'])
        ->name('community.notifications');

    // Topbar AJAX/JSON
    Route::get('/community/notifications/data', [NotificationController::class, 'index'])
        ->name('community.notifications.data');

});