<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGuideline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGuidelineController extends Controller
{
    /**
     * Display listing of guidelines.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = CommunityGuideline::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $guidelines = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => CommunityGuideline::count(),
            'active' => CommunityGuideline::where('is_active', true)->count(),
            'inactive' => CommunityGuideline::where('is_active', false)->count(),
        ];

        return view('admin.guidelines.index', compact('guidelines', 'stats', 'search', 'status'));
    }

    /**
     * Store new guideline.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['icon'] = null;

        CommunityGuideline::create($validated);

        return back()->with('success', 'Guideline added successfully.');
    }

    /**
     * Update guideline.
     */
    public function update(Request $request, CommunityGuideline $guideline): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['icon'] = null;

        $guideline->update($validated);

        return back()->with('success', 'Guideline updated successfully.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function updateStatus(Request $request, CommunityGuideline $guideline): RedirectResponse
    {
        $guideline->update([
            'is_active' => !$guideline->is_active,
        ]);

        $statusText = $guideline->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Guideline has been {$statusText}.");
    }

    /**
     * Delete guideline.
     */
    public function destroy(CommunityGuideline $guideline): RedirectResponse
    {
        $guideline->delete();

        return back()->with('success', 'Guideline deleted successfully.');
    }
}
