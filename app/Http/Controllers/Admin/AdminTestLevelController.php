<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminTestLevelController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = TestLevel::withCount(['questions', 'tests']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $levels = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.test-levels.index', compact('levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:test_levels,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        TestLevel::create($validated);

        return back()->with('success', 'Test Level created successfully.');
    }

    public function update(Request $request, TestLevel $level): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:test_levels,name,' . $level->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $level->update($validated);

        return back()->with('success', 'Test Level updated successfully.');
    }

    public function updateStatus(Request $request, TestLevel $level)
    {
        $newStatus = $level->status === 'active' ? 'inactive' : 'active';
        $level->update(['status' => $newStatus]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Level status updated to ' . ucfirst($newStatus),
            ]);
        }

        return back()->with('success', 'Level status updated to ' . ucfirst($newStatus));
    }

    public function destroy(TestLevel $level): RedirectResponse
    {
        if ($level->tests()->count() > 0) {
            return back()->with('error', 'Cannot delete test level that contains tests. Remove or reassign tests first.');
        }
        if ($level->questions()->count() > 0) {
            return back()->with('error', 'Cannot delete test level that contains questions. Move questions to another level first.');
        }

        $level->delete();

        return back()->with('success', 'Test Level deleted successfully.');
    }
}
