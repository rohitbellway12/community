@extends('layouts.admin')

@section('title', 'Notification Details & Delivery Report')

@section('content')
<div class="p-6 space-y-6 max-w-[1440px] w-full mx-auto" x-data="{ resendConfirm: false }">

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
                <a href="{{ route('admin.push-notifications.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-wider">← Back to Notifications</a>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">Campaign Report #{{ $pushNotification->id }}</span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-xl font-extrabold text-slate-900">{{ $pushNotification->title }}</h1>
                @php $badge = $pushNotification->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $badge['bg'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Dispatched by {{ $pushNotification->creator ? $pushNotification->creator->name : 'System' }} on {{ $pushNotification->created_at->format('M d, Y h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.push-notifications.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition">
                All Campaigns
            </a>

            <form method="POST" action="{{ route('admin.push-notifications.resend', $pushNotification) }}">
                @csrf
                <button type="button"
                        onclick="confirmPushResend(this.form, '{{ addslashes($pushNotification->title) }}')"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Resend Broadcast</span>
                </button>
            </form>

            <form method="POST" action="{{ route('admin.push-notifications.destroy', $pushNotification) }}">
                @csrf
                @method('DELETE')
                <button type="button"
                        onclick="confirmPushDelete(this.form, '{{ addslashes($pushNotification->title) }}')"
                        class="p-2.5 text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl transition border border-rose-200 cursor-pointer"
                        title="Delete Notification Record">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- STATS OVERVIEW --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Targeted Recipients</span>
            <span class="text-2xl font-black text-slate-900 mt-2 block">{{ number_format($pushNotification->total_recipients) }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Active user accounts targeted</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Delivered Successfully</span>
            <span class="text-2xl font-black text-emerald-600 mt-2 block">{{ number_format($pushNotification->success_count) }}</span>
            <span class="text-[11px] text-emerald-600 font-semibold mt-1 block">Reached device tokens</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Failed / Skipped</span>
            <span class="text-2xl font-black text-rose-600 mt-2 block">{{ number_format($pushNotification->failed_count) }}</span>
            <span class="text-[11px] text-rose-500 font-semibold mt-1 block">No token or rejected</span>
        </div>

        @php
            $rate = ($pushNotification->total_recipients > 0)
                ? round(($pushNotification->success_count / $pushNotification->total_recipients) * 100, 1)
                : 0;
        @endphp
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Delivery Success Rate</span>
            <span class="text-2xl font-black text-blue-600 mt-2 block">{{ $rate }}%</span>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $rate) }}%"></div>
            </div>
        </div>
    </div>

    {{-- NOTIFICATION DETAILS CARD & PREVIEW --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Card Details --}}
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Notification Content & Configuration</h2>

            <div class="space-y-3">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Title</span>
                    <p class="text-sm font-extrabold text-slate-900 mt-0.5">{{ $pushNotification->title }}</p>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Description / Body</span>
                    <p class="text-xs text-slate-700 leading-relaxed mt-0.5 whitespace-pre-line">{{ $pushNotification->description }}</p>
                </div>

                @if($pushNotification->action_link)
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block uppercase">Action Destination</span>
                        <span class="inline-flex items-center space-x-1.5 text-xs text-blue-600 font-bold bg-blue-50 px-3 py-1 rounded-lg mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            <span>{{ $pushNotification->action_link }}</span>
                        </span>
                    </div>
                @endif

                <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block uppercase">Audience Type</span>
                        <div class="mt-1">
                            @if($pushNotification->audience_type === 'all_students')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 inline-block">
                                    👥 All Active Students
                                </span>
                            @elseif($pushNotification->audience_type === 'country')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-block">
                                    🌍 Countries ({{ $pushNotification->targets->count() }})
                                </span>
                            @elseif($pushNotification->audience_type === 'group')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 inline-block">
                                    🏢 Groups ({{ $pushNotification->targets->count() }})
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">
                                    👤 Individual Users ({{ $pushNotification->targets->count() }})
                                </span>
                            @endif

                            @if($pushNotification->targets->isNotEmpty() && $pushNotification->audience_type !== 'all_students')
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($pushNotification->targets as $target)
                                        @if($pushNotification->audience_type === 'country' && $target->country)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                                                <span>{{ $target->country->flag ?? '🌍' }}</span>
                                                <span>{{ $target->country->name }}</span>
                                            </span>
                                        @elseif($pushNotification->audience_type === 'group' && $target->group)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                                                <span>🏢 {{ $target->group->name }}</span>
                                            </span>
                                        @elseif($target->user)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200">
                                                <span>👤 {{ $target->user->name }}</span>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block uppercase">Target Device / Platform</span>
                        <div class="mt-1">
                            @if(($pushNotification->target_platform ?? 'all') === 'android')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">
                                    🤖 Android (Google Play Store) Only
                                </span>
                            @elseif(($pushNotification->target_platform ?? 'all') === 'ios')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 inline-block">
                                    🍎 iOS (Apple App Store) Only
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 inline-block">
                                    📱 All Devices (Android & iOS)
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-[11px] font-bold text-slate-400 block uppercase">Sent Timestamp</span>
                        <p class="text-xs font-bold text-slate-800 mt-1">
                            {{ $pushNotification->sent_at ? $pushNotification->sent_at->format('M d, Y h:i:s A') : ($pushNotification->scheduled_at ? 'Scheduled: ' . $pushNotification->scheduled_at->format('M d, Y h:i A') : 'Draft') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Attached Media Card --}}
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Attached Media Asset</h2>

            @if($pushNotification->banner_url)
                <div>
                    <span class="text-[11px] font-bold text-slate-400 block mb-1.5">Notification Banner</span>
                    <img src="{{ $pushNotification->banner_url }}" alt="Notification Banner" class="w-full h-44 object-cover rounded-xl border border-slate-200 shadow-xs">
                </div>
            @else
                <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400 text-xs">
                    No banner image attached (Standard text push notification)
                </div>
            @endif
        </div>
    </div>

    {{-- DELIVERY LOGS TABLE (FR-10, FR-11) --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Device Delivery Logs</h3>
                <p class="text-xs text-slate-400">Detailed delivery breakdown for each targeted device</p>
            </div>

            {{-- Filter logs --}}
            <form method="GET" action="{{ route('admin.push-notifications.show', $pushNotification) }}" class="flex items-center space-x-2">
                <input type="text" name="log_search" value="{{ request('log_search') }}" placeholder="Search user or token..."
                       class="text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 py-1.5 px-3">

                <select name="log_status" class="text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 py-1.5">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('log_status') === 'sent' ? 'selected' : '' }}>Sent Only</option>
                    <option value="failed" {{ request('log_status') === 'failed' ? 'selected' : '' }}>Failed Only</option>
                </select>

                <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-4">Recipient Student</th>
                        <th class="py-3 px-4">Device Token</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Message / Reason</th>
                        <th class="py-3 px-4 text-right">Delivered At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                @if($log->user)
                                    <div>{{ $log->user->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-normal">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-slate-400 italic">User #{{ $log->user_id }}</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500 max-w-xs truncate">
                                @if($log->device_token)
                                    <span title="{{ $log->device_token }}">{{ Str::limit($log->device_token, 30) }}</span>
                                @else
                                    <span class="text-slate-400 italic">No token</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($log->status === 'sent')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>✓</span>
                                        <span>Delivered</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span>✕</span>
                                        <span>Failed</span>
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-[11px] text-slate-500 max-w-sm truncate">
                                {{ $log->error_message ?: 'Delivered successfully to FCM gateway' }}
                            </td>

                            <td class="py-3 px-4 text-right text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->delivered_at ? $log->delivered_at->format('M d, Y h:i:s A') : ($log->created_at ? $log->created_at->format('M d, Y h:i:s A') : 'N/A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                No delivery logs recorded yet. (Notification may still be queueing or scheduled).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
