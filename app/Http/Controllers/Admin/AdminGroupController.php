<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGroupController extends Controller
{
    /**
     * Display groups listing.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Group::with(['owner.profile'])
            ->withCount(['users', 'posts']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('owner', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
        }

        $groups = $query->latest()->paginate(15)->withQueryString();

        return view('admin.groups.index', compact('groups'));
    }

    /**
     * Delete a community group.
     */
    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();
        return back()->with('success', 'Group deleted successfully.');
    }
}
