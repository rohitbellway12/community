@extends('layouts.student')

@section('title', $test->title . ' - Examination Scorecard & Certificate')

@section('content')
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 6mm;
        }
        html, body {
            background: #ffffff !important;
            color: #0b1329 !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:block {
            display: block !important;
        }
        .cert-print-card {
            border: 3px double #0b1329 !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
            page-break-before: avoid !important;
            border-radius: 0 !important;
            width: 100% !important;
            margin: 0 !important;
        }
    }
</style>

@php
    $isPass = $attempt->result === 'pass';
    $passingPercentage = round(($test->passing_marks / max(1, $test->total_marks)) * 100);
    $accuracy = $attempt->answered_count > 0 ? round(($attempt->correct_count / $attempt->answered_count) * 100, 1) : 0;
    $scoreProgress = min(100, max(0, round(($attempt->score_obtained / max(1, $test->total_marks)) * 100)));
    $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
    $certNumber = 'REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT);
    $issueDate = $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y');
@endphp

{{-- =========================================================================
     1. OFFICIAL 1-PAGE CERTIFICATE (PRINT / PDF ONLY)
     ========================================================================= --}}
<div class="hidden print:block w-full">
    <div class="cert-print-card bg-white border-4 border-[#0b1329] p-3 relative text-[#0b1329]">
        <div class="border-2 border-amber-500/70 p-6 sm:p-8 relative bg-gradient-to-b from-amber-50/20 via-white to-amber-50/10">

            {{-- CORNER ORNAMENTS --}}
            <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-amber-600"></div>
            <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-amber-600"></div>
            <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-amber-600"></div>
            <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-amber-600"></div>

            {{-- WATERMARK SEAL --}}
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none select-none">
                <svg class="w-96 h-96 text-[#0b1329]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>

            {{-- INSTITUTION HEADER --}}
            <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-200 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-[#0b1329] text-amber-400 flex items-center justify-center font-black text-xl shadow-md shrink-0 border border-amber-400/40">
                        R
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-wider text-[#0b1329] uppercase">
                            REIAC Test Assessment Center
                        </div>
                        <div class="text-[11px] font-bold tracking-widest text-slate-500 uppercase">
                            Global Korean Language & Educational Proficiency
                        </div>
                    </div>
                </div>

                <div class="text-right text-[11px] font-medium text-slate-500 shrink-0">
                    <div>Certificate Ref: <strong class="font-mono text-slate-900 font-bold">{{ $certNumber }}</strong></div>
                    <div>Date of Issue: <strong class="text-slate-800 font-bold">{{ $issueDate }}</strong></div>
                </div>
            </div>

            {{-- CERTIFICATE TITLE & CANDIDATE DEDICATION --}}
            <div class="text-center my-6 relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-amber-100/70 border border-amber-300 text-amber-900 text-[10px] font-black uppercase tracking-widest mb-2">
                    ★ Official Academic Credential ★
                </div>

                <h1 class="text-3xl font-black uppercase tracking-wider text-[#0b1329]">
                    Certificate of Achievement
                </h1>
                <p class="text-xs text-slate-500 font-medium italic mt-1">
                    This is to officially certify that
                </p>

                {{-- CANDIDATE NAME --}}
                <div class="my-4">
                    <div class="inline-block border-b-2 border-[#0b1329] pb-1 px-10">
                        <span class="text-3xl font-black text-[#0b1329] tracking-wide uppercase">
                            {{ $attempt->user->name ?? 'Candidate' }}
                        </span>
                    </div>
                    <div class="text-[11px] font-bold text-slate-500 mt-1">
                        Candidate ID / Username: <span class="font-mono text-slate-800">{{ $attempt->user->username ?? ('ID-' . $attempt->user_id) }}</span>
                    </div>
                </div>

                <p class="text-xs text-slate-600 font-medium max-w-xl mx-auto leading-relaxed">
                    has successfully appeared and completed the official examination for
                </p>

                {{-- TEST TITLE & LEVEL --}}
                <div class="mt-1.5">
                    <div class="text-xl font-black text-[#0b1329] tracking-tight">
                        {{ $test->title }}
                    </div>
                    <div class="inline-block px-3 py-0.5 mt-1 rounded-md bg-slate-100 border border-slate-200 text-[11px] font-bold text-slate-700">
                        {{ $test->testLevel->name ?? 'Proficiency Assessment' }} • Attempt #{{ $attempt->attempt_number }}
                    </div>
                </div>
            </div>

            {{-- SCORE & PERFORMANCE CREDENTIALS --}}
            <div class="grid grid-cols-4 gap-3 my-6 relative z-10">
                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Score Obtained</div>
                    <div class="text-2xl font-black text-[#0b1329] mt-0.5">
                        {{ $attempt->score_obtained }} <span class="text-xs font-bold text-slate-400">/ {{ $test->total_marks }}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Total Marks</div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Percentage</div>
                    <div class="text-2xl font-black {{ $isPass ? 'text-emerald-700' : 'text-slate-800' }} mt-0.5">
                        {{ $attempt->percentage }}%
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">Cutoff: {{ $passingPercentage }}%</div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200 text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Accuracy</div>
                    <div class="text-2xl font-black text-amber-600 mt-0.5">
                        {{ $accuracy }}%
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $attempt->correct_count }} of {{ $attempt->answered_count }} Correct</div>
                </div>

                <div class="bg-white p-3 rounded-xl border {{ $isPass ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200' }} text-center shadow-2xs">
                    <div class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Result Standing</div>
                    <div class="text-lg font-black {{ $isPass ? 'text-emerald-800' : 'text-rose-700' }} mt-0.5 truncate">
                        @if($matchedSlab)
                            {{ $matchedSlab->name }}
                        @elseif($isPass)
                            QUALIFIED (PASS)
                        @else
                            COMPLETED
                        @endif
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold mt-0.5">
                        {{ $isPass ? 'Qualifying Standard Met' : 'Assessment Completed' }}
                    </div>
                </div>
            </div>

            {{-- SIGNATURES & OFFICIAL SECURITY SEAL --}}
            <div class="pt-6 border-t border-slate-200 relative z-10 flex items-center justify-between gap-4">
                <div class="text-left">
                    <div class="font-serif italic text-lg font-bold text-slate-800 tracking-wider">
                        Kang Min-Seok
                    </div>
                    <div class="w-40 border-b border-slate-400 my-1"></div>
                    <div class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wide">
                        Controller of Examinations
                    </div>
                    <div class="text-[9px] text-slate-500 font-medium">
                        REIAC Assessment Council
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center shrink-0">
                    <div class="w-20 h-20 rounded-full border-4 border-amber-400 bg-gradient-to-br from-amber-300 via-amber-400 to-amber-500 text-[#0b1329] p-1 flex flex-col items-center justify-center shadow-md text-center select-none">
                        <div class="w-full h-full rounded-full border border-amber-600/40 flex flex-col items-center justify-center p-1">
                            <span class="text-[8px] font-black uppercase tracking-widest leading-none">REIAC</span>
                            <span class="text-xs my-0.5">★</span>
                            <span class="text-[7px] font-black uppercase tracking-wider leading-none">VERIFIED</span>
                            <span class="text-[6px] font-bold tracking-tighter opacity-80 mt-0.5">CERTIFICATE</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold font-mono text-slate-500 mt-1">{{ $certNumber }}</span>
                </div>

                <div class="text-right">
                    <div class="font-serif italic text-lg font-bold text-slate-800 tracking-wider">
                        Dr. Rajesh Sharma
                    </div>
                    <div class="w-40 border-b border-slate-400 my-1 ml-auto"></div>
                    <div class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wide">
                        Academic Director
                    </div>
                    <div class="text-[9px] text-slate-500 font-medium">
                        Global Education Board
                    </div>
                </div>
            </div>

            {{-- VERIFICATION FOOTER NOTICE --}}
            <div class="mt-5 pt-3 border-t border-slate-100 text-center text-[9px] text-slate-400 font-medium">
                This is an official system-verified electronic certificate issued by REIAC Test Assessment Center.
                Authenticity can be verified at: <span class="font-mono text-slate-600 font-bold">{{ url('/community/tests/student/' . $test->id . '/result/' . $attempt->id) }}</span>
            </div>

        </div>
    </div>
