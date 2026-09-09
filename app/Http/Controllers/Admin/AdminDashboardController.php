<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Metrics
        $totalMembers = User::count();
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        
        // Active today (users logged in or created post/comment today, or fallback to users created today)
        $activeToday = User::whereDate('updated_at', today())->count();

        // 2. Recent Posts (Instead of Flagged Content)
        $recentPosts = Post::with(['user.profile', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // 3. New Members
        $newMembers = User::with('profile')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers',
            'totalPosts',
            'totalComments',
            'activeToday',
            'recentPosts',
            'newMembers'
        ));
    }
}