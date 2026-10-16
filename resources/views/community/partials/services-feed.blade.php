{{-- SERVICES FEED (When tab=services is active on Community Home) --}}
@php
    $servicesQuery = \App\Models\Service::published()->with('category')->ordered();
    if (request('service_category') && request('service_category') !== 'all') {
        $servicesQuery->whereHas('category', function($q) {
            $q->where('slug', request('service_category'))->orWhere('id', request('service_category'));
        });
    }
    if (request('search')) {
        $sTerm = trim(request('search'));
        $servicesQuery->where(function($q) use ($sTerm) {
            $q->where('title', 'like', "%{$sTerm}%")
              ->orWhere('short_description', 'like', "%{$sTerm}%")
              ->orWhere('description', 'like', "%{$sTerm}%");
        });
    }
    $tabServices = $servicesQuery->paginate(9, ['*'], 'service_page')->withQueryString();
    $serviceCategories = \App\Models\ServiceCategory::where('is_active', true)->withCount(['services' => fn($q) => $q->published()])->orderBy('sort_order')->get();
@endphp

<div class="space-y-4">
    {{-- Header Banner --}}
    <div class="p-5 rounded-2xl bg-gradient-to-r from-[#0c1b33] via-slate-900 to-[#1c2541] text-white shadow-md border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-slate-950">
                    Official Services
                </span>
                <span class="text-xs text-amber-300 font-semibold">• 1-on-1 Guidance</span>
            </div>
            <h2 class="text-lg font-black text-white">REIAC Consultancy & Student Services</h2>
            <p class="text-xs text-slate-300 mt-0.5">Connect directly with licensed immigration advisors, education counselors, and language experts via WhatsApp.</p>
        </div>

        <a href="{{ route('services.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs shrink-0 transition shadow-sm">
            <span>Open Catalog</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    {{-- Category Filters --}}
    @if($serviceCategories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-1 [&::-webkit-scrollbar]:hidden">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'services', 'service_category' => null, 'service_page' => null]) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ !request('service_category') || request('service_category') === 'all' ? 'bg-[#0c1b33] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                All ({{ \App\Models\Service::published()->count() }})
            </a>
            @foreach($serviceCategories as $scat)
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'services', 'service_category' => $scat->slug, 'service_page' => null]) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ request('service_category') === $scat->slug ? 'bg-[#0c1b33] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    {{ $scat->name }} ({{ $scat->services_count }})
                </a>
            @endforeach
        </div>
    @endif

    {{-- Services Grid --}}
    @if($tabServices->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tabServices as $srv)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        {{-- Top Meta --}}
                        <div class="flex items-start justify-between gap-3 mb-2.5">
                            @if($srv->category)
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200/60">
                                    {{ $srv->category->name }}
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-slate-500 bg-slate-100">
                                    Consultancy
                                </span>
                            @endif

                            @if($srv->image_url)
                                <img src="{{ $srv->image_url }}" alt="{{ $srv->title }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
                                    💼
                                </div>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h3 class="font-bold text-slate-900 text-sm group-hover:text-amber-600 transition leading-snug line-clamp-2">
                            <a href="{{ route('services.show', $srv->slug) }}">
                                {{ $srv->title }}
                            </a>
                        </h3>

                        {{-- Short Description --}}
                        <p class="text-xs text-slate-500 mt-1.5 line-clamp-2 leading-relaxed">
                            {{ $srv->short_description ?: Str::limit(strip_tags($srv->description), 100) }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('services.show', $srv->slug) }}"
                           class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
                            <span>Details</span>
                            <span class="text-xs">&rarr;</span>
                        </a>

                        @if(!empty($srv->whatsapp_number))
                            <a href="{{ route('services.inquiry', $srv) }}"
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
            {{ $tabServices->links() }}
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
