@extends('layouts.admin')

@section('title', 'AI Resumes & Moderation')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="{ rejectionModalOpen: false, currentVerificationId: null, rejectionReason: '' }">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-extrabold text-teal-600 uppercase tracking-wider">Services &amp; Resources</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500 font-medium">Student CV Engine</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">AI Resume Builder &amp; Watermark Moderation</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage generated student resumes, verify social reshares, and configure watermark branding.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.resumes.verifications') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Verifications Queue</span>
                @if($stats['pending_verifications'] > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">
                        {{ $stats['pending_verifications'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.resumes.settings') }}"
               class="px-4 py-2.5 rounded-xl bg-reiac-navy text-reiac-gold hover:bg-slate-900 font-black text-xs shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Watermark &amp; AI Settings</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Resumes</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</h3>
                <span class="text-[11px] text-slate-400 font-medium">Created across all templates</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-black">
                📄
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Clean / Watermark Removed</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['clean']) }}</h3>
                <span class="text-[11px] text-slate-400 font-medium">Successfully unlocked via reshare</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-black">
                ✨
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Verifications</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending_verifications']) }}</h3>
                <span class="text-[11px] text-slate-400 font-medium">Awaiting admin review</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-black">
                ⏳
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total PDF Downloads</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1">{{ number_format($stats['downloads']) }}</h3>
                <span class="text-[11px] text-slate-400 font-medium">Generated &amp; exported</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-black">
                📥
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.resumes.index') }}" class="flex flex-1 items-center gap-2">
            <div class="relative flex-1 max-w-md">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by title, student name, or email..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="watermark" onchange="this.form.submit()"
                    class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Watermark Statuses</option>
                <option value="clean" {{ $watermarkFilter === 'clean' ? 'selected' : '' }}>Clean (Unlocked)</option>
                <option value="watermarked" {{ $watermarkFilter === 'watermarked' ? 'selected' : '' }}>Watermarked</option>
            </select>

            <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                Filter
            </button>

            @if($search || $watermarkFilter)
                <a href="{{ route('admin.resumes.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- RESUMES TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Student</th>
                        <th class="py-3.5 px-4">Resume Title</th>
                        <th class="py-3.5 px-4">Template</th>
                        <th class="py-3.5 px-4 text-center">Downloads</th>
                        <th class="py-3.5 px-4 text-center">Watermark Status</th>
                        <th class="py-3.5 px-4">Created Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($resumes as $resume)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center text-xs">
                                        {{ substr($resume->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $resume->user->name ?? 'Unknown Student' }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $resume->user->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $resume->title }}
                                @if(!empty($resume->personal_info['job_title']))
                                    <span class="text-[11px] text-slate-400 font-normal block">{{ $resume->personal_info['job_title'] }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider
                                    {{ $resume->template === 'modern' ? 'bg-teal-50 text-teal-700 border border-teal-200' : '' }}
                                    {{ $resume->template === 'classic' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $resume->template === 'creative' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}">
                                    {{ $resume->template }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                {{ $resume->download_count }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($resume->is_watermark_removed)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span>✓</span> Clean (Removed)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                        <span>🔒</span> Watermarked
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $resume->created_at->format('M d, Y h:i A') }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('resume-builder.preview', $resume->id) }}" target="_blank"
                                       class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition">
                                        Preview
                                    </a>

                                    <a href="{{ route('resume-builder.download', $resume->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] transition">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-4xl mb-2">📄</div>
                                <p class="font-bold text-sm text-slate-600">No student resumes found</p>
                                <p class="text-xs text-slate-400 mt-0.5">Students can build resumes from the frontend /resume-builder page.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resumes->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $resumes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
