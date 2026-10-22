<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfluencerCampaign;
use App\Models\InfluencerClick;
use App\Models\InfluencerRegistration;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminInfluencerCampaignController extends Controller
{
    /**
     * Display a listing of all campaigns.
     */
    public function index(Request $request): View
    {
        $query = InfluencerCampaign::query()
            ->withCount([
                'clicks',
                'clicks as unique_clicks_count' => function ($q) {
                    $q->selectRaw('count(distinct ip_address)');
                },
                'clicks as suspicious_clicks_count' => function ($q) {
                    $q->where('is_suspicious', true);
                },
                'installs',
                'installs as dropped_off_installs_count' => function ($q) {
                    $q->where('is_registered', false);
                },
                'registrations',
                'registrations as suspicious_registrations_count' => function ($q) {
                    $q->where('is_suspicious', true);
                },
            ]);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('influencer_name', 'like', "%{$search}%")
                    ->orWhere('campaign_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Channel filter
        if ($channel = $request->input('channel')) {
            $query->where('channel', $channel);
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();

        // Overall stats
        $totalCampaigns = InfluencerCampaign::count();
        $totalClicks = InfluencerClick::count();
        $totalInstalls = \App\Models\InfluencerInstall::count();
        $totalDroppedOff = \App\Models\InfluencerInstall::where('is_registered', false)->count();
        $totalRegistrations = InfluencerRegistration::count();
        $totalSuspiciousClicks = InfluencerClick::where('is_suspicious', true)->count();
        $avgConversionRate = $totalClicks > 0 ? round(($totalRegistrations / $totalClicks) * 100, 2) : 0;

        return view('admin.influencer-campaigns.index', compact(
            'campaigns',
            'totalCampaigns',
            'totalClicks',
            'totalInstalls',
            'totalDroppedOff',
            'totalRegistrations',
            'totalSuspiciousClicks',
            'avgConversionRate'
        ));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        return view('admin.influencer-campaigns.create');
    }

    /**
     * Store a newly created campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'influencer_name' => ['required', 'string', 'max:255'],
            'campaign_name'   => ['required', 'string', 'max:255'],
            'code'            => ['required', 'string', 'max:50', 'alpha_dash', 'unique:influencer_campaigns,code'],
            'channel'         => ['required', 'string', 'in:youtube,instagram,telegram,facebook,twitter,tiktok,website,other'],
            'play_store_url'  => ['nullable', 'url'],
            'app_store_url'   => ['nullable', 'url'],
            'destination_url' => ['nullable', 'url'],
            'start_date'      => ['nullable', 'date'],
            'end_date'        => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:active,paused,completed'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['created_by'] = auth()->id();
        $validated['destination_url'] = $validated['destination_url'] ?? $validated['play_store_url'] ?? url('/');

        $campaign = InfluencerCampaign::create($validated);

        return redirect()
            ->route('admin.influencer-campaigns.show', $campaign)
            ->with('success', 'Influencer campaign created successfully! Shareable tracking link is ready.');
    }

    /**
     * Display campaign analytics, clicks, and registrations.
     */
    public function show(InfluencerCampaign $campaign, Request $request): View
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Base queries
        $clicksQuery = $campaign->clicks();
        $installsQuery = $campaign->installs()->with('user.profile');
        $registrationsQuery = $campaign->registrations()->with('user.profile');

        if ($dateFrom) {
            $clicksQuery->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            $installsQuery->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            $registrationsQuery->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $clicksQuery->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            $installsQuery->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            $registrationsQuery->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        // Filter suspicious only if requested
        if ($request->boolean('suspicious_only')) {
            $clicksQuery->where('is_suspicious', true);
            $registrationsQuery->where('is_suspicious', true);
        }

        $totalClicks = (clone $clicksQuery)->count();
        $uniqueClicks = (clone $clicksQuery)->selectRaw('count(distinct ip_address) as count')->value('count') ?? 0;
        $totalInstalls = (clone $installsQuery)->count();
        $droppedOffInstalls = (clone $installsQuery)->where('is_registered', false)->count();
        $totalRegistrations = (clone $registrationsQuery)->count();
        $suspiciousClicksCount = (clone $clicksQuery)->where('is_suspicious', true)->count();
        $suspiciousRegistrationsCount = (clone $registrationsQuery)->where('is_suspicious', true)->count();

        // Conversion Rate: Registrations / Installs (or Registrations / Clicks)
        $conversionRate = $totalInstalls > 0
            ? round(($totalRegistrations / $totalInstalls) * 100, 2)
            : ($totalClicks > 0 ? round(($totalRegistrations / $totalClicks) * 100, 2) : 0);

        // Daily trend data for the last 14 days or filtered period
        $trendClicks = (clone $clicksQuery)
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get()
            ->keyBy('date');

        $trendRegistrations = (clone $registrationsQuery)
            ->selectRaw('DATE(created_at) as date, count(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get()
            ->keyBy('date');

        // Recent paginated records
        $recentClicks = (clone $clicksQuery)->latest('created_at')->paginate(15, ['*'], 'clicks_page');
        $recentInstalls = (clone $installsQuery)->latest('created_at')->paginate(15, ['*'], 'installs_page');
        $recentRegistrations = (clone $registrationsQuery)->latest('created_at')->paginate(15, ['*'], 'reg_page');

        return view('admin.influencer-campaigns.show', compact(
            'campaign',
            'totalClicks',
            'uniqueClicks',
            'totalInstalls',
            'droppedOffInstalls',
            'totalRegistrations',
            'conversionRate',
            'suspiciousClicksCount',
            'suspiciousRegistrationsCount',
            'trendClicks',
            'trendRegistrations',
            'recentClicks',
            'recentInstalls',
            'recentRegistrations',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show edit form.
     */
    public function edit(InfluencerCampaign $campaign): View
    {
        return view('admin.influencer-campaigns.edit', compact('campaign'));
    }

    /**
     * Update campaign details.
     */
    public function update(Request $request, InfluencerCampaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'influencer_name' => ['required', 'string', 'max:255'],
            'campaign_name'   => ['required', 'string', 'max:255'],
            'code'            => ['required', 'string', 'max:50', 'alpha_dash', 'unique:influencer_campaigns,code,' . $campaign->id],
            'channel'         => ['required', 'string', 'in:youtube,instagram,telegram,facebook,twitter,tiktok,website,other'],
            'play_store_url'  => ['nullable', 'url'],
            'app_store_url'   => ['nullable', 'url'],
            'destination_url' => ['nullable', 'url'],
            'start_date'      => ['nullable', 'date'],
            'end_date'        => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'          => ['required', 'in:active,paused,completed'],
            'notes'           => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['destination_url'] = $validated['destination_url'] ?? $validated['play_store_url'] ?? url('/');

        $campaign->update($validated);

        return redirect()
            ->route('admin.influencer-campaigns.show', $campaign)
            ->with('success', 'Influencer campaign updated successfully.');
    }

    /**
     * Delete campaign.
     */
    public function destroy(InfluencerCampaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()
            ->route('admin.influencer-campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    /**
     * Export Campaign Clicks or Registrations to CSV.
     */
    public function export(InfluencerCampaign $campaign, Request $request): StreamedResponse
    {
        $type = $request->input('type', 'registrations');
        $fileName = Str::slug($campaign->influencer_name . '-' . $campaign->code) . "-{$type}-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($campaign, $type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'clicks') {
                fputcsv($handle, ['Click ID', 'Campaign Code', 'IP Address', 'Device Type', 'Platform', 'Browser', 'Is Bot', 'Is Suspicious', 'Suspicion Reasons', 'Referer', 'Clicked At']);

                $campaign->clicks()->chunk(200, function ($clicks) use ($handle, $campaign) {
                    foreach ($clicks as $c) {
                        fputcsv($handle, [
                            $c->id,
                            $campaign->code,
                            $c->ip_address,
                            $c->device_type,
                            $c->platform,
                            $c->browser,
                            $c->is_bot ? 'YES' : 'NO',
                            $c->is_suspicious ? 'YES' : 'NO',
                            $c->suspicion_reasons ? implode('; ', $c->suspicion_reasons) : '',
                            $c->referer,
                            $c->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } elseif ($type === 'installs') {
                fputcsv($handle, ['Install ID', 'Campaign Code', 'Device ID', 'Platform', 'App Version', 'IP Address', 'Converted To Registration', 'User Name', 'User Email', 'Installed At']);

                $campaign->installs()->with('user')->chunk(200, function ($installs) use ($handle, $campaign) {
                    foreach ($installs as $i) {
                        fputcsv($handle, [
                            $i->id,
                            $campaign->code,
                            $i->device_id,
                            $i->platform,
                            $i->app_version ?: 'N/A',
                            $i->ip_address,
                            $i->is_registered ? 'YES (Registered)' : 'NO (Dropped Off)',
                            $i->user?->name ?? 'Not registered',
                            $i->user?->email ?? '',
                            $i->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            } else {
                fputcsv($handle, ['Registration ID', 'Campaign Code', 'Influencer', 'User ID', 'User Name', 'User Email', 'IP Address', 'Device ID', 'Is Suspicious', 'Suspicion Reasons', 'Registered At']);

                $campaign->registrations()->with('user')->chunk(200, function ($registrations) use ($handle, $campaign) {
                    foreach ($registrations as $r) {
                        fputcsv($handle, [
                            $r->id,
                            $campaign->code,
                            $campaign->influencer_name,
                            $r->user_id,
                            $r->user?->name ?? 'Deleted User',
                            $r->user?->email ?? '',
                            $r->ip_address,
                            $r->device_id,
                            $r->is_suspicious ? 'YES' : 'NO',
                            $r->suspicion_reasons ? implode('; ', $r->suspicion_reasons) : '',
                            $r->created_at?->toDateTimeString(),
                        ]);
                    }
                });
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
