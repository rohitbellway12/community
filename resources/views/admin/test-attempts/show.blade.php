@extends('layouts.admin')

@section('title', 'Submission Detail')

@section('content')
<div class="p-6 max-w-[1200px] w-full mx-auto space-y-6">

    {{-- BACK --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.tests.attempts.index') }}"
           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            ← Back
        </a>
        <div class="flex items-center gap-3">
            @if($attempt->submission_type === 'tab_switch_violation')
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                    ⚠ Auto-Submitted (Tab Switch Violation)
                </span>
            @elseif($attempt->submission_type === 'timeout')
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                    ⏱ Auto-Submitted (Timeout)
                </span>
            @else
                <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    Manual Submission
                </span>
            @endif
        </div>
    </div>

    @php
        $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
    @endphp

    {{-- INFO GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student</div>
            <div class="text-sm font-bold text-slate-900 mt-1.5">{{ $attempt->user?->name ?? 'Unknown' }}</div>
            <div class="text-[11px] text-slate-400">{{ $attempt->user?->email ?? '' }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Test</div>
            <div class="text-sm font-bold text-slate-900 mt-1.5">{{ $attempt->test?->title ?? '—' }}</div>
            <div class="text-[11px] text-slate-400">{{ $attempt->test?->testLevel?->name ?? '' }}</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Score</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ $attempt->score_obtained }}</div>
            <div class="text-[11px] text-slate-400">/ {{ $attempt->test?->total_marks ?? '?' }} ({{ $attempt->percentage }}%)</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Result</div>
            <div class="text-2xl font-extrabold mt-1.5">
                @if($attempt->result === 'pass')
                    <span class="text-emerald-600">PASS</span>
                @elseif($attempt->result === 'fail')
                    <span class="text-rose-600">FAIL</span>
                @else
                    <span class="text-slate-500">PENDING</span>
                @endif
            </div>
        </div>
        {{-- RESULT SLAB (GRADE) --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Result Slab / Grade</div>
            @if($matchedSlab)
                <div class="mt-1.5 flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold border {{ $matchedSlab->badge_class }}">
                        {{ $matchedSlab->name }}
                    </span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1.5 font-semibold">
                    Range: <span class="font-mono text-slate-700">{{ (float)$matchedSlab->min_marks }} - {{ (float)$matchedSlab->max_marks }} pts</span>
                </div>
                @if($matchedSlab->description)
                    <div class="text-[10px] text-slate-400 mt-0.5 truncate">{{ $matchedSlab->description }}</div>
                @endif
            @else
                <div class="text-xs font-semibold text-slate-400 mt-2">No slab assigned</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Score: {{ (float)$attempt->score_obtained }} pts</div>
            @endif
        </div>
    </div>

    {{-- TIME INFO --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-3">Time Details</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-semibold">Started:</span>
                <span class="font-bold text-slate-800">{{ $attempt->started_at?->format('M d, Y H:i:s') ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold">Submitted:</span>
                <span class="font-bold text-slate-800">{{ $attempt->submitted_at?->format('M d, Y H:i:s') ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold">Allowed Until:</span>
                <span class="font-bold text-slate-800">{{ $attempt->allowed_until?->format('M d, Y H:i:s') ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-semibold">Duration:</span>
                <span class="font-bold text-slate-800">{{ $attempt->test?->duration_minutes }} min</span>
            </div>
        </div>
    </div>

    {{-- ANSWER BREAKDOWN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-3">Answer Breakdown</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div class="p-3 bg-emerald-50 rounded-xl">
                <div class="text-xl font-extrabold text-emerald-600">{{ $attempt->correct_count }}</div>
                <div class="text-[10px] font-bold text-emerald-700 uppercase">Correct</div>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl">
                <div class="text-xl font-extrabold text-rose-600">{{ $attempt->incorrect_count }}</div>
                <div class="text-[10px] font-bold text-rose-700 uppercase">Incorrect</div>
            </div>
            <div class="p-3 bg-slate-100 rounded-xl">
                <div class="text-xl font-extrabold text-slate-600">{{ $attempt->unanswered_count }}</div>
                <div class="text-[10px] font-bold text-slate-500 uppercase">Unanswered</div>
            </div>
            <div class="p-3 bg-amber-50 rounded-xl">
                <div class="text-xl font-extrabold text-amber-600">{{ $attempt->tab_switch_count }}</div>
                <div class="text-[10px] font-bold text-amber-700 uppercase">Switches</div>
            </div>
        </div>
    </div>

    {{-- DETAILED ANSWERS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Student Responses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-16">#</th>
                        <th class="py-3 px-4">Question</th>
                        <th class="py-3 px-4">Student Answer</th>
                        <th class="py-3 px-4">Correct Answer</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Marks</th>
                        <th class="py-3 px-4">Answered At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($attempt->answers as $idx => $answer)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs">
                                <div class="line-clamp-2">{{ $answer->question?->question_text ?? '—' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($answer->option)
                                    <span class="font-medium text-slate-800">{{ $answer->option->option_label }}. {{ Str::limit($answer->option->option_text, 60) }}</span>
                                @elseif($answer->selected_option_id)
                                    <span class="text-slate-400">(deleted)</span>
                                @else
                                    <span class="text-slate-400 italic">Not answered</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $correctOption = $answer->question?->options->firstWhere('is_correct', true);
                                @endphp
                                @if($correctOption)
                                    <span class="font-medium text-emerald-700">{{ $correctOption->option_label }}. {{ Str::limit($correctOption->option_text, 60) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($answer->is_correct)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✅ Correct</span>
                                @elseif($answer->selected_option_id)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">❌ Wrong</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">⚠ Skipped</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $answer->marks_obtained }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                {{ $answer->answered_at?->format('H:i:s') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">No answers recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
