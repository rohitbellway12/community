<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminGroupController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminGuidelineController;
use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Community\EventWebController;
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

Route::get('/dashboard', function () {
    return redirect()->route('community.index');
})->name('dashboard');

Route::get('/home', function () {
    return redirect()->route('community.index');
})->name('home');

Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/events/{slug}', [EventWebController::class, 'show'])->name('events.show');
Route::get('/c/{code}', [\App\Http\Controllers\InfluencerTrackController::class, 'track'])->name('influencer.track');


// Route::prefix('community') removed — XAMPP folder already provides the /community base path
Route::group([], function () {
    require __DIR__ . '/auth.php';

    Route::middleware('auth')->group(function () {

        Route::get('/account/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/account/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/account/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

    // Student Test-Taking

    Route::name('tests.student.')->prefix('tests')->group(function () {
        Route::get('/student', [\App\Http\Controllers\StudentTestController::class, 'index'])->name('index');
        Route::get('/student/{test}/result/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'result'])->name('result');
        Route::get('/student/{test}/certificate/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'certificate'])->name('certificate');
        Route::get('/student/{test}/certificate/{attempt}/print', [\App\Http\Controllers\StudentTestController::class, 'certificatePrint'])->name('certificate.print');
        Route::get('/student/{test}/certificate/{attempt}/download', [\App\Http\Controllers\StudentTestController::class, 'downloadCertificatePdf'])->name('certificate.download');

        Route::middleware('auth')->group(function () {
            Route::get('/student/{test}/show', [\App\Http\Controllers\StudentTestController::class, 'show'])->name('show');
            Route::post('/student/{test}/start', [\App\Http\Controllers\StudentTestController::class, 'start'])->name('start');
            Route::get('/student/{test}/take/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'take'])->name('take');
            Route::post('/student/{test}/submit/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'submit'])->name('submit');
            Route::post('/student/{test}/answer/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'saveAnswer'])->name('answer.save');
            Route::post('/student/{test}/clear-answer/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'clearAnswer'])->name('answer.clear');
            Route::post('/student/{test}/mark-review/{attempt}', [\App\Http\Controllers\StudentTestController::class, 'toggleMarkForReview'])->name('answer.mark-review');
        });
    });

    // Fallback redirect for legacy or double-prefixed community test URLs
    Route::get('/community/tests/student/{test}/show', function ($test) {
        return redirect()->route('tests.student.show', ['test' => $test], 301);
    });
    Route::get('/community/tests/student/{path?}', function ($path = null) {
        return redirect('/tests/student/' . ($path ?? ''), 301);
    })->where('path', '.*');

    Route::get('/community/community/tests/student/{test}/show', function ($test) {
        return redirect()->route('tests.student.show', ['test' => $test], 301);
    });
    Route::get('/community/community/tests/student/{path?}', function ($path = null) {
        return redirect('/tests/student/' . ($path ?? ''), 301);
    })->where('path', '.*');

    // Public Consultancy Services
    Route::get('/services', [\App\Http\Controllers\PublicServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}', [\App\Http\Controllers\PublicServiceController::class, 'show'])->name('services.show');
    Route::match(['GET', 'POST'], '/services/{service}/inquiry', [\App\Http\Controllers\PublicServiceController::class, 'inquiry'])->name('services.inquiry');

    Route::get('/', [PostController::class, 'index'])
        ->name('community.index');

    Route::get('/posts/{post}', [PostController::class, 'show'])
        ->whereNumber('post')
        ->name('community.posts.show');

    Route::get(
        '/posts/{post}/comments',
        [CommunityController::class, 'loadMoreComments']
    )
        ->whereNumber('post')
        ->name('community.posts.comments');

    Route::post(
        '/posts/{post}/share',
        [CommunityController::class, 'share']
    )
        ->whereNumber('post')
        ->name('community.posts.share');

    Route::get(
        '/profile/{username}',
        [UserProfileController::class, 'show']
    )->name('community.profile');

    Route::get(
        '/profile/{username}/followers',
        [UserProfileController::class, 'followers']
    )->name('community.profile.followers');

    Route::get(
        '/profile/{username}/following',
        [UserProfileController::class, 'following']
    )->name('community.profile.following');

    Route::get(
        '/guidelines',
        [CommunityController::class, 'guidelines']
    )->name('community.guidelines');

    Route::get(
        '/privacy-policy',
        [CommunityController::class, 'privacyPolicy']
    )->name('community.privacy');

    Route::get(
        '/terms-and-conditions',
        [CommunityController::class, 'terms']
    )->name('community.terms');


    Route::get('/community/events/{slug}', [EventWebController::class, 'show'])
        ->name('community.events.show');

    Route::get('/documents/{document}/download', [\App\Http\Controllers\Community\DocumentWebController::class, 'download'])
        ->name('community.documents.download');
    Route::get('/documents/files/{file}/download', [\App\Http\Controllers\Community\DocumentWebController::class, 'downloadFile'])
        ->name('community.documents.files.download');

    /*
    |--------------------------------------------------------------------------
    | Authenticated Community
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')
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

            Route::delete(
                '/groups/{group:slug}/members/{userToRemove}',
                [GroupController::class, 'removeMember']
            )->name('groups.members.remove');

            Route::post('/groups/{group:slug}/requests/{userToAccept}/accept', [GroupController::class, 'acceptRequest'])
                ->name('groups.requests.accept');

            Route::post('/groups/{group:slug}/requests/{userToReject}/reject', [GroupController::class, 'rejectRequest'])
                ->name('groups.requests.reject');

            Route::post('/groups/{group:slug}/invitation/accept', [GroupController::class, 'acceptInvitation'])
                ->name('groups.invitation.accept');

            Route::post('/groups/{group:slug}/invitation/reject', [GroupController::class, 'rejectInvitation'])
                ->name('groups.invitation.reject');

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

            Route::get(
                '/dashboard/analytics',
                [AdminDashboardController::class, 'analytics']
            )->name('dashboard.analytics');

            Route::get(
                '/dashboard/analytics/table',
                [AdminDashboardController::class, 'analyticsTable']
            )->name('dashboard.analytics.table');

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

            Route::delete(
                '/users/{user}',
                [AdminUserController::class, 'destroy']
            )->name('users.destroy');

            // Profile Activities & Progress Bar Settings
            Route::get('/profile-activities', [\App\Http\Controllers\Admin\AdminProfileActivityController::class, 'index'])->name('profile-activities.index');
            Route::post('/profile-activities', [\App\Http\Controllers\Admin\AdminProfileActivityController::class, 'store'])->name('profile-activities.store');
            Route::put('/profile-activities/{activity}', [\App\Http\Controllers\Admin\AdminProfileActivityController::class, 'update'])->name('profile-activities.update');
            Route::patch('/profile-activities/{activity}/toggle-status', [\App\Http\Controllers\Admin\AdminProfileActivityController::class, 'toggleStatus'])->name('profile-activities.toggleStatus');
            Route::delete('/profile-activities/{activity}', [\App\Http\Controllers\Admin\AdminProfileActivityController::class, 'destroy'])->name('profile-activities.destroy');

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

            Route::delete(
                '/posts/{post}',
                [PostController::class, 'adminDestroy']
            )->name('posts.destroy');

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

            // Categories
            Route::resource('categories', AdminCategoryController::class);

            // Groups Moderation
            Route::get('/groups', [AdminGroupController::class, 'index'])->name('groups.index');
            Route::delete('/groups/{group}', [AdminGroupController::class, 'destroy'])->name('groups.destroy');

            // Reports
            Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
            Route::patch('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.updateStatus');
            Route::delete('/reports/{report}/content', [AdminReportController::class, 'deleteContent'])->name('reports.deleteContent');
            Route::post('/reports/{report}/ban-user', [AdminReportController::class, 'banUser'])->name('reports.banUser');
            Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('reports.destroy');

            // Guidelines
            Route::get('/guidelines', [AdminGuidelineController::class, 'index'])->name('guidelines.index');
            Route::post('/guidelines', [AdminGuidelineController::class, 'store'])->name('guidelines.store');
            Route::put('/guidelines/{guideline}', [AdminGuidelineController::class, 'update'])->name('guidelines.update');
            Route::patch('/guidelines/{guideline}/status', [AdminGuidelineController::class, 'updateStatus'])->name('guidelines.updateStatus');
            Route::delete('/guidelines/{guideline}', [AdminGuidelineController::class, 'destroy'])->name('guidelines.destroy');
             // Banners
             
             Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
             Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
             Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
             Route::patch('/banners/{banner}/status', [AdminBannerController::class, 'updateStatus'])->name('banners.updateStatus');
             Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');

             // Events & Referral Contests
             Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
             Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
             Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
             Route::patch('/events/{event}/status', [AdminEventController::class, 'updateStatus'])->name('events.updateStatus');
             Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
             Route::get('/events/{event}/leaderboard', [AdminEventController::class, 'leaderboard'])->name('events.leaderboard');

            // Push Notifications Module (Broadcast campaigns, targeting, preview, logs)
            Route::get('/push-notifications', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'index'])->name('push-notifications.index');
            Route::get('/push-notifications/create', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'create'])->name('push-notifications.create');
            Route::post('/push-notifications', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'store'])->name('push-notifications.store');
            Route::get('/push-notifications/recipient-count', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'recipientCount'])->name('push-notifications.recipient-count');
            Route::get('/push-notifications/search-users', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'searchUsers'])->name('push-notifications.search-users');
            Route::get('/push-notifications/{pushNotification}', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'show'])->name('push-notifications.show');
            Route::post('/push-notifications/{pushNotification}/resend', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'resend'])->name('push-notifications.resend');
            Route::delete('/push-notifications/{pushNotification}', [\App\Http\Controllers\Admin\AdminPushNotificationController::class, 'destroy'])->name('push-notifications.destroy');

            // Influencer Tracking System
            Route::resource('influencer-campaigns', \App\Http\Controllers\Admin\AdminInfluencerCampaignController::class)
                ->parameters(['influencer-campaigns' => 'campaign']);
            Route::get('/influencer-campaigns/{campaign}/export', [\App\Http\Controllers\Admin\AdminInfluencerCampaignController::class, 'export'])->name('influencer-campaigns.export');

            // Notification Templates (combined: push toggles + message editing + Firebase settings)
            Route::get('/notification-templates', [\App\Http\Controllers\Admin\AdminNotificationTemplateController::class, 'index'])->name('notification-templates.index');
            Route::post('/notification-templates/bulk-update', [\App\Http\Controllers\Admin\AdminNotificationTemplateController::class, 'updateAll'])->name('notification-templates.bulk-update');
            Route::post('/notification-templates/firebase-update', [\App\Http\Controllers\Admin\AdminNotificationTemplateController::class, 'updateFirebase'])->name('notification-templates.firebase-update');

            // Legacy redirects
            Route::get('/notification-settings', fn () => redirect()->route('admin.notification-templates.index'))->name('notification-settings.index');
            Route::get('/firebase-settings', fn () => redirect()->route('admin.notification-templates.index'))->name('firebase-settings.edit');

            // Test Levels
            Route::patch('/test-levels/{level}/status', [\App\Http\Controllers\Admin\AdminTestLevelController::class, 'updateStatus'])->name('test-levels.updateStatus');
            Route::resource('test-levels', \App\Http\Controllers\Admin\AdminTestLevelController::class)->parameters([
                'test-levels' => 'level',
            ]);

            // Questions
            Route::get('/questions/sample-template', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'sampleTemplate'])->name('questions.sampleTemplate');
            Route::post('/questions/import', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'import'])->name('questions.import');
            Route::patch('/questions/{question}/status', [\App\Http\Controllers\Admin\AdminQuestionController::class, 'updateStatus'])->name('questions.updateStatus');
            Route::resource('questions', \App\Http\Controllers\Admin\AdminQuestionController::class);

            // Result Slabs
            Route::patch('/result-slabs/{slab}/status', [\App\Http\Controllers\Admin\AdminResultSlabController::class, 'updateStatus'])->name('result-slabs.updateStatus');
            Route::resource('result-slabs', \App\Http\Controllers\Admin\AdminResultSlabController::class)->parameters([
                'result-slabs' => 'slab',
            ]);

            // Tests

            // Test Attempts (Analytics) - before resource to avoid conflict

            Route::prefix('tests')->name('tests.')->group(function () {
                Route::get('/attempts', [\App\Http\Controllers\Admin\AdminTestAttemptController::class, 'index'])->name('attempts.index');
                Route::get('/attempts/export', [\App\Http\Controllers\Admin\AdminTestAttemptController::class, 'export'])->name('attempts.export');
                Route::get('/attempts/{attempt}', [\App\Http\Controllers\Admin\AdminTestAttemptController::class, 'show'])->name('attempts.show');
            });

            Route::resource('tests', \App\Http\Controllers\Admin\AdminTestController::class);

            // Documents & Resources (Universal Format & External Links)
            Route::get('documents/{document}/download', [\App\Http\Controllers\Admin\AdminDocumentController::class, 'download'])->name('documents.download');
            Route::delete('documents/{document}/files/{file}', [\App\Http\Controllers\Admin\AdminDocumentController::class, 'deleteFile'])->name('documents.files.destroy');
            Route::resource('documents', \App\Http\Controllers\Admin\AdminDocumentController::class);

            // Services & WhatsApp Integration
            Route::patch('/services/{service}/status', [\App\Http\Controllers\Admin\AdminServiceController::class, 'updateStatus'])->name('services.update-status');
            Route::patch('/services/{service}/toggle-publish', [\App\Http\Controllers\Admin\AdminServiceController::class, 'togglePublish'])->name('services.toggle-publish');
            Route::post('/services/reorder', [\App\Http\Controllers\Admin\AdminServiceController::class, 'reorder'])->name('services.reorder');
            Route::resource('services', \App\Http\Controllers\Admin\AdminServiceController::class);

            // Service Categories
            Route::resource('service-categories', \App\Http\Controllers\Admin\AdminServiceCategoryController::class)->except(['create', 'show', 'edit']);

            // AI Resume Builder & Watermark Moderation
            Route::get('/resumes', [\App\Http\Controllers\Admin\AdminResumeController::class, 'index'])->name('resumes.index');
            Route::get('/resumes/verifications', [\App\Http\Controllers\Admin\AdminResumeController::class, 'verifications'])->name('resumes.verifications');
            Route::post('/resumes/verifications/{verification}/approve', [\App\Http\Controllers\Admin\AdminResumeController::class, 'approveVerification'])->name('resumes.verifications.approve');
            Route::post('/resumes/verifications/{verification}/reject', [\App\Http\Controllers\Admin\AdminResumeController::class, 'rejectVerification'])->name('resumes.verifications.reject');
            Route::get('/resumes/settings', [\App\Http\Controllers\Admin\AdminResumeController::class, 'settings'])->name('resumes.settings');
            Route::post('/resumes/settings', [\App\Http\Controllers\Admin\AdminResumeController::class, 'updateSettings'])->name('resumes.settings.update');

            // Website & System Settings
            Route::get('/website-settings', [\App\Http\Controllers\Admin\AdminAnalyticsSettingController::class, 'edit'])->name('website-settings.index');
            Route::put('/website-settings', [\App\Http\Controllers\Admin\AdminAnalyticsSettingController::class, 'update'])->name('website-settings.update');

            // Aliases / Legacy redirects
            Route::get('/analytics-settings', fn () => redirect()->route('admin.website-settings.index'))->name('analytics-settings.edit');
            Route::put('/analytics-settings', fn () => redirect()->route('admin.website-settings.index'))->name('analytics-settings.update');
        });

    // AI Resume Builder (Student Routes)
    Route::get('/resume-builder', [\App\Http\Controllers\ResumeBuilderController::class, 'index'])->name('resume-builder.index');
    Route::get('/resume-builder/sample/{template?}', [\App\Http\Controllers\ResumeBuilderController::class, 'samplePreview'])->name('resume-builder.sample');
    Route::middleware('auth')->group(function () {
        Route::get('/resume-builder/create', [\App\Http\Controllers\ResumeBuilderController::class, 'create'])->name('resume-builder.create');
        Route::get('/resume-builder/my-resumes', [\App\Http\Controllers\ResumeBuilderController::class, 'myResumes'])->name('resume-builder.my-resumes');
        Route::get('/resume-builder/{resume}/edit', [\App\Http\Controllers\ResumeBuilderController::class, 'edit'])->name('resume-builder.edit');
        Route::post('/resume-builder/save', [\App\Http\Controllers\ResumeBuilderController::class, 'save'])->name('resume-builder.save');
        Route::post('/resume-builder/upload-photo', [\App\Http\Controllers\ResumeBuilderController::class, 'uploadPhoto'])->name('resume-builder.upload-photo');
        Route::post('/resume-builder/ai/polish-summary', [\App\Http\Controllers\ResumeBuilderController::class, 'aiPolishSummary'])->name('resume-builder.ai.summary');
        Route::post('/resume-builder/ai/polish-experience', [\App\Http\Controllers\ResumeBuilderController::class, 'aiPolishExperience'])->name('resume-builder.ai.experience');
        Route::post('/resume-builder/ai/suggest-skills', [\App\Http\Controllers\ResumeBuilderController::class, 'aiSuggestSkills'])->name('resume-builder.ai.skills');
        Route::get('/resume-builder/{resume}/preview', [\App\Http\Controllers\ResumeBuilderController::class, 'preview'])->name('resume-builder.preview');
        Route::get('/resume-builder/{resume}/download', [\App\Http\Controllers\ResumeBuilderController::class, 'downloadPdf'])->name('resume-builder.download');
        Route::post('/resume-builder/{resume}/share', [\App\Http\Controllers\ResumeBuilderController::class, 'submitShare'])->name('resume-builder.share');
        Route::delete('/resume-builder/{resume}', [\App\Http\Controllers\ResumeBuilderController::class, 'destroy'])->name('resume-builder.destroy');
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

        Route::post('/followers/{user}/remove', ['App\\Http\\Controllers\\Community\\FollowController', 'removeFollower'])
            ->name('users.followers.remove');

        Route::get('/users/{user}/following', ['App\\Http\\Controllers\\Community\\FollowController', 'following'])
            ->name('users.following');
    });

    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('community.notifications.destroy');

    Route::middleware(['auth'])->group(function () {

        Route::resource('admin/tags', TagController::class)
            ->except(['show', 'create', 'edit']);

    });

    Route::middleware('auth')->group(function () {

        // Notification page
        Route::get('/notifications', [NotificationController::class, 'page'])
            ->name('community.notifications');

        // Topbar AJAX/JSON
        Route::get('/notifications/data', [NotificationController::class, 'index'])
            ->name('community.notifications.data');

    });
});