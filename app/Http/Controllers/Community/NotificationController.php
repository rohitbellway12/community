<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Notification data for the Community topbar.
     *
     * This endpoint stays JSON because the topbar uses it only
     * when notification data is requested asynchronously.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                $data = is_array($notification->data)
                    ? $notification->data
                    : [];

                return [
                    'id' => $notification->id,

                    'type' => $notification->type,

                    'title' => $data['title']
                        ?? 'Notification',

                    'message' => $data['message']
                        ?? 'You have a new notification.',

                    'created_at' => $notification->created_at
                        ? $notification->created_at->diffForHumans()
                        : null,

                    'read' => $notification->read_at !== null,

                    'read_at' => $notification->read_at,

                    'data' => $data,
                ];
            })
            ->values();

        $unreadCount = $user
            ->unreadNotifications()
            ->count();

        return response()->json([
            'success' => true,

            'data' => [
                'notifications' => $notifications,

                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Full Community notification page.
     *
     * Shows ALL notifications for the authenticated user,
     * both read and unread.
     */
    public function page(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | All Notifications
        |--------------------------------------------------------------------------
        |
        | Do not use take(10) here. The dedicated page must show the
        | complete notification history.
        |
        */
        $notifications = $user->notifications()
            ->latest()
            ->get();

        $unreadCount = $user
            ->unreadNotifications()
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Community Sidebar / Rightbar Data
        |--------------------------------------------------------------------------
        */

        $topContributors = User::query()
            ->with('profile')
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();

        $trendingTopics = Category::query()
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        return view('community.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,

            'notificationsCount' => $unreadCount,

            'user' => $user,

            'topContributors' => $topContributors,
            'trendingTopics' => $trendingTopics,

            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Return only the unread notification count.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()
            ->unreadNotifications()
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark one notification as read.
     *
     * The notification is fetched through the authenticated user's
     * relationship, so a user cannot mark another user's notification.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $unreadCount = $request->user()
            ->unreadNotifications()
            ->count();

        return response()->json([
            'success' => true,

            'message' => 'Notification marked as read.',

            'data' => [
                'id' => $notification->id,
                'read' => true,
                'read_at' => $notification->read_at,
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark every notification belonging to the authenticated user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        return response()->json([
            'success' => true,

            'message' => 'All notifications marked as read.',

            'data' => [
                'unread_count' => 0,
            ],
        ]);
    }

    /**
     * Delete one notification belonging to the authenticated user.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }
}