</div>

{{-- =========================================================================
     2. ON-SCREEN RESULT & QUESTION ANALYSIS VIEW (HIDDEN ON PRINT)
     ========================================================================= --}}
<div class="print:hidden p-3 sm:p-6 lg:p-8 max-w-[1020px] w-full mx-auto" x-data="{ filter: 'all' }">

    {{-- TOP CONTROLS & FLASH MESSAGES --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <a href="{{ route('tests.student.index') }}"
           class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Test Portal</span>
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('community.index') }}"
               class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-2xs">
                Community Home
            </a>

            <button type="button"
                    onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#0b1329] to-indigo-950 text-amber-300 hover:text-white hover:from-slate-900 hover:to-indigo-900 font-black text-xs transition shadow-md hover:shadow-lg cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Print Certificate / Save PDF</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl text-xs sm:text-sm font-bold flex items-center justify-between shadow-xs mb-4 sm:mb-6">
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="truncate">{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold ml-3 cursor-pointer shrink-0">✕</button>
        </div>
    @endif

    {{-- MAIN SCORECARD CONTAINER --}}
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-6">

        {{-- HEADER BANNER & RESULT BADGE --}}
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
                        <span class="font-mono text-[11px] text-slate-400">Ref: {{ $certNumber }}</span>
                    </p>
                </div>

                {{-- RESULT SEAL / SLAB --}}
                <div class="w-full md:w-auto shrink-0 flex items-center justify-start md:justify-end">
                    @if($matchedSlab)
                        <div class="w-full md:w-auto inline-flex items-center gap-3 sm:gap-3.5 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl bg-amber-400/20 border-2 border-amber-400 text-amber-300 shadow-xl shadow-amber-950/50">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-400 text-[#0b1329] flex items-center justify-center font-black text-xl sm:text-2xl shrink-0 shadow-md">
                                ★
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-amber-300">RESULT GRADE / SLAB</div>
                                <div class="text-lg sm:text-2xl font-black tracking-wider text-white truncate">{{ $matchedSlab->name }}</div>
                                <div class="text-[10px] text-amber-200/90 font-medium truncate">Range: {{ (float)$matchedSlab->min_marks }} - {{ (float)$matchedSlab->max_marks }} marks</div>
                            </div>
                        </div>
                    @elseif($isPass)
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

            <div class="relative w-full h-3.5 sm:h-4 bg-slate-200 rounded-full overflow-hidden shadow-inner">
                <div class="absolute top-0 bottom-0 z-20 w-0.5 bg-slate-700 pointer-events-none" style="left: {{ $passingPercentage }}%;"></div>
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
                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Score Obtained</div>
                    <div class="text-xl sm:text-3xl font-black text-[#0b1329] mt-0.5 sm:mt-1">{{ $attempt->score_obtained }}</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Out of {{ $test->total_marks }} marks</div>
                </div>

                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Percentage</div>
                    <div class="text-xl sm:text-3xl font-black {{ $isPass ? 'text-emerald-600' : 'text-rose-600' }} mt-0.5 sm:mt-1">
                        {{ $attempt->percentage }}%
                    </div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Cutoff: {{ $passingPercentage }}%+</div>
                </div>

                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Accuracy Rate</div>
                    <div class="text-xl sm:text-3xl font-black text-amber-500 mt-0.5 sm:mt-1">{{ $accuracy }}%</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Correct vs attempted</div>
                </div>

                <div class="bg-slate-50 rounded-xl sm:rounded-2xl border border-slate-200/80 p-3 sm:p-4 text-center">
                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400">Questions Answered</div>
                    <div class="text-xl sm:text-3xl font-black text-slate-800 mt-0.5 sm:mt-1">{{ $attempt->answered_count }}</div>
                    <div class="text-[10px] sm:text-[11px] text-slate-500 font-semibold mt-0.5">Out of {{ $attempt->total_questions }} Qs</div>
                </div>
            </div>

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

        {{-- DETAILED QUESTION-BY-QUESTION REVIEW SECTION (ON-SCREEN ONLY) --}}
        <div class="p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Detailed Question Analysis & Solutions</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Review your chosen answers compared against standard correct keys and explanations.</p>
                </div>

                {{-- FILTER TABS --}}
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl sm:rounded-2xl text-xs font-bold overflow-x-auto w-full sm:w-auto scrollbar-none">
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
                        $statusClass = 'all';
                        if ($answer->is_correct) {
                            $statusClass = 'correct';
                        } elseif ($answer->selected_option_id) {
                            $statusClass = 'incorrect';
                        } else {
                            $statusClass = 'unanswered';
                        }
                    @endphp

                    <div x-show="filter === 'all' || filter === '{{ $statusClass }}'"
                         class="p-4 sm:p-5 rounded-2xl border transition
                            @if($answer->is_correct)
                                bg-emerald-50/30 border-emerald-200/80
                            @elseif($answer->selected_option_id)
                                bg-rose-50/30 border-rose-200/80
                            @else
                                bg-slate-50/60 border-slate-200/80
                            @endif
                         ">

                        {{-- QUESTION HEADER --}}
                        <div class="flex items-start justify-between gap-3 mb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center font-black text-xs text-slate-800 shrink-0 shadow-2xs">
                                    Q{{ $idx + 1 }}
                                </span>
                                <div>
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">{{ $question->subject ?? 'Section' }}</span>
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
        <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <a href="{{ route('tests.student.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition text-center w-full sm:w-auto shadow-2xs">
                ← Back to Test Portal
            </a>

            <a href="{{ route('community.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition text-center w-full sm:w-auto shadow-2xs">
                Community Home →
            </a>
        </div>

    </div>

</div>
@endsection