<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Consultancy & Student Services | REIAC Community</title>

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

    {{-- MAIN 3-COLUMN COMMUNITY GRID --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        {{-- LEFT SIDEBAR --}}
        <x-community.sidebar />

        {{-- CENTER CONTENT --}}
        <div class="space-y-5 min-w-0">

            {{-- HERO HEADER CARD --}}
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-[#0c1b33] via-[#102447] to-[#1a2f55] text-white p-5 sm:p-7 shadow-sm border border-slate-800">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-amber-400/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="space-y-2 relative z-10">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow-xs">
                        <span>💼</span>
                        <span>Official Consultancy</span>
                        <span class="text-slate-800">• 1-on-1 WhatsApp Support</span>
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        REIAC Consultancy & Student Services
                    </h1>

                    <p class="text-xs text-slate-300 leading-relaxed font-medium">
                        Explore certified services for visas, university admissions, and exam prep. Connect directly with counselors via WhatsApp.
                    </p>
                </div>

                {{-- SEARCH --}}
                <form method="GET" action="{{ route('services.index') }}" class="mt-4 max-w-lg relative flex items-center z-10">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <span class="absolute left-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search services (e.g. Visa, Admission, TOPIK)..."
                        class="w-full bg-white text-slate-900 rounded-xl pl-10 pr-20 py-2.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-amber-400 shadow-sm"
                    >
                    <button
                        type="submit"
                        class="absolute right-1.5 px-3 py-1.5 bg-[#0c1b33] hover:bg-slate-900 text-white rounded-lg text-xs font-bold transition shadow-xs"
                    >
                        Search
                    </button>
                </form>
            </div>

            {{-- CATEGORY FILTER PILLS --}}
            @if($categories->isNotEmpty())
                <div class="flex items-center gap-2 overflow-x-auto pb-1 [&::-webkit-scrollbar]:hidden">
                    <a
                        href="{{ route('services.index', array_filter(['search' => $search])) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-1.5 {{ empty($activeCategory) ? 'bg-[#0c1b33] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}"
                    >
                        <span>All</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($activeCategory) ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-100 text-slate-600' }}">
                            {{ $totalServices }}
                        </span>
                    </a>

                    @foreach($categories as $cat)
                        <a
                            href="{{ route('services.index', array_filter(['category' => $cat->slug, 'search' => $search])) }}"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-1.5 {{ !empty($activeCategory) && $activeCategory->id === $cat->id ? 'bg-[#0c1b33] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}"
                        >
                            <span>{{ $cat->name }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ !empty($activeCategory) && $activeCategory->id === $cat->id ? 'bg-amber-400 text-slate-950 font-black' : 'bg-slate-100 text-slate-600' }}">
                                {{ $cat->services_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- SERVICES GRID --}}
            @if($services->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($services as $service)
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                            <div>
                                {{-- Top Meta & Compact Thumbnail --}}
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="space-y-1 min-w-0">
                                        @if($service->category)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-900 border border-amber-200/60 inline-block truncate">
                                                {{ $service->category->name }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-slate-500 bg-slate-100 inline-block">
                                                Consultancy
                                            </span>
                                        @endif

                                        @if(!empty($service->whatsapp_number))
                                            <div class="flex items-center gap-1 text-[10px] font-extrabold text-emerald-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                WhatsApp Chat Available
                                            </div>
                                        @endif
                                    </div>

                                    @if($service->image_url)
                                        <div class="w-14 h-14 rounded-xl border border-slate-200/80 bg-slate-50 p-1 flex items-center justify-center shrink-0 shadow-2xs">
                                            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="max-w-full max-h-full object-contain">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center font-black text-lg shrink-0 shadow-xs">
                                            💼
                                        </div>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h2 class="font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-amber-600 transition leading-snug line-clamp-2">
                                    <a href="{{ route('services.show', $service->slug) }}">
                                        {{ $service->title }}
                                    </a>
                                </h2>

                                {{-- Description --}}
                                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed font-medium">
                                    {{ $service->short_description ?: Str::limit(strip_tags($service->description), 110) }}
                                </p>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('services.show', $service->slug) }}"
                                   class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                                    <span>Details</span>
                                    <span>&rarr;</span>
                                </a>

                                @if(!empty($service->whatsapp_number))
                                    <a href="{{ route('services.inquiry', $service) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.771.884 2.796.885 3.183 0 5.767-2.587 5.768-5.766.001-3.181-2.583-5.772-5.768-5.772zm3.387 8.163c-.14.394-.807.747-1.118.777-.312.03-.709.137-2.317-.525-1.936-.795-3.189-2.772-3.285-2.9-.096-.129-.785-1.045-.785-1.993 0-.948.497-1.414.673-1.608.176-.194.384-.243.513-.243.129 0 .258.002.37.008.118.006.276-.045.431.328.156.373.535 1.306.582 1.402.047.096.079.208.016.333-.062.125-.094.204-.187.312-.093.109-.197.243-.281.327-.094.093-.192.195-.082.383.11.188.487.804 1.047 1.302.721.642 1.329.841 1.517.935.188.094.298.082.408-.045.11-.127.472-.549.598-.737.126-.188.252-.157.424-.094.172.063 1.09.514 1.277.608.187.094.312.141.359.22.047.078.047.452-.093.846z"/>
                                        </svg>
                                        <span>WhatsApp</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2">
                    {{ $services->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl mb-3">
                        💼
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm">No services found</h3>
                    <p class="text-xs text-slate-400 mt-1">Try selecting another category or check back later.</p>
                </div>
            @endif

        </div>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar />

    </main>

</body>
</html>
