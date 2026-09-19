<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTestLevelController extends Controller
{
    /**
     * Display all test levels.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $query = TestLevel::withCount([
            'questions',
            'tests',
        ]);

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $levels = $query
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.test-levels.index', compact('levels'));
    }

    /**
     * Store a new test level.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:test_levels,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $validated['name'] = trim($validated['name']);

        $validated['slug'] = Str::slug($validated['name']);

        TestLevel::create($validated);

        return back()->with(
            'success',
            'Test Level created successfully.'
        );
    }

    /**
     * Update an existing test level.
     */
    public function update(
        Request $request,
        TestLevel $level
    ) {
        if (! $level->exists) {
            $id = $request->route('level') ?? $request->route('test_level') ?? $request->route('id');
            if ($id) {
                $level = TestLevel::findOrFail($id);
            }
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                // Ignore current record — no "already taken" error for same name
                Rule::unique('test_levels', 'name')->ignore($level->id),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $validated['name']  = trim($validated['name']);
        $validated['slug']  = Str::slug($validated['name']);

        $level->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Test Level updated successfully.',
                'level'   => $level,
            ]);
        }

        return back()->with('success', 'Test Level updated successfully.');
    }
    /**
     * Toggle active/inactive status.
     */
    public function updateStatus(
        Request $request,
        TestLevel $level
    ) {
        if (! $level->exists) {
            $id = $request->route('level') ?? $request->route('test_level') ?? $request->route('id');
            if ($id) {
                $level = TestLevel::findOrFail($id);
            }
        }

        $newStatus = $level->status === 'active'
            ? 'inactive'
            : 'active';

        $level->update([
            'status' => $newStatus,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Level status updated to ' . ucfirst($newStatus),
            ]);
        }

        return back()->with(
            'success',
            'Level status updated to ' . ucfirst($newStatus)
        );
    }

    /**
     * Delete a test level.
     */
    public function destroy(Request $request, TestLevel $level): RedirectResponse
    {
        if (! $level->exists) {
            $id = $request->route('level') ?? $request->route('test_level') ?? $request->route('id');
            if ($id) {
                $level = TestLevel::findOrFail($id);
            }
        }

        $testCount = $level->tests()->count();
        $questionCount = $level->questions()->count();

        $level->delete();

        $message = 'Test Level deleted successfully.';

        if ($testCount > 0 || $questionCount > 0) {
            $message .= " {$testCount} test(s) and {$questionCount} question(s) were also deleted.";
        }

        return back()->with('success', $message);
    }
}