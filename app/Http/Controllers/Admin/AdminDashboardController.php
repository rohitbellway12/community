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

        // 2. Today's Pulse Metrics — all relative to KST (Asia/Seoul, UTC+9)
        $kstNow   = now('Asia/Seoul');
        $kstToday = $kstNow->toDateString(); // 'YYYY-MM-DD' in KST

        $todayRegistrations = User::whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+09:00')) = ?", [$kstToday])->count();
        $todayPosts         = Post::whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+09:00')) = ?", [$kstToday])->count();

        $todayUserUpdates    = DB::table('users')   ->whereRaw("DATE(CONVERT_TZ(updated_at, '+00:00', '+09:00')) = ?", [$kstToday])->pluck('id');
        $todayPostAuthors    = DB::table('posts')   ->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+09:00')) = ?", [$kstToday])->pluck('user_id');
        $todayCommentAuthors = DB::table('comments')->whereRaw("DATE(CONVERT_TZ(created_at, '+00:00', '+09:00')) = ?", [$kstToday])->pluck('user_id');
        $todayActiveUsers    = $todayUserUpdates->merge($todayPostAuthors)->merge($todayCommentAuthors)->unique()->count();

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
        // All date logic runs in KST (Asia/Seoul, UTC+9)
        $KST = 'Asia/Seoul';
        $filter = strtolower(trim($request->get('filter', 'daily')));

        // SQL expression: shift stored UTC timestamp into KST before grouping
        $kstCreated = "CONVERT_TZ(created_at, '+00:00', '+09:00')";
        $kstUpdated = "CONVERT_TZ(updated_at, '+00:00', '+09:00')";

        if ($filter === 'monthly') {
            $startDate = now($KST)->subMonths(11)->startOfMonth()->setTimezone('UTC');
            $endDate   = now($KST)->endOfMonth()->setTimezone('UTC');
            $groupByCreated = "DATE_FORMAT({$kstCreated}, '%Y-%m')";
            $groupByUpdated = "DATE_FORMAT({$kstUpdated}, '%Y-%m')";
            $intervalType = 'month';
        } elseif ($filter === 'yearly') {
            $startDate = now($KST)->subYears(4)->startOfYear()->setTimezone('UTC');
            $endDate   = now($KST)->endOfYear()->setTimezone('UTC');
            $groupByCreated = "YEAR({$kstCreated})";
            $groupByUpdated = "YEAR({$kstUpdated})";
            $intervalType = 'year';
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            try {
                // Treat user-supplied dates as KST calendar days, then convert boundaries to UTC
                $startDate = Carbon::parse($request->input('start_date'), $KST)->startOfDay()->setTimezone('UTC');
                $endDate   = Carbon::parse($request->input('end_date'),   $KST)->endOfDay()->setTimezone('UTC');
                if ($startDate->gt($endDate)) {
                    [$startDate, $endDate] = [$endDate, $startDate];
                }
            } catch (\Exception $e) {
                $startDate = now($KST)->subDays(13)->startOfDay()->setTimezone('UTC');
                $endDate   = now($KST)->endOfDay()->setTimezone('UTC');
            }

            $diffDays = $startDate->diffInDays($endDate);
            if ($diffDays > 90) {
                $groupByCreated = "DATE_FORMAT({$kstCreated}, '%Y-%m')";
                $groupByUpdated = "DATE_FORMAT({$kstUpdated}, '%Y-%m')";
                $intervalType = 'month';
            } else {
                $groupByCreated = "DATE({$kstCreated})";
                $groupByUpdated = "DATE({$kstUpdated})";
                $intervalType = 'day';
            }
        } else {
            // Default: daily — last 14 KST days
            $filter    = 'daily';
            $startDate = now($KST)->subDays(13)->startOfDay()->setTimezone('UTC');
            $endDate   = now($KST)->endOfDay()->setTimezone('UTC');
            $groupByCreated = "DATE({$kstCreated})";
            $groupByUpdated = "DATE({$kstUpdated})";
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
            'filter'     => $filter,
            // Return the boundary dates displayed to the user in KST
            'start_date' => $startDate->copy()->setTimezone('Asia/Seoul')->format('Y-m-d'),
            'end_date'   => $endDate->copy()->setTimezone('Asia/Seoul')->format('Y-m-d'),
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