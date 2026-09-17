@extends('layouts.student')

@section('title', $test->title . ' - Examination Scorecard')

@section('content')
<div class="p-3 sm:p-6 lg:p-8 max-w-[1020px] w-full mx-auto" x-data="{ filter: 'all' }">

    {{-- PRINT HEADER (ONLY VISIBLE ON OFFICIAL PRINT / PDF) --}}
    <div class="hidden print:block text-center border-b-2 border-slate-900 pb-5 mb-8">
        <div class="text-[11px] font-black tracking-widest text-slate-500 uppercase">Official Korean Language Assessment Center</div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Official Examination Performance Scorecard</h1>
        <p class="text-xs text-slate-600 mt-0.5">CERTIFICATE OF EXAMINATION RESULT & CANDIDATE REPORT</p>
        <div class="text-[10px] text-slate-400 mt-2 font-mono">Verification Ref: KOR-{{ date('Y') }}-{{ str_pad($attempt->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="print:hidden bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs mb-4 sm:mb-6">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate">{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold ml-3 cursor-pointer shrink-0">✕</button>
        </div>
    @endif

    @if(session('info'))
        <div class="print:hidden bg-amber-50 border border-amber-200 text-amber-900 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs mb-4 sm:mb-6">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="truncate">{{ session('info') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-amber-500 hover:text-amber-800 font-bold ml-3 cursor-pointer shrink-0">✕</button>
        </div>
    @endif

    {{-- MAIN SCORECARD CONTAINER --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        
        {{-- HEADER BANNER & PASS/FAIL SEAL --}}
        @php
            $isPass = $attempt->result === 'pass';
            $passingPercentage = round(($test->passing_marks / max(1, $test->total_marks)) * 100);
            $accuracy = $attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0;
            $scoreProgress = min(100, max(0, round(($attempt->score_obtained / max(1, $test->total_marks)) * 100)));
        @endphp

        <div class="bg-[#0b1329] text-white p-5 sm:p-8 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-amber-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6 relative z-10">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-[#0b1329]">
                            Official Scorecard
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700 truncate max-w-full">
                            {{ $test->testLevel->name ?? 'Korean Proficiency Assessment' }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            Attempt #{{ $attempt->attempt_number }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight text-white leading-snug break-words">{{ $test->title }}</h1>
                    <p class="text-xs text-slate-300 mt-2 flex items-center gap-x-3 gap-y-1 flex-wrap">
                        <span>Submitted At: <strong class="text-white">{{ $attempt->submitted_at?->format('M d, Y · h:i A') ?? 'N/A' }}</strong></span>
                        <span class="text-slate-500 hidden sm:inline">|</span>
                        <span class="font-mono text-[11px] text-slate-400">Ref: KOR-{{ date('Y') }}-{{ str_pad($attempt->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </p>
                </div>

                {{-- BIG RESULT SEAL --}}
                <div class="w-full md:w-auto shrink-0 flex items-center justify-start md:justify-end">
                    @if($isPass)
                        <div class="w-full md:w-auto inline-flex items-center gap-3 sm:gap-3.5 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl bg-emerald-500/20 border-2 border-emerald-400 text-emerald-300 shadow-xl shadow-emerald-950/50">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-xl sm:text-2xl shrink-0 shadow-md">
                                ✓
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-emerald-400">FINAL RESULT</div>
                                <div class="text-lg sm:text-2xl font-black tracking-wider text-white truncate">QUALIFIED (PASS)</div>
                                <div class="text-[10px] text-emerald-300/90 font-medium truncate">Passed Qualifying Cutoff</div>
                            </div>
                        </div>
                    @else
                        <div class="w-full md:w-auto inline-flex items-center gap-3 sm:gap-3.5 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl bg-rose-500/20 border-2 border-rose-400 text-rose-300 shadow-xl shadow-rose-950/50">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-rose-500 text-white flex items-center justify-center font-black text-xl sm:text-2xl shrink-0 shadow-md">
                                ✕
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-rose-400">FINAL RESULT</div>
                                <div class="text-lg sm:text-2xl font-black tracking-wider text-white truncate">NOT QUALIFIED (FAIL)</div>
                                <div class="text-[10px] text-rose-300/90 font-medium truncate">Below Cutoff (Re-attempt Required)</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CANDIDATE & EXAMINATION METADATA BAR --}}
        <div class="bg-slate-50 border-b border-slate-200/80 px-4 sm:px-8 py-3.5 sm:py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 text-xs">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-[#0b1329] font-black flex items-center justify-center text-xs shrink-0 shadow-2xs">
                        {{ strtoupper(substr($attempt->user->name ?? 'C', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Candidate Name</div>
                        <div class="font-extrabold text-slate-900 truncate mt-0.5">{{ $attempt->user->name ?? auth()->user()->name }}</div>
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Candidate ID / Username</div>
                    <div class="font-bold text-slate-800 truncate mt-0.5">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</div>
                </div>

                <div class="min-w-0">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Submission Mode</div>
                    <div class="font-bold mt-0.5 truncate">
                        @if($attempt->submission_type === 'timeout')
                            <span class="text-amber-700 font-extrabold">⏱ Auto-submitted upon timeout</span>
                        @elseif($attempt->submission_type === 'tab_switch_violation')
                            <span class="text-rose-700 font-extrabold">⚠ Auto-submitted due to tab switch</span>
                        @else
                            <span class="text-emerald-700 font-extrabold">✓ Submitted manually by candidate</span>
                        @endif
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Tab Switches Detected</div>
                    <div class="font-bold text-slate-800 mt-0.5">
                        @if(($attempt->tab_switch_count ?? 0) > 0)
                            <span class="text-rose-600 font-bold">{{ $attempt->tab_switch_count }} violation(s) recorded</span>
                        @else
                            <span class="text-emerald-600 font-bold">0 (Compliant)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- VISUAL PASSING CUTOFF PROGRESS METER --}}
        <div class="px-4 sm:px-8 py-4 sm:py-5 border-b border-slate-200/80 bg-gradient-to-r from-slate-50/50 via-white to-slate-50/50">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4 mb-2 text-xs">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Cutoff Achievement Gauge</span>
                    <span class="text-slate-900 font-bold ml-1 sm:ml-2">{{ $attempt->score_obtained }} / {{ $test->total_marks }} marks</span>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-500">Passing Cutoff:</span>
                    <strong class="text-emerald-700 font-extrabold text-xs sm:text-sm ml-1">{{ $test->passing_marks }} marks ({{ $passingPercentage }}% or higher)</strong>
                </div>
            </div>

            {{-- PROGRESS TRACK --}}
            <div class="relative w-full h-3.5 sm:h-4 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                {{-- PASSING THRESHOLD MARKER LINE --}}
                <div class="absolute top-0 bottom-0 z-20 w-0.5 bg-slate-700 pointer-events-none" style="left: {{ $passingPercentage }}%;"></div>

                {{-- FILL BAR --}}
                <div class="h-full rounded-full transition-all duration-700 {{ $isPass ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' : 'bg-gradient-to-r from-rose-500 to-rose-600' }}"
                     style="width: {{ $scoreProgress }}%;"></div>
            </div>

            <div class="flex items-center justify-between text-[9px] sm:text-[10px] text-slate-500 mt-1.5 font-semibold">
                <span>0 marks (Min)</span>
                <span class="text-slate-700 font-bold flex items-center gap-1">
                    <span>▲</span> Cutoff: {{ $test->passing_marks }} marks ({{ $passingPercentage }}%)
                </span>
                <span>{{ $test->total_marks }} marks (Max)</span>
            </div>
        </div>

        {{-- KEY PERFORMANCE METRICS GRID --}}
        <div class="p-4 sm:p-8 border-b border-slate-200/80">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3 sm:mb-4">Performance Breakdown</h3>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-4">
                {{-- MARKS --}}
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Score Obtained</div>
                    <div class="text-xl sm:text-3xl font-black text-[#0b1329] mt-0.5 sm:mt-1">{{ $attempt->score_obtained }}</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Out of {{ $test->total_marks }} marks</div>
                </div>

                {{-- PERCENTAGE --}}
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Percentage</div>
                    <div class="text-xl sm:text-3xl font-black {{ $isPass ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5 sm:mt-1">
                        {{ $attempt->percentage }}%
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Cutoff: {{ $passingPercentage }}%+</div>
                </div>

                {{-- ACCURACY --}}
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Accuracy Rate</div>
                    <div class="text-xl sm:text-3xl font-black text-amber-500 mt-0.5 sm:mt-1">{{ $accuracy }}%</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Correct vs attempted</div>
                </div>

                {{-- QUESTIONS ANSWERED --}}
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Questions Answered</div>
                    <div class="text-xl sm:text-3xl font-black text-slate-800 mt-0.5 sm:mt-1">{{ $attempt->answered_count }}</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Out of {{ $attempt->total_questions }} Qs</div>
                </div>
            </div>

            {{-- SECONDARY METRICS (CORRECT, WRONG, SKIPPED) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 mt-3">
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-sm sm:text-base shrink-0 shadow-2xs">✓</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[9px] sm:text-[10px] font-bold uppercase text-emerald-800 tracking-wider">Correct Answers</div>
                        <div class="text-base sm:text-lg font-extrabold text-emerald-950 mt-0.5">{{ $attempt->correct_count }} <span class="text-xs font-semibold text-emerald-700">Questions</span></div>
                    </div>
                </div>

                <div class="bg-rose-50/70 border border-rose-200/80 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-600 text-white font-black flex items-center justify-center text-sm sm:text-base shrink-0 shadow-2xs">✕</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[9px] sm:text-[10px] font-bold uppercase text-rose-800 tracking-wider">Incorrect Answers</div>
                        <div class="text-base sm:text-lg font-extrabold text-rose-950 mt-0.5">{{ $attempt->incorrect_count }} <span class="text-xs font-semibold text-rose-700">Questions</span></div>
                    </div>
                </div>

                <div class="bg-slate-100 border border-slate-200 rounded-xl sm:rounded-2xl p-3 sm:p-3.5 flex items-center gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-300 text-slate-700 font-black flex items-center justify-center text-sm sm:text-base shrink-0 shadow-2xs">○</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[9px] sm:text-[10px] font-bold uppercase text-slate-600 tracking-wider">Skipped / Unanswered</div>
                        <div class="text-base sm:text-lg font-extrabold text-slate-800 mt-0.5">{{ $attempt->unanswered_count }} <span class="text-xs font-semibold text-slate-600">Questions</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAILED QUESTION-BY-QUESTION REVIEW SECTION --}}
        <div class="p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Detailed Question Analysis & Solutions</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Review your chosen answers compared against standard correct keys and explanations.</p>
                </div>

                {{-- FILTER TABS --}}
                <div class="print:hidden flex items-center gap-1 bg-slate-100 p-1 rounded-xl sm:rounded-2xl text-xs font-bold overflow-x-auto w-full sm:w-auto scrollbar-none">
                    <button type="button"
                            @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs">
                        All ({{ $attempt->answers->count() }})
                    </button>
                    <button type="button"
                            @click="filter = 'correct'"
                            :class="filter === 'correct' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs">
                        Correct ({{ $attempt->correct_count }})
                    </button>
                    <button type="button"
                            @click="filter = 'incorrect'"
                            :class="filter === 'incorrect' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs">
                        Incorrect ({{ $attempt->incorrect_count }})
                    </button>
                    <button type="button"
                            @click="filter = 'unanswered'"
                            :class="filter === 'unanswered' ? 'bg-slate-700 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-2.5 sm:px-3 py-1.5 rounded-lg sm:rounded-xl transition cursor-pointer whitespace-nowrap text-[11px] sm:text-xs">
                        Unanswered ({{ $attempt->unanswered_count }})
                    </button>
                </div>
            </div>

            {{-- QUESTIONS LIST --}}
            <div class="space-y-3.5 sm:space-y-4">
                @foreach($attempt->answers as $idx => $answer)
                    @php
                        $question = $answer->question;
                        $correctOption = $question?->options->firstWhere('is_correct', true);
                        $userOption = $question?->options->firstWhere('id', $answer->selected_option_id);
                        $status = $answer->is_correct ? 'correct' : ($answer->selected_option_id ? 'incorrect' : 'unanswered');
                    @endphp

                    <div x-show="filter === 'all' || filter === '{{ $status }}'"
                         class="rounded-xl sm:rounded-2xl border p-3.5 sm:p-5 transition-all {{ $answer->is_correct ? 'bg-emerald-50/20 border-emerald-200/80' : ($answer->selected_option_id ? 'bg-rose-50/20 border-rose-200/80' : 'bg-slate-50/50 border-slate-200') }}">
                        
                        {{-- QUESTION TOP BAR --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-black text-xs flex items-center justify-center shadow-2xs {{ $answer->is_correct ? 'bg-emerald-600 text-white' : ($answer->selected_option_id ? 'bg-rose-600 text-white' : 'bg-slate-300 text-slate-700') }}">
                                    Q{{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-slate-900">Question {{ $idx + 1 }}</span>
                                    <span class="text-[11px] text-slate-400 ml-1">· Marks: {{ $question->marks ?? 1 }} (Awarded: <strong>{{ $answer->marks_obtained }}</strong>)</span>
                                </div>
                            </div>

                            <div class="self-start sm:self-auto">
                                @if($answer->is_correct)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        ✓ Correct (+{{ $answer->marks_obtained }})
                                    </span>
                                @elseif($answer->selected_option_id)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 shadow-2xs">
                                        ✕ Incorrect (0)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-slate-200 text-slate-700 border border-slate-300">
                                        ○ Unanswered (0)
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- QUESTION TEXT --}}
                        <div class="text-xs sm:text-sm font-black text-slate-900 mb-3 sm:mb-4 pl-0 sm:pl-10 leading-relaxed">
                            {{ $question->question_text ?? 'Question text not available.' }}
                        </div>

                        {{-- OPTIONS COMPARISON --}}
                        <div class="pl-0 sm:pl-10 space-y-2 sm:space-y-2.5 max-w-3xl mb-3">
                            @foreach(($question->options ?? []) as $option)
                                @php
                                    $isThisCorrect = $option->is_correct;
                                    $isThisUserChoice = $answer->selected_option_id == $option->id;
                                @endphp

                                <div class="p-3 sm:p-3.5 rounded-xl border text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-3 transition
                                    @if($isThisCorrect && $isThisUserChoice)
                                        bg-emerald-100/80 border-emerald-400 text-emerald-950 font-bold shadow-2xs
                                    @elseif($isThisCorrect)
                                        bg-emerald-50/90 border-emerald-300 text-emerald-900 font-bold
                                    @elseif($isThisUserChoice)
                                        bg-rose-100/80 border-rose-400 text-rose-950 font-bold shadow-2xs
                                    @else
                                        bg-white border-slate-200 text-slate-600
                                    @endif
                                ">
                                    <div class="flex items-start sm:items-center gap-2.5 min-w-0 flex-1">
                                        <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg flex items-center justify-center font-black text-[11px] sm:text-xs shrink-0 mt-0.5 sm:mt-0
                                            @if($isThisCorrect) bg-emerald-600 text-white
                                            @elseif($isThisUserChoice) bg-rose-600 text-white
                                            @else bg-slate-100 text-slate-600 border border-slate-200
                                            @endif
                                        ">
                                            {{ $option->option_label }}
                                        </span>
                                        <span class="text-xs leading-snug break-words">{{ $option->option_text }}</span>
                                    </div>

                                    <div class="shrink-0 self-end sm:self-auto">
                                        @if($isThisCorrect && $isThisUserChoice)
                                            <span class="text-[9px] sm:text-[10px] font-black uppercase px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-emerald-600 text-white shadow-2xs flex items-center gap-1">
                                                <span>✓</span> Your Choice (Correct)
                                            </span>
                                        @elseif($isThisCorrect)
                                            <span class="text-[9px] sm:text-[10px] font-black uppercase px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-emerald-600 text-white shadow-2xs flex items-center gap-1">
                                                <span>✓</span> Correct Key
                                            </span>
                                        @elseif($isThisUserChoice)
                                            <span class="text-[9px] sm:text-[10px] font-black uppercase px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md bg-rose-600 text-white shadow-2xs flex items-center gap-1">
                                                <span>✕</span> Your Choice (Incorrect)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- EXPLANATION BOX (IF AVAILABLE) --}}
                        @if($question && $question->explanation)
                            <div class="ml-0 sm:ml-10 mt-2.5 sm:mt-3 p-3 sm:p-4 bg-amber-50/70 border border-amber-200/80 rounded-xl sm:rounded-2xl text-xs">
                                <div class="flex items-center gap-1.5 text-amber-900 font-extrabold uppercase text-[10px] sm:text-[11px] tracking-wider mb-1">
                                    <span>💡</span>
                                    <span>Explanation & Solution:</span>
                                </div>
                                <p class="text-slate-800 leading-relaxed font-medium text-xs">{{ $question->explanation }}</p>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

        </div>

        {{-- ACTION FOOTER --}}
        <div class="print:hidden p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <a href="{{ route('tests.student.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition text-center w-full sm:w-auto">
                ← Back to Test Portal
            </a>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-800 hover:bg-slate-100 font-bold text-xs transition shadow-2xs cursor-pointer w-full sm:w-auto">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Print Scorecard / Save PDF</span>
                </button>
            </div>
        </div>

    </div>

</div>
@endsection