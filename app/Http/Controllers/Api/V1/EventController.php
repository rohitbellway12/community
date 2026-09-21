<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;

    /**
     * Get the currently active & running event with Top 5 leaderboard.
     * Used by the mobile app's home screen top banner.
     *
     * GET /api/v1/active-event
     */
    public function activeEvent(Request $request): JsonResponse
    {
        $event = Event::running()->orderByDesc('sort_order')->first();

        if (!$event) {
            return $this->successResponse(null, 'No active event at the moment.');
        }

        $topUsers = $event->getTopUsers(5)->map(function ($user) {
            $profile = $user->profile;
            $avatar  = $profile?->avatar
                ? (str_starts_with($profile->avatar, 'http')
                    ? $profile->avatar
                    : asset('storage/' . $profile->avatar))
                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff';

            return [
                'id'           => $user->id,
                'name'         => $user->name,
                'username'     => $profile?->username,
                'avatar'       => $avatar,
                'event_score'  => (int) $user->event_score,
            ];
        });

        return $this->successResponse([
            'event' => [
                'id'             => $event->id,
                'title'          => $event->title,
                'slug'           => $event->slug,
                'banner_image'   => $event->banner_image_url,
                'banner_link'    => $event->banner_link_url ?? url('/api/v1/events/' . $event->slug),
                'start_at'       => $event->start_at->toIso8601String(),
                'end_at'         => $event->end_at->toIso8601String(),
                'ends_in_human'  => $event->end_at->diffForHumans(),
                'scoring'        => [
                    'posts_weight'      => $event->posts_weight,
                    'comments_weight'   => $event->comments_weight,
                    'likes_weight'      => $event->likes_weight,
                    'referrals_weight'  => $event->referrals_weight,
                ],
            ],
            'top_users' => $topUsers,
        ], 'Active event retrieved successfully.');
    }

    /**
     * Get event detail & rules page content.
     *
     * GET /api/v1/events/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$event) {
            return $this->errorResponse('Event not found.', 404);
        }

        return $this->successResponse([
            'id'           => $event->id,
            'title'        => $event->title,
            'slug'         => $event->slug,
            'rules'        => $event->rules,
            'banner_image' => $event->banner_image_url,
            'start_at'     => $event->start_at->toIso8601String(),
            'end_at'       => $event->end_at->toIso8601String(),
            'is_running'   => $event->isRunning(),
            'status'       => $event->status,
            'scoring'      => [
                'posts_weight'     => $event->posts_weight,
                'comments_weight'  => $event->comments_weight,
                'likes_weight'     => $event->likes_weight,
                'referrals_weight' => $event->referrals_weight,
            ],
        ], 'Event retrieved successfully.');
    }

    /**
     * Get the full leaderboard for a specific event (paginated).
     *
     * GET /api/v1/events/{slug}/leaderboard
     */
    public function leaderboard(Request $request, string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$event) {
            return $this->errorResponse('Event not found.', 404);
        }

        $limit = min($request->integer('limit', 20), 100);
        $topUsers = $event->getTopUsers($limit)->map(function ($user, $index) {
            $profile = $user->profile;
            $avatar  = $profile?->avatar
                ? (str_starts_with($profile->avatar, 'http')
                    ? $profile->avatar
                    : asset('storage/' . $profile->avatar))
                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff';

            return [
                'rank'        => $index + 1,
                'id'          => $user->id,
                'name'        => $user->name,
                'username'    => $profile?->username,
                'avatar'      => $avatar,
                'event_score' => (int) $user->event_score,
            ];
        });

        return $this->successResponse([
            'event_title' => $event->title,
            'leaderboard' => $topUsers,
        ], 'Leaderboard retrieved successfully.');
    }

    /**
     * Get the authenticated user's score and rank in a specific event.
     *
     * GET /api/v1/events/{slug}/my-rank
     */
    public function myRank(Request $request, string $slug): JsonResponse
    {
        $event = Event::where('slug', $slug)
            ->where('status', '!=', 'draft')
            ->first();

        if (!$event) {
            return $this->errorResponse('Event not found.', 404);
        }

        /** @var User $user */
        $user  = $request->user();
        $score = $event->getUserScore($user);

        return $this->successResponse([
            'event_title' => $event->title,
            'is_running'  => $event->isRunning(),
            'score'       => $score,
        ], 'Your event score retrieved successfully.');
    }
}
