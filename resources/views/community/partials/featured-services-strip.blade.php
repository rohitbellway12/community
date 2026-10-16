{{-- FEATURED SERVICES SHOWCASE STRIP (Landing Page) --}}
@php
    $servicesList = $featuredServices ?? \App\Models\Service::published()->with('category')->ordered()->take(6)->get();
@endphp

@if(isset($servicesList) && $servicesList->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs space-y-3.5">
        {{-- Section Header --}}
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center font-bold text-sm shadow-2xs">
                    💼
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight">
                            Consultancy & Student Services
                        </h2>
                        <span class="inline-flex items-center gap-1 text-[9px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Support
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">Direct 1-on-1 WhatsApp inquiry with certified counselors</p>
                </div>
            </div>

            <a href="{{ route('services.index') }}"
               class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 shrink-0 group">
                <span>View All</span>
                <span class="transition transform group-hover:translate-x-0.5">&rarr;</span>
            </a>
        </div>

        {{-- Horizontal Carousel / Scrollable Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($servicesList->take(3) as $srv)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-white hover:border-amber-300 hover:shadow-xs transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        {{-- Category Badge --}}
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md bg-amber-100/70 text-amber-900 truncate">
                                {{ $srv->category?->name ?? 'Consultancy' }}
                            </span>
                            @if(!empty($srv->whatsapp_number))
                                <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.2 rounded flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    WhatsApp
                                </span>
                            @endif
                        </div>

                        {{-- Service Title --}}
                        <h3 class="font-bold text-xs text-slate-900 group-hover:text-amber-600 transition line-clamp-2 leading-snug">
                            <a href="{{ route('services.show', $srv->slug) }}">
                                {{ $srv->title }}
                            </a>
                        </h3>

                        {{-- Description --}}
                        <p class="text-[11px] text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                            {{ $srv->short_description ?: Str::limit(strip_tags($srv->description), 80) }}
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between gap-2">
                        <a href="{{ route('services.show', $srv->slug) }}"
                           class="text-[11px] font-bold text-slate-600 hover:text-slate-900 transition">
                            Details &rarr;
                        </a>

                        @if(!empty($srv->whatsapp_number))
                            <a href="{{ route('services.inquiry', $srv) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow-xs transition active:scale-95">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.698c.969.587 1.771.884 2.796.885 3.183 0 5.767-2.587 5.768-5.766.001-3.181-2.583-5.772-5.768-5.772zm3.387 8.163c-.14.394-.807.747-1.118.777-.312.03-.709.137-2.317-.525-1.936-.795-3.189-2.772-3.285-2.9-.096-.129-.785-1.045-.785-1.993 0-.948.497-1.414.673-1.608.176-.194.384-.243.513-.243.129 0 .258.002.37.008.118.006.276-.045.431.328.156.373.535 1.306.582 1.402.047.096.079.208.016.333-.062.125-.094.204-.187.312-.093.109-.197.243-.281.327-.094.093-.192.195-.082.383.11.188.487.804 1.047 1.302.721.642 1.329.841 1.517.935.188.094.298.082.408-.045.11-.127.472-.549.598-.737.126-.188.252-.157.424-.094.172.063 1.09.514 1.277.608.187.094.312.141.359.22.047.078.047.452-.093.846z"/>
                                </svg>
                                <span>Inquire</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
