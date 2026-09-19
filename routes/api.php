<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\DeviceTokenController;
use App\Http\Controllers\Api\V1\FollowController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your mobile application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    // Common / Dropdown Helpers
    Route::get('countries', [CountryController::class, 'index'])->name('api.v1.countries.index');

    // Post Routes
    Route::get('posts', [PostController::class, 'index'])->name('api.v1.posts.index');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('api.v1.posts.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('posts', [PostController::class, 'store'])->name('api.v1.posts.store');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('api.v1.posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('api.v1.posts.destroy');
        Route::post('posts/{post}/like', [PostController::class, 'like'])->name('api.v1.posts.like');
        Route::post('posts/{post}/save', [PostController::class, 'save'])->name('api.v1.posts.save');
        Route::post('posts/{post}/share', [PostController::class, 'share'])->name('api.v1.posts.share');
        Route::post('posts/{post}/mark-solved', [PostController::class, 'markSolved'])->name('api.v1.posts.mark-solved');
    });

    // Comment Routes
    Route::get('posts/{post}/comments', [CommentController::class, 'index'])->name('api.v1.posts.comments.index');
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies'])->name('api.v1.comments.replies');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('api.v1.posts.comments.store');
        Route::put('comments/{comment}', [CommentController::class, 'update'])->name('api.v1.comments.update');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('api.v1.comments.destroy');
        Route::post('comments/{comment}/like', [CommentController::class, 'like'])->name('api.v1.comments.like');
    });

    // Notification Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('api.v1.notifications.index');
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.v1.notifications.unread-count');
        Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('api.v1.notifications.read');
        Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('api.v1.notifications.read-all');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('api.v1.notifications.destroy');
    });

    // Device Token Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('device-tokens', [DeviceTokenController::class, 'index'])->name('api.v1.device-tokens.index');
        Route::post('device-tokens', [DeviceTokenController::class, 'store'])->name('api.v1.device-tokens.store');
        Route::delete('device-tokens/{deviceToken}', [DeviceTokenController::class, 'destroy'])->name('api.v1.device-tokens.destroy');
    });

    // Follow Routes
    Route::get('users/{user}/followers', [FollowController::class, 'followers'])->name('api.v1.users.followers');
    Route::get('users/{user}/following', [FollowController::class, 'following'])->name('api.v1.users.following');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('users/{user}/follow/status', [FollowController::class, 'status'])->name('api.v1.users.follow.status');
        Route::post('users/{user}/follow', [FollowController::class, 'store'])->name('api.v1.users.follow');
        Route::delete('users/{user}/follow', [FollowController::class, 'destroy'])->name('api.v1.users.unfollow');
        Route::delete('users/{follower}/follower/remove', [FollowController::class, 'removeFollower'])->name('api.v1.users.follower.remove');
    });

    // Group Routes
    Route::get('groups', [GroupController::class, 'index'])->name('api.v1.groups.index');
    Route::get('groups/{group}', [GroupController::class, 'show'])->name('api.v1.groups.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('groups', [GroupController::class, 'store'])->name('api.v1.groups.store');
        Route::put('groups/{group}', [GroupController::class, 'update'])->name('api.v1.groups.update');
        Route::delete('groups/{group}', [GroupController::class, 'destroy'])->name('api.v1.groups.destroy');

        Route::post('groups/{group}/join', [GroupController::class, 'join'])->name('api.v1.groups.join');
        Route::delete('groups/{group}/leave', [GroupController::class, 'leave'])->name('api.v1.groups.leave');
        Route::post('groups/{group}/invite', [GroupController::class, 'invite'])->name('api.v1.groups.invite');
        Route::delete('groups/{group}/members/{userToRemove}', [GroupController::class, 'removeMember'])->name('api.v1.groups.members.remove');

        Route::get('groups/{group}/members', [GroupController::class, 'members'])->name('api.v1.groups.members');
        Route::get('groups/{group}/requests', [GroupController::class, 'requests'])->name('api.v1.groups.requests.index');
        Route::get('groups/{group}/invitations', [GroupController::class, 'invitations'])->name('api.v1.groups.invitations.index');

        Route::post('groups/{group}/posts', [GroupController::class, 'posts'])->name('api.v1.groups.posts');

        Route::post('groups/{group}/requests/{targetUser}/accept', [GroupController::class, 'acceptRequest'])->name('api.v1.groups.requests.accept');
        Route::post('groups/{group}/requests/{targetUser}/reject', [GroupController::class, 'rejectRequest'])->name('api.v1.groups.requests.reject');
        Route::post('groups/{group}/invitations/accept', [GroupController::class, 'acceptInvitation'])->name('api.v1.groups.invitations.accept');
        Route::post('groups/{group}/invitations/reject', [GroupController::class, 'rejectInvitation'])->name('api.v1.groups.invitations.reject');
    });

    // Public Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('api.v1.auth.login');
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('api.v1.auth.forgot-password');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('api.v1.auth.verify-otp');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('api.v1.auth.reset-password');

        // Authenticated Auth Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::post('change-password', [AuthController::class, 'changePassword'])->name('api.v1.auth.change-password');
            Route::delete('delete-account', [AuthController::class, 'deleteAccount'])->name('api.v1.auth.delete-account');
        });
    });

});
