@props([
    'title' => 'Privacy Policy | REIAC Community',
    'active' => 'privacy',
    'user' => auth()->user(),
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

    <style>
        [x-cloak] {
            display: none !important;
        }
        html {
            scroll-behavior: smooth;
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
                <a href="{{ route('community.guidelines') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Guidelines
                </a>
                <a href="{{ route('community.privacy') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Privacy Policy
                </a>
                <a href="{{ route('community.terms') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Terms & Conditions
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

            {{-- BREADCRUMBS & TOP CONTROLS --}}
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <nav class="flex items-center gap-2 text-slate-500 font-medium">
                    <a href="{{ route('community.index') }}" class="hover:text-slate-900 transition">Community</a>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">Privacy Policy</span>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('community.terms') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/70 px-3 py-1.5 rounded-lg font-bold transition">
                        <span>Terms & Conditions</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('community.index') }}"
                       class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-900 font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to Feed</span>
                    </a>
                </div>
            </div>

            {{-- HERO HEADER CARD --}}
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold tracking-wider uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Official Privacy Policy</span>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        Last Updated: October 01, 2026
                    </span>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Privacy Policy for REIAC Community
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                        Welcome to <strong class="text-slate-900">REIAC Community</strong> ("we," "our," or "us"). We are committed to protecting your privacy and ensuring you have a safe and transparent experience using our mobile application and related services.
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                        This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you use our mobile application, REIAC Community (the "App") and community platform.
                    </p>
                    <div class="p-3 bg-amber-50/80 border-l-4 border-amber-500 rounded-r-xl text-xs text-amber-900 font-medium">
                        Please read this Privacy Policy carefully. By creating an account or using the App, you agree to the collection and use of information in accordance with this policy.
                    </div>
                </div>

                {{-- SEARCH CLAUSE BAR --}}
                <div class="pt-2 max-w-md">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text"
                               x-model="searchRule"
                               placeholder="Search privacy clauses (e.g. collect, photos, cbt, delete, contact)..."
                               class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition shadow-inner">
                        <button type="button" x-show="searchRule" @click="searchRule = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 text-xs">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            {{-- PRIVACY SECTIONS --}}
            <div class="space-y-4">

                {{-- SECTION 1: INFORMATION WE COLLECT --}}
                <div x-show="!searchRule || 'information collect account registration profile user-generated content cbt exam device otp'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            01
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">1. Information We Collect</h2>
                            <p class="text-xs text-slate-500 mt-0.5">We only collect information that is necessary to provide, maintain, and improve the features of REIAC Community:</p>
                        </div>
                    </div>

                    <div class="space-y-4 pt-1">
                        {{-- Part A --}}
                        <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/70 space-y-2.5">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>A. Information You Provide to Us</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-600">
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">Account Registration</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">When you register, we collect your full name, email address, username, password, and optional referral code.</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">Profile Information</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">You may choose to provide additional profile details, including your profile picture (avatar), bio, and country/location.</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">User-Generated Content (UGC)</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">Content you voluntarily post on the platform, such as discussion posts, questions, comments, tags, and images attached to posts.</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">Authentication Details</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">Verification OTPs sent to your email for account verification or password resets.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Part B --}}
                        <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/70 space-y-2.5">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-800"></span>
                                <span>B. Information Collected Automatically</span>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-600">
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">Device & Log Information</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">We collect non-identifiable technical data to maintain app security and prevent abuse, such as device type, operating system version, IP address, and last active timestamp.</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-100 shadow-2xs">
                                    <span class="font-bold text-slate-900 block mb-0.5">App Activity</span>
                                    <span class="text-slate-600 text-[11px] leading-relaxed">Interaction data such as posts liked, saved bookmarks, groups joined, and CBT practice test progress.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: HOW WE USE YOUR INFORMATION --}}
                <div x-show="!searchRule || 'use purpose features cbt korean practice test safety manage'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            02
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">2. How We Use Your Information</h2>
                            <p class="text-xs text-slate-500 mt-0.5">We use the collected information for the following specific purposes:</p>
                        </div>
                    </div>

                    <ul class="space-y-2.5 text-xs text-slate-700 pt-1">
                        <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">✓</span>
                            <span>To create, verify, and manage your user account.</span>
                        </li>
                        <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">✓</span>
                            <span>To provide core community features: publishing posts, participating in groups, commenting, and following other authors.</span>
                        </li>
                        <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">✓</span>
                            <span>To enable Korean CBT exam practice tests and display test scores.</span>
                        </li>
                        <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">✓</span>
                            <span>To maintain community safety, moderate content, and prevent spam, fraud, or policy violations.</span>
                        </li>
                        <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">✓</span>
                            <span>To communicate important account updates, security notifications, and service-related messages.</span>
                        </li>
                    </ul>
                </div>

                {{-- SECTION 3: MEDIA & STORAGE ACCESS --}}
                <div x-show="!searchRule || 'media storage access photo image gallery avatar upload'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            03
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">3. Media & Storage Access</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Guidelines regarding images and photo gallery permissions.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/70 text-xs text-slate-700 space-y-2 leading-relaxed">
                        <p>
                            <strong class="text-slate-900">Photos / Image Uploads:</strong> If you choose to attach an image to a post or update your profile avatar, the App requests temporary access to your device’s photo gallery.
                        </p>
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-900 font-medium">
                            We only access the specific image you explicitly select. We do not access, scan, or store your private photo library.
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: DATA SHARING AND DISCLOSURE --}}
                <div x-show="!searchRule || 'sharing disclosure sell rent trade public visibility service providers legal'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            04
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">4. Data Sharing and Disclosure</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Our commitment to keeping your data uncompromised.</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-1">
                        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 font-bold text-xs flex items-center gap-2">
                            <span class="text-base">🚫</span>
                            <span>We do NOT sell, rent, or trade your personal information to third parties or advertisers.</span>
                        </div>

                        <p class="text-xs text-slate-600">
                            We may disclose your data only in the following limited circumstances:
                        </p>

                        <div class="space-y-2 text-xs text-slate-700">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <strong class="text-slate-900 block mb-1">Public Visibility:</strong>
                                Your public profile (name, handle, avatar, bio) and any posts or comments you publish are visible to other registered community members.
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <strong class="text-slate-900 block mb-1">Service Providers:</strong>
                                We may share necessary data with trusted cloud infrastructure providers who host our database and servers under strict confidentiality agreements.
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <strong class="text-slate-900 block mb-1">Legal Requirements:</strong>
                                We may disclose your information if required to do so by applicable law, regulation, or legal process.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: DATA SECURITY --}}
                <div x-show="!searchRule || 'security ssl https encryption authentication tokens'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            05
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">5. Data Security</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Technological safeguards protecting community members.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed pt-1">
                        We implement industry-standard security measures, including SSL/HTTPS encrypted transmission and secure authentication tokens, to protect your personal information against unauthorized access, loss, alteration, or misuse.
                    </p>
                </div>

                {{-- SECTION 6: DATA RETENTION AND ACCOUNT DELETION --}}
                <div x-show="!searchRule || 'retention deletion delete account control rights prabin.ac.kr@gmail.com'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            06
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">6. Data Retention and Account Deletion (User Rights)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">You retain full control over your personal information:</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs pt-1">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/70 space-y-1.5">
                            <span class="font-extrabold text-slate-900 block">Edit or Delete Posts</span>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                You have full control to edit or delete your own posts directly within the App at any time.
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/70 space-y-1.5">
                            <span class="font-extrabold text-slate-900 block">Account Deletion</span>
                            <p class="text-[11px] text-slate-600 leading-relaxed">
                                If you wish to permanently delete your account and all associated personal data from our servers, you can submit a deletion request by contacting us at: <a href="mailto:prabin.ac.kr@gmail.com" class="text-amber-600 font-bold hover:underline">prabin.ac.kr@gmail.com</a> (or via in-app settings). Upon receiving your request, your account and personal data will be permanently removed within 30 days.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- SECTION 7: CHILDREN'S PRIVACY --}}
                <div x-show="!searchRule || 'children privacy minor age 13'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            07
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">7. Children's Privacy</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Policies regarding minors and age limits.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed pt-1">
                        REIAC Community is not directed to children under the age of 13. We do not knowingly collect personal identifiable information from children under 13. If we become aware that a child under 13 has provided personal data, we will immediately take steps to delete such data from our servers.
                    </p>
                </div>

                {{-- SECTION 8: CHANGES TO THIS PRIVACY POLICY --}}
                <div x-show="!searchRule || 'changes updates policy periodic'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            08
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">8. Changes to This Privacy Policy</h2>
                            <p class="text-xs text-slate-500 mt-0.5">How revisions are published.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed pt-1">
                        We may update our Privacy Policy from time to time. Any changes will be posted on this page with an updated "Last Updated" date. We encourage you to review this Privacy Policy periodically.
                    </p>
                </div>

                {{-- SECTION 9: CONTACT US CARD --}}
                <div class="bg-gradient-to-br from-[#0c1b33] to-slate-950 rounded-2xl p-6 sm:p-7 text-white space-y-4 shadow-md">
                    <div class="flex items-center gap-2 text-amber-400 font-bold text-xs uppercase tracking-wider">
                        <span>9. Contact Us</span>
                    </div>

                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight">
                            Have Questions or Data Privacy Requests?
                        </h2>
                        <p class="text-xs text-slate-300 max-w-2xl leading-relaxed mt-1">
                            If you have questions, feedback, or data privacy requests regarding this Privacy Policy, please contact us at:
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs">
                        <div class="bg-white/5 border border-white/10 p-3.5 rounded-xl">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Entity</span>
                            <span class="font-extrabold text-white text-xs mt-0.5 block">REIAC Community / Bellway Infotech</span>
                        </div>

                        <div class="bg-white/5 border border-white/10 p-3.5 rounded-xl">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Email Support</span>
                            <a href="mailto:prabin.ac.kr@gmail.com" class="font-extrabold text-amber-400 hover:text-amber-300 text-xs mt-0.5 block break-all">
                                prabin.ac.kr@gmail.com
                            </a>
                        </div>

                        <div class="bg-white/5 border border-white/10 p-3.5 rounded-xl">
                            <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block">Official Website</span>
                            <a href="https://community.bellwayinfotech.in" target="_blank" class="font-extrabold text-amber-400 hover:text-amber-300 text-xs mt-0.5 block break-all">
                                community.bellwayinfotech.in
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar :user="$user" :trending-topics="$trendingTopics" />

    </main>

</body>
</html>
