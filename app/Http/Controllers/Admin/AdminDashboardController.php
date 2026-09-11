<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // If request wants JSON (e.g. AJAX filter switch)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($this->getAnalyticsData($request));
        }

        // 1. Overall Stats Metrics
        $totalMembers = User::count();
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $totalGroups = Group::count();
        $totalCategories = Category::count();
        $pendingReportsCount = Report::where('status', 'pending')->count();

        // 2. Today's Pulse Metrics
        $todayRegistrations = User::whereDate('created_at', today())->count();
        $todayPosts = Post::whereDate('created_at', today())->count();

        $todayUserUpdates = DB::table('users')->whereDate('updated_at', today())->pluck('id');
        $todayPostAuthors = DB::table('posts')->whereDate('created_at', today())->pluck('user_id');
        $todayCommentAuthors = DB::table('comments')->whereDate('created_at', today())->pluck('user_id');
        $todayActiveUsers = $todayUserUpdates->merge($todayPostAuthors)->merge($todayCommentAuthors)->unique()->count();

        // Active today (legacy name kept for compatibility)
        $activeToday = $todayActiveUsers;

        // 3. Initial Chart Payload (Default: Daily - last 14 days)
        $initialChartData = $this->getAnalyticsData($request);

        // 4. Recent Posts
        $recentPosts = Post::with(['user.profile', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // 5. New Members
        $newMembers = User::with('profile')
            ->latest()
            ->take(5)
            ->get();

        // 6. Top Contributors
        $topContributors = User::getTopContributors(5);

        return view('admin.dashboard', compact(
            'totalMembers',
            'totalPosts',
            'totalComments',
            'totalGroups',
            'totalCategories',
            'pendingReportsCount',
            'activeToday',
            'todayRegistrations',
            'todayPosts',
            'todayActiveUsers',
            'initialChartData',
            'recentPosts',
            'newMembers',
            'topContributors'
        ));
    }

    /**
     * Dedicated AJAX endpoint for chart analytics
     */
    public function analytics(Request $request)
    {
        return response()->json($this->getAnalyticsData($request));
    }

    /**
     * Query and assemble analytics datasets for Chart.js
     */
    protected function getAnalyticsData(Request $request): array
    {
        $filter = strtolower(trim($request->get('filter', 'daily')));

        if ($filter === 'monthly') {
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            $groupByCreated = "DATE_FORMAT(created_at, '%Y-%m')";
            $groupByUpdated = "DATE_FORMAT(updated_at, '%Y-%m')";
            $intervalType = 'month';
        } elseif ($filter === 'yearly') {
            $startDate = now()->subYears(4)->startOfYear();
            $endDate = now()->endOfYear();
            $groupByCreated = "YEAR(created_at)";
            $groupByUpdated = "YEAR(updated_at)";
            $intervalType = 'year';
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
                if ($startDate->gt($endDate)) {
                    $tmp = $startDate;
                    $startDate = $endDate;
                    $endDate = $tmp;
                }
            } catch (\Exception $e) {
                $startDate = now()->subDays(13)->startOfDay();
                $endDate = now()->endOfDay();
            }

            $diffDays = $startDate->diffInDays($endDate);
            if ($diffDays > 90) {
                $groupByCreated = "DATE_FORMAT(created_at, '%Y-%m')";
                $groupByUpdated = "DATE_FORMAT(updated_at, '%Y-%m')";
                $intervalType = 'month';
            } else {
                $groupByCreated = "DATE(created_at)";
                $groupByUpdated = "DATE(updated_at)";
                $intervalType = 'day';
            }
        } else {
            // Default: daily (last 14 days)
            $filter = 'daily';
            $startDate = now()->subDays(13)->startOfDay();
            $endDate = now()->endOfDay();
            $groupByCreated = "DATE(created_at)";
            $groupByUpdated = "DATE(updated_at)";
            $intervalType = 'day';
        }

        // 1. Registrations
        $registrations = DB::table('users')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupByCreated} as period_key, COUNT(*) as aggregate")
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // 2. Posts
        $posts = DB::table('posts')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupByCreated} as period_key, COUNT(*) as aggregate")
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // 3. Active Users (Users who updated profile, created posts or commented)
        $userUpdates = DB::table('users')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw("{$groupByUpdated} as period_key, id as user_id");

        $postActivity = DB::table('posts')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupByCreated} as period_key, user_id");

        $commentActivity = DB::table('comments')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupByCreated} as period_key, user_id");

        $union = $userUpdates->union($postActivity)->union($commentActivity);

        $activeUsers = DB::query()->fromSub($union, 't')
            ->selectRaw('period_key, COUNT(DISTINCT user_id) as aggregate')
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // Build continuous date intervals without gaps
        $labels = [];
        $regData = [];
        $postData = [];
        $activeData = [];

        if ($intervalType === 'year') {
            $currYear = (int) $startDate->format('Y');
            $endYear = (int) $endDate->format('Y');
            for ($y = $currYear; $y <= $endYear; $y++) {
                $key = (string) $y;
                $labels[] = $key;
                $regData[] = (int) ($registrations[$key] ?? 0);
                $postData[] = (int) ($posts[$key] ?? 0);
                $activeData[] = (int) ($activeUsers[$key] ?? 0);
            }
        } elseif ($intervalType === 'month') {
            $curr = $startDate->copy()->startOfMonth();
            $end = $endDate->copy()->endOfMonth();
            while ($curr <= $end) {
                $key = $curr->format('Y-m');
                $labels[] = $curr->format('M Y');
                $regData[] = (int) ($registrations[$key] ?? 0);
                $postData[] = (int) ($posts[$key] ?? 0);
                $activeData[] = (int) ($activeUsers[$key] ?? 0);
                $curr->addMonth();
            }
        } else {
            // Day by day
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $regData[] = (int) ($registrations[$key] ?? 0);
                $postData[] = (int) ($posts[$key] ?? 0);
                $activeData[] = (int) ($activeUsers[$key] ?? 0);
            }
        }

        return [
            'filter' => $filter,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'labels' => $labels,
            'datasets' => [
                'registrations' => $regData,
                'posts' => $postData,
                'active_users' => $activeData,
            ],
            'totals' => [
                'registrations' => array_sum($regData),
                'posts' => array_sum($postData),
                'active_users' => array_sum($activeData),
            ],
        ];
    }
}