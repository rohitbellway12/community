@extends('layouts.student')

@section('title', $test->title . ' - Examination Instructions')

@php
    $hasExhaustedAttempts = ($test->max_attempts > 0) && ($completedAttemptsCount >= $test->max_attempts);
    $hasActiveAttempt = isset($activeAttempt) && $activeAttempt;
@endphp

@section('content')
<div class="p-3 sm:p-6 lg:p-8 max-w-[960px] w-full mx-auto" x-data="{ agreed: false, isStarting: false }">

    {{-- ALERT MESSAGES --}}
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-5">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="break-words">{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-400 hover:text-rose-700 font-bold ml-3 shrink-0">✕</button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-2xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-5">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="break-words">{{ session('info') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-blue-400 hover:text-blue-700 font-bold ml-3 shrink-0">✕</button>
        </div>
    @endif

    {{-- ALREADY COMPLETED EXAM BANNER (IF ATTEMPTS EXHAUSTED) --}}
    @if($hasExhaustedAttempts && $latestAttempt)
        <div class="bg-gradient-to-r from-slate-900 to-[#0b1329] text-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl shadow-md mb-5 sm:mb-6 flex flex-col md:flex-row items-center justify-between gap-4 sm:gap-5 border border-slate-700">
            <div class="flex items-center gap-3.5 sm:gap-4 w-full md:w-auto">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $latestAttempt->result === 'pass' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-400 border border-rose-500/40' }} flex items-center justify-center text-xl sm:text-2xl font-black shrink-0">
                    {{ $latestAttempt->result === 'pass' ? '✓' : '✕' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap mb-1">
                        <span class="font-extrabold text-xs sm:text-sm text-white">Examination Completed</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $latestAttempt->result === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                            {{ strtoupper($latestAttempt->result) }} ({{ $latestAttempt->percentage }}%)
                        </span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-300">
                        Score: <strong class="text-white">{{ $latestAttempt->score_obtained }} / {{ $test->total_marks }}</strong> pts · {{ $latestAttempt->submitted_at?->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <a href="{{ route('tests.student.result', ['test' => $test, 'attempt' => $latestAttempt]) }}"
               class="w-full md:w-auto px-5 sm:px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-[#0b1329] font-extrabold text-xs uppercase tracking-wider transition shadow-sm shrink-0 flex items-center justify-center gap-2 cursor-pointer">
                <span>View Full Scorecard</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    @elseif($hasActiveAttempt)
        {{-- ONGOING EXAM NOTICE (IF ACTIVE) --}}
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-md mb-5 sm:mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 animate-spin text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-extrabold text-xs sm:text-sm">Examination Session In Progress!</h3>
                    <p class="text-[11px] text-amber-100 mt-0.5">Attempt #{{ $activeAttempt->attempt_number }} · Allowed until: {{ $activeAttempt->allowed_until?->format('h:i A') }}</p>
                </div>
            </div>
            <a href="{{ route('tests.student.take', ['test' => $test, 'attempt' => $activeAttempt]) }}"
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-[#0b1329] text-white hover:bg-slate-900 font-bold text-xs uppercase tracking-wider transition shadow-sm shrink-0 text-center">
                Resume Exam Now →
            </a>
        </div>
    @endif

    {{-- REAL EXAM ADMIT & INSTRUCTIONS CARD --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
        
        {{-- EXAM TITLE BANNER --}}
        <div class="bg-[#0b1329] text-white px-4 sm:px-6 py-5 sm:py-6 border-b border-slate-800">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-1.5 sm:gap-2 mb-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-400 text-[#0b1329]">
                            CBT Examination
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $test->testLevel->name ?? 'Proficiency Examination' }}
                        </span>

                        @if($hasExhaustedAttempts)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                Completed ({{ $completedAttemptsCount }}/{{ $test->max_attempts }})
                            </span>
                        @elseif($hasActiveAttempt)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                In Progress
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Attempt 1 of {{ $test->max_attempts > 0 ? $test->max_attempts : 'Unlimited' }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-lg sm:text-2xl font-black tracking-tight text-white break-words">{{ $test->title }}</h1>
                    <p class="text-xs text-slate-300 mt-1">Official Online Assessment Portal · System Verified Session</p>
                </div>

                {{-- DURATION STAMP --}}
                <div class="bg-slate-800/80 backdrop-blur-xs border border-slate-700 rounded-2xl px-4 py-2.5 sm:py-3 text-center w-full md:w-auto shrink-0 flex md:flex-col items-center justify-between md:justify-center gap-2">
                    <div>
                        <div class="text-[9px] sm:text-[10px] text-amber-400 font-bold uppercase tracking-wider">Exam Duration</div>
                        <div class="text-[9px] text-slate-400 md:block hidden mt-0.5">Strict Auto-Submit at 00:00</div>
                    </div>
                    <div class="text-xl sm:text-2xl font-black text-white">{{ $test->duration_minutes }} <span class="text-xs font-semibold text-slate-300">Min</span></div>
                </div>
            </div>
        </div>

        {{-- CANDIDATE DETAILS BAR --}}
        <div class="bg-slate-50 border-b border-slate-200/80 px-4 sm:px-6 py-3.5 sm:py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-amber-400 text-[#0b1329] font-black flex items-center justify-center text-xs shrink-0 shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Candidate Name</div>
                        <div class="font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Candidate ID / Username</div>
                    <div class="font-bold text-slate-800 truncate">{{ auth()->user()->username ?? ('USER-' . auth()->id()) }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Registered Email</div>
                    <div class="font-bold text-slate-800 truncate">{{ auth()->user()->email }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Candidate Attempt Status</div>
                    <div class="font-bold mt-0.5">
                        @if($hasExhaustedAttempts)
                            <span class="text-rose-600 font-extrabold">✓ Completed (1 of 1 Used)</span>
                        @elseif($hasActiveAttempt)
                            <span class="text-amber-600 font-extrabold">● Active In Progress</span>
                        @else
                            <span class="text-emerald-600 font-extrabold">Eligible (0 of {{ $test->max_attempts }} Used)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- EXAM SCHEME & METRICS --}}
        <div class="p-4 sm:p-6 border-b border-slate-200/80">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Examination Scheme & Marking Structure</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/70 p-3 sm:p-3.5 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Questions</div>
                    <div class="text-lg sm:text-xl font-extrabold text-[#0b1329] mt-0.5 sm:mt-1">{{ $test->questions->count() }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">MCQ Questions</div>
                </div>
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/70 p-3 sm:p-3.5 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Marks</div>
                    <div class="text-lg sm:text-xl font-extrabold text-slate-900 mt-0.5 sm:mt-1">{{ $test->total_marks }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Maximum Marks</div>
                </div>
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/70 p-3 sm:p-3.5 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Qualifying Cutoff</div>
                    <div class="text-lg sm:text-xl font-extrabold text-emerald-600 mt-0.5 sm:mt-1">{{ $test->passing_marks }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ round(($test->passing_marks / max(1, $test->total_marks)) * 100) }}% to Pass</div>
                </div>
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/70 p-3 sm:p-3.5 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Negative Marking</div>
                    <div class="text-lg sm:text-xl font-extrabold text-amber-600 mt-0.5 sm:mt-1">None</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">0 penalty for wrong</div>
                </div>
            </div>
        </div>

        {{-- CBT QUESTION PALETTE SYMBOLS & LEGEND --}}
        <div class="p-4 sm:p-6 border-b border-slate-200/80 bg-slate-50/50">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3">Computer-Based Test (CBT) Question Status Legend</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                <div class="flex items-center gap-2 sm:gap-2.5 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-6 h-6 rounded-md bg-emerald-600 text-white font-bold flex items-center justify-center text-[10px] shrink-0">✓</span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Answered</div>
                        <div class="text-[10px] text-slate-500 truncate">Option saved</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[10px] shrink-0">?</span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Not Answered</div>
                        <div class="text-[10px] text-slate-500 truncate">Visited but empty</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-6 h-6 rounded-md bg-amber-500 text-white font-bold flex items-center justify-center text-[10px] shrink-0">★</span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Marked for Review</div>
                        <div class="text-[10px] text-slate-500 truncate">Flagged to revisit</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 sm:gap-2.5 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-400 font-bold flex items-center justify-center text-[10px] shrink-0 border border-slate-300">○</span>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 truncate">Not Visited</div>
                        <div class="text-[10px] text-slate-500 truncate">Not yet opened</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CRITICAL EXAMINATION RULES --}}
        <div class="p-4 sm:p-6 space-y-3.5 sm:space-y-4 text-xs text-slate-700">
            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Critical Guidelines & Anti-Cheat Rules</h3>
            
            <div class="space-y-2.5 leading-relaxed">
                <div class="flex items-start gap-2.5 sm:gap-3">
                    <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-black flex items-center justify-center text-[10px] shrink-0 mt-0.5">1</div>
                    <p><strong class="text-slate-900">Strict Countdown Timer:</strong> The examination timer of exactly <strong>{{ $test->duration_minutes }} minutes</strong> will initiate immediately upon starting. The timer is synchronized with the central server and cannot be paused.</p>
                </div>

                <div class="flex items-start gap-2.5 sm:gap-3">
                    <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-black flex items-center justify-center text-[10px] shrink-0 mt-0.5">2</div>
                    <p><strong class="text-slate-900">Automatic Submission Engine:</strong> If you do not click "Submit Exam" before the countdown reaches <strong>00:00</strong>, the system will <strong>automatically freeze and submit</strong> all currently saved answers without loss of data.</p>
                </div>

                <div class="flex items-start gap-2.5 sm:gap-3">
                    <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-black flex items-center justify-center text-[10px] shrink-0 mt-0.5">3</div>
                    <p><strong class="text-slate-900">Live Answer Auto-Saving:</strong> Every time you select an option and click <strong>"Save & Next"</strong>, your response is instantly saved to the server. You can also click <strong>"Clear Response"</strong> if you wish to deselect an option.</p>
                </div>

                <div class="flex items-start gap-2.5 sm:gap-3">
                    <div class="w-5 h-5 rounded-full bg-rose-100 text-rose-800 font-black flex items-center justify-center text-[10px] shrink-0 mt-0.5">4</div>
                    <p><strong class="text-rose-700">Strict Screen Lock:</strong> Once the examination starts, switching browser tabs, minimizing the window, or changing the screen is strictly forbidden. <strong>Leaving the examination screen will immediately auto-submit your test</strong>.</p>
                </div>

                @if($test->instructions)
                    <div class="bg-amber-50/70 border border-amber-200/80 p-3.5 sm:p-4 rounded-2xl mt-3">
                        <div class="text-[11px] font-extrabold text-amber-900 uppercase tracking-wider mb-1">Administrator Specific Instructions:</div>
                        <p class="text-slate-800 leading-relaxed">{{ $test->instructions }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- CANDIDATE ACTION CONTAINER --}}
        @if($hasExhaustedAttempts && $latestAttempt)
            {{-- CASE 1: ALREADY COMPLETED (NO "READY TO BEGIN" BUTTON) --}}
            <div class="p-4 sm:p-6 bg-gradient-to-b from-slate-50 to-amber-50/30 border-t border-slate-200">
                <div class="bg-white rounded-2xl border border-amber-200/80 p-4 sm:p-5 shadow-xs mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex items-center gap-3 sm:gap-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $latestAttempt->result === 'pass' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center text-xl font-black shrink-0">
                            {{ $latestAttempt->result === 'pass' ? '✓' : '✕' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-wider">Examination Completed</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $latestAttempt->result === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ strtoupper($latestAttempt->result) }} ({{ $latestAttempt->percentage }}%)
                                </span>
                            </div>
                            <div class="text-xs text-slate-600 mt-1">
                                Score: <strong class="text-slate-900">{{ $latestAttempt->score_obtained }} / {{ $test->total_marks }}</strong> marks · Attempt {{ $completedAttemptsCount }} of {{ $test->max_attempts }} used · Submitted: {{ $latestAttempt->submitted_at?->format('d M Y, h:i A') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right shrink-0">
                        <span class="text-[11px] font-semibold text-slate-500 block">No Re-attempts Allowed</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <a href="{{ route('tests.student.index') }}"
                       class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition text-center">
                        ← Back to Test Portal
                    </a>

                    <a href="{{ route('tests.student.result', ['test' => $test, 'attempt' => $latestAttempt]) }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 rounded-xl text-[#0b1329] bg-amber-400 hover:bg-amber-500 font-extrabold text-xs uppercase tracking-wider transition shadow-md cursor-pointer hover:shadow-lg text-center">
                        <span>View Detailed Scorecard & Answers</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        @elseif($hasActiveAttempt)
            {{-- CASE 2: ACTIVE ATTEMPT ONGOING (RESUME BUTTON) --}}
            <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <a href="{{ route('tests.student.index') }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition text-center">
                        ← Exit to Test Portal
                    </a>

                    <a href="{{ route('tests.student.take', ['test' => $test, 'attempt' => $activeAttempt]) }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 rounded-xl text-white bg-amber-500 hover:bg-amber-600 font-extrabold text-xs uppercase tracking-wider transition shadow-md cursor-pointer text-center">
                        <span>Resume Ongoing Examination</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        @else
            {{-- CASE 3: FRESH STUDENT (DECLARATION & "I AM READY TO BEGIN") --}}
            <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200">
                <label class="flex items-start gap-2.5 sm:gap-3 cursor-pointer select-none">
                    <input type="checkbox"
                           x-model="agreed"
                           class="mt-1 w-4 h-4 rounded text-amber-500 border-slate-300 focus:ring-amber-400 cursor-pointer shrink-0">
                    <span class="text-xs text-slate-700 leading-relaxed font-medium">
                        I have read and understood all the above instructions and examination regulations. I confirm that my internet connectivity and computer system are stable. I understand that the <strong class="text-slate-900">{{ $test->duration_minutes }}-minute countdown timer</strong> will commence immediately and will auto-submit when the duration finishes.
                    </span>
                </label>

                <div class="mt-5 sm:mt-6 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 sm:gap-4">
                    <a href="{{ route('tests.student.index') }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition text-center">
                        ← Exit to Test Portal
                    </a>

                    <form action="{{ route('tests.student.start', $test) }}" method="POST" @submit="isStarting = true" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit"
                                :disabled="!agreed || isStarting"
                                :class="{ 'opacity-50 cursor-not-allowed bg-slate-400': !agreed || isStarting, 'bg-[#0b1329] hover:bg-slate-900 shadow-md': agreed && !isStarting }"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3.5 rounded-xl text-white font-extrabold text-xs uppercase tracking-wider transition cursor-pointer text-center">
                            <template x-if="isStarting">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span>Initiating Examination...</span>
                                </span>
                            </template>
                            <template x-if="!isStarting">
                                <span class="inline-flex items-center gap-2">
                                    <span>I am Ready to Begin</span>
                                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>

</div>
@endsection