<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\InfluencerCampaign;
use App\Models\InfluencerInstall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfluencerApiController extends Controller
{
    /**
     * Track app install on first app open.
     * Called by Flutter / Mobile App on initial launch.
     */
    public function trackInstall(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'        => ['nullable', 'string', 'max:255'],
            'referrer'    => ['nullable', 'string', 'max:500'],
            'device_id'   => ['required', 'string', 'max:255'],
            'platform'    => ['nullable', 'string', 'in:android,ios,web,other'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ]);

        $rawCode = $validated['code'] ?? $validated['referrer'] ?? null;
        if (empty($rawCode)) {
            return response()->json([
                'success' => false,
                'message' => 'No campaign referral code or referrer provided.',
            ], 422);
        }

        // Parse code if passed as full referrer string e.g. "utm_source=influencer&utm_campaign=ROHIT50"
        $campaignCode = trim($rawCode);
        if (str_contains($campaignCode, 'utm_campaign=')) {
            parse_str($campaignCode, $params);
            $campaignCode = $params['utm_campaign'] ?? $params['ref_code'] ?? $campaignCode;
        }

        $campaign = InfluencerCampaign::where('code', $campaignCode)
            ->orWhere('code', strtoupper($campaignCode))
            ->first();

        if (!$campaign) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign with this code does not exist.',
            ], 404);
        }

        $deviceId = trim($validated['device_id']);
        $platform = $validated['platform'] ?? ($request->header('X-Platform') ?: 'android');
        $appVersion = $validated['app_version'] ?? $request->header('X-App-Version');

        // Record install (or retrieve existing without duplicate)
        $install = InfluencerInstall::firstOrCreate(
            [
                'campaign_id' => $campaign->id,
                'device_id'   => $deviceId,
            ],
            [
                'platform'      => strtolower($platform),
                'app_version'   => $appVersion,
                'ip_address'    => $request->ip(),
                'is_registered' => false,
                'created_at'    => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'App install attributed successfully.',
            'data'    => [
                'campaign_code'    => $campaign->code,
                'influencer_name'  => $campaign->influencer_name,
                'device_id'        => $deviceId,
                'platform'         => $install->platform,
                'is_first_install' => $install->wasRecentlyCreated,
                'is_registered'    => (bool) $install->is_registered,
            ],
        ], 200);
    }
}
