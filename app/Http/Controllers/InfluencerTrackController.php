<?php

namespace App\Http\Controllers;

use App\Models\InfluencerCampaign;
use App\Services\InfluencerTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class InfluencerTrackController extends Controller
{
    protected InfluencerTrackingService $trackingService;

    public function __construct(InfluencerTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Handle public campaign tracking link: /c/{code}
     */
    public function track(string $code, Request $request): RedirectResponse|View
    {
        $campaign = InfluencerCampaign::where('code', $code)->first();

        // If campaign doesn't exist, redirect to home page
        if (!$campaign) {
            return redirect('/');
        }

        // Check if campaign is active
        $isActive = ($campaign->status === 'active');
        if ($campaign->start_date && now()->lt($campaign->start_date->startOfDay())) {
            $isActive = false;
        }
        if ($campaign->end_date && now()->gt($campaign->end_date->endOfDay())) {
            $isActive = false;
        }

        // Record click if active
        $click = null;
        if ($isActive) {
            $click = $this->trackingService->recordClick($campaign, $request);

            // Store attribution in session & 30-day cookie
            session([
                'influencer_campaign_id' => $campaign->id,
                'influencer_click_id'    => $click->id,
                'influencer_code'        => $campaign->code,
            ]);

            Cookie::queue('influencer_campaign_id', (string) $campaign->id, 60 * 24 * 30);
            Cookie::queue('influencer_click_id', (string) $click->id, 60 * 24 * 30);
            Cookie::queue('influencer_code', (string) $campaign->code, 60 * 24 * 30);
        }

        // Smart Device Store & Deep Link Configuration
        $userAgent = $request->userAgent() ?? '';
        $isAndroid = (bool) preg_match('/android/i', $userAgent);
        $isIos     = (bool) preg_match('/iphone|ipad|ipod/i', $userAgent);

        // Configure Play Store URL with UTM Referrer
        $playUrl = $campaign->play_store_url ?: 'https://play.google.com/store/apps/details?id=com.community.reiacuser';
        $packageName = 'com.community.reiacuser';
        if (preg_match('/id=([a-zA-Z0-9_\.]+)/', $playUrl, $matches)) {
            $packageName = $matches[1];
        }

        $separator = str_contains($playUrl, '?') ? '&' : '?';
        $referrer = 'referrer=' . urlencode('utm_source=' . $campaign->code . '&utm_campaign=' . $campaign->code);
        $fullPlayStoreUrl = $playUrl . $separator . $referrer;

        // Mobile Launch: Intent for Android, Custom Scheme for iOS (scheme: reiac)
        if ($isAndroid) {
            $openAppUrl = 'intent://campaign/' . rawurlencode($campaign->code)
                . '#Intent;scheme=reiac;package=' . $packageName
                . ';S.browser_fallback_url=' . rawurlencode($fullPlayStoreUrl)
                . ';end';
            $fallbackStoreUrl = $fullPlayStoreUrl;

            return view('influencer.redirect', compact('campaign', 'openAppUrl', 'fallbackStoreUrl', 'isAndroid', 'isIos'));
        }

        if ($isIos) {
            $openAppUrl = 'reiac://campaign/' . rawurlencode($campaign->code);
            $fallbackStoreUrl = !empty($campaign->app_store_url) ? $campaign->app_store_url : $fullPlayStoreUrl;

            return view('influencer.redirect', compact('campaign', 'openAppUrl', 'fallbackStoreUrl', 'isAndroid', 'isIos'));
        }

        // 3. Desktop / Web Fallback
        $destination = $campaign->destination_url;

        // If destination is default localhost or empty, route desktop to Play Store with referrer
        if (empty($destination) || $destination === url('/') || $destination === url('') || str_contains($destination, 'localhost/community')) {
            return redirect()->away($fullPlayStoreUrl);
        }

        if (!str_starts_with($destination, 'http://') && !str_starts_with($destination, 'https://')) {
            $destination = url($destination);
        }

        return redirect()->away($destination);
    }
}
