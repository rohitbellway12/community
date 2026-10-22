@extends('layouts.admin')

@section('title', 'Edit Influencer Campaign')

@section('content')
<div class="space-y-6 max-w-[920px] w-full mx-auto" x-data="{
    code: '{{ old('code', $campaign->code) }}'
}">

    {{-- BREADCRUMB / HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.influencer-campaigns.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Influencer Campaigns</a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-emerald-600">Edit Campaign</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Edit Campaign: {{ $campaign->campaign_name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Influencer: {{ $campaign->influencer_name }} ({{ $campaign->code }})</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.influencer-campaigns.show', $campaign) }}"
               class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs shadow-xs transition">
                View Analytics
            </a>
            <a href="{{ route('admin.influencer-campaigns.index') }}"
               class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs shadow-xs transition">
                ← Back to List
            </a>
        </div>
    </div>

    {{-- ERROR SUMMARY --}}
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-800 text-xs">
            <div class="font-bold mb-1">Please fix the following errors:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM CARD --}}
    <form action="{{ route('admin.influencer-campaigns.update', $campaign) }}" method="POST" class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            {{-- Influencer Name --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Influencer / Creator Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="influencer_name" value="{{ old('influencer_name', $campaign->influencer_name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            {{-- Campaign Name --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Campaign Title <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="campaign_name" value="{{ old('campaign_name', $campaign->campaign_name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            {{-- Tracking Code --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Unique Tracking Code <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="code" x-model="code" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <p class="text-[11px] text-slate-400 mt-1">Live tracking link: <code class="text-emerald-600 font-bold" x-text="'{{ url('/c') }}/' + code"></code></p>
            </div>

            {{-- Channel --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Promotion Channel <span class="text-rose-500">*</span>
                </label>
                <select name="channel" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @foreach(['youtube' => 'YouTube', 'instagram' => 'Instagram', 'telegram' => 'Telegram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'twitter' => 'Twitter / X', 'website' => 'Website / Blog', 'other' => 'Other'] as $k => $label)
                        <option value="{{ $k }}" {{ old('channel', $campaign->channel) === $k ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Android Play Store URL --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span>🤖 Android Play Store URL</span>
                        <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Smart Redirect</span>
                    </span>
                    <span class="text-[11px] text-slate-400 font-normal">For Android users</span>
                </label>
                <input type="url" name="play_store_url" value="{{ old('play_store_url', $campaign->play_store_url ?? 'https://play.google.com/store/apps/details?id=com.community.reiacuser') }}"
                       placeholder="https://play.google.com/store/apps/details?id=com.community.reiacuser"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <p class="text-[11px] text-slate-400 mt-1">When an Android user clicks the link, they are automatically sent here with referral code attached for auto-attribution.</p>
            </div>

            {{-- iOS App Store URL --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    🍎 Apple App Store URL (Optional)
                </label>
                <input type="url" name="app_store_url" value="{{ old('app_store_url', $campaign->app_store_url) }}"
                       placeholder="https://apps.apple.com/app/id..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <p class="text-[11px] text-slate-400 mt-1">iPhone/iPad users will be routed here automatically.</p>
            </div>

            {{-- Destination / Fallback URL --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    🌐 Website / Desktop Fallback (Optional)
                </label>
                <input type="url" name="destination_url" value="{{ old('destination_url', $campaign->destination_url) }}"
                       placeholder="https://play.google.com/store/apps/details?id=com.community.reiacuser"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <p class="text-[11px] text-slate-400 mt-1">Where to redirect if opened from desktop computer.</p>
            </div>

            {{-- Dates --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Start Date (Optional)
                </label>
                <input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    End Date (Optional)
                </label>
                <input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select name="status" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="active" {{ old('status', $campaign->status) === 'active' ? 'selected' : '' }}>Active (Tracking Live)</option>
                    <option value="paused" {{ old('status', $campaign->status) === 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="completed" {{ old('status', $campaign->status) === 'completed' ? 'selected' : '' }}>Completed / Expired</option>
                </select>
            </div>

            {{-- Notes --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Internal Notes &amp; Terms
                </label>
                <textarea name="notes" rows="3"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('notes', $campaign->notes) }}</textarea>
            </div>
        </div>

        {{-- SUBMIT ACTIONS --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.influencer-campaigns.show', $campaign) }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                Update Campaign
            </button>
        </div>
    </form>

</div>
@endsection
