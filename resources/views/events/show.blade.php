<!DOCTYPE html>
@php
    $currentUser = $currentUser ?? auth()->user();
    $currentUserScore = $currentUserScore ?? ($currentUser ? $event->getUserScore($currentUser) : null);
    $notificationsCount = $notificationsCount ?? ($currentUser ? $currentUser->unreadNotifications()->count() : 0);
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} — Community Contest & Leaderboard | REIAC</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        [x-cloak] {
            display: none !important;
        }

        /* Responsive 2-Column Grid that never fails */
        .event-container {
            max-width: 1160px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .event-layout-grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .event-layout-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 360px;
                gap: 1.5rem;
                align-items: start;
            }
            .event-sidebar-sticky {
                position: sticky;
                top: 5rem;
            }
        }

        /* Compact & Professional Banner */
        .event-hero-banner {
            position: relative;
            width: 100%;
            height: 180px;
            max-height: 200px;
            overflow: hidden;
            background-color: #0f172a;
        }
        @media (max-width: 640px) {
            .event-hero-banner {
                height: 130px;
            }
        }
        .event-hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>
</head>
<body
    class="min-h-screen bg-[#f3f4f6] text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-16"
    x-data="eventPage({
        endAt: '{{ $event->end_at->toIso8601String() }}',
        startAt: '{{ $event->start_at->toIso8601String() }}',
        eventUrl: '{{ url('/community/events/' . $event->slug) }}'
    })"
