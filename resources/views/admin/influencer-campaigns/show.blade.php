@extends('layouts.admin')

@section('title', 'Campaign Analytics - ' . $campaign->campaign_name)

@section('content')
<div class="space-y-6 max-w-[1440px] w-full mx-auto" x-data="{
    activeTab: '{{ $totalRegistrations > 0 ? 'registrations' : ($totalInstalls > 0 ? 'installs' : 'clicks') }}',
    copiedLink: false,
    copyUrl() {
        navigator.clipboard.writeText('{{ $campaign->tracking_url }}');
        this.copiedLink = true;
        setTimeout(() => this.copiedLink = false, 2500);
    }
}">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.influencer-campaigns.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Influencer Campaigns</a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-emerald-600">{{ $campaign->influencer_name }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $campaign->influencer_name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    {{ $campaign->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($campaign->status === 'paused' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                    {{ $campaign->status }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $campaign->channel }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Campaign: <strong class="text-slate-700">{{ $campaign->campaign_name }}</strong>
                @if($campaign->start_date || $campaign->end_date)
                    • Active from <span class="text-slate-700">{{ $campaign->start_date?->format('M d, Y') ?? 'Launch' }}</span> to <span class="text-slate-700">{{ $campaign->end_date?->format('M d, Y') ?? 'Ongoing' }}</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.influencer-campaigns.edit', $campaign) }}"
               class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>Edit Details</span>
            </a>

            <div class="relative" x-data="{ exportOpen: false }">
                <button @click="exportOpen = !exportOpen" type="button"
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Export CSV</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7 7" /></svg>
                </button>
                <div x-show="exportOpen" @click.away="exportOpen = false" x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg p-1.5 z-20 text-xs">
                    <a href="{{ route('admin.influencer-campaigns.export', ['campaign' => $campaign, 'type' => 'registrations']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 font-bold transition">
                        <span>👥 Registrations Records CSV</span>
                    </a>
                    <a href="{{ route('admin.influencer-campaigns.export', ['campaign' => $campaign, 'type' => 'installs']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-purple-50 hover:text-purple-700 font-bold transition">
                        <span>📱 App Installs &amp; Drop-offs CSV</span>
                    </a>
                    <a href="{{ route('admin.influencer-campaigns.export', ['campaign' => $campaign, 'type' => 'clicks']) }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-sky-50 hover:text-sky-700 font-bold transition">
                        <span>👆 Clicks &amp; Bot Audit CSV</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- SHAREABLE TRACKING LINK BANNER --}}
    <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-md border border-slate-800">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2">
                <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Shareable Tracking Link (Provide to Influencer)
                </span>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-xs md:text-sm font-bold text-emerald-300 bg-slate-950/80 px-3.5 py-2 rounded-xl border border-slate-800 select-all">
                        {{ $campaign->tracking_url }}
                    </span>
                    <button type="button" @click="copyUrl()"
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1.5 active:scale-95 shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span x-text="copiedLink ? 'Copied to Clipboard!' : 'Copy Link'"></span>
                    </button>
                    <a href="{{ $campaign->tracking_url }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs transition flex items-center gap-1 border border-slate-700">
                        Test Link ↗
                    </a>
                </div>
            </div>

            <div class="text-left md:text-right text-xs text-slate-300 md:border-l md:border-slate-800 md:pl-6 shrink-0">
                <div class="text-[10.5px] text-slate-400 uppercase tracking-wider font-bold">Android Play Store Target</div>
                <div class="font-mono text-emerald-400 truncate max-w-xs mt-0.5 font-bold" title="{{ $campaign->play_store_url ?: 'https://play.google.com/store/apps/details?id=com.community.reiacuser' }}">
                    {{ $campaign->play_store_url ?: 'com.community.reiacuser' }}
                </div>
                <div class="text-[10px] text-slate-400 mt-1">Unique Referral Code: <strong class="text-white bg-slate-800 px-1.5 py-0.5 rounded font-mono">{{ $campaign->code }}</strong></div>
            </div>
        </div>
    </div>

    {{-- METRIC CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        {{-- Total Clicks --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-sky-200 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">1. Link Clicks</span>
            <div class="text-xl font-black text-slate-900 mt-2">{{ number_format($totalClicks) }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ number_format($uniqueClicks) }} unique visitors</div>
        </div>

        {{-- App Installs --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-purple-200 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">2. App Installs</span>
            <div class="text-xl font-black text-purple-700 mt-2">{{ number_format($totalInstalls) }}</div>
            <div class="text-[10px] text-purple-600 mt-0.5">Mobile app opens</div>
        </div>

        {{-- Attributed Registrations --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-emerald-200 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">3. Registrations</span>
            <div class="text-xl font-black text-emerald-600 mt-2">{{ number_format($totalRegistrations) }}</div>
            <div class="text-[10px] text-emerald-600 mt-0.5">Verified signups</div>
        </div>

        {{-- Dropped Off (Install kiya but register nahi kiya) --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-200 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">4. Dropped Off</span>
            <div class="text-xl font-black text-amber-600 mt-2">{{ number_format($droppedOffInstalls) }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5">Installed, no signup</div>
        </div>

        {{-- Conversion Rate --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Conversion</span>
            <div class="text-xl font-black text-slate-900 mt-2">{{ $conversionRate }}%</div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ $totalInstalls > 0 ? 'Signup / Install' : 'Signup / Click' }}</div>
        </div>

        {{-- Fraud Flags --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-rose-200 transition-all flex flex-col justify-between">
            <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Fraud Signals</span>
            <div class="text-xl font-black {{ ($suspiciousClicksCount + $suspiciousRegistrationsCount) > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-2">
                {{ number_format($suspiciousClicksCount + $suspiciousRegistrationsCount) }}
            </div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ $suspiciousClicksCount }} clicks, {{ $suspiciousRegistrationsCount }} signups</div>
        </div>
    </div>

    {{-- DATE FILTER & SUSPICIOUS TOGGLE --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('admin.influencer-campaigns.show', $campaign) }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500">From:</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-500">To:</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                    <input type="checkbox" name="suspicious_only" value="1" {{ request('suspicious_only') ? 'checked' : '' }} onchange="this.form.submit()"
                           class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                    <span class="text-xs font-bold text-slate-700">⚠️ Show Suspicious Only</span>
                </label>

                <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                    Filter
                </button>

                @if($dateFrom || $dateTo || request('suspicious_only'))
                    <a href="{{ route('admin.influencer-campaigns.show', $campaign) }}"
                       class="text-xs font-bold text-slate-500 hover:text-slate-800 px-2 py-1">
                        Clear Filters
                    </a>
                @endif
            </div>

            @if($campaign->notes)
                <div class="text-xs text-slate-500 max-w-md italic">
                    Note: "{{ $campaign->notes }}"
                </div>
            @endif
        </form>
    </div>

    {{-- TAB SWITCHER --}}
    <div class="flex items-center gap-3 border-b border-slate-200 pb-px">
        <button type="button" @click="activeTab = 'registrations'"
                :class="activeTab === 'registrations' ? 'border-emerald-600 text-emerald-700 font-extrabold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 font-bold bg-slate-50'"
                class="px-4 py-2.5 border-b-2 text-xs flex items-center gap-2.5 transition rounded-t-xl">
            <span class="w-2 h-2 rounded-full" :class="activeTab === 'registrations' ? 'bg-emerald-500' : 'bg-slate-300'"></span>
            <span>Attributed Registrations</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black"
                  :class="activeTab === 'registrations' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700'">
                {{ $recentRegistrations->total() }}
            </span>
        </button>

        <button type="button" @click="activeTab = 'installs'"
                :class="activeTab === 'installs' ? 'border-purple-600 text-purple-700 font-extrabold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 font-bold bg-slate-50'"
                class="px-4 py-2.5 border-b-2 text-xs flex items-center gap-2.5 transition rounded-t-xl">
            <span class="w-2 h-2 rounded-full" :class="activeTab === 'installs' ? 'bg-purple-500' : 'bg-slate-300'"></span>
            <span>App Installs &amp; Drop-offs</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black"
                  :class="activeTab === 'installs' ? 'bg-purple-100 text-purple-800' : 'bg-slate-200 text-slate-700'">
                {{ $recentInstalls->total() }}
            </span>
        </button>

        <button type="button" @click="activeTab = 'clicks'"
                :class="activeTab === 'clicks' ? 'border-sky-600 text-sky-700 font-extrabold bg-white shadow-2xs' : 'border-transparent text-slate-600 hover:text-slate-900 font-bold bg-slate-50'"
                class="px-4 py-2.5 border-b-2 text-xs flex items-center gap-2.5 transition rounded-t-xl">
            <span class="w-2 h-2 rounded-full" :class="activeTab === 'clicks' ? 'bg-sky-500' : 'bg-slate-300'"></span>
            <span>Clicks &amp; Telemetry Log</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black"
                  :class="activeTab === 'clicks' ? 'bg-sky-100 text-sky-800' : 'bg-slate-200 text-slate-700'">
                {{ $recentClicks->total() }}
            </span>
        </button>
    </div>

    {{-- TAB 1: REGISTRATIONS --}}
    <div x-show="activeTab === 'registrations'" class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4">Device ID</th>
                        <th class="py-3 px-4 text-center">Fraud Signal</th>
                        <th class="py-3 px-4 text-right">Registered At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentRegistrations as $reg)
                        <tr class="hover:bg-slate-50/70 transition {{ $reg->is_suspicious ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($reg->user?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $reg->user?->name ?? 'Deleted User' }}</div>
                                        <div class="text-[10px] text-slate-400">ID: #{{ $reg->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $reg->user?->email ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-600">
                                {{ $reg->ip_address ?? '—' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                {{ $reg->device_id ? Str::limit($reg->device_id, 20) : 'Web Browser' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($reg->is_suspicious)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-100 text-rose-800"
                                          title="{{ $reg->suspicion_reasons ? implode(', ', $reg->suspicion_reasons) : 'Flagged' }}">
                                        ⚠️ Flagged
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-medium bg-emerald-50 text-emerald-700">
                                        ✓ Verified
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500 text-[11px]">
                                {{ $reg->created_at?->format('M d, Y h:i A') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                No registrations recorded for this period yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentRegistrations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recentRegistrations->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- TAB 2: APP INSTALLS & DROP-OFFS --}}
    <div x-show="activeTab === 'installs'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3 px-4">Device ID</th>
                        <th class="py-3 px-4">Platform</th>
                        <th class="py-3 px-4">App Version</th>
                        <th class="py-3 px-4 text-center">Conversion Status</th>
                        <th class="py-3 px-4">Associated User</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4 text-right">Installed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentInstalls as $install)
                        <tr class="hover:bg-slate-50/70 transition {{ !$install->is_registered ? 'bg-amber-50/20' : '' }}">
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-800 font-bold" title="{{ $install->device_id }}">
                                {{ Str::limit($install->device_id, 24) }}
                            </td>
                            <td class="py-3 px-4">
                                @if(strtolower($install->platform) === 'android')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        🤖 Android
                                    </span>
                                @elseif(strtolower($install->platform) === 'ios')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-900 text-white">
                                        🍎 iOS
                                    </span>
                                @else
                                    <span class="capitalize text-slate-700 font-semibold">{{ $install->platform }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                {{ $install->app_version ?: 'v1.0.0' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($install->is_registered)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Registered
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-100 text-amber-800" title="User downloaded app but did not complete registration">
                                        ⏱️ Dropped Off
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($install->user)
                                    <div class="font-bold text-slate-900">{{ $install->user->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $install->user->email }}</div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Not signed up</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500">
                                {{ $install->ip_address ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500 text-[11px]">
                                {{ $install->created_at?->format('M d, Y h:i A') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No app installs recorded for this campaign yet.
                                <span class="block text-[11px] text-slate-400 mt-1">When users open the app from this campaign link, their installs and drop-off status will appear here.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentInstalls->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recentInstalls->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- TAB 3: CLICKS & BOT AUDIT LOG --}}
    <div x-show="activeTab === 'clicks'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3 px-4">Click IP</th>
                        <th class="py-3 px-4">Device</th>
                        <th class="py-3 px-4">Platform &amp; Browser</th>
                        <th class="py-3 px-4 text-center">Bot Status</th>
                        <th class="py-3 px-4 text-center">Suspicious Flag</th>
                        <th class="py-3 px-4">Referer</th>
                        <th class="py-3 px-4 text-right">Clicked At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($recentClicks as $click)
                        <tr class="hover:bg-slate-50/70 transition {{ $click->is_suspicious ? 'bg-rose-50/30' : '' }}">
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-800 font-bold">
                                {{ $click->ip_address ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="capitalize text-slate-700 font-semibold">{{ $click->device_type }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $click->platform }} / {{ $click->browser }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($click->is_bot)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">BOT</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Human</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($click->is_suspicious)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800"
                                          title="{{ $click->suspicion_reasons ? implode(', ', $click->suspicion_reasons) : 'Abnormal pattern' }}">
                                        ⚠️ Flagged
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px]">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-[11px] truncate max-w-xs" title="{{ $click->referer }}">
                                {{ $click->referer ? Str::limit($click->referer, 35) : 'Direct / App' }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500 text-[11px]">
                                {{ $click->created_at?->format('M d, Y h:i:s A') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No clicks recorded yet for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentClicks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $recentClicks->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
