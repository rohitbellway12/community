<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResultSlab;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminResultSlabController extends Controller
{
    /**
     * Display all result slabs.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $testId = $request->input('test_id');
        $status = $request->input('status');

        $query = ResultSlab::with('test');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($testId !== null && $testId !== '') {
            if ($testId === 'global') {
                $query->whereNull('test_id');
            } else {
                $query->where('test_id', $testId);
            }
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $slabs = $query
            ->orderBy('test_id', 'asc')
            ->orderByDesc('min_marks')
            ->paginate(15)
            ->withQueryString();

        $tests = Test::orderBy('title')->get(['id', 'title']);

        return view('admin.result-slabs.index', compact('slabs', 'tests', 'search', 'testId', 'status'));
    }

    /**
     * Store a new result slab.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'min_marks' => [
                'required',
                'numeric',
                'min:0',
            ],
            'max_marks' => [
                'required',
                'numeric',
                'gte:min_marks',
            ],
            'badge_color' => [
                'required',
                'string',
                'in:emerald,blue,amber,purple,rose,slate',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'test_id' => [
                'nullable',
                'exists:tests,id',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $validated['name'] = trim($validated['name']);

        ResultSlab::create($validated);

        return back()->with('success', 'Result Slab created successfully.');
    }

    /**
     * Update an existing result slab.
     */
    public function update(
        Request $request,
        ResultSlab $slab
    ) {
        if (! $slab->exists) {
            $id = $request->route('slab') ?? $request->route('result_slab') ?? $request->route('id');
            if ($id) {
                $slab = ResultSlab::findOrFail($id);
            }
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'min_marks' => [
                'required',
                'numeric',
                'min:0',
            ],
            'max_marks' => [
                'required',
                'numeric',
                'gte:min_marks',
            ],
            'badge_color' => [
                'required',
                'string',
                'in:emerald,blue,amber,purple,rose,slate',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'test_id' => [
                'nullable',
                'exists:tests,id',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $validated['name'] = trim($validated['name']);

        $slab->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Result Slab updated successfully.',
                'slab'    => $slab,
            ]);
        }

        return back()->with('success', 'Result Slab updated successfully.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function updateStatus(
        Request $request,
        ResultSlab $slab
    ) {
        if (! $slab->exists) {
            $id = $request->route('slab') ?? $request->route('result_slab') ?? $request->route('id');
            if ($id) {
                $slab = ResultSlab::findOrFail($id);
            }
        }

        $newStatus = $slab->status === 'active' ? 'inactive' : 'active';

        $slab->update([
            'status' => $newStatus,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Slab status updated to ' . ucfirst($newStatus),
            ]);
        }

        return back()->with('success', 'Slab status updated to ' . ucfirst($newStatus));
    }

    /**
     * Delete a result slab.
     */
    public function destroy(Request $request, ResultSlab $slab): RedirectResponse
    {
        if (! $slab->exists) {
            $id = $request->route('slab') ?? $request->route('result_slab') ?? $request->route('id');
            if ($id) {
                $slab = ResultSlab::findOrFail($id);
            }
        }

        $slab->delete();

        return back()->with('success', 'Result Slab deleted successfully.');
    }
}
