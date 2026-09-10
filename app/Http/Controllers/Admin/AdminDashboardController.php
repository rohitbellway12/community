<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Metrics
        $totalMembers = User::count();
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $totalGroups = Group::count();
        $totalCategories = Category::count();
        $pendingReportsCount = Report::where('status', 'pending')->count();
        
        // Active today
        $activeToday = User::whereDate('updated_at', today())->count();

        // 2. Recent Posts
        $recentPosts = Post::with(['user.profile', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // 3. New Members
        $newMembers = User::with('profile')
            ->latest()
            ->take(5)
            ->get();

        // 4. Top Contributors
        $topContributors = User::getTopContributors(5);

        return view('admin.dashboard', compact(
            'totalMembers',
            'totalPosts',
            'totalComments',
            'totalGroups',
            'totalCategories',
            'pendingReportsCount',
            'activeToday',
            'recentPosts',
            'newMembers',
            'topContributors'
        ));
    }
}