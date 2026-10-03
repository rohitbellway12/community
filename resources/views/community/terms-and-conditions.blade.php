@props([
    'title' => 'Terms & Conditions | REIAC Community',
    'active' => 'terms',
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
                <a href="{{ route('community.privacy') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Privacy Policy
                </a>
                <a href="{{ route('community.terms') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
                    <span class="text-slate-900 font-bold">Terms & Conditions</span>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('community.privacy') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/70 px-3 py-1.5 rounded-lg font-bold transition">
                        <span>Privacy Policy</span>
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
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold tracking-wider uppercase">
                        <span>REIAC Community Terms of Use</span>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
                        Last Updated: October 01, 2026
                    </span>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Terms & Conditions for REIAC Community
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                        Welcome to <strong class="text-slate-900">REIAC Community</strong>, operated by <strong class="text-slate-900">Bellway Infotech</strong>. By registering for, downloading, or using our mobile application and online community platform, you agree to comply with and be bound by these Terms & Conditions.
                    </p>
                    <div class="p-3 bg-slate-50 border-l-4 border-slate-900 rounded-r-xl text-xs text-slate-700 font-medium">
                        Please read these terms carefully before accessing or using the community. If you do not agree to all terms, you may not access or use our services.
                    </div>
                </div>

                {{-- SEARCH BAR --}}
                <div class="pt-2 max-w-md">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text"
                               x-model="searchRule"
                               placeholder="Search terms (e.g., account, rules, cbt, ban, liability)..."
                               class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition shadow-inner">
                        <button type="button" x-show="searchRule" @click="searchRule = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 text-xs">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            {{-- TERMS SECTIONS --}}
            <div class="space-y-4">

                {{-- SECTION 1: ACCEPTANCE & ELIGIBILITY --}}
                <div x-show="!searchRule || 'acceptance eligibility age registration account'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            01
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">1. Acceptance of Terms & Eligibility</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Rules regarding participation and eligibility.</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 leading-relaxed pt-1">
                        <p>
                            By signing up for REIAC Community, you confirm that you are at least 13 years old (or have authorized parental/guardian approval if below the legal age in your jurisdiction) and possess full legal capacity to enter into these Terms.
                        </p>
                        <p>
                            You agree to supply accurate, current, and complete registration information and to keep your credentials confidential. You are solely responsible for all actions conducted under your account.
                        </p>
                    </div>
                </div>

                {{-- SECTION 2: CODE OF CONDUCT & COMMUNITY RULES --}}
                <div x-show="!searchRule || 'conduct rules prohibited spam abuse harassment illegal'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            02
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">2. Code of Conduct & Prohibited Activities</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Upholding a constructive, friendly learning environment.</p>
                        </div>
                    </div>

                    <div class="space-y-3 pt-1 text-xs text-slate-600">
                        <p>Members must engage respectfully. The following activities are strictly prohibited:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="p-3 bg-rose-50/70 border border-rose-200/60 rounded-xl space-y-1">
                                <span class="font-bold text-rose-900 flex items-center gap-1.5">
                                    <span>✕</span> Hate Speech & Harassment
                                </span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    No defamatory, obscene, racist, threatening, or harassing messages targeting any student, consultant, or staff member.
                                </p>
                            </div>

                            <div class="p-3 bg-rose-50/70 border border-rose-200/60 rounded-xl space-y-1">
                                <span class="font-bold text-rose-900 flex items-center gap-1.5">
                                    <span>✕</span> Spam & Commercial Promotion
                                </span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    No unsolicited advertisements, affiliate links, mass messaging, or repetitive automated posts.
                                </p>
                            </div>

                            <div class="p-3 bg-rose-50/70 border border-rose-200/60 rounded-xl space-y-1">
                                <span class="font-bold text-rose-900 flex items-center gap-1.5">
                                    <span>✕</span> Fake Visa or Academic Claims
                                </span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    No misleading guarantees regarding university admissions, visas, or fraudulent document services.
                                </p>
                            </div>

                            <div class="p-3 bg-rose-50/70 border border-rose-200/60 rounded-xl space-y-1">
                                <span class="font-bold text-rose-900 flex items-center gap-1.5">
                                    <span>✕</span> Unauthorized Scraping & Bots
                                </span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    No automated crawlers, question bank scraping, or attempts to compromise server integrity.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: USER CONTENT & LICENSING --}}
                <div x-show="!searchRule || 'content license ownership ugc posts comments copyright'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            03
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">3. User-Generated Content & Ownership</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Rights and responsibilities regarding what you publish.</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 leading-relaxed pt-1">
                        <p>
                            <strong class="text-slate-900">Your Ownership:</strong> You retain ownership of all original text, study notes, questions, and media you submit to the community.
                        </p>
                        <p>
                            <strong class="text-slate-900">License to REIAC:</strong> By submitting content, you grant REIAC Community and Bellway Infotech a worldwide, non-exclusive, royalty-free license to store, format, display, and distribute your contributions across the community platform.
                        </p>
                        <p>
                            <strong class="text-slate-900">Moderation Rights:</strong> We reserve the right to review, edit, or remove any content that violates these Terms or our Community Guidelines without prior notice.
                        </p>
                    </div>
                </div>

                {{-- SECTION 4: CBT EXAMS & PRACTICE TESTS --}}
                <div x-show="!searchRule || 'cbt exam test score korean practice disclaimer'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            04
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">4. CBT Exams & Practice Tests Disclaimer</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Educational nature of practice tests and mock evaluations.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/70 text-xs text-slate-600 space-y-2 leading-relaxed">
                        <p>
                            Practice assessments provided in the App (including Korean CBT exam practice tests) are solely intended for academic preparation and self-evaluation.
                        </p>
                        <p>
                            Scores, percentiles, or feedback generated on REIAC Community do not guarantee results in official government or third-party examination boards. All official test bookings and certifications must be conducted through their respective governing authorities.
                        </p>
                    </div>
                </div>

                {{-- SECTION 5: ACCOUNT TERMINATION --}}
                <div x-show="!searchRule || 'termination ban suspension deactivate delete'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            05
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">5. Account Suspension & Termination</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Conditions under which access may be restricted.</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-slate-600 leading-relaxed pt-1">
                        <p>
                            We reserve the right to temporarily suspend or permanently terminate your account and access if you violate these Terms, engage in fraud or spam, or cause harm to our community members.
                        </p>
                        <p>
                            You may voluntarily request deletion of your account at any time by contacting our support team at <a href="mailto:prabin.ac.kr@gmail.com" class="text-amber-600 font-bold hover:underline">prabin.ac.kr@gmail.com</a>.
                        </p>
                    </div>
                </div>

                {{-- SECTION 6: LIMITATION OF LIABILITY --}}
                <div x-show="!searchRule || 'liability disclaimer warranty as-is'.includes(searchRule.toLowerCase())"
                     class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4 hover:border-slate-300 transition">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            06
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-extrabold text-slate-900">6. Limitation of Liability</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Legal disclaimers regarding service provision.</p>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed pt-1">
                        REIAC Community is provided on an "as is" and "as available" basis without warranties of any kind. Under no circumstances shall REIAC Community, Bellway Infotech, or its affiliates be liable for indirect, incidental, punitive, or consequential damages resulting from your use of or inability to use the platform.
                    </p>
                </div>

                {{-- SECTION 7: CHANGES TO TERMS & CONTACT US --}}
                <div class="bg-gradient-to-br from-[#0c1b33] to-slate-950 rounded-2xl p-6 sm:p-7 text-white space-y-4 shadow-md">
                    <div class="flex items-center gap-2 text-amber-400 font-bold text-xs uppercase tracking-wider">
                        <span>7. Contact Information</span>
                    </div>

                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight">
                            Questions Regarding Terms & Conditions?
                        </h2>
                        <p class="text-xs text-slate-300 max-w-2xl leading-relaxed mt-1">
                            For any inquiries, clarifications, or reports of violations, please contact us through our official channels:
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
