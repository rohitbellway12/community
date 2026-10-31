@extends('layouts.admin')

@section('title', 'Push Notifications Management')

@section('content')
<div class="w-full space-y-6">

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

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl flex items-center justify-between text-rose-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-black">✕</span>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Communications</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">Push Notifications</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-0.5">Push Notification Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Send targeted rich push broadcasts, track live delivery logs, and manage notification schedules.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.notification-templates.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>FCM Settings</span>
            </a>

            <a href="{{ route('admin.push-notifications.create') }}"
               class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition flex items-center gap-2 active:scale-95">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Compose Notification</span>
            </a>
        </div>
    </div>

    {{-- METRIC SUMMARY CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        {{-- Total Campaigns --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Campaigns</span>
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black text-slate-900">{{ number_format($stats['total_campaigns']) }}</span>
                <span class="text-[10px] text-slate-400 font-semibold bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">All Time</span>
            </div>
        </div>

        {{-- Dispatched --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Dispatched</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black text-blue-600">{{ number_format($stats['total_sent']) }}</span>
                <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100/70">Sent</span>
            </div>
        </div>

        {{-- Delivered --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-emerald-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Delivered</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black text-emerald-600">{{ number_format($stats['total_delivered']) }}</span>
                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100/70">Success</span>
            </div>
        </div>

        {{-- Failed / Skipped --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-rose-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Failed / Skipped</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black text-rose-600">{{ number_format($stats['total_failed']) }}</span>
                <span class="text-[10px] text-rose-700 font-bold bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100/70">Error</span>
            </div>
        </div>

        {{-- Scheduled --}}
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-200 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Scheduled</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black text-amber-600">{{ number_format($stats['total_scheduled']) }}</span>
                <span class="text-[10px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100/70">Upcoming</span>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.push-notifications.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5">
            {{-- Search Box --}}
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or description..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white pl-9 pr-3 py-2 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Audience Type Filter --}}
            <div class="w-full lg:w-44">
                <select name="audience_type" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    <option value="">All Audiences</option>
                    <option value="all_students" {{ request('audience_type') === 'all_students' ? 'selected' : '' }}>All Students</option>
                    <option value="country" {{ request('audience_type') === 'country' ? 'selected' : '' }}>By Country</option>
                    <option value="group" {{ request('audience_type') === 'group' ? 'selected' : '' }}>Specific Groups</option>
                    <option value="individual" {{ request('audience_type') === 'individual' ? 'selected' : '' }}>Individual Users</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="w-full lg:w-40">
                <select name="status" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="partially_failed" {{ request('status') === 'partially_failed' ? 'selected' : '' }}>Partially Failed</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            {{-- Date Range --}}
            <div class="flex items-center gap-1.5 w-full lg:w-auto">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white px-2.5 py-1.5 transition">
                <span class="text-slate-400 text-xs font-bold">-</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white px-2.5 py-1.5 transition">
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-2xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'audience_type', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.push-notifications.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- NOTIFICATIONS DATA TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Notification Details</th>
                        <th class="py-3.5 px-4">Audience</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Recipients / Delivered</th>
                        <th class="py-3.5 px-4">Date & Time</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($notifications as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Details --}}
                            <td class="py-3.5 px-4 max-w-md">
                                <div class="flex items-start gap-3">
                                    @if($item->banner_url || $item->image_url)
                                        <div class="w-11 h-11 rounded-xl overflow-hidden shrink-0 border border-slate-200 bg-slate-50">
                                            <img src="{{ $item->banner_url ?: $item->image_url }}" alt="Media" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('admin.push-notifications.show', $item) }}" class="font-extrabold text-slate-900 hover:text-amber-600 transition-colors block truncate">
                                            {{ $item->title }}
                                        </a>
                                        <p class="text-slate-500 text-[11px] line-clamp-1 mt-0.5">{{ $item->description }}</p>
                                        @if($item->action_link)
                                            <span class="inline-flex items-center gap-1 text-[10px] text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md mt-1 font-semibold">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                <span class="truncate max-w-[200px]">{{ $item->action_link }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Audience & Platform --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($item->audience_type === 'all_students')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                                            👥 All Students
                                        </span>
                                    @elseif($item->audience_type === 'country')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/70">
                                            🌍 {{ $item->targets->count() }} Countries
                                        </span>
                                    @elseif($item->audience_type === 'group')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                                            🏢 {{ $item->targets->count() }} Groups
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            👤 {{ $item->targets->count() }} Students
                                        </span>
                                    @endif

                                    @if(($item->target_platform ?? 'all') === 'android')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            🤖 Android
                                        </span>
                                    @elseif(($item->target_platform ?? 'all') === 'ios')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            🍎 iOS
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    By: {{ $item->creator ? $item->creator->name : 'System' }}
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php $badge = $item->status_badge; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>

                            {{-- Recipients / Delivered Counts --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="font-black text-slate-900 text-sm">
                                    {{ number_format($item->total_recipients) }}
                                </div>
                                <div class="text-[11px] mt-0.5 flex items-center justify-center gap-2">
                                    <span class="text-emerald-700 font-bold" title="Delivered">✓ {{ number_format($item->success_count) }}</span>
                                    @if($item->failed_count > 0)
                                        <span class="text-rose-600 font-bold" title="Failed">✕ {{ number_format($item->failed_count) }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Date / Schedule --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 text-[11px]">
                                @if($item->status === 'scheduled')
                                    <span class="text-amber-600 font-extrabold block">⏰ Scheduled for:</span>
                                    <span class="font-medium">{{ $item->scheduled_at ? $item->scheduled_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                @else
                                    <span class="text-slate-400 block font-medium">Dispatched:</span>
                                    <span class="font-bold text-slate-700">{{ $item->sent_at ? $item->sent_at->format('M d, Y h:i A') : $item->created_at->format('M d, Y h:i A') }}</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.push-notifications.show', $item) }}"
                                       class="p-2 text-slate-500 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition"
                                       title="View Delivery Report">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if(in_array($item->status, ['sent', 'partially_failed', 'failed']))
                                        <form method="POST" action="{{ route('admin.push-notifications.resend', $item) }}" class="inline-block">
                                            @csrf
                                            <button type="button"
                                                    onclick="confirmPushResend(this.form, '{{ addslashes($item->title) }}')"
                                                    class="p-2 text-blue-600 hover:text-blue-800 rounded-xl hover:bg-blue-50 transition cursor-pointer"
                                                    title="Resend Notification">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.push-notifications.destroy', $item) }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                onclick="confirmPushDelete(this.form, '{{ addslashes($item->title) }}')"
                                                class="p-2 text-rose-500 hover:text-rose-700 rounded-xl hover:bg-rose-50 transition cursor-pointer"
                                                title="Delete Notification">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-extrabold text-slate-900">No push notifications found</h3>
                                    <p class="text-xs text-slate-500 mt-1">Start by creating your first push notification broadcast to reach your audience.</p>
                                    <a href="{{ route('admin.push-notifications.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow-xs transition">
                                        <span>Create First Notification</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
