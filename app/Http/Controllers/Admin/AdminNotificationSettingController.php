<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNotificationSettingController extends Controller
{
    public function index(): View
    {
        $settings = NotificationSetting::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('admin.notification-settings.index', compact('settings'));
    }

    public function update(Request $request, NotificationSetting $setting): RedirectResponse
    {
        $data = $request->validate([
            'label'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'is_push_enabled' => ['required', 'boolean'],
            'is_active'       => ['required', 'boolean'],
        ]);

        $setting->update($data);

        NotificationSetting::clearCache();

        return back()->with('success', 'Notification setting updated successfully.');
    }

    public function updateAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'push_enabled' => ['array'],
            'active'       => ['array'],
        ]);

        $pushKeys = $validated['push_enabled'] ?? [];
        $activeKeys = $validated['active'] ?? [];

        NotificationSetting::query()->each(function (NotificationSetting $setting) use ($pushKeys, $activeKeys) {
            $setting->update([
                'is_push_enabled' => in_array($setting->key, $pushKeys, true),
                'is_active'       => in_array($setting->key, $activeKeys, true),
            ]);
        });

        NotificationSetting::clearCache();

        cache()->forget('notification_settings_all');

        return back()->with('success', 'Notification settings updated successfully.');
    }
}
