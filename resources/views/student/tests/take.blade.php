@extends('layouts.student')

@section('title', $test->title . ' - CBT Examination Hall')
@section('hide_header', 'true')

@section('content')
<script>
    window.CBT_EXAM_CONFIG = {
        questions: {!! json_encode($questionsData, JSON_UNESCAPED_UNICODE) !!},
        answers: {!! json_encode($answersData, JSON_UNESCAPED_UNICODE) !!},
        remainingSeconds: {{ (int) $remainingSeconds }},
        totalSeconds: {{ (int) max($remainingSeconds, $test->duration_minutes * 60) }},
        tabSwitchLimit: {{ (int) ($test->tab_switch_limit ?? 0) }}
    };
</script>

<div class="min-h-screen bg-slate-100 flex flex-col antialiased selection:bg-amber-400 selection:text-slate-900 pb-20 lg:pb-0"
     x-data="cbtExamApp()"
     x-cloak>

    {{-- ============================================================ --}}
    {{-- 1. TOP EXAM BAR (RESPONSIVE FOR DESKTOP & MOBILE)             --}}
    {{-- ============================================================ --}}
    <header class="bg-[#0b1329] text-white border-b border-slate-800 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 h-14 sm:h-16 flex items-center justify-between gap-2 sm:gap-4">
            
            {{-- EXAM TITLE & SECTION --}}
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-400 text-[#0b1329] font-black flex items-center justify-center text-xs shrink-0 shadow-sm">
                    CBT
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xs sm:text-sm font-extrabold text-white truncate leading-tight">{{ $test->title }}</h1>
                    <div class="flex items-center gap-1.5 sm:gap-2 text-[10px] text-slate-300 truncate mt-0.5">
                        <span class="truncate">{{ $test->testLevel->name ?? 'General Assessment' }}</span>
                        <span>•</span>
                        <span class="shrink-0 font-medium text-amber-300" x-text="'Q ' + (currentQ + 1) + ' of ' + questions.length"></span>
                    </div>
                </div>
            </div>

            {{-- DIGITAL COUNTDOWN TIMER & CONTROLS --}}
            <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                
                {{-- TIMER PILL --}}
                <div class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-4 py-1 sm:py-1.5 rounded-xl border transition-all duration-300 shadow-inner"
                     :class="timerSeconds <= 120 ? 'bg-rose-950/90 border-rose-500 text-rose-300 animate-pulse ring-2 ring-rose-500/50' : (timerSeconds <= 300 ? 'bg-amber-950/90 border-amber-500 text-amber-300' : 'bg-slate-800/90 border-slate-700 text-white')">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-right">
                        <div class="text-[8px] sm:text-[9px] uppercase tracking-wider font-bold text-slate-400 hidden sm:block">Time Left</div>
                        <div class="text-xs sm:text-base font-mono font-black tracking-wider" x-text="formatTime(timerSeconds)">--:--</div>
                    </div>
                </div>

                {{-- MOBILE PALETTE TRIGGER BUTTON --}}
                <button type="button"
                        @click="mobilePaletteOpen = !mobilePaletteOpen"
                        class="lg:hidden inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-extrabold text-xs border border-slate-700 transition shrink-0 cursor-pointer shadow-sm"
                        title="Open Question Palette">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="text-[11px] font-mono" x-text="(currentQ + 1) + '/' + questions.length"></span>
                </button>

                {{-- FULLSCREEN TOGGLE (DESKTOP / TABLET) --}}
                <button type="button"
                        @click="toggleFullscreen()"
                        class="hidden sm:flex items-center justify-center w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700 cursor-pointer"
                        title="Toggle Fullscreen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>

                {{-- CANDIDATE BADGE (DESKTOP) --}}
                <div class="hidden md:flex items-center gap-2.5 pl-3 border-l border-slate-700">
                    <div class="w-8 h-8 rounded-full bg-slate-700 text-amber-400 font-black flex items-center justify-center text-xs border border-slate-600">
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    </div>
                    <div class="text-left text-xs leading-tight">
                        <div class="font-bold text-slate-200 truncate max-w-[110px]">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-400">Roll: {{ auth()->user()->username ?? ('ID-' . auth()->id()) }}</div>
                    </div>
                </div>

            </div>

        </div>

        {{-- TIME PROGRESS BAR --}}
        <div class="w-full bg-slate-800 h-1 overflow-hidden">
            <div class="h-full transition-all duration-1000"
                 :style="'width: ' + timeProgress + '%'"
                 :class="timerSeconds <= 120 ? 'bg-rose-500' : (timerSeconds <= 300 ? 'bg-amber-400' : 'bg-emerald-400')"></div>
        </div>
    </header>

    {{-- ============================================================ --}}
    {{-- 2. MAIN CBT EXAM BODY                                        --}}
    {{-- ============================================================ --}}
    <main class="flex-1 max-w-7xl w-full mx-auto p-2.5 sm:p-5 flex flex-col lg:flex-row gap-4 sm:gap-6">
        
        {{-- HIDDEN SUBMISSION FORM WITH ANSWERS PAYLOAD --}}
        <form id="cbt-exam-form" action="{{ route('tests.student.submit', ['test' => $test, 'attempt' => $attempt]) }}" method="POST">
            @csrf
            <input type="hidden" name="tab_switch_count" id="tab_switch_count" x-model="tabSwitches">
            <input type="hidden" name="is_timeout" id="is_timeout" value="0">
            <input type="hidden" name="answers" id="answers_payload" value="">
        </form>

        {{-- LEFT PANEL: QUESTION PAPER & ANSWER CONTROLS --}}
        <div class="flex-1 flex flex-col min-w-0">
            
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm flex-1 flex flex-col overflow-hidden">
                
                {{-- QUESTION HEADER META --}}
                <div class="bg-slate-50 border-b border-slate-200 px-3.5 sm:px-6 py-3 sm:py-4 flex items-center justify-between gap-2 sm:gap-3 flex-wrap">
                    
                    <div class="flex items-center gap-1.5 sm:gap-2.5 flex-wrap">
                        <span class="px-2.5 sm:px-3 py-1 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-black bg-[#0b1329] text-white shadow-2xs">
                            Question <span x-text="currentQ + 1"></span> of <span x-text="questions.length"></span>
                        </span>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-200 text-slate-700">
                            +<span x-text="currentQuestion.marks"></span> Mark(s)
                        </span>
                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-200 text-slate-700 hidden xs:inline">
                            0 Negative
                        </span>
                    </div>

                    {{-- LIVE STATUS BADGE FOR CURRENT QUESTION --}}
                    <div class="flex items-center gap-1.5">
                        <template x-if="isMarkedForReview(currentQuestion.id)">
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-amber-500 animate-ping"></span> Marked for Review
                            </span>
                        </template>
                        <template x-if="!isMarkedForReview(currentQuestion.id) && selectedAnswers[currentQuestion.id]">
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500"></span> Answered &amp; Saved
                            </span>
                        </template>
                        <template x-if="!isMarkedForReview(currentQuestion.id) && !selectedAnswers[currentQuestion.id]">
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-slate-400"></span> Not Answered
                            </span>
                        </template>
                    </div>
                </div>

                {{-- QUESTION CONTENT & OPTIONS AREA --}}
                <div class="p-4 sm:p-7 flex-1 overflow-y-auto">
                    
                    {{-- QUESTION TEXT --}}
                    <div class="mb-5 sm:mb-6">
                        <div class="text-sm sm:text-base md:text-lg font-bold text-slate-900 leading-relaxed tracking-tight break-words"
                             x-text="currentQuestion.question_text">
                        </div>
                    </div>

                    {{-- OPTIONS LIST (FULL CARD CLICKABLE) --}}
                    <div class="space-y-2.5 sm:space-y-3 max-w-3xl">
                        <template x-for="option in (currentQuestion.options || [])" :key="'opt_' + (currentQuestion.id || 0) + '_' + option.id">
                            <label @click="selectOption(currentQuestion.id, option.id)"
                                   class="flex items-start gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition-all cursor-pointer select-none active:scale-[0.99]"
                                   :class="selectedAnswers[currentQuestion.id] == option.id ? 'border-amber-400 bg-amber-50/70 shadow-sm ring-2 ring-amber-400/30' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/60'">
                                
                                {{-- RADIO / OPTION LABEL BADGE --}}
                                <div class="pt-0.5 shrink-0 flex items-center gap-2.5">
                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl font-black text-xs flex items-center justify-center transition-all"
                                         :class="selectedAnswers[currentQuestion.id] == option.id ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                         x-text="option.option_label">
                                    </div>
                                    <input type="radio"
                                           :name="'answer_' + currentQuestion.id"
                                           :value="option.id"
                                           :checked="selectedAnswers[currentQuestion.id] == option.id"
                                           class="hidden">
                                </div>

                                {{-- OPTION TEXT --}}
                                <div class="flex-1 text-xs sm:text-sm text-slate-800 leading-relaxed font-medium min-w-0 pt-0.5">
                                    <span x-text="option.option_text" class="break-words"></span>
                                </div>

                                {{-- SELECTED CHECKMARK BADGE --}}
                                <template x-if="selectedAnswers[currentQuestion.id] == option.id">
                                    <div class="shrink-0 self-center hidden sm:flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                                        <span>✓</span>
                                        <span>Selected</span>
                                    </div>
                                </template>
                            </label>
                        </template>
                    </div>

                </div>

                {{-- DESKTOP ACTION BAR --}}
                <div class="bg-slate-50 border-t border-slate-200 p-3 sm:px-6 sm:py-4 hidden sm:flex items-center justify-between gap-3">
                    
                    {{-- REVIEW & CLEAR ROW --}}
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="toggleMarkForReview(currentQuestion.id)"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition border cursor-pointer"
                                :class="isMarkedForReview(currentQuestion.id) ? 'bg-amber-100 text-amber-900 border-amber-300 hover:bg-amber-200 shadow-2xs' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100'">
                            <span x-text="isMarkedForReview(currentQuestion.id) ? '★ Marked' : '☆ Mark for Review'"></span>
                        </button>

                        <button type="button"
                                @click="clearResponse(currentQuestion.id)"
                                :disabled="!selectedAnswers[currentQuestion.id]"
                                class="inline-flex items-center justify-center px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer">
                            Clear Response
                        </button>
                    </div>

                    {{-- PREVIOUS & SAVE & NEXT ROW --}}
                    <div class="flex items-center gap-2.5">
                        <button type="button"
                                @click="prevQuestion()"
                                :disabled="currentQ === 0"
                                class="inline-flex items-center justify-center gap-1 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer">
                            ← Previous
                        </button>

                        <button type="button"
                                @click="saveAndNext()"
                                class="inline-flex items-center justify-center gap-1.5 px-5 sm:px-6 py-2.5 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm cursor-pointer active:scale-95">
                            <span x-text="currentQ === questions.length - 1 ? 'Save & Submit' : 'Save & Next'"></span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT PANEL: CBT QUESTION PALETTE & STATUS SUMMARY (DESKTOP SIDEBAR) --}}
        <aside class="hidden lg:flex w-80 shrink-0 flex-col gap-4">
            
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col">
                
                {{-- PALETTE HEADER --}}
                <div class="flex items-center justify-between mb-3.5 pb-3 border-b border-slate-100">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">Question Palette</h2>
                    <span class="text-[11px] font-bold text-slate-400">Total: <strong class="text-slate-800" x-text="questions.length"></strong> Qs</span>
                </div>

                {{-- STATUS LEGEND SUMMARY COUNTS --}}
                <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200/60 p-2 rounded-xl">
                        <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white font-black flex items-center justify-center text-xs shrink-0 shadow-2xs" x-text="countAnswered()"></span>
                        <span class="text-[11px] font-bold text-emerald-950">Answered</span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/60 p-2 rounded-xl">
                        <span class="w-6 h-6 rounded-lg bg-slate-300 text-slate-700 font-black flex items-center justify-center text-xs shrink-0" x-text="countUnanswered()"></span>
                        <span class="text-[11px] font-bold text-slate-700">Not Answered</span>
                    </div>
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200/60 p-2 rounded-xl">
                        <span class="w-6 h-6 rounded-lg bg-amber-500 text-white font-black flex items-center justify-center text-xs shrink-0 shadow-2xs" x-text="countMarked()"></span>
                        <span class="text-[11px] font-bold text-amber-950">Marked</span>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 border border-slate-200 p-2 rounded-xl">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-300 text-slate-500 font-black flex items-center justify-center text-xs shrink-0" x-text="countNotVisited()"></span>
                        <span class="text-[11px] font-bold text-slate-600">Not Visited</span>
                    </div>
                </div>

                {{-- QUESTION NUMBER TILES (DESKTOP) --}}
                <div class="mb-5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Question Navigation:</div>
                    <div class="grid grid-cols-5 gap-2 max-h-[260px] overflow-y-auto p-1">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <button type="button"
                                    @click="jumpToQuestion(idx)"
                                    class="h-9 sm:h-10 rounded-xl font-black text-xs transition-all relative flex items-center justify-center cursor-pointer shadow-2xs"
                                    :class="getQuestionTileClass(idx)">
                                <span x-text="idx + 1"></span>
                                <template x-if="isMarkedForReview(q.id)">
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-400 rounded-full border-2 border-white shadow-2xs"></span>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- TAB SWITCH AUDIT WARNING --}}
                @if($test->tab_switch_limit > 0)
                    <div class="bg-slate-50 border border-slate-200 p-3 rounded-2xl text-[11px] mb-4">
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-0.5">
                            <span>Tab Violations:</span>
                            <span :class="tabSwitches >= tabSwitchLimit ? 'text-rose-600 font-black' : 'text-slate-900'">
                                <strong x-text="tabSwitches"></strong> / {{ $test->tab_switch_limit }}
                            </span>
                        </div>
                        <div class="text-[10px] text-slate-400">Strictly monitored by exam engine.</div>
                    </div>
                @endif

                {{-- PROMINENT SUBMIT EXAMINATION BUTTON --}}
                <div class="mt-auto pt-3 border-t border-slate-100">
                    <button type="button"
                            @click="openSubmitModal = true"
                            class="w-full py-3 rounded-xl sm:rounded-2xl font-black text-xs uppercase tracking-wider text-white bg-[#0b1329] hover:bg-slate-900 transition shadow-md flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Submit Examination</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center mt-2">All answers are saved automatically.</p>
                </div>

            </div>

        </aside>

    </main>

    {{-- ============================================================ --}}
    {{-- 3. MOBILE FIXED BOTTOM ACTION BAR (OPTIMIZED FOR PHONES)      --}}
    {{-- ============================================================ --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-30 bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 py-2.5 shadow-xl">
        
        {{-- SECONDARY ACTIONS (MARK FOR REVIEW & CLEAR) --}}
        <div class="flex items-center justify-between gap-2 mb-2">
            <button type="button"
                    @click="toggleMarkForReview(currentQuestion.id)"
                    class="flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold border transition text-center cursor-pointer"
                    :class="isMarkedForReview(currentQuestion.id) ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-slate-50 text-slate-700 border-slate-200'">
                <span x-text="isMarkedForReview(currentQuestion.id) ? '★ Marked' : '☆ Review'"></span>
            </button>
            <button type="button"
                    @click="clearResponse(currentQuestion.id)"
                    :disabled="!selectedAnswers[currentQuestion.id]"
                    class="flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold bg-slate-50 text-slate-600 border border-slate-200 disabled:opacity-40 disabled:cursor-not-allowed transition text-center cursor-pointer">
                Clear
            </button>
            <button type="button"
                    @click="openSubmitModal = true"
                    class="flex-1 py-1.5 px-2 rounded-lg text-[11px] font-black uppercase text-white bg-[#0b1329] border border-[#0b1329] transition text-center cursor-pointer shadow-2xs">
                Submit
            </button>
        </div>

        {{-- PRIMARY NAVIGATION (PREVIOUS & SAVE & NEXT) --}}
        <div class="flex items-center gap-2">
            <button type="button"
                    @click="prevQuestion()"
                    :disabled="currentQ === 0"
                    class="w-1/3 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 border border-slate-300 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed transition cursor-pointer">
                ← Prev
            </button>

            <button type="button"
                    @click="saveAndNext()"
                    class="w-2/3 py-2.5 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-md flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                <span x-text="currentQ === questions.length - 1 ? 'Save & Submit' : 'Save & Next'"></span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- 4. MOBILE SLIDE-UP QUESTION PALETTE DRAWER                   --}}
    {{-- ============================================================ --}}
    <div x-show="mobilePaletteOpen"
         class="lg:hidden fixed inset-0 z-50 flex items-end justify-center p-0 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-full"
         style="display: none;">
        
        <div class="bg-white rounded-t-3xl max-w-lg w-full p-5 shadow-2xl border-t border-slate-200 max-h-[85vh] overflow-y-auto"
             @click.away="mobilePaletteOpen = false">
            
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-wider text-slate-900">Question Palette</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Tap any number below to jump directly to that question</p>
                </div>
                <button type="button"
                        @click="mobilePaletteOpen = false"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-sm cursor-pointer">
                    ✕
                </button>
            </div>

            {{-- SUMMARY COUNTS --}}
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200/60 p-2 rounded-xl">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white font-black flex items-center justify-center text-xs shrink-0 shadow-2xs" x-text="countAnswered()"></span>
                    <span class="text-[10px] font-bold text-emerald-950">Answered</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200/60 p-2 rounded-xl">
                    <span class="w-6 h-6 rounded-lg bg-slate-300 text-slate-700 font-black flex items-center justify-center text-xs shrink-0" x-text="countUnanswered()"></span>
                    <span class="text-[10px] font-bold text-slate-700">Not Answered</span>
                </div>
                <div class="flex items-center gap-2 bg-amber-50 border border-amber-200/60 p-2 rounded-xl">
                    <span class="w-6 h-6 rounded-lg bg-amber-500 text-white font-black flex items-center justify-center text-xs shrink-0 shadow-2xs" x-text="countMarked()"></span>
                    <span class="text-[10px] font-bold text-amber-950">Marked</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-100 border border-slate-200 p-2 rounded-xl">
                    <span class="w-6 h-6 rounded-lg bg-slate-100 border border-slate-300 text-slate-500 font-black flex items-center justify-center text-xs shrink-0" x-text="countNotVisited()"></span>
                    <span class="text-[10px] font-bold text-slate-600">Not Visited</span>
                </div>
            </div>

            {{-- QUESTION TILES --}}
            <div class="grid grid-cols-5 gap-2 max-h-60 overflow-y-auto p-1 mb-5">
                <template x-for="(q, idx) in questions" :key="'mob_' + q.id">
                    <button type="button"
                            @click="jumpToQuestion(idx)"
                            class="h-10 rounded-xl font-black text-xs transition-all relative flex items-center justify-center cursor-pointer shadow-2xs"
                            :class="getQuestionTileClass(idx)">
                        <span x-text="idx + 1"></span>
                        <template x-if="isMarkedForReview(q.id)">
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-400 rounded-full border-2 border-white shadow-2xs"></span>
                        </template>
                    </button>
                </template>
            </div>

            <button type="button"
                    @click="mobilePaletteOpen = false; openSubmitModal = true;"
                    class="w-full py-3.5 rounded-xl font-black text-xs uppercase tracking-wider text-white bg-[#0b1329] hover:bg-slate-900 transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                <span>Submit Examination</span>
            </button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 5. SUBMIT CONFIRMATION MODAL                                 --}}
    {{-- ============================================================ --}}
    <div x-show="openSubmitModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-2xl border border-slate-200"
             @click.away="openSubmitModal = false">
            
            <div class="text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Confirm Exam Submission</h3>
                <p class="text-xs text-slate-500 mt-1">Review your response breakdown before final submission.</p>
            </div>

            {{-- SUMMARY TABLE IN MODAL --}}
            <div class="bg-slate-50 rounded-xl sm:rounded-2xl p-3.5 sm:p-4 my-4 sm:my-5 border border-slate-200 text-xs space-y-2">
                <div class="flex justify-between font-bold">
                    <span class="text-slate-600">Total Questions:</span>
                    <span class="text-slate-900" x-text="questions.length"></span>
                </div>
                <div class="flex justify-between font-bold">
                    <span class="text-emerald-700">Answered Questions:</span>
                    <span class="text-emerald-700 font-extrabold" x-text="countAnswered()"></span>
                </div>
                <div class="flex justify-between font-bold">
                    <span class="text-slate-600">Unanswered Questions:</span>
                    <span class="text-rose-600 font-extrabold" x-text="countUnanswered() + countNotVisited()"></span>
                </div>
                <div class="flex justify-between font-bold">
                    <span class="text-amber-700">Marked for Review:</span>
                    <span class="text-amber-700 font-extrabold" x-text="countMarked()"></span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between font-extrabold text-[11px] text-slate-400">
                    <span>Remaining Duration:</span>
                    <span class="font-mono text-slate-800" x-text="formatTime(timerSeconds)"></span>
                </div>
            </div>

            <p class="text-[10px] sm:text-[11px] text-slate-500 text-center mb-4 sm:mb-5 leading-relaxed">
                Once submitted, you will not be able to modify any responses and your scorecard will be evaluated immediately.
            </p>

            <div class="flex gap-2.5 sm:gap-3">
                <button type="button"
                        @click="openSubmitModal = false"
                        class="flex-1 py-2.5 sm:py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition cursor-pointer">
                    Resume Exam
                </button>
                <button type="button"
                        @click="executeFinalSubmission()"
                        class="flex-1 py-2.5 sm:py-3 rounded-xl bg-[#0b1329] text-white font-extrabold text-xs hover:bg-slate-900 transition shadow-xs cursor-pointer">
                    Yes, Submit Exam
                </button>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 6. TIME-UP AUTO-SUBMISSION SCREEN OVERLAY                    --}}
    {{-- ============================================================ --}}
    <div x-show="isTimeUp"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0b1329]/90 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl border border-slate-200">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 animate-bounce shadow-inner">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg sm:text-xl font-black text-slate-900">Examination Time Expired!</h3>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                The allocated duration of <strong>{{ $test->duration_minutes }} minutes</strong> has ended. The system is securing and submitting your answers now.
            </p>
            <div class="mt-5 sm:mt-6 inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 rounded-full bg-slate-100 text-slate-700 font-bold text-xs">
                <svg class="w-4 h-4 animate-spin text-amber-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span>Submitting responses to evaluation engine...</span>
            </div>
        </div>
    </div>

