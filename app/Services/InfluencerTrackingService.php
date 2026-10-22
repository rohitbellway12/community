<?php

namespace App\Services;

use App\Models\InfluencerCampaign;
use App\Models\InfluencerClick;
use App\Models\InfluencerRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class InfluencerTrackingService
{
    /**
     * Process an incoming click on an influencer link.
     */
    public function recordClick(InfluencerCampaign $campaign, Request $request): InfluencerClick
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $referer = $request->header('referer');

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        $isBot = false;
        $isSuspicious = false;
        $suspicionReasons = [];

        // 1. Robot detection via Agent & keyword regex
        if ($agent->isRobot()) {
            $isBot = true;
            $isSuspicious = true;
            $suspicionReasons[] = 'bot_user_agent_detected';
        } else {
            $botPatterns = '/(curl|wget|python|postman|headless|phantomjs|crawler|spider|scraper)/i';
            if (preg_match($botPatterns, $userAgent)) {
                $isBot = true;
                $isSuspicious = true;
                $suspicionReasons[] = 'automated_tool_user_agent';
            }
        }

        // Empty user-agent is abnormal
        if (empty(trim($userAgent))) {
            $isSuspicious = true;
            $suspicionReasons[] = 'empty_user_agent';
        }

        // 2. Click burst detection (e.g. > 15 clicks within last 2 minutes from same IP)
        if (!empty($ip)) {
            $recentClicksFromIp = InfluencerClick::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subMinutes(2))
                ->count();

            if ($recentClicksFromIp >= 15) {
                $isSuspicious = true;
                $suspicionReasons[] = 'abnormal_click_burst_from_ip';
            }
        }

        // Determine device type
        $deviceType = 'desktop';
        if ($isBot) {
            $deviceType = 'bot';
        } elseif ($agent->isTablet()) {
            $deviceType = 'tablet';
        } elseif ($agent->isMobile()) {
            $deviceType = 'mobile';
        }

        $platform = $agent->platform() ?: 'Unknown';
        $browser = $agent->browser() ?: 'Unknown';

        return InfluencerClick::create([
            'campaign_id'       => $campaign->id,
            'ip_address'        => $ip,
            'user_agent'        => substr($userAgent, 0, 500),
            'referer'           => $referer ? substr($referer, 0, 500) : null,
            'device_type'       => $deviceType,
            'platform'          => substr($platform, 0, 50),
            'browser'           => substr($browser, 0, 50),
            'is_bot'            => $isBot,
            'is_suspicious'     => $isSuspicious,
            'suspicion_reasons' => !empty($suspicionReasons) ? $suspicionReasons : null,
            'created_at'        => now(),
        ]);
    }

    /**
     * Record a registration conversion attributed to a campaign.
     */
    public function attributeRegistration(User $user, InfluencerCampaign $campaign, ?int $clickId = null, ?string $deviceId = null, ?string $ip = null): ?InfluencerRegistration
    {
        // Avoid duplicate attribution for the same user and campaign
        $existing = InfluencerRegistration::where('campaign_id', $campaign->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // A single click can only convert ONCE (prevents stale cookie from repeatedly attributing)
        if ($clickId && InfluencerRegistration::where('click_id', $clickId)->exists()) {
            return null;
        }

        $ip = $ip ?: request()->ip();
        $isSuspicious = false;
        $suspicionReasons = [];

        // Check for duplicate registrations from same IP in short time window
        if (!empty($ip)) {
            $recentRegCount = InfluencerRegistration::where('ip_address', $ip)
                ->where('created_at', '>=', now()->subHours(2))
                ->count();

            if ($recentRegCount >= 4) {
                $isSuspicious = true;
                $suspicionReasons[] = 'high_velocity_ip_registrations';
            }
        }

        // Check duplicate device registration
        if (!empty($deviceId)) {
            $deviceRegCount = InfluencerRegistration::where('device_id', $deviceId)->count();
            if ($deviceRegCount >= 2) {
                $isSuspicious = true;
                $suspicionReasons[] = 'multiple_registrations_same_device';
            }
        }

        return InfluencerRegistration::create([
            'campaign_id'       => $campaign->id,
            'user_id'           => $user->id,
            'click_id'          => $clickId,
            'ip_address'        => $ip,
            'device_id'         => $deviceId,
            'is_suspicious'     => $isSuspicious,
            'suspicion_reasons' => !empty($suspicionReasons) ? $suspicionReasons : null,
            'created_at'        => now(),
        ]);
    }
}
