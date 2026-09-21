@extends('layouts.admin')

@section('title', 'Leaderboard - ' . $event->title)

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">

    {{-- TOP NAVIGATION / HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center gap-1">
                    ← Back to Contests
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">Live Rankings</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $event->title }} - Standings</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Timeline: {{ $event->start_at->format('M d, Y h:i A') }} to {{ $event->end_at->format('M d, Y h:i A') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('events.show', $event->slug) }}"
                target="_blank"
                class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-xs transition flex items-center gap-1.5"
            >
                <span>Preview Public Page</span>
                <span>↗</span>
            </a>
        </div>
    </div>

    {{-- SCORING WEIGHTS BAR --}}
    <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Formula:</span>
            <span class="font-mono bg-slate-100 text-slate-700 px-2 py-1 rounded-lg">
                ({{ $event->referrals_weight }} × Referrals) + ({{ $event->posts_weight }} × Posts) + ({{ $event->comments_weight }} × Comments) + ({{ $event->likes_weight }} × Likes)
            </span>
        </div>
        <div>
            @if($event->isRunning())
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Event
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    {{ ucfirst($event->status) }}
                </span>
            @endif
        </div>
    </div>

    {{-- LEADERBOARD TABLE CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Top Participants (Top 50)</h2>
            <span class="text-xs text-slate-400">Calculated in real-time within event date window</span>
        </div>

        @if(count($topUsers) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-400 font-extrabold uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3.5 px-4 text-center">Rank</th>
                            <th class="py-3.5 px-4">User Details</th>
                            <th class="py-3.5 px-4 text-center">Referral Code</th>
                            <th class="py-3.5 px-4 text-center">Referrals (×{{ $event->referrals_weight }})</th>
                            <th class="py-3.5 px-4 text-center">Posts (×{{ $event->posts_weight }})</th>
                            <th class="py-3.5 px-4 text-center">Comments (×{{ $event->comments_weight }})</th>
                            <th class="py-3.5 px-4 text-center">Likes (×{{ $event->likes_weight }})</th>
                            <th class="py-3.5 px-4 text-right">Total Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topUsers as $index => $u)
                            <tr class="hover:bg-slate-50/60 transition {{ $index < 5 ? 'bg-amber-50/20' : '' }}">
                                {{-- Rank --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if($index === 0)
                                        <span class="inline-flex w-7 h-7 rounded-xl bg-amber-400 text-slate-950 font-black text-xs items-center justify-center shadow-xs">🥇</span>
                                    @elseif($index === 1)
                                        <span class="inline-flex w-7 h-7 rounded-xl bg-slate-300 text-slate-900 font-black text-xs items-center justify-center shadow-xs">🥈</span>
                                    @elseif($index === 2)
                                        <span class="inline-flex w-7 h-7 rounded-xl bg-amber-700 text-white font-black text-xs items-center justify-center shadow-xs">🥉</span>
                                    @else
                                        <span class="inline-flex w-7 h-7 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs items-center justify-center">#{{ $index + 1 }}</span>
                                    @endif
                                </td>

                                {{-- User --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                                            @if($u->avatar)
                                                <img src="{{ str_starts_with($u->avatar, 'http') ? $u->avatar : asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-amber-500 text-white font-bold text-xs">
                                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 text-sm truncate">{{ $u->name }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Referral Code --}}
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded">
                                        {{ $u->referral_code ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- Breakdown --}}
                                <td class="py-3.5 px-4 text-center font-bold text-amber-700">
                                    {{ number_format($u->referrals_count ?? 0) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-medium text-blue-700">
                                    {{ number_format($u->posts_count ?? 0) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-medium text-emerald-700">
                                    {{ number_format($u->comments_count ?? 0) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-medium text-rose-600">
                                    {{ number_format($u->likes_count ?? 0) }}
                                </td>

                                {{-- Total Score --}}
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex px-3 py-1 rounded-lg bg-amber-100 text-amber-900 font-black text-sm">
                                        {{ number_format($u->total_score ?? 0) }} pts
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-16 text-center text-slate-400">
                <div class="text-4xl mb-2">🏁</div>
                <div class="text-base font-bold text-slate-700">No activity recorded for this event yet.</div>
                <p class="text-xs text-slate-400 mt-1">Scores will be recorded as soon as members post, comment, like, or invite others during the event window.</p>
            </div>
        @endif
    </div>

</div>
@endsection
