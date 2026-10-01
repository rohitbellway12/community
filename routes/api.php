<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\FollowController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GuidelineController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ReferralController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\StudentTestController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api/v1
| Routes inside auth:sanctum group require Bearer token authentication
|
*/

Route::prefix('v1')->group(function () {

    // ============================================================
    // PUBLIC APIs (No Auth Required)
    // ============================================================

    // Countries, Categories, Tags, Banners, Guidelines
    Route::get('countries', [CountryController::class, 'index'])->name('api.v1.countries.index');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
    Route::get('tags', [TagController::class, 'index'])->name('api.v1.tags.index');
    Route::get('banners', [BannerController::class, 'index'])->name('api.v1.banners.index');
    Route::get('guidelines', [GuidelineController::class, 'index'])->name('api.v1.guidelines.index');
    Route::get('leaderboard', [UserProfileController::class, 'topContributors'])->name('api.v1.leaderboard');

    // Events (Public)
    Route::get('active-event', [EventController::class, 'activeEvent'])->name('api.v1.active-event');
    Route::get('events/{slug}', [EventController::class, 'show'])->name('api.v1.events.show');
    Route::get('events/{slug}/leaderboard', [EventController::class, 'leaderboard'])->name('api.v1.events.leaderboard');

    // Posts (Public Read)
    Route::get('posts', [PostController::class, 'index'])->name('api.v1.posts.index');
    Route::get('posts/{post}', [PostController::class, 'show'])->whereNumber('post')->name('api.v1.posts.show');

    // Post Comments (Public Read)
    Route::get('posts/{post}/comments', [CommentController::class, 'index'])->whereNumber('post')->name('api.v1.posts.comments.index');
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies'])->whereNumber('comment')->name('api.v1.comments.replies');

    // User Profiles (Public)
    Route::get('users/{id_or_username}', [UserProfileController::class, 'show'])->name('api.v1.users.show');
    Route::get('users/{id_or_username}/posts', [UserProfileController::class, 'posts'])->name('api.v1.users.posts');

    // Groups (Public Read)
    Route::get('groups', [GroupController::class, 'index'])->name('api.v1.groups.index');
    Route::get('groups/{group}', [GroupController::class, 'show'])->name('api.v1.groups.show');

    // Tests (Public Read)
    Route::get('tests', [StudentTestController::class, 'index'])->name('api.v1.tests.index');
    Route::get('tests/{test}', [StudentTestController::class, 'show'])->name('api.v1.tests.show');

    // ============================================================
    // AUTHENTICATION
    // ============================================================

    Route::prefix('auth')->group(function () {

        // Public Auth (No Auth Token Required)
        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('api.v1.auth.forgot-password');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('api.v1.auth.verify-otp');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('api.v1.auth.reset-password');

        // Authenticated Auth (Auth Token Required)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::post('change-password', [AuthController::class, 'changePassword'])->name('api.v1.auth.change-password');
            Route::delete('delete-account', [AuthController::class, 'deleteAccount'])->name('api.v1.auth.delete-account');
        });
    });

    // ============================================================
    // USER PRESENCE & DEVICE
    // ============================================================

    // Heartbeat - Mark User Online + Save Device Info
    Route::middleware('auth:sanctum')->post('ping', function (\Illuminate\Http\Request $request) {
        $user = $request->user('sanctum');

        if ($user) {
            $user->update([
                'last_seen_at' => now(),
                'device_type'  => $request->header('X-Device-Type') ?: $request->input('device_type'),
                'device_os'    => $request->header('X-Device-OS') ?: $request->input('device_os'),
                'browser'      => $request->header('X-Browser') ?: $request->input('browser'),
                'ip_address'   => $request->ip(),
            ]);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Pong',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('api.v1.ping');

    // Device Management
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('device', [DeviceController::class, 'store'])->name('api.v1.device.store');
        Route::get('device', [DeviceController::class, 'show'])->name('api.v1.device.show');
    });

    // FCM Device Tokens (Register, List, Delete)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('device-tokens', [DeviceTokenController::class, 'store'])->name('api.v1.device-tokens.store');
        Route::get('device-tokens', [DeviceTokenController::class, 'index'])->name('api.v1.device-tokens.index');
        Route::delete('device-tokens/{deviceToken}', [DeviceTokenController::class, 'destroy'])->name('api.v1.device-tokens.destroy');
    });

    // ============================================================
    // POSTS (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        // Saved Posts
        Route::get('posts/saved', [PostController::class, 'saved'])->name('api.v1.posts.saved');

        // Post CRUD
        Route::post('posts', [PostController::class, 'store'])->name('api.v1.posts.store');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('api.v1.posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('api.v1.posts.destroy');

        // Post Actions
        Route::post('posts/{post}/like', [PostController::class, 'like'])->name('api.v1.posts.like');
        Route::post('posts/{post}/save', [PostController::class, 'save'])->name('api.v1.posts.save');
        Route::post('posts/{post}/share', [PostController::class, 'share'])->name('api.v1.posts.share');
        Route::post('posts/{post}/mark-solved', [PostController::class, 'markSolved'])->name('api.v1.posts.mark-solved');

        // Comment CRUD
        Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('api.v1.posts.comments.store');
        Route::put('comments/{comment}', [CommentController::class, 'update'])->name('api.v1.comments.update');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('api.v1.comments.destroy');
        Route::post('comments/{comment}/like', [CommentController::class, 'like'])->name('api.v1.comments.like');

        // Notification Actions
        Route::get('notifications', [NotificationController::class, 'index'])->name('api.v1.notifications.index');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.v1.notifications.unread-count');
        Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.v1.notifications.read');
        Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.v1.notifications.read-all');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('api.v1.notifications.destroy');

        // Profile Update & Activity
        Route::post('profile/update', [UserProfileController::class, 'update'])->name('api.v1.profile.update');
        Route::get('profile/activities', [UserProfileController::class, 'activityProgress'])->name('api.v1.profile.activities');
        Route::get('user/activity', [UserProfileController::class, 'activity'])->name('api.v1.user.activity');

        // Report Content
        Route::post('reports', [ReportController::class, 'store'])->name('api.v1.reports.store');
    });

    // ============================================================
    // FOLLOW (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('users/{user}/follow/status', [FollowController::class, 'status'])->name('api.v1.users.follow.status');
        Route::post('users/{user}/follow', [FollowController::class, 'store'])->name('api.v1.users.follow');
        Route::delete('users/{user}/follow', [FollowController::class, 'destroy'])->name('api.v1.users.unfollow');
        Route::delete('users/{follower}/follower/remove', [FollowController::class, 'removeFollower'])->name('api.v1.users.follower.remove');
    });

    // ============================================================
    // GROUPS (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        // Group CRUD
        Route::post('groups', [GroupController::class, 'store'])->name('api.v1.groups.store');
        Route::put('groups/{group}', [GroupController::class, 'update'])->name('api.v1.groups.update');
        Route::delete('groups/{group}', [GroupController::class, 'destroy'])->name('api.v1.groups.destroy');

        // Membership
        Route::post('groups/{group}/join', [GroupController::class, 'join'])->name('api.v1.groups.join');
        Route::delete('groups/{group}/leave', [GroupController::class, 'leave'])->name('api.v1.groups.leave');
        Route::post('groups/{group}/invite', [GroupController::class, 'invite'])->name('api.v1.groups.invite');
        Route::delete('groups/{group}/members/{userToRemove}', [GroupController::class, 'removeMember'])->name('api.v1.groups.members.remove');

        // Group Info
        Route::get('groups/{group}/members', [GroupController::class, 'members'])->name('api.v1.groups.members');
        Route::get('groups/{group}/requests', [GroupController::class, 'requests'])->name('api.v1.groups.requests.index');
        Route::get('groups/{group}/invitations', [GroupController::class, 'invitations'])->name('api.v1.groups.invitations.index');

        // Group Posts
        Route::post('groups/{group}/posts', [GroupController::class, 'posts'])->name('api.v1.groups.posts');

        // Requests & Invitations
        Route::post('groups/{group}/requests/{targetUser}/accept', [GroupController::class, 'acceptRequest'])->name('api.v1.groups.requests.accept');
        Route::post('groups/{group}/requests/{targetUser}/reject', [GroupController::class, 'rejectRequest'])->name('api.v1.groups.requests.reject');
        Route::post('groups/{group}/invitations/accept', [GroupController::class, 'acceptInvitation'])->name('api.v1.groups.invitations.accept');
        Route::post('groups/{group}/invitations/reject', [GroupController::class, 'rejectInvitation'])->name('api.v1.groups.invitations.reject');
    });

    // ============================================================
    // EVENTS (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('events/{slug}/my-rank', [EventController::class, 'myRank'])->name('api.v1.events.my-rank');
    });

    // ============================================================
    // REFERRAL (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('my-referral', [ReferralController::class, 'myReferral'])->name('api.v1.my-referral');
    });

    // ============================================================
    // ONLINE TESTS (Auth Required)
    // ============================================================

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('tests/my-attempts', [StudentTestController::class, 'myAttempts'])->name('api.v1.tests.my-attempts');
        Route::post('tests/{test}/start', [StudentTestController::class, 'start'])->name('api.v1.tests.start');
        Route::get('tests/{test}/attempts/{attempt}', [StudentTestController::class, 'take'])->name('api.v1.tests.take');
        Route::post('tests/{test}/attempts/{attempt}/answer', [StudentTestController::class, 'saveAnswer'])->name('api.v1.tests.answer');
        Route::post('tests/{test}/attempts/{attempt}/clear-answer', [StudentTestController::class, 'clearAnswer'])->name('api.v1.tests.clear-answer');
        Route::post('tests/{test}/attempts/{attempt}/mark-review', [StudentTestController::class, 'toggleMarkReview'])->name('api.v1.tests.mark-review');
        Route::post('tests/{test}/attempts/{attempt}/submit', [StudentTestController::class, 'submit'])->name('api.v1.tests.submit');
        Route::get('tests/{test}/attempts/{attempt}/result', [StudentTestController::class, 'result'])->name('api.v1.tests.result');
    });

});
