@props([
    'title' => 'Community Guidelines | REIAC',
    'active' => 'guidelines',
    'user' => auth()->user(),
    'guidelines' => collect(),
    'categories' => collect(),
    'tags' => collect(),
    'trendingTopics' => collect(),
    'topContributors' => [],
    'notificationsCount' => 0,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false, searchRule: '' }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    {{-- Prevent Alpine-controlled elements from flashing before Alpine initializes --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">

    {{-- TOPBAR --}}
    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden flex" style="display: none;">
        <div @click="mobileMenuOpen = false" x-show="mobileMenuOpen" x-transition.opacity
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">

            @auth
                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ $user?->profile?->avatar
                        ? asset('storage/' . $user->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff' }}"
                        alt="{{ $user?->name ?? 'User' }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $user?->name }}</div>
                        <a href="{{ route('community.profile.me') }}" class="text-xs text-amber-600 font-semibold hover:underline">
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a href="{{ route('community.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6"></path>
                    </svg>
                    Community Home
                </a>
                <a href="{{ route('community.guidelines') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Guidelines
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN 3-COLUMN LAYOUT --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        {{-- LEFT SIDEBAR --}}
        <x-community.sidebar :top-contributors="$topContributors" :categories="$categories" :tags="$tags" />

        {{-- CENTER CONTENT --}}
        <section class="space-y-5 min-w-0">

            {{-- BREADCRUMB & QUICK BACK LINK --}}
            <div class="flex items-center justify-between text-xs">
                <nav class="flex items-center space-x-1.5 font-medium text-slate-500">
                    <a href="{{ route('community.index') }}" class="hover:text-slate-900 transition">Community</a>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">Guidelines</span>
                </nav>

                <a href="{{ route('community.index') }}"
                   class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-900 font-semibold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Feed</span>
                </a>
            </div>

            {{-- HERO HEADER CARD (HIGH CONTRAST, CLEAN & FULLY VISIBLE) --}}
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold tracking-wider uppercase">
                    <span>REIAC Community Standards</span>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Community Guidelines & Rules
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                        Welcome to REIAC Community! Our goal is to maintain a safe, respectful, and empowering space for students, consultants, and professionals. Please follow these principles in all discussions, comments, and group interactions.
                    </p>
                </div>

                {{-- SEARCH RULES BAR --}}
                <div class="pt-1 max-w-md">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text"
                               x-model="searchRule"
                               placeholder="Search rules (e.g. spam, respect, category)..."
                               class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition shadow-inner">
                        <button type="button" x-show="searchRule" @click="searchRule = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 text-xs">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3 CORE PILLARS (CLEAN NUMBERED TILES - NO ICONS) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                        01
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Mutual Respect</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Treat every member with courtesy and professional dignity.</p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                        02
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Authentic Knowledge</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Share factual, verified insights and genuine advice.</p>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                        03
                    </div>
                    <div>
                        <h2 class="text-xs font-bold text-slate-900">Zero Spam</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">No unauthorized promotion, scam links, or advertising bots.</p>
                    </div>
                </div>
            </div>

            {{-- OFFICIAL GUIDELINES LIST (CLEAN TYPOGRAPHY - NO ICONS) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>Official Guidelines</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold">
                            {{ $guidelines->count() }}
                        </span>
                    </h2>
                    <span class="text-[11px] text-slate-400">Maintained by REIAC Moderation</span>
                </div>

                <div class="space-y-3">
                    @forelse($guidelines as $index => $guideline)
                        @php
                            $searchKey = strtolower($guideline->title . ' ' . $guideline->description);
                        @endphp
                        <div x-show="!searchRule || '{{ addslashes($searchKey) }}'.includes(searchRule.toLowerCase())"
                             class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-amber-300 hover:shadow-sm transition-all duration-200">
                            <div class="flex items-start gap-3.5">
                                {{-- Numeric Rule Badge --}}
                                <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900">
                                            {{ $guideline->title }}
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-bold text-[10px] uppercase tracking-wider">
                                            Rule {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>

                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                        {{ $guideline->description ?: 'Adhere to this rule to maintain a productive and welcoming community.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-10 text-center border border-slate-200/80">
                            <p class="text-xs font-bold text-slate-700">No guidelines found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- DOs & DON'Ts COMPARISON (CLEAN TEXTUAL COMPARISON - NO EMOJIS) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- DOs --}}
                <div class="bg-white rounded-2xl p-5 border border-emerald-200/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-[11px] text-emerald-700 font-black">✓</span>
                        <span>What We Encourage (Do's)</span>
                    </div>

                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold shrink-0">✓</span>
                            <span>Ask thoughtful questions with clear and relevant context.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold shrink-0">✓</span>
                            <span>Share authentic experiences to guide fellow applicants.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold shrink-0">✓</span>
                            <span>Give constructive, respectful, and encouraging feedback.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold shrink-0">✓</span>
                            <span>Mark correct solutions and upvote genuinely helpful replies.</span>
                        </li>
                    </ul>
                </div>

                {{-- DONTs --}}
                <div class="bg-white rounded-2xl p-5 border border-rose-200/70 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 text-rose-800 font-bold text-xs uppercase tracking-wider">
                        <span class="w-5 h-5 rounded-full bg-rose-100 flex items-center justify-center text-[11px] text-rose-700 font-black">✕</span>
                        <span>What We Disallow (Don'ts)</span>
                    </div>

                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="text-rose-600 font-bold shrink-0">✕</span>
                            <span>Hate speech, racism, personal abuse, or harassment.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-rose-600 font-bold shrink-0">✕</span>
                            <span>Unsolicited promotional links, affiliate spam, or advertising.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-rose-600 font-bold shrink-0">✕</span>
                            <span>Leaking private phone numbers, emails, or personal details.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-rose-600 font-bold shrink-0">✕</span>
                            <span>Misleading immigration, visa, or academic claims.</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- REPORTING SECTION (CLEAN 3-STEP GUIDE) --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">How to Report a Violation</h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">The moderation team reviews all reported posts & comments promptly.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-extrabold text-slate-900 block mb-1">Step 1: Click Options</span>
                        <span class="text-slate-600 text-[11px] leading-relaxed">Click the three dots (···) on the top right corner of any post or comment.</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-extrabold text-slate-900 block mb-1">Step 2: Select Report</span>
                        <span class="text-slate-600 text-[11px] leading-relaxed">Choose the violation reason such as Spam, Harassment, or Inappropriate.</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="font-extrabold text-slate-900 block mb-1">Step 3: Admin Review</span>
                        <span class="text-slate-600 text-[11px] leading-relaxed">Moderators evaluate the report and take down content or ban violators.</span>
                    </div>
                </div>
            </div>

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar :user="$user" :trending-topics="$trendingTopics" />

    </main>

</body>
</html>
