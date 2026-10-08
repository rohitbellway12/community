@extends('layouts.admin')

@section('title', 'Documents & Resources Management')

@section('content')
<div class="space-y-6">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">✕</button>
        </div>
    @endif

    {{-- HEADER & ACTION BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Documents & Study Resources</h1>
            <p class="text-xs text-slate-500 mt-1">Upload and manage all files (PDF, PPT, Word, Excel, HWP, Images, Videos) and clickable reference links.</p>
        </div>

        <a href="{{ route('admin.documents.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-reiac-navy text-reiac-gold hover:bg-slate-900 font-bold text-xs uppercase tracking-wider transition shadow-sm cursor-pointer shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Upload Document / Link</span>
        </a>
    </div>

    {{-- METRIC STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Resources</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Documents in catalog</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Files Uploaded</div>
            <div class="text-2xl font-black text-indigo-600 mt-1">{{ $stats['with_file'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">All formats supported</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Clickable Links</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['with_link'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">External URL references</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Downloads</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['total_downloads'] }}</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Student downloads logged</div>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by title, description, or file name..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="sm:col-span-3">
                <select name="category" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-reiac-navy focus:outline-hidden">
                    <option value="">All Types</option>
                    <option value="file" {{ request('type') === 'file' ? 'selected' : '' }}>With File</option>
                    <option value="link" {{ request('type') === 'link' ? 'selected' : '' }}>With Link</option>
                </select>
            </div>

            <div class="sm:col-span-1 flex items-center gap-1">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 text-white hover:bg-slate-900 font-bold text-xs transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- DOCUMENTS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-black text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Document Details</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Uploaded File</th>
                        <th class="py-3 px-4">External Link</th>
                        <th class="py-3 px-4">Downloads</th>
                        <th class="py-3 px-4">Created</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                        @php
                            $badge = $doc->file_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            {{-- Title & Description --}}
                            <td class="py-3.5 px-4 min-w-[240px]">
                                <div class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $doc->title }}</span>
                                    @if(!$doc->is_active)
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-200 text-slate-600">Inactive</span>
                                    @endif
                                </div>
                                @if($doc->description)
                                    <p class="text-slate-500 text-[11px] mt-0.5 line-clamp-2 max-w-md">{{ $doc->description }}</p>
                                @endif
                            </td>

                            {{-- Category --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $doc->category ?: 'General' }}
                                </span>
                            </td>

                            {{-- Uploaded File --}}
                            <td class="py-3.5 px-4 min-w-[180px]">
                                @if($doc->file_path)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 rounded-lg text-[10px] font-black {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }} shrink-0">
                                            {{ $badge['icon'] }} {{ $badge['label'] }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 truncate text-[11px]" title="{{ $doc->file_name }}">
                                                {{ $doc->file_name ?: ('File.' . $doc->file_type) }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $doc->formatted_size }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <a href="{{ route('admin.documents.download', $doc) }}"
                                           class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline">
                                            <span>⬇ Download File</span>
                                        </a>
                                        <span class="text-slate-300">·</span>
                                        <a href="{{ $doc->file_url }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 hover:text-slate-800 hover:underline">
                                            <span>View ↗</span>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">No file attached</span>
                                @endif
                            </td>

                            {{-- External Link --}}
                            <td class="py-3.5 px-4 min-w-[180px]">
                                @if($doc->link_url)
                                    <a href="{{ $doc->link_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold text-[11px] transition max-w-[200px] truncate"
                                       title="{{ $doc->link_url }}">
                                        <span>🔗</span>
                                        <span class="truncate">{{ parse_url($doc->link_url, PHP_URL_HOST) ?: 'Open External Link' }}</span>
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">No link</span>
                                @endif
                            </td>

                            {{-- Downloads --}}
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono font-bold text-slate-700">
                                {{ $doc->download_count }}
                            </td>

                            {{-- Date --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 text-[11px]">
                                <div>{{ $doc->created_at->format('M d, Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $doc->created_at->format('h:i A') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.documents.edit', $doc) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-4xl mb-2">📁</div>
                                <p class="font-bold text-slate-600 text-sm">No documents uploaded yet</p>
                                <p class="text-xs text-slate-400 mt-1">Upload study materials, guides, forms, presentations or external links for students.</p>
                                <a href="{{ route('admin.documents.create') }}" class="inline-block mt-3 px-4 py-2 bg-reiac-navy text-reiac-gold text-xs font-bold rounded-xl shadow-xs">
                                    Upload First Document
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
