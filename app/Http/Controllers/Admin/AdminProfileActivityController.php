<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileActivity;
use App\Services\ProfileActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProfileActivityController extends Controller
{
    /**
     * Display all profile activities and completion settings.
     */
    public function index(): View
    {
        $activities = ProfileActivity::orderBy('sort_order')->orderBy('id')->get();
        $totalActivePoints = $activities->where('status', true)->sum('points');
        $activeCount = $activities->where('status', true)->count();
        $availableTriggers = ProfileActivityService::getAvailableTriggers();

        return view('admin.profile_activities.index', compact(
            'activities',
            'totalActivePoints',
            'activeCount',
            'availableTriggers'
        ));
    }

    /**
     * Store a newly created profile activity.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key'          => ['required', 'string', 'max:50', 'unique:profile_activities,key'],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'points'       => ['required', 'integer', 'min:1', 'max:100'],
            'action_url'   => ['nullable', 'string', 'max:255'],
            'action_label' => ['nullable', 'string', 'max:50'],
            'icon'         => ['nullable', 'string', 'max:50'],
            'status'       => ['nullable', 'boolean'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $validated['status'] = $request->has('status');
        $validated['sort_order'] = $validated['sort_order'] ?? (ProfileActivity::max('sort_order') + 1);

        $activity = ProfileActivity::create($validated);

        return redirect()
            ->route('admin.profile-activities.index')
            ->with('success', "New activity '{$activity->title}' (+{$activity->points}%) added successfully.");
    }

    /**
     * Update activity configuration.
     */
    public function update(Request $request, ProfileActivity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'points'       => ['required', 'integer', 'min:1', 'max:100'],
            'action_url'   => ['nullable', 'string', 'max:255'],
            'action_label' => ['nullable', 'string', 'max:50'],
            'status'       => ['nullable', 'boolean'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $validated['status'] = $request->has('status');

        $activity->update($validated);

        return redirect()
            ->route('admin.profile-activities.index')
            ->with('success', "Activity '{$activity->title}' updated successfully.");
    }

    /**
     * Toggle active/inactive status of an activity.
     */
    public function toggleStatus(ProfileActivity $activity): RedirectResponse
    {
        $activity->update([
            'status' => !$activity->status,
        ]);

        $statusText = $activity->status ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.profile-activities.index')
            ->with('success', "Activity '{$activity->title}' was {$statusText}.");
    }

    /**
     * Delete an activity.
     */
    public function destroy(ProfileActivity $activity): RedirectResponse
    {
        $title = $activity->title;
        $activity->delete();

        return redirect()
            ->route('admin.profile-activities.index')
            ->with('success', "Activity '{$title}' removed successfully.");
    }
}
