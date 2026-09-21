<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user() ?: $request->user('sanctum');

            if ($user) {
                $now = now();
                $today = $now->toDateString();

                // 1. Update real-time last_seen_at (throttled to once per minute)
                $lastSeenCacheKey = 'user_last_seen_' . $user->id;
                if (!Cache::has($lastSeenCacheKey)) {
                    Cache::put($lastSeenCacheKey, true, $now->copy()->addMinutes(1));
                    DB::table('users')->where('id', $user->id)->update(['last_seen_at' => $now]);
                    $user->last_seen_at = $now;
                }

                // 2. Record daily active visit (once per day)
                $dailyVisitCacheKey = 'user_daily_visit_' . $user->id . '_' . $today;
                if (!Cache::has($dailyVisitCacheKey)) {
                    Cache::put($dailyVisitCacheKey, true, $now->copy()->endOfDay());
                    DB::table('user_visits')->insertOrIgnore([
                        'user_id'    => $user->id,
                        'visit_date' => $today,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // 3. Detect and store device info (throttled to once per minute)
                if (!Cache::has($lastSeenCacheKey . '_device')) {
                    Cache::put($lastSeenCacheKey . '_device', true, $now->copy()->addMinutes(1));

                    $deviceType = null;
                    $deviceOs = null;
                    $browser = null;
                    $ipAddress = $request->ip();

                    // For API requests: check custom headers first, then request body
                    if ($request->is('api/*')) {
                        $deviceType = $request->header('X-Device-Type') ?: $request->input('device_type');
                        $deviceOs = $request->header('X-Device-OS') ?: $request->input('device_os');
                        $browser = $request->header('X-Browser') ?: $request->input('browser');
                    }

                    // For web requests: use jenssegers/agent
                    if (!$deviceType) {
                        $agent = new Agent();
                        $agent->setUserAgent($request->header('User-Agent'));

                        $deviceType = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');
                        $deviceOs = $agent->platform() ?: null;
                        $browser = $agent->browser() ?: null;
                    }

                    DB::table('users')->where('id', $user->id)->update([
                        'device_type' => $deviceType,
                        'device_os'   => $deviceOs,
                        'browser'     => $browser,
                        'ip_address'  => $ipAddress,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }
}
