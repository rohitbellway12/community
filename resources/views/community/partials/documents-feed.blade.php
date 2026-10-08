{{-- Community Documents & Resources Feed (Clean & Simple) --}}
<div class="space-y-4">

    @forelse($documents as $doc)
        @php
            $files = $doc->files;
            $fileCount = $files->count();
            $badge = $doc->file_badge;
            $hasFile = $fileCount > 0 || !empty($doc->file_path);
            $hasLink = !empty($doc->link_url);
            $downloadUrl = route('community.documents.download', $doc);
        @endphp

        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-3.5 min-w-0 transition hover:border-slate-300">

            {{-- Header: Official Uploader & Category --}}
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-full {{ $badge['bg'] }} {{ $badge['text'] }} flex items-center justify-center text-lg font-black shrink-0 border {{ $badge['border'] }}">
                        {{ $badge['icon'] }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-slate-900 truncate">REIAC Official</span>
                            <span class="text-[10px] font-black uppercase px-1.5 py-0.2 rounded bg-amber-100 text-amber-800">Admin</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $doc->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @if(!empty($doc->category))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 shrink-0">
                        {{ $doc->category }}
                    </span>
                @endif
            </div>

            {{-- Document Title --}}
            <h3 class="text-base font-bold text-slate-900 leading-snug">
                @if($hasLink && !$hasFile)
                    <a href="{{ $doc->link_url }}" target="_blank" rel="noopener noreferrer" class="hover:text-amber-600 transition">
                        {{ $doc->title }}
                    </a>
                @elseif($hasFile && $fileCount <= 1)
                    <a href="{{ $downloadUrl }}" class="hover:text-amber-600 transition">
                        {{ $doc->title }}
                    </a>
                @else
                    {{ $doc->title }}
                @endif
            </h3>

            {{-- Description --}}
            @if(!empty($doc->description))
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $doc->description }}
                </p>
            @endif

            {{-- Multiple Attachments List (if 2 or more files) --}}
            @if($fileCount > 1)
                <div class="space-y-2 pt-1">
                    <p class="text-xs font-bold text-slate-700">Attached Documents & Files ({{ $fileCount }}):</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($files as $f)
                            @php $fBadge = $f->file_badge; @endphp
                            <a href="{{ route('community.documents.files.download', $f) }}"
                               class="flex items-center justify-between gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-50/80 hover:bg-amber-50/80 hover:border-amber-300 transition group shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-8 h-8 rounded-lg {{ $fBadge['bg'] }} {{ $fBadge['text'] }} flex items-center justify-center text-xs font-black shrink-0 border {{ $fBadge['border'] }}">
                                        {{ $fBadge['icon'] }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-900 group-hover:text-amber-950 truncate">{{ $f->file_name }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $f->formatted_size }}</div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 group-hover:bg-amber-600 text-slate-950 text-xs font-bold shrink-0 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    <span>Download</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- External Clickable Link (Real Link Style with Primary Color) --}}
            @if($hasLink)
                <div class="flex items-center gap-2 pt-1">
                    <span class="text-xs font-bold text-slate-500 shrink-0">Official Link:</span>
                    <a href="{{ $doc->link_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline break-all transition">
                        <span>{{ $doc->link_url }}</span>
                        <svg class="w-3.5 h-3.5 shrink-0 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            @endif

            {{-- Single Clean Action Area (If 1 file or footer stats) --}}
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">

                <div class="flex items-center gap-2">
                    {{-- Primary Action: Download File (if single file attached) --}}
                    @if($hasFile && $fileCount <= 1)
                        <a href="{{ $downloadUrl }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition">
                            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>Download ({{ $badge['label'] }}{{ $doc->file_size ? ' • ' . $doc->formatted_size : '' }})</span>
                        </a>
                    @endif
                </div>

                {{-- Download count indicator --}}
                @if($doc->download_count > 0)
                    <span class="text-xs text-slate-400 font-medium">
                        {{ number_format($doc->download_count) }} {{ Str::plural('download', $doc->download_count) }}
                    </span>
                @endif

            </div>

        </div>

    @empty

        {{-- Simple Empty State --}}
        <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-slate-200/70">
            <p class="text-xs text-slate-500">
                No documents or resources uploaded yet.
            </p>
        </div>

    @endforelse

    {{-- Pagination --}}
    @if(method_exists($documents, 'links'))
        <div class="mt-4">
            {{ $documents->appends(request()->query())->links() }}
        </div>
    @endif

</div>
