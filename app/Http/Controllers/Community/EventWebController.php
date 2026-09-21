<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventWebController extends Controller
{
    /**
     * Display the public event page with rules, regulations, and leaderboard.
     */
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $topUsers = $event->getTopUsers(10);

        $currentUser = auth()->user();
        $currentUserScore = null;
        $notificationsCount = 0;

        if ($currentUser) {
            $currentUserScore = $event->getUserScore($currentUser);
            $notificationsCount = $currentUser->unreadNotifications()->count();
        }

        return view('events.show', compact('event', 'topUsers', 'currentUser', 'currentUserScore', 'notificationsCount'));
    }
}
