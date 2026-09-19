<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirebaseSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFirebaseSettingController extends Controller
{
    public function edit(): View
    {
        $firebase = FirebaseSetting::config();

        return view('admin.firebase-settings.edit', compact('firebase'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id'       => ['nullable', 'string', 'max:255'],
            'credentials_json' => ['nullable', 'string'],
            'server_key'       => ['nullable', 'string', 'max:255'],
            'is_enabled'       => ['required', 'boolean'],
            'default_title'    => ['required', 'string', 'max:255'],
            'default_icon'     => ['nullable', 'string', 'max:255'],
            'default_color'    => ['required', 'string', 'max:7'],
        ]);

        $firebase = FirebaseSetting::firstOrCreate([]);

        $firebase->update($validated);

        FirebaseSetting::clearCache();

        return back()->with('success', 'Firebase settings updated successfully.');
    }
}
