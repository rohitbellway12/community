@extends('layouts.admin')

@section('title', 'Test Submissions')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         testId: '{{ request('test_id') }}',
         levelId: '{{ request('level_id') }}',
         result: '{{ request('result') }}',
         submissionType: '{{ request('submission_type') }}',
         dateFrom: '{{ request('date_from') }}',
         dateTo: '{{ request('date_to') }}',
     }">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Test Submissions</h1>
            <p class="text-xs text-slate-500 mt-0.5">Review all student submissions, proctoring violations, and results.</p>
        </div>

        <div>
            <a
                href="{{ route('admin.tests.attempts.export', request()->query()) }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export to Excel
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col lg:flex-row items-start lg:items-end gap-3">
        <form action="{{ route('admin.tests.attempts.index') }}" method="GET" class="w-full lg:w-56">
            <select name="test_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Tests</option>
                @foreach($tests as $test)
                    <option value="{{ $test->id }}" {{ request('test_id') == $test->id ? 'selected' : '' }}>
                        {{ $test->title }}
                    </option>
                @endforeach
            </select>
        </form>

        <form action="{{ route('admin.tests.attempts.index') }}" method="GET" class="w-full lg:w-48">
            <select name="level_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </form>

        <form action="{{ route('admin.tests.attempts.index') }}" method="GET" class="w-full lg:w-36">
            <select name="result" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Results</option>
                <option value="pass" {{ request('result') === 'pass' ? 'selected' : '' }}>Pass</option>
                <option value="fail" {{ request('result') === 'fail' ? 'selected' : '' }}>Fail</option>
                <option value="pending" {{ request('result') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </form>

        <form action="{{ route('admin.tests.attempts.index') }}" method="GET" class="w-full lg:w-36">
            <select name="submission_type" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="manual" {{ request('submission_type') === 'manual' ? 'selected' : '' }}>Manual</option>
                <option value="timeout" {{ request('submission_type') === 'timeout' ? 'selected' : '' }}>Timeout</option>
                <option value="tab_switch_violation" {{ request('submission_type') === 'tab_switch_violation' ? 'selected' : '' }}>Tab Switch</option>
            </select>
        </form>

        <div class="flex gap-2 w-full lg:w-auto">
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   placeholder="From" class="text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   placeholder="To" class="text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
            <button type="submit" class="px-3 py-2 bg-reiac-navy text-white text-xs font-bold rounded-xl hover:bg-slate-800 transition">
                Apply
            </button>
        </div>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Submissions</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ $attempts->total() }}</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Passed</div>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1.5">
                {{ $attempts->where('result', 'pass')->count() }}
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Failed</div>
            <div class="text-2xl font-extrabold text-rose-600 mt-1.5">
                {{ $attempts->where('result', 'fail')->count() }}
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Violations</div>
            <div class="text-2xl font-extrabold text-amber-600 mt-1.5">
                {{ $attempts->where('submission_type', '!=', 'manual')->count() }}
            </div>
        </div>
    </div>

    {{-- SUBMISSIONS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16">S.No.</th>
                        <th class="py-3.5 px-4">Student</th>
                        <th class="py-3.5 px-4">Test</th>
                        <th class="py-3.5 px-4">Level</th>
                        <th class="py-3.5 px-4">Score</th>
                        <th class="py-3.5 px-4">Result / Slab</th>
                        <th class="py-3.5 px-4">Submission</th>
                        <th class="py-3.5 px-4">Switched</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-500 font-mono">
                                    #{{ $attempt->id }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $attempt->user?->name ?? 'Unknown' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $attempt->user?->email ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $attempt->test?->title ?? '—' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $attempt->test?->testLevel?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $attempt->score_obtained }}</span>
                                <span class="text-[10px] text-slate-400">/ {{ $attempt->test?->total_marks ?? '?' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $slab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
                                @endphp
                                @if($slab)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $slab->badge_class }}">
                                        {{ $slab->name }}
                                    </span>
                                @elseif($attempt->result === 'pass')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">PASS</span>
                                @elseif($attempt->result === 'fail')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">FAIL</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">PENDING</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($attempt->submission_type === 'manual')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Manual</span>
                                @elseif($attempt->submission_type === 'timeout')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Timeout</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Tab Switch</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($attempt->tab_switch_count > 0)
                                    <span class="font-bold text-rose-600">{{ $attempt->tab_switch_count }}</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                {{ $attempt->submitted_at?->format('M d, Y H:i') ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.tests.attempts.show', $attempt) }}"
                                   class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                No submissions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
