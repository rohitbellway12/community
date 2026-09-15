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
            ->whereNotNull('created_at')
            ->latest('created_at')
            ->take(5)
            ->get();

        // 5. New Members
        $newMembers = User::with('profile')
            ->whereNotNull('created_at')
            ->latest('created_at')
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
     * Dedicated full-page Data Table view for Analytics
     */
    public function analyticsTable(Request $request)
    {
        $analytics = $this->getAnalyticsData($request);

        return view('admin.analytics_table', [
            'analytics' => $analytics,
            'filter'    => $analytics['filter'],
            'startDate' => $analytics['start_date'],
            'endDate'   => $analytics['end_date'],
            'tableRows' => $analytics['table_rows'],
            'totals'    => $analytics['totals'],
        ]);
    }

    /**
     * Query and assemble analytics datasets for Chart.js and Data Table
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
            $startKst = now($KST)->subMonths(11)->startOfMonth();
            $endKst   = now($KST)->endOfMonth();
            $groupByCreated = "DATE_FORMAT({$kstCreated}, '%Y-%m')";
            $groupByUpdated = "DATE_FORMAT({$kstUpdated}, '%Y-%m')";
            $intervalType = 'month';
        } elseif ($filter === 'yearly') {
            $startKst = now($KST)->subYears(4)->startOfYear();
            $endKst   = now($KST)->endOfYear();
            $groupByCreated = "YEAR({$kstCreated})";
            $groupByUpdated = "YEAR({$kstUpdated})";
            $intervalType = 'year';
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startKst = Carbon::parse($request->input('start_date'), $KST)->startOfDay();
                $endKst   = Carbon::parse($request->input('end_date'),   $KST)->endOfDay();
                if ($startKst->gt($endKst)) {
                    [$startKst, $endKst] = [$endKst, $startKst];
                }
            } catch (\Exception $e) {
                $startKst = now($KST)->subDays(13)->startOfDay();
                $endKst   = now($KST)->endOfDay();
            }

            $diffDays = $startKst->diffInDays($endKst);
            if ($diffDays > 90) {
                $startKst = $startKst->copy()->startOfMonth();
                $endKst   = $endKst->copy()->endOfMonth();
                $groupByCreated = "DATE_FORMAT({$kstCreated}, '%Y-%m')";
                $groupByUpdated = "DATE_FORMAT({$kstUpdated}, '%Y-%m')";
                $intervalType = 'month';
            } else {
                $groupByCreated = "DATE({$kstCreated})";
                $groupByUpdated = "DATE({$kstUpdated})";
                $intervalType = 'day';
            }
        } else {
            // Default: daily — last 14 KST days (including today)
            $filter   = 'daily';
            $startKst = now($KST)->subDays(13)->startOfDay();
            $endKst   = now($KST)->endOfDay();
            $groupByCreated = "DATE({$kstCreated})";
            $groupByUpdated = "DATE({$kstUpdated})";
            $intervalType = 'day';
        }

        // Convert boundaries to UTC for querying the database
        $utcStart = $startKst->copy()->setTimezone('UTC');
        $utcEnd   = $endKst->copy()->setTimezone('UTC');

        // 1. Registrations
        $registrations = DB::table('users')
            ->whereBetween('created_at', [$utcStart, $utcEnd])
            ->selectRaw("{$groupByCreated} as period_key, COUNT(*) as aggregate")
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // 2. Posts
        $posts = DB::table('posts')
            ->whereBetween('created_at', [$utcStart, $utcEnd])
            ->selectRaw("{$groupByCreated} as period_key, COUNT(*) as aggregate")
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // 3. Active Users (Users who updated profile, created posts or commented)
        $userUpdates = DB::table('users')
            ->whereBetween('updated_at', [$utcStart, $utcEnd])
            ->selectRaw("{$groupByUpdated} as period_key, id as user_id");

        $postActivity = DB::table('posts')
            ->whereBetween('created_at', [$utcStart, $utcEnd])
            ->selectRaw("{$groupByCreated} as period_key, user_id");

        $commentActivity = DB::table('comments')
            ->whereBetween('created_at', [$utcStart, $utcEnd])
            ->selectRaw("{$groupByCreated} as period_key, user_id");

        $union = $userUpdates->union($postActivity)->union($commentActivity);

        $activeUsers = DB::query()->fromSub($union, 't')
            ->selectRaw('period_key, COUNT(DISTINCT user_id) as aggregate')
            ->groupBy('period_key')
            ->pluck('aggregate', 'period_key');

        // Build continuous intervals and table rows without gaps
        $labels = [];
        $regData = [];
        $postData = [];
        $activeData = [];
        $tableRows = [];

        if ($intervalType === 'year') {
            $currYear = (int) $startKst->format('Y');
            $endYear  = (int) $endKst->format('Y');
            for ($y = $currYear; $y <= $endYear; $y++) {
                $key = (string) $y;
                $labels[] = $key;
                $reg = (int) ($registrations[$key] ?? 0);
                $pst = (int) ($posts[$key] ?? 0);
                $act = (int) ($activeUsers[$key] ?? 0);
                $regData[] = $reg;
                $postData[] = $pst;
                $activeData[] = $act;
                $tableRows[] = [
                    'date'          => $key,
                    'registrations' => $reg,
                    'posts'         => $pst,
                    'active_users'  => $act,
                ];
            }
        } elseif ($intervalType === 'month') {
            $curr = $startKst->copy()->startOfMonth();
            $end  = $endKst->copy()->endOfMonth();
            while ($curr <= $end) {
                $key = $curr->format('Y-m');
                $label = $curr->format('M Y');
                $labels[] = $label;
                $reg = (int) ($registrations[$key] ?? 0);
                $pst = (int) ($posts[$key] ?? 0);
                $act = (int) ($activeUsers[$key] ?? 0);
                $regData[] = $reg;
                $postData[] = $pst;
                $activeData[] = $act;
                $tableRows[] = [
                    'date'          => $label,
                    'registrations' => $reg,
                    'posts'         => $pst,
                    'active_users'  => $act,
                ];
                $curr->addMonth();
            }
        } else {
            // Day by day in KST
            $curr = $startKst->copy()->startOfDay();
            $end  = $endKst->copy()->startOfDay();
            while ($curr <= $end) {
                $key = $curr->format('Y-m-d');
                $label = $curr->format('d M');
                $labels[] = $label;
                $reg = (int) ($registrations[$key] ?? 0);
                $pst = (int) ($posts[$key] ?? 0);
                $act = (int) ($activeUsers[$key] ?? 0);
                $regData[] = $reg;
                $postData[] = $pst;
                $activeData[] = $act;
                $tableRows[] = [
                    'date'          => $curr->format('d M Y'),
                    'registrations' => $reg,
                    'posts'         => $pst,
                    'active_users'  => $act,
                ];
                $curr->addDay();
            }
        }

        return [
            'filter'     => $filter,
            'start_date' => $startKst->format('Y-m-d'),
            'end_date'   => $endKst->format('Y-m-d'),
            'labels'     => $labels,
            'datasets'   => [
                'registrations' => $regData,
                'posts'         => $postData,
                'active_users'  => $activeData,
            ],
            'table_rows' => $tableRows,
            'totals'     => [
                'registrations' => array_sum($regData),
                'posts'         => array_sum($postData),
                'active_users'  => array_sum($activeData),
            ],
        ];
    }
}