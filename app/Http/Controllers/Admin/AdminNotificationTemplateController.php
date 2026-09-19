<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirebaseSetting;
use App\Models\NotificationSetting;
use App\Models\NotificationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminNotificationTemplateController extends Controller
{
    /**
     * Combined index: shows push toggles (notification_settings) +
     * editable title/body templates (notification_templates) for each event type.
     */
    public function index(Request $request): View
    {
        $templates = NotificationTemplate::query()
            ->orderBy('sort_order')
            ->paginate(30)
            ->withQueryString();

        $settings = NotificationSetting::whereIn('key', $templates->pluck('event'))
            ->get()
            ->keyBy('key');

        $firebase = FirebaseSetting::config();

        return view('admin.notification-templates.index', compact('templates', 'settings', 'firebase'));
    }

    /**
     * Bulk-save all templates + push toggles from the combined index form.
     */
    public function updateAll(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'templates'    => ['required', 'array'],
            'push_enabled' => ['array'],
            'active'       => ['array'],
        ]);

        NotificationTemplate::query()->each(function (NotificationTemplate $template) use ($data) {
            $event = $template->event;
            $row   = $data['templates'][$event] ?? [];

            $template->update([
                'title'    => $row['title'] ?? $template->title,
                'body'     => $row['body'] ?? $template->body,
                'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : $template->is_active,
            ]);

            $template->clearCache($event);
        });

        NotificationSetting::query()->each(function (NotificationSetting $setting) use ($data) {
            $setting->update([
                'is_push_enabled' => in_array($setting->key, $data['push_enabled'] ?? [], true),
                'is_active'       => in_array($setting->key, $data['active'] ?? [], true),
            ]);
            $setting->clearCache();
        });

        return back()->with('success', 'All notification templates and settings saved.');
    }

    /**
     * Update Firebase / FCM credentials from the combined page.
     */
    public function updateFirebase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id'       => ['nullable', 'string', 'max:255'],
            'credentials_json' => ['nullable', 'string'],
            'is_enabled'       => ['boolean'],
        ]);

        $firebase = FirebaseSetting::firstOrCreate([]);

        $firebase->update($validated);

        FirebaseSetting::clearCache();

        return back()->with('success', 'Firebase push settings updated successfully.');
    }
}