>

    {{-- TOAST NOTIFICATION --}}
    <div
        x-show="toast.show"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="fixed bottom-6 right-6 z-50 bg-slate-950 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-amber-500/30 text-xs font-bold backdrop-blur-md"
    >
        <span class="w-6 h-6 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-xs shadow-xs">✓</span>
        <span x-text="toast.message"></span>
    </div>

    {{-- REIAC COMMUNITY TOPBAR --}}
    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden flex" style="display: none;">
        <div @click="mobileMenuOpen = false" x-show="mobileMenuOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">

            @auth
                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ $currentUser?->profile?->avatar
                        ? asset('storage/' . $currentUser->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser?->name ?? 'User') . '&background=0c1b33&color=fff' }}"
                        alt="{{ $currentUser?->name ?? 'User' }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $currentUser?->name }}</div>
                        <a href="{{ route('community.profile.me') }}" class="text-xs text-amber-600 font-semibold hover:underline">View Profile</a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a href="{{ route('community.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-900 font-medium">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6"/>
                    </svg>
                    Community Home
                </a>
                <a href="{{ url('/community/events/' . $event->slug) }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-amber-50 text-amber-900 font-bold">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0V5.625a2.25 2.25 0 00-2.25-2.25h-1.5a2.25 2.25 0 00-2.25 2.25v8.625"/>
                    </svg>
                    Current Contest
                </a>
            </nav>
        </div>
    </div>

    {{-- SUBHEADER BREADCRUMB & CONTROLS --}}
    <div class="bg-white border-b border-slate-200/80 shadow-2xs">
        <div class="event-container py-2.5 sm:py-3 flex flex-wrap items-center justify-between gap-3">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs text-slate-500 min-w-0">
                <a href="{{ route('community.index') }}" class="inline-flex items-center gap-1 text-slate-700 hover:text-amber-600 font-bold transition">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Community</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-slate-400 hidden sm:inline">Events</span>
                <span class="text-slate-300 hidden sm:inline">/</span>
                <span class="font-bold text-slate-900 truncate max-w-[200px] sm:max-w-xs">{{ $event->title }}</span>
            </div>

            {{-- Live Status + Share --}}
            <div class="flex items-center gap-2 shrink-0">
                @if($event->isRunning())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Contest</span>
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                        {{ ucfirst($event->status) }}
                    </span>
                @endif

                <button
                    type="button"
                    @click="shareEvent()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition active:scale-95 border border-slate-200/70"
                >
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                    </svg>
                    <span>Share</span>
                </button>
            </div>

        </div>
    </div>

    {{-- MAIN CONTENT AREA --}}
    <main class="event-container pt-5 sm:pt-6">
        <div class="event-layout-grid">

            {{-- ========================================================
                 LEFT COLUMN: HERO CARD + LEADERBOARD + RULES
            ========================================================= --}}
            <div class="space-y-5 min-w-0">

                {{-- 1. HERO BANNER CARD (COMPACT, PROFESSIONAL HEIGHT) --}}
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                    
                    {{-- Compact Cover Banner --}}
                    @if($event->banner_image_url)
                        <div class="event-hero-banner">
                            <img
                                src="{{ $event->banner_image_url }}"
                                alt="{{ $event->title }}"
                                loading="eager"
                            >
                            {{-- Soft bottom gradient overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-black/10"></div>
                            
                            {{-- Floating Live Tag on Banner --}}
                            <div class="absolute bottom-3 left-3 sm:left-4 z-10 flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow-sm">
                                    Official Contest
                                </span>
                                <span class="text-[11px] font-semibold text-white/90 drop-shadow-sm flex items-center gap-1">
                                    <span>🗓️</span>
                                    <span>{{ $event->start_at->format('d M') }} — {{ $event->end_at->format('d M, Y') }}</span>
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Header Body: Title + Countdown --}}
                    <div class="p-4 sm:p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            
                            {{-- Left: Event Title & Description --}}
                            <div class="space-y-1 min-w-0">
                                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight leading-tight">
                                    {{ $event->title }}
                                </h1>
                                <p class="text-xs text-slate-500 font-medium">
                                    Earn points by referring friends, posting insightful doubts, commenting, and liking.
                                </p>
                            </div>

                            {{-- Right: Compact Digital Countdown Box --}}
                            <div class="bg-[#0B132B] text-white px-3.5 py-2.5 rounded-xl shrink-0 border border-amber-500/20 shadow-sm self-start sm:self-auto">
                                <div class="text-[9px] uppercase font-bold tracking-wider text-amber-400 mb-1.5 flex items-center justify-between gap-3">
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Time Remaining</span>
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-mono" x-show="countdown.ended">Ended</span>
                                </div>

                                <div class="flex items-center gap-1.5 text-center">
                                    {{-- Days --}}
                                    <div class="bg-white/10 px-2 py-1 rounded-lg min-w-[38px]">
                                        <div class="text-sm font-black text-amber-400 font-mono leading-none" x-text="countdown.days">00</div>
                                        <div class="text-[8px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Days</div>
                                    </div>
                                    <span class="text-amber-400 font-bold text-xs">:</span>
                                    {{-- Hours --}}
                                    <div class="bg-white/10 px-2 py-1 rounded-lg min-w-[38px]">
                                        <div class="text-sm font-black text-white font-mono leading-none" x-text="countdown.hours">00</div>
                                        <div class="text-[8px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Hours</div>
                                    </div>
                                    <span class="text-white/40 font-bold text-xs">:</span>
                                    {{-- Mins --}}
                                    <div class="bg-white/10 px-2 py-1 rounded-lg min-w-[38px]">
                                        <div class="text-sm font-black text-white font-mono leading-none" x-text="countdown.minutes">00</div>
                                        <div class="text-[8px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Mins</div>
                                    </div>
                                    <span class="text-white/40 font-bold text-xs">:</span>
                                    {{-- Secs --}}
                                    <div class="bg-white/10 px-2 py-1 rounded-lg min-w-[38px]">
                                        <div class="text-sm font-black text-amber-400 font-mono leading-none" x-text="countdown.seconds">00</div>
                                        <div class="text-[8px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Secs</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Quick scoring highlights bar --}}
                        <div class="mt-3.5 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] font-bold">
                            <span class="text-slate-400 text-[10px] font-medium uppercase tracking-wider mr-1">Earn:</span>
                            <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">👥 +{{ $event->referrals_weight }} / Referral</span>
                            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-800 border border-blue-200">📝 +{{ $event->posts_weight }} / Post</span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">💬 +{{ $event->comments_weight }} / Comment</span>
                            <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-800 border border-rose-200">❤️ +{{ $event->likes_weight }} / Like</span>
                        </div>
                    </div>

                </div>

                {{-- MOBILE-ONLY CONTEST DASHBOARD (Shown on small screens above leaderboard) --}}
                <div class="lg:hidden">
                    @auth
                        <div class="bg-[#0B132B] rounded-2xl p-4 text-white border border-amber-500/30 shadow-md space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 font-black text-sm flex items-center justify-center overflow-hidden shadow-xs">
                                        @if($currentUser?->profile?->avatar)
                                            <img src="{{ asset('storage/' . $currentUser->profile->avatar) }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-white truncate max-w-[140px]">{{ $currentUser->name }}</div>
                                        <div class="text-[10px] text-amber-300 font-medium">Participant</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[9px] text-slate-400 uppercase tracking-wider">Your Score</div>
                                    <div class="text-base font-black text-amber-400 font-mono">
                                        {{ number_format($currentUserScore['score'] ?? 0) }} <span class="text-[10px] font-sans">pts</span>
                                        @if(!empty($currentUserScore['rank']))
                                            <span class="text-[10px] text-emerald-400 ml-1 font-bold">(Rank #{{ $currentUserScore['rank'] }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Referral Box Mobile --}}
                            <div class="p-2.5 bg-white/5 rounded-xl border border-white/10 flex items-center gap-2">
                                <div class="flex-1 bg-black/40 border border-white/10 rounded-lg px-2.5 py-1.5 text-center text-xs font-mono font-bold text-amber-300 tracking-wider">
                                    {{ $currentUser->referral_code ?? 'REIAC' }}
                                </div>
                                <button
                                    type="button"
                                    @click="copyText('{{ $currentUser->referral_code }}', 'Referral code copied!')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-black transition active:scale-95 shadow-xs"
                                >
                                    Copy
                                </button>
                                <button
                                    type="button"
                                    @click="shareReferral('{{ $currentUser->referral_code }}')"
                                    class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs transition"
                                    title="Share Link"
                                >
                                    ↗
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-4 text-white shadow-xs flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-xs font-bold">Participate in this Contest</h3>
                                <p class="text-[11px] text-amber-100">Sign up to get your referral code and win!</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg bg-white text-slate-900 font-bold text-xs shadow-xs">Login</a>
                                <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-lg bg-slate-950 text-white font-bold text-xs shadow-xs">Register</a>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- 2. LEADERBOARD STANDINGS CARD --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-4">
                    
                    {{-- Leaderboard Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm shadow-xs">
                                🏆
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Leaderboard Standings</h2>
                                <p class="text-[11px] text-slate-500">Live rankings updated automatically</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Top 10</span>
                        </span>
                    </div>

                    @if(count($topUsers) > 0)

                        {{-- PODIUM FOR TOP 3 --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 pt-1">
                            @foreach($topUsers->take(3) as $podiumIndex => $pUser)
                                @php
                                    $isFirst = $podiumIndex === 0;
                                    $isSecond = $podiumIndex === 1;
                                    $isThird = $podiumIndex === 2;
                                @endphp
                                <div class="relative rounded-xl p-3 text-center border transition {{ $isFirst ? 'bg-gradient-to-b from-amber-50 to-white border-amber-300 shadow-xs sm:-translate-y-1 col-span-2 sm:col-span-1 order-1 sm:order-2' : ($isSecond ? 'bg-slate-50/70 border-slate-200 order-2 sm:order-1' : 'bg-slate-50/70 border-slate-200 order-3') }}">
                                    
                                    <div class="text-xs font-black mb-1">
                                        @if($isFirst)
                                            <span class="inline-flex items-center gap-1 text-amber-600 font-extrabold text-[11px]">👑 #1 Gold</span>
                                        @elseif($isSecond)
                                            <span class="text-slate-600 text-[11px]">🥈 #2 Silver</span>
                                        @else
                                            <span class="text-amber-800 text-[11px]">🥉 #3 Bronze</span>
                                        @endif
                                    </div>

                                    <div class="w-11 h-11 rounded-full mx-auto overflow-hidden bg-white border-2 {{ $isFirst ? 'border-amber-400 ring-2 ring-amber-300/40' : 'border-slate-200' }} shadow-xs">
                                        @if(!empty($pUser->avatar))
                                            <img src="{{ $pUser->avatar }}" alt="{{ $pUser->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-[#0c1b33] text-amber-400 font-bold text-xs">
                                                {{ strtoupper(substr($pUser->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <h3 class="font-bold text-slate-900 text-xs mt-2 truncate">{{ $pUser->name }}</h3>
                                    
                                    @if(!empty($pUser->referral_code))
                                        <div class="text-[9px] text-slate-400 font-mono">{{ $pUser->referral_code }}</div>
                                    @endif

                                    <div class="mt-1.5 inline-block px-2.5 py-0.5 rounded-lg bg-slate-900 text-amber-400 font-mono font-black text-xs shadow-2xs">
                                        {{ number_format($pUser->total_score ?? $pUser->event_score ?? 0) }} pts
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- DETAILED PARTICIPANTS TABLE --}}
                        <div class="overflow-x-auto rounded-xl border border-slate-100 pt-1">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
                                        <th class="py-2.5 px-3">Rank</th>
                                        <th class="py-2.5 px-3">Participant</th>
                                        <th class="py-2.5 px-3 text-center">Referrals</th>
                                        <th class="py-2.5 px-3 text-center">Posts</th>
                                        <th class="py-2.5 px-3 text-center">Comments</th>
                                        <th class="py-2.5 px-3 text-right">Score</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($topUsers as $rIdx => $u)
                                        <tr class="hover:bg-amber-50/15 transition {{ $rIdx < 3 ? 'bg-amber-50/5' : '' }}">
                                            <td class="py-2.5 px-3 whitespace-nowrap">
                                                <span class="inline-flex w-6 h-6 rounded-lg items-center justify-center font-bold text-xs {{ $rIdx === 0 ? 'bg-amber-400 text-slate-950' : ($rIdx === 1 ? 'bg-slate-200 text-slate-700' : ($rIdx === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                                    #{{ $rIdx + 1 }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full overflow-hidden bg-slate-100 shrink-0 border border-slate-200">
                                                        @if(!empty($u->avatar))
                                                            <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center bg-[#0c1b33] text-amber-400 font-bold text-[10px]">
                                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-bold text-slate-900 text-xs truncate max-w-[120px] sm:max-w-xs">{{ $u->name }}</div>
                                                        @if(!empty($u->referral_code))
                                                            <div class="text-[9px] text-slate-400 font-mono">{{ $u->referral_code }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-center font-bold text-amber-700 whitespace-nowrap">
                                                {{ number_format($u->referrals_count ?? 0) }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center text-slate-700 whitespace-nowrap">
                                                {{ number_format($u->posts_count ?? 0) }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center text-slate-600 whitespace-nowrap">
                                                {{ number_format($u->comments_count ?? 0) }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-mono font-black text-amber-600 text-xs whitespace-nowrap">
                                                {{ number_format($u->total_score ?? $u->event_score ?? 0) }} pts
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    @else

                        {{-- EMPTY STATE --}}
                        <div class="py-8 px-4 rounded-xl bg-slate-50/70 border border-dashed border-slate-200 text-center space-y-2">
                            <div class="text-3xl">🚀</div>
                            <h3 class="text-xs font-bold text-slate-800">The Contest Has Officially Launched!</h3>
                            <p class="text-[11px] text-slate-400 max-w-sm mx-auto">
                                Be the first to share your referral code or create posts to claim the #1 spot on the leaderboard!
                            </p>
                        </div>

                    @endif

                </div>

                {{-- 3. RULES & REGULATIONS CARD --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3.5">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm shadow-xs">
                            📜
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Contest Rules & Guidelines</h2>
                            <p class="text-[11px] text-slate-500">Official conditions for participation</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        @if(!empty($event->rules))
                            @php
                                $rulesArray = array_filter(array_map('trim', explode("\n", $event->rules)));
                            @endphp

                            <div class="space-y-2">
                                @foreach($rulesArray as $rIdx => $ruleText)
                                    <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50/80 border border-slate-100">
                                        <span class="w-5 h-5 rounded-md bg-amber-100 text-amber-900 font-mono font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $rIdx + 1 }}
                                        </span>
                                        <div class="text-xs font-medium text-slate-700 leading-relaxed flex-1">
                                            {{ preg_replace('/^\d+[\.\)]\s*/', '', $ruleText) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">No specific rules text provided for this contest.</p>
                        @endif
                    </div>

                    {{-- Fair Play Shield --}}
                    <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200/70 flex items-start gap-2 text-xs text-amber-950">
                        <span class="text-sm shrink-0">🛡️</span>
                        <div class="text-[11px] leading-relaxed">
                            <strong class="font-bold">Fair Play Policy:</strong> Only genuine community activity during the contest duration counts. Spamming or duplicate accounts result in immediate disqualification.
                        </div>
                    </div>
                </div>

            </div>

            {{-- ========================================================
                 RIGHT COLUMN (SIDEBAR): USER CARD + SCORING MATRIX
            ========================================================= --}}
            <div class="event-sidebar-sticky space-y-5">

                {{-- 1. USER CONTEST DASHBOARD (DESKTOP) --}}
                <div class="hidden lg:block">
                    @auth
                        <div class="bg-[#0B132B] rounded-2xl p-5 text-white border border-amber-500/30 shadow-md space-y-4">
                            
                            {{-- User Info --}}
                            <div class="flex items-center gap-3 pb-3 border-b border-white/10">
                                <div class="w-11 h-11 rounded-xl bg-amber-400 text-slate-950 font-black text-sm flex items-center justify-center overflow-hidden shadow-xs">
                                    @if($currentUser?->profile?->avatar)
                                        <img src="{{ asset('storage/' . $currentUser->profile->avatar) }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-bold text-white truncate">{{ $currentUser->name }}</h3>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-400 bg-emerald-950/80 px-2 py-0.5 rounded border border-emerald-500/30 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Participant
                                    </span>
                                </div>
                            </div>

                            {{-- Score Metric --}}
                            <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-white/5 border border-white/10 text-center">
                                <div>
                                    <div class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Your Score</div>
                                    <div class="text-lg font-black text-amber-400 font-mono mt-0.5">
                                        {{ number_format($currentUserScore['score'] ?? 0) }} <span class="text-[10px] font-sans">pts</span>
                                    </div>
                                </div>
                                <div class="border-l border-white/10">
                                    <div class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">Your Rank</div>
                                    <div class="text-lg font-black text-white font-mono mt-0.5">
                                        {{ !empty($currentUserScore['rank']) ? '#' . $currentUserScore['rank'] : 'Unranked' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Referral Box --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-300">Your Referral Code:</span>
                                    <span class="text-amber-400 font-mono font-bold">+{{ $event->referrals_weight }} pts</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-black/50 border border-white/20 rounded-xl px-3 py-2 text-center text-sm font-mono font-bold text-amber-300 tracking-wider shadow-inner">
                                        {{ $currentUser->referral_code ?? 'REIAC' }}
                                    </div>
                                    <button
                                        type="button"
                                        @click="copyText('{{ $currentUser->referral_code }}', 'Referral code copied!')"
                                        class="px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-black transition active:scale-95 shadow-xs"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="space-y-2 pt-1">
                                <button
                                    type="button"
                                    @click="shareReferral('{{ $currentUser->referral_code }}')"
                                    class="w-full py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition flex items-center justify-center gap-1.5"
                                >
                                    <span>Share Invite Link</span>
                                    <span>↗</span>
                                </button>
                                <a
                                    href="{{ route('community.index') }}"
                                    class="w-full py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs"
                                >
                                    <span>Post in Community</span>
                                    <span>→</span>
                                </a>
                            </div>

                        </div>
                    @else
                        <div class="bg-[#0B132B] rounded-2xl p-5 text-white border border-amber-500/30 shadow-md space-y-3 text-center">
                            <div class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-xl mx-auto shadow-xs">
                                🏆
                            </div>
                            <div>
                                <h3 class="text-xs font-bold">Join this Contest!</h3>
                                <p class="text-[11px] text-slate-300 mt-0.5">
                                    Sign up or log in to get your referral code and win badges.
                                </p>
                            </div>
                            <div class="space-y-1.5 pt-1">
                                <a href="{{ route('register') }}" class="block w-full py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs shadow-xs transition">
                                    Register Free
                                </a>
                                <a href="{{ route('login') }}" class="block w-full py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition">
                                    Login
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

                {{-- 2. POINTS BREAKDOWN MATRIX --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Points Breakdown
                        </h3>
                        <span class="text-[10px] font-bold text-slate-500">Automatic</span>
                    </div>

                    <div class="space-y-2">
                        {{-- Referral --}}
                        <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center text-xs">👥</div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Friend Referral</div>
                                    <div class="text-[10px] text-slate-500">Per verified signup</div>
                                </div>
                            </div>
                            <div class="text-xs font-black text-amber-700 font-mono">+{{ $event->referrals_weight }} pts</div>
                        </div>

                        {{-- Post --}}
                        <div class="p-2.5 rounded-xl bg-blue-50/80 border border-blue-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center text-xs">📝</div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Community Post</div>
                                    <div class="text-[10px] text-slate-500">Approved discussion</div>
                                </div>
                            </div>
                            <div class="text-xs font-black text-blue-700 font-mono">+{{ $event->posts_weight }} pts</div>
                        </div>

                        {{-- Comment --}}
                        <div class="p-2.5 rounded-xl bg-emerald-50/80 border border-emerald-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">💬</div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Helpful Comment</div>
                                    <div class="text-[10px] text-slate-500">Constructive reply</div>
                                </div>
                            </div>
                            <div class="text-xs font-black text-emerald-700 font-mono">+{{ $event->comments_weight }} pts</div>
                        </div>

                        {{-- Like --}}
                        <div class="p-2.5 rounded-xl bg-rose-50/80 border border-rose-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center text-xs">❤️</div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Like Received</div>
                                    <div class="text-[10px] text-slate-500">Genuine like on posts</div>
                                </div>
                            </div>
                            <div class="text-xs font-black text-rose-700 font-mono">+{{ $event->likes_weight }} pts</div>
                        </div>
                    </div>
                </div>

                {{-- 3. CONTEST INFORMATION --}}
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs space-y-2.5">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                        Contest Information
                    </h3>

                    <dl class="divide-y divide-slate-100 text-xs">
                        <div class="py-1.5 flex items-center justify-between">
                            <dt class="text-slate-500">Status</dt>
                            <dd class="font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </dd>
                        </div>
                        <div class="py-1.5 flex items-center justify-between">
                            <dt class="text-slate-500">Duration</dt>
                            <dd class="font-bold text-slate-800">{{ $event->start_at->diffInDays($event->end_at) }} Days</dd>
                        </div>
                        <div class="py-1.5 flex items-center justify-between">
                            <dt class="text-slate-500">Start Date</dt>
                            <dd class="font-medium text-slate-700">{{ $event->start_at->format('d M Y') }}</dd>
                        </div>
                        <div class="py-1.5 flex items-center justify-between">
                            <dt class="text-slate-500">End Date</dt>
                            <dd class="font-medium text-slate-700">{{ $event->end_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

            </div>

        </div>
    </main>

    {{-- SCRIPTS --}}
    <script>
        function eventPage(config) {
            return {
                toast: {
                    show: false,
                    message: ''
                },
                countdown: {
                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                    ended: false
                },

                init() {
                    this.updateCountdown();
                    setInterval(() => {
                        this.updateCountdown();
                    }, 1000);
                },

                updateCountdown() {
                    const target = new Date(config.endAt).getTime();
                    const now = new Date().getTime();
                    const diff = target - now;

                    if (diff <= 0) {
                        this.countdown = { days: '00', hours: '00', minutes: '00', seconds: '00', ended: true };
                        return;
                    }

                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    this.countdown = {
                        days: String(d).padStart(2, '0'),
                        hours: String(h).padStart(2, '0'),
                        minutes: String(m).padStart(2, '0'),
                        seconds: String(s).padStart(2, '0'),
                        ended: false
                    };
                },

                copyText(text, message) {
                    if (!text) return;
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(text).then(() => {
                            this.showToast(message || 'Copied!');
                        }).catch(() => {
                            this.fallbackCopy(text, message);
                        });
                    } else {
                        this.fallbackCopy(text, message);
                    }
                },

                fallbackCopy(text, message) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        this.showToast(message || 'Copied!');
                    } catch (err) {
                        this.showToast('Unable to copy');
                    }
                    document.body.removeChild(ta);
                },

                shareEvent() {
                    if (navigator.share) {
                        navigator.share({
                            title: '{{ addslashes($event->title) }}',
                            text: 'Join the {{ addslashes($event->title) }} on REIAC Community!',
                            url: config.eventUrl
                        }).catch(() => {});
                    } else {
                        this.copyText(config.eventUrl, 'Event link copied to clipboard!');
                    }
                },

                shareReferral(code) {
                    const registerUrl = '{{ url('/community/register') }}?ref=' + encodeURIComponent(code);
                    if (navigator.share) {
                        navigator.share({
                            title: 'Join REIAC Community',
                            text: 'Join REIAC Community using my referral code ' + code + ' and participate in {{ addslashes($event->title) }}!',
                            url: registerUrl
                        }).catch(() => {});
                    } else {
                        this.copyText(registerUrl, 'Referral link copied to clipboard!');
                    }
                },

                showToast(msg) {
                    this.toast.message = msg;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                }
            };
        }
    </script>
</body>
</html>
