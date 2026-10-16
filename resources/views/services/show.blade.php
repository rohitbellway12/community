<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $service->title }} — REIAC Services</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f4f6] text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-12">

    {{-- TOPBAR --}}
    <x-community.topbar :notifications-count="auth()->check() ? auth()->user()->unreadNotifications()->count() : 0" />

    {{-- MAIN 3-COLUMN COMMUNITY GRID (Matches Landing Page) --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        {{-- LEFT SIDEBAR --}}
        <x-community.sidebar />

        {{-- CENTER CONTENT --}}
        <div class="space-y-5 min-w-0">

            {{-- BREADCRUMBS & NAVIGATION --}}
            <div class="bg-white px-4 py-3 rounded-2xl border border-slate-200/70 shadow-xs flex items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 font-semibold text-slate-500 truncate">
                    <a href="{{ route('community.index') }}" class="hover:text-amber-600 transition flex items-center gap-1 shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6"/></svg>
                        <span>Home</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('services.index') }}" class="hover:text-amber-600 transition shrink-0">Services</a>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-900 font-bold truncate">{{ $service->title }}</span>
                </div>

                <a href="{{ route('services.index') }}"
                   class="px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs shrink-0">
                    <span>&larr; All Services</span>
                </a>
            </div>

            {{-- SERVICE HERO CARD --}}
            <article class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-7 shadow-xs space-y-6">

                {{-- TOP HEADER WITH IMAGE & META --}}
                <div class="flex flex-col sm:flex-row gap-5 items-start">

                    {{-- Image Thumbnail (Nicely Framed & Compact) --}}
                    @if($service->image_url)
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border border-slate-200/80 bg-slate-50 p-2 flex items-center justify-center shrink-0 shadow-2xs">
                            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center text-3xl font-black shadow-md shrink-0">
                            💼
                        </div>
                    @endif

                    {{-- Service Title & Badges --}}
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($service->category)
                                <a href="{{ route('services.index', ['category' => $service->category->slug]) }}"
                                   class="px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200 hover:bg-amber-200 transition">
                                    {{ $service->category->name }}
                                </a>
                            @endif

                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Support
                            </span>

                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                ✓ Verified REIAC Service
                            </span>
                        </div>

                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
                            {{ $service->title }}
                        </h1>

                        @if(!empty($service->short_description))
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                                {{ $service->short_description }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- QUICK HIGHLIGHTS PILLS --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-2 border-t border-slate-100 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 block">Channel</span>
                        <span class="font-bold text-slate-900 mt-0.5 flex items-center gap-1 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> WhatsApp
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 block">Response</span>
                        <span class="font-bold text-slate-900 mt-0.5 text-xs">⚡ Direct Chat</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 block">Assistance</span>
                        <span class="font-bold text-slate-900 mt-0.5 text-xs">1-on-1 Guidance</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 block">Counselor</span>
                        <span class="font-bold text-slate-900 mt-0.5 text-xs">Authorized Expert</span>
                    </div>
                </div>

                {{-- SERVICE DETAILS & DESCRIPTION --}}
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <h2 class="text-xs font-black uppercase tracking-wider text-slate-700">Service Description & Eligibility</h2>
                    </div>

                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/70 text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line font-medium">
                        {{ $service->description ?: $service->short_description ?: 'Connect directly via WhatsApp to receive full documentation checklist, admission requirements, or visa eligibility criteria.' }}
                    </div>
                </div>

                {{-- PRIMARY WHATSAPP ACTION HERO CARD --}}
                @if(!empty($service->whatsapp_number))
                    <div class="p-5 sm:p-6 rounded-2xl shadow-lg space-y-4"
                         style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #064e3b 100%) !important; color: #ffffff !important;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                     style="background: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important;">
                                    <span>💬</span>
                                    <span>Direct Inquiry</span>
                                </div>
                                <h3 class="text-base sm:text-lg font-black" style="color: #ffffff !important;">
                                    Start Your Consultation on WhatsApp
                                </h3>
                                <p class="text-xs max-w-md" style="color: #d1fae5 !important;">
                                    Our counselors will guide you step-by-step with complete documentation and next actions.
                                </p>
                            </div>

                            <a href="{{ route('services.inquiry', $service) }}"
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-black text-xs sm:text-sm shadow-md transition transform active:scale-95 shrink-0 hover:opacity-95"
                               style="background: #ffffff !important; color: #065f46 !important;">
                                <svg class="w-5 h-5 fill-current" style="color: #059669 !important;" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.771.884 2.796.885 3.183 0 5.767-2.587 5.768-5.766.001-3.181-2.583-5.772-5.768-5.772zm3.387 8.163c-.14.394-.807.747-1.118.777-.312.03-.709.137-2.317-.525-1.936-.795-3.189-2.772-3.285-2.9-.096-.129-.785-1.045-.785-1.993 0-.948.497-1.414.673-1.608.176-.194.384-.243.513-.243.129 0 .258.002.37.008.118.006.276-.045.431.328.156.373.535 1.306.582 1.402.047.096.079.208.016.333-.062.125-.094.204-.187.312-.093.109-.197.243-.281.327-.094.093-.192.195-.082.383.11.188.487.804 1.047 1.302.721.642 1.329.841 1.517.935.188.094.298.082.408-.045.11-.127.472-.549.598-.737.126-.188.252-.157.424-.094.172.063 1.09.514 1.277.608.187.094.312.141.359.22.047.078.047.452-.093.846z"/>
                                </svg>
                                <span>Chat on WhatsApp</span>
                                <span class="text-xs">&rarr;</span>
                            </a>
                        </div>

                        {{-- Prefilled Message Preview --}}
                        <div class="p-3.5 rounded-xl text-xs flex items-start gap-2"
                             style="background: rgba(0, 0, 0, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; color: #ffffff !important;">
                            <span class="font-extrabold shrink-0" style="color: #fde047 !important;">💬 Message Preview:</span>
                            <span class="italic font-medium leading-relaxed" style="color: #ffffff !important;">"{{ $service->whatsapp_message ?: "Hello REIAC Consultancy, I am interested in {$service->title}. Please guide me with more details." }}"</span>
                        </div>
                    </div>
                @endif

                {{-- CONTACT NUMBERS STRIP --}}
                <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    @if(!empty($service->whatsapp_number))
                        <div class="p-3.5 rounded-xl border border-slate-200/70 flex items-center gap-3" style="background: #ffffff;">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-base shrink-0" style="background: #d1fae5; color: #047857;">📱</span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-500 block font-bold uppercase tracking-wider">WhatsApp Number</span>
                                <span class="font-extrabold text-slate-900 truncate block text-xs mt-0.5">{{ $service->whatsapp_number }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($service->phone))
                        <div class="p-3.5 rounded-xl border border-slate-200/70 flex items-center gap-3" style="background: #ffffff;">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-base shrink-0" style="background: #dbeafe; color: #1d4ed8;">📞</span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-500 block font-bold uppercase tracking-wider">Direct Phone</span>
                                <a href="tel:{{ $service->phone }}" class="font-extrabold text-slate-900 hover:text-amber-600 truncate block text-xs mt-0.5">{{ $service->phone }}</a>
                            </div>
                        </div>
                    @endif

                    @if(!empty($service->email))
                        <div class="p-3.5 rounded-xl border border-slate-200/70 flex items-center gap-3" style="background: #ffffff;">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-base shrink-0" style="background: #e0e7ff; color: #4338ca;">✉️</span>
                            <div class="min-w-0">
                                <span class="text-[10px] text-slate-500 block font-bold uppercase tracking-wider">Direct Email</span>
                                <a href="mailto:{{ $service->email }}" class="font-extrabold text-slate-900 hover:text-amber-600 break-all block text-xs mt-0.5" title="{{ $service->email }}">{{ $service->email }}</a>
                            </div>
                        </div>
                    @endif
                </div>

            </article>

            {{-- RELATED SERVICES --}}
            @if(isset($relatedServices) && $relatedServices->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-amber-500 font-black text-sm">✦</span>
                            <h3 class="font-black text-slate-900 text-sm tracking-tight">Other Services in this Category</h3>
                        </div>
                        <a href="{{ route('services.index', ['category' => $service->category?->slug]) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">View All &rarr;</a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($relatedServices as $rel)
                            <a href="{{ route('services.show', $rel->slug) }}"
                               class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-white hover:border-amber-300 hover:shadow-xs transition group flex flex-col justify-between">
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-wider text-amber-600 block">{{ $rel->category?->name ?? 'Consultancy' }}</span>
                                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-amber-600 transition truncate mt-1">{{ $rel->title }}</h4>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-700 mt-2 block">Learn more &rarr;</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar />

    </main>

</body>
</html>
