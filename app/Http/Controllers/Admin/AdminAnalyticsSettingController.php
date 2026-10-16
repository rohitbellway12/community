<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnalyticsSettingController extends Controller
{
    public function edit(): View
    {
        $setting = AnalyticsSetting::config();

        return view('admin.settings.analytics', compact('setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'is_enabled'          => ['nullable'],
            'play_store_url'      => ['nullable', 'url', 'max:500'],
            'app_store_url'       => ['nullable', 'url', 'max:500'],
            'custom_head_scripts' => ['nullable', 'string'],
            'custom_body_scripts' => ['nullable', 'string'],
        ]);

        $validated['is_enabled'] = $request->boolean('is_enabled');
        
        // Clean GA ID if user entered full script or whitespace
        if (!empty($validated['google_analytics_id'])) {
            $id = trim($validated['google_analytics_id']);
            if (!str_starts_with(strtoupper($id), 'G-') && !str_starts_with(strtoupper($id), 'UA-')) {
                $id = 'G-' . ltrim($id, '-');
            }
            $validated['google_analytics_id'] = strtoupper($id);
        }

        $setting = AnalyticsSetting::firstOrCreate([]);
        $setting->update($validated);

        AnalyticsSetting::clearCache();

        return back()->with('success', 'Website settings updated successfully!');
    }
}
