@extends('layouts.admin')

@section('title', 'Moderation Reports')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="{ activeModal: null, selectedReport: null }">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">✕</button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Moderation Reports</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review user complaints, reported discussions/comments, and handle policy violations.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($stats['pending'] > 0)
                <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-xl border border-amber-200 flex items-center gap-1.5 animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    {{ $stats['pending'] }} Pending Review
                </span>
            @else
                <span class="bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-emerald-200 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    No Pending Reports
                </span>
            @endif
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.reports') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs hover:border-slate-300 transition">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Reports</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ number_format($stats['total']) }}</div>
            <span class="text-[11px] text-slate-500 font-semibold mt-1">All complaints filed</span>
        </a>

        <a href="{{ route('admin.reports', ['status' => 'pending']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs hover:border-amber-300 transition">
            <span class="text-xs font-bold text-amber-500 uppercase tracking-wider">Pending Review</span>
            <div class="text-2xl font-extrabold text-amber-600 mt-2">{{ number_format($stats['pending']) }}</div>
            <span class="text-[11px] text-amber-600 font-semibold mt-1">Needs moderation action</span>
        </a>

        <a href="{{ route('admin.reports', ['status' => 'resolved']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs hover:border-emerald-300 transition">
            <span class="text-xs font-bold text-emerald-500 uppercase tracking-wider">Resolved</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2">{{ number_format($stats['resolved']) }}</div>
            <span class="text-[11px] text-emerald-600 font-semibold mt-1">Action taken</span>
        </a>

        <a href="{{ route('admin.reports', ['status' => 'dismissed']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs hover:border-slate-300 transition">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dismissed</span>
            <div class="text-2xl font-extrabold text-slate-600 mt-2">{{ number_format($stats['dismissed']) }}</div>
            <span class="text-[11px] text-slate-400 font-semibold mt-1">Ignored or invalid</span>
        </a>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        {{-- Status Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto">
            <a href="{{ route('admin.reports') }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-reiac-slate text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                All
            </a>
            <a href="{{ route('admin.reports', ['status' => 'pending']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.reports', ['status' => 'reviewing']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'reviewing' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Under Review
            </a>
            <a href="{{ route('admin.reports', ['status' => 'resolved']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Resolved
            </a>
            <a href="{{ route('admin.reports', ['status' => 'dismissed']) }}"
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'dismissed' ? 'bg-slate-500 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Dismissed
            </a>
        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.reports') }}" method="GET" class="w-full md:w-72 flex items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search reason or reporter..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    {{-- REPORTS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Report</th>
                        <th class="py-3.5 px-4">Flagged Content</th>
                        <th class="py-3.5 px-4">Reason / Details</th>
                        <th class="py-3.5 px-4">Content Author</th>
                        <th class="py-3.5 px-4">Reporter</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($reports as $report)
                        @php
                            $content = $report->reportable;
                            $author = $content?->user;
                            $contentType = class_basename($report->reportable_type);
                            $contentTitle = $contentType === 'Post' ? ($content?->title ?? 'Untitled Post') : ($content?->content ?? 'Comment deleted');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- ID & Date --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-mono text-slate-700 font-bold">#REP-{{ $report->id }}</span>
                                <div class="text-[10px] text-slate-400">{{ $report->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Flagged Content Snippet --}}
                            <td class="py-3 px-4 max-w-xs">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $contentType === 'Post' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                        {{ $contentType }}
                                    </span>
                                    @if(!$content)
                                        <span class="text-[10px] text-red-500 font-semibold">(Content Deleted)</span>
                                    @endif
                                </div>
                                <p class="text-slate-900 font-semibold line-clamp-2" title="{{ $contentTitle }}">
                                    {{ $contentTitle }}
                                </p>
                            </td>

                            {{-- Reason --}}
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 inline-block">
                                    {{ $report->reason }}
                                </span>
                                @if($report->description)
                                    <p class="text-slate-500 text-[11px] mt-1 line-clamp-2" title="{{ $report->description }}">
                                        {{ $report->description }}
                                    </p>
                                @endif
                            </td>

                            {{-- Author --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($author)
                                    <div class="font-bold text-slate-800">{{ $author->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $author->email }}</div>
                                    @if($author->status?->value === 'blocked' || $author->status === 'blocked')
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-red-100 text-red-600 font-bold">Blocked</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Unknown</span>
                                @endif
                            </td>

                            {{-- Reporter --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-700">{{ $report->reporter->name ?? 'Anonymous' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $report->reporter->email ?? '' }}</div>
                            </td>

                            {{-- Status --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $statusValue = $report->status instanceof \App\Enums\ReportStatus ? $report->status->value : $report->status;
                                @endphp
                                @if($statusValue === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Pending</span>
                                @elseif($statusValue === 'reviewing')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Reviewing</span>
                                @elseif($statusValue === 'resolved')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Resolved</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Dismissed</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    {{-- Quick Status Form (Resolve) --}}
                                    @if($statusValue !== 'resolved')
                                        <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit"
                                                    title="Mark as Resolved"
                                                    class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition border border-emerald-200">
                                                Resolve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Quick Dismiss --}}
                                    @if($statusValue !== 'dismissed')
                                        <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="dismissed">
                                            <button type="submit"
                                                    title="Dismiss Report"
                                                    class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-xs transition">
                                                Dismiss
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Delete Content Action --}}
                                    @if($content)
                                        <form action="{{ route('admin.reports.deleteContent', $report) }}" method="POST" class="inline" onsubmit="return confirm('Delete this reported {{ $contentType }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Delete Reported {{ $contentType }}"
                                                    class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition border border-rose-200">
                                                Delete Content
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Ban User Action --}}
                                    @if($author && $author->status !== 'blocked')
                                        <form action="{{ route('admin.reports.banUser', $report) }}" method="POST" class="inline" onsubmit="return confirm('Block user {{ $author->name }} from the community?');">
                                            @csrf
                                            <button type="submit"
                                                    title="Block User"
                                                    class="px-2 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700 font-bold text-xs transition">
                                                Ban Author
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                No moderation reports found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection