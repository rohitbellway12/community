<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminEventController extends Controller
{
    /**
     * Display all events for admin management.
     */
    public function index()
    {
        $events = Event::orderBy('sort_order', 'asc')
            ->latest('start_at')
            ->paginate(15);

        $totalEvents = Event::count();
        $activeEventsCount = Event::where('status', 'active')->count();
        $runningEventsCount = Event::running()->count();
        $totalReferralsCount = Referral::count();

        return view('admin.events.index', compact(
            'events',
            'totalEvents',
            'activeEventsCount',
            'runningEventsCount',
            'totalReferralsCount'
        ));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'rules' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'banner_link_url' => 'nullable|string|max:500',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'posts_weight' => 'required|numeric|min:0',
            'comments_weight' => 'required|numeric|min:0',
            'likes_weight' => 'required|numeric|min:0',
            'referrals_weight' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('events', 'public');
        }

        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        while (Event::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $event = Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'rules' => $validated['rules'] ?? null,
            'banner_image' => $imagePath,
            'banner_link_url' => $validated['banner_link_url'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'posts_weight' => $validated['posts_weight'] ?? 5,
            'comments_weight' => $validated['comments_weight'] ?? 3,
            'likes_weight' => $validated['likes_weight'] ?? 2,
            'referrals_weight' => $validated['referrals_weight'] ?? 10,
            'status' => $validated['status'] ?? 'draft',
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully!');
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'rules' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'banner_link_url' => 'nullable|string|max:500',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'posts_weight' => 'required|numeric|min:0',
            'comments_weight' => 'required|numeric|min:0',
            'likes_weight' => 'required|numeric|min:0',
            'referrals_weight' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,inactive',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $event->banner_image;
        if ($request->hasFile('banner_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('banner_image')->store('events', 'public');
        }

        $event->update([
            'title' => $validated['title'],
            'rules' => $validated['rules'] ?? null,
            'banner_image' => $imagePath,
            'banner_link_url' => $validated['banner_link_url'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'posts_weight' => $validated['posts_weight'],
            'comments_weight' => $validated['comments_weight'],
            'likes_weight' => $validated['likes_weight'],
            'referrals_weight' => $validated['referrals_weight'],
            'status' => $validated['status'],
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Toggle status (active/inactive).
     */
    public function updateStatus(Request $request, Event $event)
    {
        $newStatus = $event->status === 'active' ? 'inactive' : 'active';
        $event->update(['status' => $newStatus]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Event status updated to ' . ucfirst($newStatus),
            ]);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Event status updated to ' . ucfirst($newStatus));
    }

    /**
     * View leaderboard for an event.
     */
    public function leaderboard(Event $event)
    {
        $topUsers = $event->getTopUsers(50);

        return view('admin.events.leaderboard', compact('event', 'topUsers'));
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        if ($event->banner_image && Storage::disk('public')->exists($event->banner_image)) {
            Storage::disk('public')->delete($event->banner_image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully!');
    }
}
