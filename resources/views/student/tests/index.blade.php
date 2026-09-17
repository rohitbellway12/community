@extends('layouts.student')

@section('title', 'Language Tests Portal')

@section('content')
<div class="p-3.5 sm:p-6 max-w-[1100px] w-full mx-auto">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-4">
            <span class="break-words">{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold ml-2">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs mb-4">
            <span class="break-words">{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold ml-2">✕</button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Online Language Tests</h1>
            <p class="text-xs text-slate-500 mt-0.5">Active & Scheduled Korean Language Examination Hall</p>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('community.profile', auth()->user()->profile?->username ?? auth()->id()) }}?tab=tests"
                   class="w-full sm:w-auto px-4 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:border-amber-300 hover:bg-amber-50 rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>My Past Results</span>
                    @if($completedCountTotal > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">{{ $completedCountTotal }}</span>
                    @endif
                </a>
            @endauth
        </div>
    </div>

    {{-- TESTS LIST --}}
    <div class="space-y-3.5 sm:space-y-4">
        @forelse($tests as $test)
            @php
                $hasActiveAttempt = isset($test->active_attempt) && $test->active_attempt;
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-6 hover:shadow-sm transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-5">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $test->testLevel->name ?? 'Level Test' }}
                            </span>

                            {{-- STATUS BADGES --}}
                            @if($hasActiveAttempt)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    SESSION IN PROGRESS
                                </span>
                            @elseif($test->can_take)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    LIVE NOW
                                </span>
                            @elseif($test->open_date && now()->lt($test->open_date))
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                    Upcoming: Starts {{ $test->open_date->format('M d, Y') }} {{ $test->open_time ? 'at ' . $test->open_time->format('H:i') : '' }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-sm sm:text-base font-bold text-slate-900 break-words">{{ $test->title }}</h3>

                        @if($test->description)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $test->description }}</p>
                        @endif

                        {{-- TEST METRICS --}}
                        <div class="flex items-center gap-x-4 sm:gap-x-5 gap-y-1.5 mt-3 text-xs text-slate-500 flex-wrap font-medium">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $test->questions->count() }} Questions
                            </span>

                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $test->duration_minutes }} Minutes
                            </span>

                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Pass: {{ $test->passing_marks }} / {{ $test->total_marks }} marks
                            </span>

                            {{-- SCHEDULE TIME --}}
                            @if($test->close_date)
                                <span class="text-slate-400 text-[11px]">
                                    Closes: {{ $test->close_date->format('M d, Y') }} {{ $test->close_time ? $test->close_time->format('H:i') : '' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="grid grid-cols-2 sm:flex items-center gap-2 sm:gap-2.5 w-full lg:w-auto mt-2 lg:mt-0 shrink-0">
                        <a href="{{ route('tests.student.show', $test) }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200 text-center">
                            Details
                        </a>

                        @if($hasActiveAttempt)
                            <a href="{{ route('tests.student.take', ['test' => $test, 'attempt' => $test->active_attempt]) }}"
                               class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition shadow-xs gap-1.5 cursor-pointer text-center">
                                <span>Resume Exam →</span>
                            </a>
                        @elseif($test->can_take)
                            <form id="start-exam-form-{{ $test->id }}" action="{{ route('tests.student.start', $test) }}" method="POST" class="w-full sm:w-auto">
                                @csrf
                                <button type="button"
                                        onclick="promptStartExam('start-exam-form-{{ $test->id }}', '{{ addslashes($test->title) }}', {{ $test->duration_minutes }})"
                                        class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-reiac-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Start Test</span>
                                </button>
                            </form>
                        @elseif(!$test->is_open && $test->open_date && now()->lt($test->open_date))
                            <button disabled
                                    class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed text-center">
                                Starts {{ $test->open_date->format('M d') }}
                            </button>
                        @else
                            <button disabled
                                    class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed text-center">
                                Test Closed
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-12 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-800">No Pending Tests Available</h3>
                <p class="text-xs text-slate-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                    You have completed all available tests, or no new examinations are currently open. When administrators schedule new tests, they will appear here.
                </p>
                @if($completedCountTotal > 0)
                    <div class="mt-5 flex items-center justify-center gap-3">
                        <a href="{{ route('community.profile', auth()->user()->profile?->username ?? auth()->id()) }}?tab=tests"
                           class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold text-[#0b1329] bg-amber-400 hover:bg-amber-500 rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                            <span>View My Past Results ({{ $completedCountTotal }})</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

</div>

<script>
function promptStartExam(formId, title, durationMinutes) {
    Swal.fire({
        title: 'Ready to Begin Examination?',
        html: `
            <div class="text-left text-xs text-slate-600 space-y-3 mt-3">
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                    <div class="font-extrabold text-slate-900 text-sm mb-0.5">${title}</div>
                    <div class="text-[11px] text-slate-500 font-semibold">Official Computer-Based Examination (CBT)</div>
                </div>
                <div class="p-3.5 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-center gap-3 text-amber-900">
                    <span class="text-2xl shrink-0">⏱</span>
                    <div>
                        <div class="font-extrabold text-xs">Strict Countdown Timer:</div>
                        <div class="text-[11px] leading-relaxed mt-0.5">The timer of <strong>${durationMinutes} minutes</strong> will start immediately upon confirmation and will auto-submit when the duration finishes.</div>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Make sure you have an uninterrupted internet connection before starting.
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Start Examination',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0b1329',
        cancelButtonColor: '#94a3b8',
        customClass: {
            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-200',
            confirmButton: 'rounded-xl font-extrabold text-xs px-6 py-3 shadow-sm',
            cancelButton: 'rounded-xl font-bold text-xs px-5 py-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endsection