</div>

<script>
function cbtExamApp() {
    const config = window.CBT_EXAM_CONFIG || { questions: [], answers: [], remainingSeconds: 0, totalSeconds: 1200, tabSwitchLimit: 0 };
    return {
        questions: config.questions || [],
        currentQ: 0,
        timerSeconds: Number(config.remainingSeconds) || 0,
        totalSeconds: Number(config.totalSeconds) || 1200,
        timerInterval: null,
        tabSwitches: 0,
        tabSwitchLimit: Number(config.tabSwitchLimit) || 0,
        selectedAnswers: {},
        markedForReview: {},
        visitedQuestions: { 0: true },
        openSubmitModal: false,
        mobilePaletteOpen: false,
        isTimeUp: false,
        isSubmitting: false,

        get currentQuestion() {
            if (this.questions && this.questions.length > 0 && this.questions[this.currentQ]) {
                return this.questions[this.currentQ];
            }
            return { id: 0, marks: 1, question_text: 'Loading Question...', options: [] };
        },

        get timeProgress() {
            if (this.totalSeconds <= 0) return 0;
            return Math.max(0, Math.min(100, (this.timerSeconds / this.totalSeconds) * 100));
        },

        init() {
            // Load initial attempt responses
            if (Array.isArray(config.answers)) {
                config.answers.forEach(a => {
                    if (a.selected_option_id) {
                        this.selectedAnswers[a.question_id] = a.selected_option_id;
                    }
                    if (a.is_marked_for_review) {
                        this.markedForReview[a.question_id] = true;
                    }
                });
            }

            this.startTimer();
            this.setupTabSwitchMonitoring();

            // Guard against accidental tab close
            window.addEventListener('beforeunload', (e) => {
                if (!this.isSubmitting && !this.isTimeUp) {
                    e.preventDefault();
                    e.returnValue = 'Your examination is actively in progress. Leaving this window will submit your answers.';
                }
            });
        },

        startTimer() {
            if (this.timerSeconds <= 0) {
                this.triggerAutoSubmitOnTimeout();
                return;
            }

            this.timerInterval = setInterval(() => {
                if (this.timerSeconds <= 1) {
                    this.timerSeconds = 0;
                    clearInterval(this.timerInterval);
                    this.triggerAutoSubmitOnTimeout();
                    return;
                }
                this.timerSeconds--;
            }, 1000);
        },

        triggerAutoSubmitOnTimeout() {
            this.isTimeUp = true;
            this.isSubmitting = true;
            document.getElementById('is_timeout').value = '1';
            const payload = document.getElementById('answers_payload');
            if (payload) {
                payload.value = JSON.stringify(this.selectedAnswers);
            }
            setTimeout(() => {
                document.getElementById('cbt-exam-form').submit();
            }, 1200);
        },

        formatTime(totalSeconds) {
            const hrs = Math.floor(totalSeconds / 3600);
            const mins = Math.floor((totalSeconds % 3600) / 60);
            const secs = totalSeconds % 60;
            if (hrs > 0) {
                return String(hrs).padStart(2, '0') + ':' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            }
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        },

        selectOption(questionId, optionId) {
            this.selectedAnswers[questionId] = optionId;
            this.visitedQuestions[this.currentQ] = true;

            // Instant Background Auto-Save (AJAX)
            fetch('{{ route("tests.student.answer.save", ["test" => $test, "attempt" => $attempt]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id: questionId,
                    selected_option_id: optionId,
                    option_id: optionId,
                }),
            }).then(r => r.json()).then(data => {
                if (!data.success) {
                    console.warn('Auto-Save response:', data);
                }
            }).catch(e => console.error('Auto-Save Error:', e));
        },

        clearResponse(questionId) {
            delete this.selectedAnswers[questionId];

            fetch('{{ route("tests.student.answer.clear", ["test" => $test, "attempt" => $attempt]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ question_id: questionId }),
            }).catch(e => console.error('Clear Response Error:', e));
        },

        toggleMarkForReview(questionId) {
            const newState = !this.markedForReview[questionId];
            this.markedForReview[questionId] = newState;

            fetch('{{ route("tests.student.answer.mark-review", ["test" => $test, "attempt" => $attempt]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ question_id: questionId }),
            }).catch(e => console.error('Mark Review Error:', e));
        },

        isMarkedForReview(questionId) {
            return !!this.markedForReview[questionId];
        },

        saveAndNext() {
            // Guarantee currently selected option is saved immediately
            const qId = this.currentQuestion.id;
            if (this.selectedAnswers[qId]) {
                this.selectOption(qId, this.selectedAnswers[qId]);
            }

            if (this.currentQ < this.questions.length - 1) {
                this.currentQ++;
                this.visitedQuestions[this.currentQ] = true;
            } else {
                this.openSubmitModal = true;
            }
        },

        prevQuestion() {
            if (this.currentQ > 0) {
                this.currentQ--;
                this.visitedQuestions[this.currentQ] = true;
            }
        },

        jumpToQuestion(idx) {
            this.currentQ = idx;
            this.visitedQuestions[idx] = true;
            this.mobilePaletteOpen = false;
        },

        countAnswered() {
            return Object.keys(this.selectedAnswers).length;
        },

        countUnanswered() {
            let count = 0;
            this.questions.forEach((q, idx) => {
                if (this.visitedQuestions[idx] && !this.selectedAnswers[q.id] && !this.markedForReview[q.id]) {
                    count++;
                }
            });
            return count;
        },

        countMarked() {
            return Object.values(this.markedForReview).filter(Boolean).length;
        },

        countNotVisited() {
            let count = 0;
            this.questions.forEach((q, idx) => {
                if (!this.visitedQuestions[idx] && !this.selectedAnswers[q.id] && !this.markedForReview[q.id]) {
                    count++;
                }
            });
            return count;
        },

        getQuestionTileClass(idx) {
            const q = this.questions[idx];
            const isCurrent = this.currentQ === idx;
            const ring = isCurrent ? 'ring-2 ring-[#0b1329] ring-offset-2 scale-105 ' : '';

            if (this.markedForReview[q.id]) {
                return ring + 'bg-amber-100 text-amber-900 border border-amber-400 font-extrabold';
            }
            if (this.selectedAnswers[q.id]) {
                return ring + 'bg-emerald-600 text-white font-extrabold shadow-xs';
            }
            if (this.visitedQuestions[idx]) {
                return ring + 'bg-slate-200 text-slate-800 border border-slate-300';
            }
            return ring + 'bg-slate-100 text-slate-400 border border-slate-200 hover:bg-slate-200';
        },

        executeFinalSubmission() {
            this.isSubmitting = true;
            const payload = document.getElementById('answers_payload');
            if (payload) {
                payload.value = JSON.stringify(this.selectedAnswers);
            }
            document.getElementById('cbt-exam-form').submit();
        },

        setupTabSwitchMonitoring() {
            let triggered = false;
            const onSwitch = () => {
                if (this.isSubmitting || this.isTimeUp || triggered) return;
                triggered = true;
                this.tabSwitches = Math.max(1, this.tabSwitches + 1);

                Swal.fire({
                    icon: 'error',
                    title: 'Screen Change Detected!',
                    html: `
                        <div class="text-xs text-slate-600 mt-2 space-y-2">
                            <p class="font-bold text-rose-700">You left the examination screen.</p>
                            <p class="leading-relaxed">As per strict examination regulations, switching tabs or leaving the screen triggers an <strong>immediate automatic submission</strong>.</p>
                            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 font-extrabold text-[11px]">
                                Your examination is being submitted now...
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-3xl p-6 shadow-2xl border border-rose-200'
                    }
                }).then(() => {
                    this.executeFinalSubmission();
                });

                // Fail-safe immediate submission
                setTimeout(() => {
                    this.executeFinalSubmission();
                }, 2100);
            };

            // Trigger when student leaves the examination tab or minimizes
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    onSwitch();
                }
            });
        },

        toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => console.log(err));
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    };
}
</script>
@endsection
