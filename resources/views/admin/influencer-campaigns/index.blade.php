@extends('layouts.admin')

@section('title', 'Influencer Tracking')

@section('content')
<div class="w-full space-y-6" x-data="{ copiedCode: '' }">

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

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-extrabold text-emerald-600 uppercase tracking-wider">Marketing &amp; Growth</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500 font-medium">Influencer Attribution</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">Influencer Tracking System</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Generate tracking links for YouTubers &amp; creators, attribute registrations, monitor conversion rates, and flag bot/suspicious bursts.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.influencer-campaigns.create') }}"
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Campaign</span>
            </a>
        </div>
    </div>

    {{-- 5 BALANCED KPI SUMMARY CARDS (FUNNEL) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        {{-- Total Campaigns --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-slate-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">Campaigns</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalCampaigns) }}</span>
                <span class="text-[10px] text-slate-500 font-semibold bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200/60">Total</span>
            </div>
        </div>

        {{-- Total Clicks --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-sky-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">1. Link Clicks</span>
                <span class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalClicks) }}</span>
                <span class="text-[10px] text-sky-700 font-bold bg-sky-50 px-1.5 py-0.5 rounded border border-sky-100">Visits</span>
            </div>
        </div>

        {{-- App Installs --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-purple-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">2. App Installs</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-black text-purple-700">{{ number_format($totalInstalls) }}</span>
                <span class="text-[10px] text-purple-700 font-bold bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">Downloads</span>
            </div>
        </div>

        {{-- Registrations --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-emerald-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">3. Registrations</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-black text-emerald-700">{{ number_format($totalRegistrations) }}</span>
                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Signed Up</span>
            </div>
        </div>

        {{-- Dropped Off (Chhod Diya) --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider">4. Dropped Off</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600">{{ number_format($totalDroppedOff) }}</span>
                <span class="text-[10px] text-amber-800 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">No Signup</span>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="bg-white border border-slate-200/90 rounded-2xl p-3.5 shadow-2xs">
        <form method="GET" action="{{ route('admin.influencer-campaigns.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center">
            {{-- Search --}}
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search influencer name, campaign, or code..."
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Channel --}}
            <div class="sm:col-span-3">
                <select name="channel" onchange="this.form.submit()"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    <option value="">All Channels</option>
                    <option value="youtube" {{ request('channel') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="instagram" {{ request('channel') === 'instagram' ? 'selected' : '' }}>Instagram</option>
                    <option value="telegram" {{ request('channel') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                    <option value="facebook" {{ request('channel') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="tiktok" {{ request('channel') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                    <option value="twitter" {{ request('channel') === 'twitter' ? 'selected' : '' }}>Twitter / X</option>
                    <option value="website" {{ request('channel') === 'website' ? 'selected' : '' }}>Website / Blog</option>
                    <option value="other" {{ request('channel') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- Status --}}
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/50 text-xs font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="sm:col-span-1 flex justify-end">
                @if(request()->hasAny(['search', 'channel', 'status']))
                    <a href="{{ route('admin.influencer-campaigns.index') }}"
                       class="w-full text-center py-2 px-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-900 text-xs font-bold transition">
                        Reset
                    </a>
                @else
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Filter
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- MAIN CAMPAIGNS CONTENT / TABLE --}}
    @if($campaigns->count() > 0)
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                            <th class="py-3.5 px-4">Influencer &amp; Campaign</th>
                            <th class="py-3.5 px-3">Channel</th>
                            <th class="py-3.5 px-4">Tracking Code &amp; Link</th>
                            <th class="py-3.5 px-3 text-center">Clicks</th>
                            <th class="py-3.5 px-3 text-center">App Installs</th>
                            <th class="py-3.5 px-3 text-center">Registrations</th>
                            <th class="py-3.5 px-3 text-center">Dropped Off</th>
                            <th class="py-3.5 px-3 text-center">Conv. Rate</th>
                            <th class="py-3.5 px-3 text-center">Fraud Flags</th>
                            <th class="py-3.5 px-3 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($campaigns as $campaign)
                            @php
                                $rate = $campaign->installs_count > 0 
                                    ? round(($campaign->registrations_count / $campaign->installs_count) * 100, 1)
                                    : ($campaign->clicks_count > 0 ? round(($campaign->registrations_count / $campaign->clicks_count) * 100, 1) : 0);
                                $channelColors = [
                                    'youtube'   => 'bg-red-50 text-red-600 border-red-200',
                                    'instagram' => 'bg-pink-50 text-pink-600 border-pink-200',
                                    'telegram'  => 'bg-sky-50 text-sky-600 border-sky-200',
                                    'facebook'  => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'tiktok'    => 'bg-slate-900 text-white border-slate-900',
                                    'twitter'   => 'bg-slate-100 text-slate-800 border-slate-300',
                                    'website'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'other'     => 'bg-slate-50 text-slate-600 border-slate-200',
                                ];
                                $channelBadge = $channelColors[$campaign->channel] ?? $channelColors['other'];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                {{-- Influencer & Campaign --}}
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.influencer-campaigns.show', $campaign) }}" class="font-bold text-slate-900 hover:text-emerald-600 text-sm block">
                                        {{ $campaign->influencer_name }}
                                    </a>
                                    <span class="text-[11px] text-slate-500 block mt-0.5 font-normal">{{ $campaign->campaign_name }}</span>
                                    @if($campaign->start_date || $campaign->end_date)
                                        <span class="text-[10px] text-slate-400 mt-0.5 inline-block">
                                            {{ $campaign->start_date?->format('M d') ?? 'Start' }} → {{ $campaign->end_date?->format('M d, Y') ?? 'Ongoing' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Channel --}}
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold border uppercase tracking-wider {{ $channelBadge }}">
                                        {{ $campaign->channel }}
                                    </span>
                                </td>

                                {{-- Tracking Code & Link --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-800 font-mono font-bold text-xs select-all">
                                            {{ $campaign->code }}
                                        </span>
                                        <button type="button"
                                                @click="navigator.clipboard.writeText('{{ $campaign->tracking_url }}'); copiedCode = '{{ $campaign->code }}'; setTimeout(() => copiedCode = '', 2000)"
                                                class="p-1 rounded-md text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                                                title="Copy Shareable Link">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <span x-show="copiedCode === '{{ $campaign->code }}'" x-transition class="text-[10px] text-emerald-600 font-bold block mt-0.5">
                                        ✓ Copied to clipboard!
                                    </span>
                                </td>

                                {{-- Clicks --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="font-black text-slate-900">{{ number_format($campaign->clicks_count) }}</span>
                                    <span class="text-[10px] text-slate-400 block font-normal">({{ number_format($campaign->unique_clicks_count) }} unq)</span>
                                </td>

                                {{-- App Installs --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="font-black text-purple-700 text-sm">{{ number_format($campaign->installs_count) }}</span>
                                </td>

                                {{-- Registrations --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="font-black text-emerald-700 text-sm">{{ number_format($campaign->registrations_count) }}</span>
                                </td>

                                {{-- Dropped Off (Install Kiya but Register nahi kiya) --}}
                                <td class="py-3.5 px-3 text-center">
                                    @if($campaign->dropped_off_installs_count > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Installed app but did not register">
                                            {{ number_format($campaign->dropped_off_installs_count) }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">0</span>
                                    @endif
                                </td>

                                {{-- Conversion Rate --}}
                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $rate >= 5 ? 'bg-emerald-100 text-emerald-800' : ($rate > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $rate }}%
                                    </span>
                                </td>

                                {{-- Fraud / Bot Signals --}}
                                <td class="py-3.5 px-3 text-center">
                                    @if($campaign->suspicious_clicks_count > 0 || $campaign->suspicious_registrations_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            ⚠️ {{ $campaign->suspicious_clicks_count + $campaign->suspicious_registrations_count }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Clean</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 px-3 text-center">
                                    @if($campaign->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active
                                        </span>
                                    @elseif($campaign->status === 'paused')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            Paused
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                            Completed
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.influencer-campaigns.show', $campaign) }}"
                                           class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition"
                                           title="View Analytics">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('admin.influencer-campaigns.edit', $campaign) }}"
                                           class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition"
                                           title="Edit Campaign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('admin.influencer-campaigns.destroy', $campaign) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this campaign? All tracked click history will be permanently deleted.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                    title="Delete Campaign">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($campaigns->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    @else
        {{-- BEAUTIFUL EMPTY STATE --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-8 sm:p-12 shadow-2xs text-center">
            <div class="max-w-md mx-auto space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-100 to-sky-100 text-emerald-700 flex items-center justify-center mx-auto shadow-inner">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">No Influencer Campaigns Yet</h3>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Create your first tracking campaign to generate dedicated shareable links for YouTubers, Instagram creators, and affiliates.
                    </p>
                </div>

                {{-- Feature Pills --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 text-left">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-700">
                        <div class="font-bold text-[11px] text-emerald-700">⚡ Tracking Link</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Short public URL redirects to App or Store.</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-700">
                        <div class="font-bold text-[11px] text-sky-700">👥 Attribution</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Auto-credits signups via Web &amp; Mobile API.</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-slate-700">
                        <div class="font-bold text-[11px] text-rose-700">🛡️ Fraud Defense</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">Filters scrapers, bots &amp; click bursts.</div>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ route('admin.influencer-campaigns.create') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Create First Campaign</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
