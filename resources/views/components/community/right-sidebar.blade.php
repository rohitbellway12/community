<?php

namespace App\View\Components\Community;

use Illuminate\View\Component;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class RightSidebar extends Component
{
    public $user;
    public $trendingTopics;
    public $notifications;

    public function __construct()
    {
        $this->user = Auth::user();
        
        // Fetch top 5 trending tags based on post count
        $this->trendingTopics = Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();
            
        if ($this->user) {
            // Load user statistics dynamically
            $this->user->loadCount(['posts', 'comments', 'likes']); 
            $this->notifications = $this->user->unreadNotifications()->take(5)->get();
        } else {
            $this->notifications = collect();
        }
    }

    public function render()
    {
        return view('components.community.right-sidebar');
    }
}