<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-slate-50" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Resume Builder & Career Suite | REIAC Community</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .preview-scroll { -webkit-overflow-scrolling: touch; overscroll-behavior: contain; }
        .resume-preview-overlay {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            align-items: stretch;
            justify-content: center;
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(6px);
            padding: 0;
        }
        .resume-preview-dialog {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
            height: 100dvh;
            max-height: 100vh;
            max-height: 100dvh;
            min-height: 0;
            overflow: hidden;
            background: #ffffff;
            border-radius: 0;
        }
        @media (min-width: 640px) {
            .resume-preview-overlay {
                align-items: center;
                padding: 16px;
            }
            .resume-preview-dialog {
                width: 95vw;
                max-width: 1200px;
                height: 92vh;
                height: min(92vh, 920px);
                height: min(92dvh, 920px);
                max-height: calc(100vh - 32px);
                max-height: calc(100dvh - 32px);
                border-radius: 1.25rem;
                border: 1px solid #cbd5e1;
                box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased selection:bg-amber-500 selection:text-slate-950 flex flex-col" style="background-color: #f8fafc;">

    {{-- OFFICIAL REIAC COMMUNITY TOPBAR --}}
    <x-community.topbar :notifications-count="auth()->check() ? auth()->user()->unreadNotifications()->count() : 0" />

<div class="flex-1" x-data="{
        heroTab: 'modern',
        activeFilter: 'all',
        previewModalOpen: false,
        iframeLoading: true,
        activeTemplateId: 'korean_standard',
        activeTemplateName: 'Korean Standard Job Application',
        sampleLang: 'ko',
        zoomMode: 'fit',
        sampleBaseUrl: '{{ url('/resume-builder/sample') }}',
        createBaseUrl: '{{ url('/resume-builder/create') }}',

        init() {
            this.$watch('previewModalOpen', (open) => {
                document.body.classList.toggle('overflow-hidden', open);
                if (open) {
                    this.$nextTick(() => {
                        this.notifyFrame('RESCALE_A4');
                    });
                }
            });
        },

        openSampleModal(id, name, lang = 'ko') {
            const isDifferent = (this.activeTemplateId !== id || this.sampleLang !== lang);
            this.activeTemplateId = id;
            this.activeTemplateName = name;
            this.sampleLang = lang;
            this.previewModalOpen = true;
            this.iframeLoading = isDifferent;
            this.zoomMode = 'fit';
            setTimeout(() => {
                this.iframeLoading = false;
                this.notifyFrame('RESCALE_A4');
            }, 600);
        },
        switchModalTemplate(id, name) {
            if (this.activeTemplateId !== id) {
                this.activeTemplateId = id;
                this.activeTemplateName = name;
                this.iframeLoading = true;
                setTimeout(() => { this.iframeLoading = false; }, 600);
            }
        },
        setSampleLang(lang) {
            if (this.sampleLang !== lang) {
                this.sampleLang = lang;
                this.iframeLoading = true;
                setTimeout(() => { this.iframeLoading = false; }, 600);
            }
        },
        setZoom(mode) {
            this.zoomMode = mode;
            this.notifyFrame('SET_A4_MODE', { mode: mode === 100 ? 'full' : 'fit' });
        },
        notifyFrame(type, payload = {}) {
            try {
                const frame = this.$refs.previewFrame;
                if (!frame) return;
                if (frame.contentWindow) {
                    frame.contentWindow.postMessage({ type: type, ...payload }, '*');
                    if (type === 'SET_A4_MODE' && frame.contentWindow.setA4ScaleMode) {
                        frame.contentWindow.setA4ScaleMode(payload.mode);
                    }
                }
            } catch(e) {}
        },
        onPreviewLoad() {
            this.iframeLoading = false;
            this.notifyFrame('SET_A4_MODE', { mode: this.zoomMode === 100 ? 'full' : 'fit' });
            setTimeout(() => this.notifyFrame('RESCALE_A4'), 150);
            setTimeout(() => this.notifyFrame('RESCALE_A4'), 400);
        },
        get currentSampleUrl() {
            return `${this.sampleBaseUrl}/${this.activeTemplateId}?embed=1&lang=${this.sampleLang}`;
        },
        get currentFullUrl() {
            return `${this.sampleBaseUrl}/${this.activeTemplateId}?lang=${this.sampleLang}`;
        },
        get currentCreateUrl() {
            return `${this.createBaseUrl}?template=${this.activeTemplateId}&language=${this.sampleLang}`;
        }
    }">

    {{-- =========================================================================
         1. COMPACT, BALANCED & HIGH-END HERO SECTION (EXECUTIVE DARK NAVY)
         ========================================================================= --}}
    <section class="relative text-white overflow-hidden border-b border-slate-800"
             style="background: linear-gradient(180deg, #0B132B 0%, #0d1a3a 50%, #10214a 100%); color: #ffffff;">

        {{-- Subtle architectural grid overlay --}}
        <div class="absolute inset-0 pointer-events-none opacity-20"
             style="background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 lg:py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">

                {{-- LEFT COLUMN: HERO HEADLINE & CTAS (7 Cols) --}}
                <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                    
                    {{-- Eyebrow Live Badge --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide shadow-sm"
                         style="background: rgba(30, 41, 59, 0.85); border: 1px solid rgba(71, 85, 105, 0.7); color: #fef08a;">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span class="text-slate-200">AI-Powered Career Suite</span>
                        <span class="text-slate-500">•</span>
                        <span style="color: #fbbf24;" class="font-semibold">Bilingual KR &amp; EN</span>
                    </div>

                    {{-- Main Headline --}}
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.18] text-white">
                        Build an <span style="color: #fbbf24;">ATS-Approved Resume</span> That Lands Real Interviews
                    </h1>

                    {{-- Description --}}
                    <p class="text-sm sm:text-base text-slate-300 font-normal leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Scientifically budgeted for 1-page A4 perfection. Switch effortlessly between corporate English ATS and official Korean <strong class="text-white font-bold">입사지원서 &amp; 자기소개서</strong> with 1-click watermark-free PDF export.
                    </p>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        <a href="#templates"
                           class="w-full sm:w-auto px-7 py-3.5 rounded-xl font-extrabold text-xs uppercase tracking-wider transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center gap-2 cursor-pointer shadow-lg"
                           style="background: #f59e0b; color: #0f172a; box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.35);">
                            <span>✨ Choose Template &amp; Start</span>
                            <span class="text-sm">↓</span>
                        </a>

                        @auth
                            <a href="{{ route('resume-builder.my-resumes') }}"
                               class="w-full sm:w-auto px-5 py-3.5 rounded-xl font-bold text-xs tracking-wide transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                                style="background: rgba(30, 41, 59, 0.9); border: 1px solid rgba(71, 85, 105, 0.8); color: #ffffff;">
                                <span>📂 My Saved Resumes</span>
                                <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 text-[11px] font-black">{{ $userResumesCount }}</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full sm:w-auto px-5 py-3.5 rounded-xl font-bold text-xs tracking-wide transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer"
                               style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.22); color: #ffffff;">
                                <span>🔒 Log In to Save Resumes</span>
                            </a>
                        @endauth
                    </div>

                    {{-- Trust Pills Strip --}}
                    <div class="pt-4 border-t border-slate-800/90 grid grid-cols-2 sm:flex sm:flex-wrap items-center justify-center lg:justify-start gap-3 text-xs text-slate-300 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>100% Free PDF Export</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>Bilingual: English &amp; 한국어</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-teal-500/20 text-teal-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>Official 3x4 Photo &amp; 서식</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-purple-500/20 text-purple-400 flex items-center justify-center text-[10px] font-bold">✓</span>
                            <span>98%+ ATS Pass Rate</span>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: HIGH-TECH LIVE PREVIEW SHOWCASE (5 Cols) --}}
                <div class="lg:col-span-5 relative">
                    
                    {{-- Window Frame Mockup --}}
                    <div class="relative rounded-2xl p-4 transition"
                         style="background: #0f172a; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);">
                        
                        {{-- Top Window Bar with Tab Selector --}}
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                            {{-- macOS-style traffic dots --}}
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500/80"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            </div>

                            {{-- Interactive Template Switcher Pills inside Hero --}}
                            <div class="inline-flex p-0.5 rounded-lg bg-slate-800 border border-slate-700 text-[10.5px] font-bold">
                                <button type="button"
                                        @click="heroTab = 'modern'"
                                        :style="heroTab === 'modern' ? 'background: #f59e0b; color: #0f172a; font-weight: 800;' : 'color: #94a3b8;'"
                                        class="px-2.5 py-1 rounded-md transition cursor-pointer flex items-center gap-1">
                                    <span>📄 Modern ATS</span>
                                </button>
                                <button type="button"
                                        @click="heroTab = 'korean'"
                                        :style="heroTab === 'korean' ? 'background: #10b981; color: #0f172a; font-weight: 800;' : 'color: #94a3b8;'"
                                        class="px-2.5 py-1 rounded-md transition cursor-pointer flex items-center gap-1">
                                    <span>🇰🇷 한국 표준</span>
                                </button>
                            </div>

                            {{-- ATS Score Badge --}}
                            <div class="flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>98% ATS</span>
                            </div>
                        </div>

                        {{-- TAB 1: MODERN PROFESSIONAL MINI PREVIEW --}}
                        <div x-show="heroTab === 'modern'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white text-slate-800 rounded-xl p-3.5 shadow-sm border border-slate-200 space-y-2.5 text-[10px]">
                            <div class="flex justify-between items-start pb-2 border-b border-slate-200">
                                <div>
                                    <h4 class="font-black text-slate-950 text-xs tracking-tight">ALEX JOHNSON / 김민수</h4>
                                    <p class="text-[9.5px] font-bold text-amber-700">Senior Full-Stack Software Engineer</p>
                                    <p class="text-[8px] text-slate-400 mt-0.5">Seoul, Korea • minsoo@reiac.org • +82 10-9876-5432</p>
                                </div>
                                <span class="text-[8.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded">
                                    A4 Ready
                                </span>
                            </div>

                            {{-- Experience Highlight --}}
                            <div class="space-y-1 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                <div class="flex justify-between items-center font-bold text-slate-900 text-[9px]">
                                    <span>Kakao Enterprise • Senior Engineer</span>
                                    <span class="text-slate-400 font-normal text-[8px]">2022 – Present</span>
                                </div>
                                <p class="text-slate-600 text-[8px] leading-relaxed">
                                    • Architected event-driven microservices processing 2.5M daily transactions on AWS ECS.
                                </p>
                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50 border border-amber-200 text-amber-900 text-[7.5px] font-semibold">
                                    <span>✨ AI Polished: Action-verb optimized</span>
                                </div>
                            </div>

                            {{-- Skills Chips --}}
                            <div class="flex items-center justify-between pt-0.5">
                                <div class="flex flex-wrap gap-1">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[8px] font-bold">PHP/Laravel</span>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[8px] font-bold">AWS ECS</span>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[8px] font-bold">Vue.js</span>
                                </div>
                                <button type="button" @click.stop="openSampleModal('modern', 'Modern Professional', 'en')"
                                        class="text-[9px] font-bold text-amber-700 hover:text-amber-800 hover:underline cursor-pointer flex items-center gap-0.5">
                                    <span>View Full Sample</span>
                                    <span>→</span>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 2: KOREAN STANDARD MINI PREVIEW --}}
                        <div x-show="heroTab === 'korean'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white text-slate-800 rounded-xl p-3.5 shadow-sm border border-slate-200 space-y-2.5 text-[9.5px]">
                            <div class="text-center pb-1.5 border-b border-slate-200">
                                <span class="font-black text-slate-950 text-xs tracking-widest">입 사 지 원 서</span>
                                <span class="text-[7.5px] text-slate-400 block">(이력서 &amp; 자기소개서)</span>
                            </div>

                            {{-- Official Korean Table --}}
                            <table class="w-full border-collapse border border-slate-300 text-[8px]">
                                <tr>
                                    <td class="border border-slate-200 bg-slate-100 font-bold p-1 text-center w-16">성 명</td>
                                    <td class="border border-slate-200 p-1 font-medium">김민수 (MIN SOO KIM)</td>
                                    <td rowspan="3" class="border border-slate-200 p-1 text-center bg-slate-50 w-14">
                                        <div class="w-9 h-12 mx-auto border border-dashed border-slate-400 rounded bg-slate-100 flex flex-col items-center justify-center text-center">
                                            <span class="text-[11px]">📷</span>
                                            <span class="text-[6px] text-slate-500 font-bold">3cm×4cm</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-slate-200 bg-slate-100 font-bold p-1 text-center">지원분야</td>
                                    <td class="border border-slate-200 p-1 font-semibold text-emerald-700">SW 엔지니어 / 개발</td>
                                </tr>
                                <tr>
                                    <td class="border border-slate-200 bg-slate-100 font-bold p-1 text-center">어학/병역</td>
                                    <td class="border border-slate-200 p-1">TOEIC 940 • 육군 만기전역</td>
                                </tr>
                            </table>

                            <div class="bg-slate-50 border border-slate-200 rounded p-1.5 text-[7.5px] space-y-0.5">
                                <span class="font-bold text-slate-800 block">자기소개서 (지원동기 &amp; 포부)</span>
                                <p class="text-slate-600 line-clamp-1">대용량 분산 아키텍처 구축 역량과 빠른 기술 습득력을 바탕으로 귀사의 성장에 기여...</p>
                            </div>

                            <div class="flex items-center justify-between pt-0.5">
                                <span class="text-[8px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                    Official Format
                                </span>
                                <button type="button" @click.stop="openSampleModal('korean_standard', 'Korean Standard (이력서)', 'ko')"
                                        class="text-[9px] font-bold text-emerald-700 hover:text-emerald-800 hover:underline cursor-pointer flex items-center gap-0.5">
                                    <span>View Full Sample</span>
                                    <span>→</span>
                                </button>
                            </div>
                        </div>

                        {{-- Floating Micro-pill Badges --}}
                        <div class="mt-3 flex items-center justify-between text-[10px] text-slate-400 px-1">
                            <span class="flex items-center gap-1">
                                <span class="text-amber-400">⚡</span> 1-Click Watermark-Free Export
                            </span>
                            <span class="flex items-center gap-1 text-slate-300">
                                <span class="text-emerald-400">🛡️</span> Recruiter-Vetted
                            </span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- =========================================================================
         2. TEMPLATE SELECTOR GALLERY
         ========================================================================= --}}
    <section id="templates" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-6 scroll-mt-16">

        {{-- Section Header & Filter Controls --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-4 border-b border-slate-200">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-md bg-amber-50 border border-amber-200 text-amber-900 font-extrabold text-[11px] uppercase tracking-wider">
                    <span>📑 Step 1</span>
                    <span>•</span>
                    <span>Choose Your Preferred Layout</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">
                    Select a Recruiter-Tested Template
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm max-w-2xl">
                    Every layout is mathematically budgeted for 1-page A4 printing, ATS readability, and executive impact. All templates support both <strong>English</strong> and <strong>한국어</strong>.
                </p>
            </div>

            {{-- Filter Pills --}}
            <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold shrink-0">
                <button type="button"
                        @click="activeFilter = 'all'"
                        :class="activeFilter === 'all' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    All (4)
                </button>
                <button type="button"
                        @click="activeFilter = 'korean'"
                        :class="activeFilter === 'korean' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer flex items-center gap-1">
                    <span>🇰🇷 Korean</span>
                </button>
                <button type="button"
                        @click="activeFilter = 'ats'"
                        :class="activeFilter === 'ats' ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    Corporate ATS
                </button>
                <button type="button"
                        @click="activeFilter = 'creative'"
                        :class="activeFilter === 'creative' ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg transition cursor-pointer">
                    Creative
                </button>
            </div>
        </div>

        {{-- 4 PROPORTIONAL DESIGNER TEMPLATE CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- 1. KOREAN STANDARD FORM (입사지원서) --}}
            <div x-show="activeFilter === 'all' || activeFilter === 'korean'"
                 class="group bg-white rounded-2xl border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1 relative">
                
                {{-- Badge --}}
                <div class="absolute top-2.5 right-2.5 z-10">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-900 text-slate-200 shadow-sm">
                        🇰🇷 Official Standard
                    </span>
                </div>

                {{-- Clean Miniature Paper Canvas --}}
                <div class="bg-gradient-to-b from-slate-100 to-slate-200/60 p-3.5 border-b border-slate-200 relative group-hover:bg-amber-50/40 transition">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white text-[8.5px] font-black uppercase">
                            Bilingual
                        </span>
                        <span class="text-[10px] font-black text-slate-500">입사지원서</span>
                    </div>

                    {{-- High-Fidelity Paper Card with Hover Overlay --}}
                    <div class="relative bg-white rounded-lg shadow-sm border border-slate-300 p-2.5 text-[8px] leading-tight text-slate-800 space-y-1 select-none transition group-hover:shadow-md">
                        <div class="text-center border-b pb-0.5">
                            <span class="text-[10px] font-black tracking-widest text-slate-950">입 사 지 원 서</span>
                        </div>

                        <table class="w-full border-collapse border border-slate-300 text-[7px]">
                            <tr>
                                <td class="border border-slate-200 bg-slate-100 font-bold p-0.5 text-center w-1/4">성 명</td>
                                <td class="border border-slate-200 p-0.5">김민수 (MIN SOO)</td>
                                <td rowspan="3" class="border border-slate-200 p-0.5 text-center bg-slate-50 w-1/4">
                                    <span class="text-[11px] block">📷</span>
                                    <span class="text-[5.5px] text-slate-500 font-bold">3cm×4cm</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-slate-200 bg-slate-100 font-bold p-0.5 text-center">지원분야</td>
                                <td class="border border-slate-200 p-0.5 font-semibold text-slate-800">SW 엔지니어</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-200 bg-slate-100 font-bold p-0.5 text-center">어학성적</td>
                                <td class="border border-slate-200 p-0.5">TOEIC 940</td>
                            </tr>
                        </table>

                        <div class="border border-slate-200 rounded p-1 bg-slate-50 text-[6.5px] text-slate-600 line-clamp-2">
                            <span class="font-bold text-slate-800">자기소개서:</span> 대용량 분산 아키텍처 구축 역량과 빠른 기술 습득력을 바탕으로...
                        </div>

                        {{-- Hover Quick-View Overlay --}}
                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                            <button type="button"
                                    @click.stop="openSampleModal('korean_standard', 'Korean Standard (이력서 & 자기소개서)', 'ko')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-400 text-slate-950 font-black text-xs shadow-md transform scale-95 group-hover:scale-100 transition">
                                👁️ Preview Live A4
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card Info & Buttons --}}
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <h3 class="text-sm font-black text-slate-950 group-hover:text-amber-600 transition">
                            Korean Standard Application
                        </h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Official tabular Korean format (이력서 &amp; 4-part 자기소개서) with 3x4 photo upload, military status, and TOEIC/JLPT scores.
                        </p>

                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">사진 3x4cm</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">자기소개서 4항목</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <button type="button"
                                @click.stop="openSampleModal('korean_standard', 'Korean Standard (이력서 & 자기소개서)', 'ko')"
                                class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:border-amber-500 hover:bg-amber-50/70 text-slate-800 font-bold text-[10.5px] uppercase tracking-wider text-center transition cursor-pointer">
                            👁️ View Live Sample
                        </button>

                        <a href="{{ route('resume-builder.create', ['template' => 'korean_standard']) }}"
                           class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider text-center block shadow-sm hover:shadow-md transition cursor-pointer">
                            Build With Korean Form →
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. MODERN PROFESSIONAL --}}
            <div x-show="activeFilter === 'all' || activeFilter === 'ats'"
                 class="group bg-white rounded-2xl border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1 relative">
                
                {{-- Badge --}}
                <div class="absolute top-2.5 right-2.5 z-10">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-900 text-slate-200 shadow-sm">
                        🔥 Most Popular
                    </span>
                </div>

                {{-- Clean Miniature Paper Canvas --}}
                <div class="bg-gradient-to-b from-slate-100 to-slate-200/60 p-3.5 border-b border-slate-200 relative group-hover:bg-amber-50/40 transition">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white text-[8.5px] font-black uppercase">
                            ATS Compliant
                        </span>
                        <span class="text-[10px] font-black text-slate-500">Corporate</span>
                    </div>

                    {{-- High-Fidelity Paper Card with Hover Overlay --}}
                    <div class="relative bg-white rounded-lg shadow-sm border border-slate-300 p-2.5 text-[8px] leading-tight text-slate-800 space-y-1 select-none transition group-hover:shadow-md">
                        <div class="border-b-2 border-slate-900 pb-0.5">
                            <span class="text-[10px] font-black text-slate-950 uppercase tracking-tight block">ALEX JOHNSON / 김민수</span>
                            <span class="text-[7.5px] font-bold text-amber-700 uppercase">Senior Full Stack Software Engineer</span>
                        </div>

                        <div class="space-y-0.5">
                            <span class="text-[7px] font-black uppercase text-slate-900 border-b border-slate-200 pb-0.5 block">Experience</span>
                            <div class="flex justify-between font-bold text-slate-900 text-[6.5px]">
                                <span>Senior Full Stack Engineer, NexGen</span>
                                <span class="text-slate-400 font-normal">2022 – Present</span>
                            </div>
                            <p class="text-[6px] text-slate-600 line-clamp-2">
                                • Architected event-driven microservices handling 2.5M daily transactions on AWS ECS.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <span class="px-1 py-0.5 bg-slate-100 text-slate-800 text-[6px] font-bold rounded">Laravel</span>
                            <span class="px-1 py-0.5 bg-slate-100 text-slate-800 text-[6px] font-bold rounded">AWS ECS</span>
                            <span class="px-1 py-0.5 bg-slate-100 text-slate-800 text-[6px] font-bold rounded">Docker</span>
                        </div>

                        {{-- Hover Quick-View Overlay --}}
                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                            <button type="button"
                                    @click.stop="openSampleModal('modern', 'Modern Professional', 'en')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-400 text-slate-950 font-black text-xs shadow-md transform scale-95 group-hover:scale-100 transition">
                                👁️ Preview Live A4
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card Info & Buttons --}}
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <h3 class="text-sm font-black text-slate-950 group-hover:text-amber-600 transition">
                            Modern Professional
                        </h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Clean dual-accent layout with navy typography, subtle gold badges, and high-density executive balance.
                        </p>

                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Balanced Layout</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Tech &amp; Corp</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <button type="button"
                                @click.stop="openSampleModal('modern', 'Modern Professional', 'en')"
                                class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:border-amber-500 hover:bg-amber-50/70 text-slate-800 font-bold text-[10.5px] uppercase tracking-wider text-center transition cursor-pointer">
                            👁️ View Live Sample
                        </button>

                        <a href="{{ route('resume-builder.create', ['template' => 'modern']) }}"
                           class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider text-center block shadow-sm hover:shadow-md transition cursor-pointer">
                            Build With Modern →
                        </a>
                    </div>
                </div>
            </div>

            {{-- 3. EXECUTIVE CLASSIC ATS --}}
            <div x-show="activeFilter === 'all' || activeFilter === 'ats'"
                 class="group bg-white rounded-2xl border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1 relative">
                
                {{-- Badge --}}
                <div class="absolute top-2.5 right-2.5 z-10">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-900 text-slate-200 shadow-sm">
                        🎯 100% ATS Safe
                    </span>
                </div>

                {{-- Clean Miniature Paper Canvas --}}
                <div class="bg-gradient-to-b from-amber-50/50 to-slate-200/60 p-3.5 border-b border-slate-200 relative group-hover:bg-amber-50/60 transition">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white text-[8.5px] font-black uppercase">
                            Harvard Standard
                        </span>
                        <span class="text-[10px] font-black text-slate-500 font-serif">Executive Serif</span>
                    </div>

                    {{-- High-Fidelity Paper Card with Hover Overlay --}}
                    <div class="relative bg-white rounded-lg shadow-sm border border-slate-300 p-2.5 text-[8px] leading-tight text-slate-800 space-y-1 font-serif select-none transition group-hover:shadow-md">
                        <div class="border-b border-black pb-0.5 text-center">
                            <span class="text-[10px] font-bold text-black uppercase tracking-wide block">ALEX JOHNSON / 김민수</span>
                            <span class="text-[7.5px] italic text-slate-700 block">Senior Full Stack Software Engineer</span>
                        </div>

                        <div class="space-y-0.5 pt-0.5">
                            <span class="text-[6.5px] font-bold uppercase text-black border-b border-black pb-0.5 block">Experience</span>
                            <div class="flex justify-between font-bold text-black text-[6.5px]">
                                <span>Senior Engineer, NexGen</span>
                                <span class="italic text-[6px]">2022 – Present</span>
                            </div>
                            <p class="text-[6px] text-slate-800 line-clamp-2">
                                • Architected event-driven microservices processing 2.5 million daily API transactions.
                            </p>
                        </div>

                        <div class="space-y-0.5 pt-0.5">
                            <span class="text-[6.5px] font-bold uppercase text-black border-b border-black pb-0.5 block">Education</span>
                            <p class="text-[6px] text-black">UC Berkeley • B.S. Computer Science</p>
                        </div>

                        {{-- Hover Quick-View Overlay --}}
                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                            <button type="button"
                                    @click.stop="openSampleModal('classic', 'Executive Classic ATS', 'en')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-400 text-slate-950 font-black text-xs shadow-md transform scale-95 group-hover:scale-100 transition">
                                👁️ Preview Live A4
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card Info & Buttons --}}
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <h3 class="text-sm font-black text-slate-950 group-hover:text-amber-600 transition">
                            Executive Classic ATS
                        </h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Single-column Harvard/Wharton standard layout strictly compliant with automated Applicant Tracking Systems.
                        </p>

                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Wharton Standard</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Fortune 500 Safe</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <button type="button"
                                @click.stop="openSampleModal('classic', 'Executive Classic ATS', 'en')"
                                class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:border-amber-500 hover:bg-amber-50/70 text-slate-800 font-bold text-[10.5px] uppercase tracking-wider text-center transition cursor-pointer">
                            👁️ View Live Sample
                        </button>

                        <a href="{{ route('resume-builder.create', ['template' => 'classic']) }}"
                           class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider text-center block shadow-sm hover:shadow-md transition cursor-pointer">
                            Build With Classic →
                        </a>
                    </div>
                </div>
            </div>

            {{-- 4. CREATIVE & IMPACT --}}
            <div x-show="activeFilter === 'all' || activeFilter === 'creative'"
                 class="group bg-white rounded-2xl border border-slate-200 hover:border-amber-400 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1 relative">
                
                {{-- Badge --}}
                <div class="absolute top-2.5 right-2.5 z-10">
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-slate-900 text-slate-200 shadow-sm">
                        🚀 High Impact
                    </span>
                </div>

                {{-- Clean Miniature Paper Canvas --}}
                <div class="bg-gradient-to-b from-indigo-50/50 to-slate-200/60 p-3.5 border-b border-slate-200 relative group-hover:bg-indigo-50/60 transition">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white text-[8.5px] font-black uppercase">
                            Startups &amp; Tech
                        </span>
                        <span class="text-[10px] font-black text-slate-500">Creative</span>
                    </div>

                    {{-- High-Fidelity Paper Card with Hover Overlay --}}
                    <div class="relative bg-white rounded-lg shadow-sm border border-slate-300 overflow-hidden text-[8px] leading-tight text-slate-800 select-none transition group-hover:shadow-md">
                        <div class="bg-slate-900 text-white p-2">
                            <span class="text-[9.5px] font-black text-amber-400 uppercase tracking-tight block">ALEX JOHNSON / 김민수</span>
                            <span class="text-[7px] font-bold text-slate-300 block">Senior Full Stack Engineer</span>
                        </div>

                        <div class="p-2 space-y-1">
                            <span class="text-[6.5px] font-black uppercase text-slate-900 border-b border-amber-500 pb-0.5 inline-block">Experience</span>
                            <p class="text-[6px] text-slate-600 line-clamp-1">Deployed real-time dashboards reducing server response latency by 40%.</p>

                            <div class="flex flex-wrap gap-0.5 pt-0.5">
                                <span class="px-1 py-0.5 bg-amber-50 text-amber-900 rounded text-[6px] font-bold border border-amber-200">Laravel</span>
                                <span class="px-1 py-0.5 bg-amber-50 text-amber-900 rounded text-[6px] font-bold border border-amber-200">Vue.js</span>
                            </div>
                        </div>

                        {{-- Hover Quick-View Overlay --}}
                        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[2px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-2">
                            <button type="button"
                                    @click.stop="openSampleModal('creative', 'Creative & Impact', 'en')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-400 text-slate-950 font-black text-xs shadow-md transform scale-95 group-hover:scale-100 transition">
                                👁️ Preview Live A4
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Card Info & Buttons --}}
                <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <h3 class="text-sm font-black text-slate-950 group-hover:text-amber-600 transition">
                            Creative &amp; Impact
                        </h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Bold header banner with amber highlights, project showcase badges, and visual tech tags.
                        </p>

                        <div class="flex flex-wrap gap-1 pt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Top Dark Banner</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-800 text-[9.5px] font-bold border border-slate-200">Portfolio Focus</span>
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <button type="button"
                                @click.stop="openSampleModal('creative', 'Creative & Impact', 'en')"
                                class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:border-amber-500 hover:bg-amber-50/70 text-slate-800 font-bold text-[10.5px] uppercase tracking-wider text-center transition cursor-pointer">
                            👁️ View Live Sample
                        </button>

                        <a href="{{ route('resume-builder.create', ['template' => 'creative']) }}"
                           class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-black text-xs uppercase tracking-wider text-center block shadow-sm hover:shadow-md transition cursor-pointer">
                            Build With Creative →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================================
         3. WHY CHOOSE REIAC RESUME BUILDER (FEATURE PILLARS)
         ========================================================================= --}}
    <section class="bg-white border-y border-slate-200/80 py-10 sm:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="text-center max-w-2xl mx-auto space-y-1.5">
                <span class="text-xs font-black text-amber-600 uppercase tracking-widest">Built for Serious Candidates</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">Why REIAC AI Resume Builder?</h2>
                <p class="text-slate-500 text-xs sm:text-sm">Designed to bypass automated ATS algorithms and impress human executive hiring managers alike.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Feature 1 --}}
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5 hover:bg-amber-50/30 hover:border-amber-200 transition group">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-bold shadow-sm group-hover:scale-105 transition">
                        🎯
                    </div>
                    <h3 class="text-sm font-black text-slate-950">Fact-Preserving AI</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Unlike generic bots that invent fake jobs, our AI strictly polishes your authentic input into corporate executive wording without hallucinations.</p>
                    <div class="text-[11px] font-bold text-amber-700 flex items-center gap-1 pt-0.5">
                        <span>✓ Zero Hallucinations</span>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5 hover:bg-emerald-50/30 hover:border-emerald-200 transition group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg font-bold shadow-sm group-hover:scale-105 transition">
                        🇰🇷
                    </div>
                    <h3 class="text-sm font-black text-slate-950">Bilingual Engine</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Switch seamlessly between English and Korean at any time. Every template dynamically translates section titles and adapts typography.</p>
                    <div class="text-[11px] font-bold text-emerald-700 flex items-center gap-1 pt-0.5">
                        <span>✓ Dynamic KR / EN Switch</span>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5 hover:bg-teal-50/30 hover:border-teal-200 transition group">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-lg font-bold shadow-sm group-hover:scale-105 transition">
                        📷
                    </div>
                    <h3 class="text-sm font-black text-slate-950">Korean Standard Form</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Complete with official 3x4 photo upload, military service status, test scores (TOEIC/JLPT), and 4-part self-introduction (자기소개서).</p>
                    <div class="text-[11px] font-bold text-teal-700 flex items-center gap-1 pt-0.5">
                        <span>✓ Standard Corporate Form</span>
                    </div>
                </div>

                {{-- Feature 4 --}}
                <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2.5 hover:bg-purple-50/30 hover:border-purple-200 transition group">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center text-lg font-bold shadow-sm group-hover:scale-105 transition">
                        📥
                    </div>
                    <h3 class="text-sm font-black text-slate-950">High-Res PDF Export</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Export pixel-perfect, print-ready A4 PDF files. Share with friends to permanently remove watermarks without paying any subscription fees.</p>
                    <div class="text-[11px] font-bold text-purple-700 flex items-center gap-1 pt-0.5">
                        <span>✓ 100% Free Forever</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         4. HOW IT WORKS SECTION (4 CONNECTED STEPS)
         ========================================================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
        <div class="text-center max-w-xl mx-auto space-y-1">
            <span class="text-xs font-black text-amber-600 uppercase tracking-widest">Fast &amp; Intuitive</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight">How It Works in 4 Simple Steps</h2>
            <p class="text-slate-500 text-xs sm:text-sm">From blank canvas to executive-ready resume in under 10 minutes.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 relative">
            {{-- Step 1 --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2 text-center hover:border-amber-400 transition">
                <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-900 font-black text-xs flex items-center justify-center mx-auto shadow-sm">1</span>
                <h4 class="font-black text-sm text-slate-900">Choose Template</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Select from Korean Standard, Modern, Classic ATS, or Creative based on your industry.</p>
            </div>

            {{-- Step 2 --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2 text-center hover:border-teal-400 transition">
                <span class="w-8 h-8 rounded-xl bg-teal-100 text-teal-900 font-black text-xs flex items-center justify-center mx-auto shadow-sm">2</span>
                <h4 class="font-black text-sm text-slate-900">Input Your Facts</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Fill out your real education, work experience, upload your photo, and list your core skills.</p>
            </div>

            {{-- Step 3 --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2 text-center hover:border-purple-400 transition">
                <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-900 font-black text-xs flex items-center justify-center mx-auto shadow-sm">3</span>
                <h4 class="font-black text-sm text-slate-900">AI Polish</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Click "Polish with AI" on summary &amp; experience bullets to elevate your phrasing to corporate standards.</p>
            </div>

            {{-- Step 4 --}}
            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2 text-center hover:border-emerald-400 transition">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-900 font-black text-xs flex items-center justify-center mx-auto shadow-sm">4</span>
                <h4 class="font-black text-sm text-slate-900">Review &amp; Export</h4>
                <p class="text-xs text-slate-500 leading-relaxed">Preview your resume in real-time, adjust styling or language, and download your clean PDF.</p>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         5. BOTTOM CALL TO ACTION BANNER
         ========================================================================= --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-[#0B132B] text-white rounded-2xl p-6 sm:p-10 shadow-xl border border-slate-800 text-center space-y-4 relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-xl mx-auto space-y-2.5">
                <span class="px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-black uppercase tracking-wider">Start Today Free</span>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">Ready to Land Your Dream Job?</h2>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">Create your bilingual, ATS-optimized resume in minutes. 100% free with no credit card required.</p>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                    <a href="#templates"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs uppercase tracking-wider shadow-md shadow-amber-400/20 transition transform hover:-translate-y-0.5">
                        <span>Choose Your Template Now</span>
                        <span>↓</span>
                    </a>
                    @auth
                        <a href="{{ route('resume-builder.my-resumes') }}"
                           class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-xs tracking-wide transition border border-white/20">
                            <span>📂 View Saved Resumes</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- =========================================================================
         6. PROFESSIONAL CLEAN LIVE PREVIEW MODAL
         ========================================================================= --}}
    <div x-show="previewModalOpen"
         x-cloak
         @keydown.escape.window="previewModalOpen = false"
         class="resume-preview-overlay">
        
        <div class="resume-preview-dialog shadow-2xl"
             role="dialog"
             aria-modal="true"
             aria-label="Live A4 resume preview">
            
            {{-- DESKTOP TOOLBAR (lg and up) --}}
            <div class="hidden lg:flex items-center justify-between gap-3 px-5 py-3 border-b border-slate-200 bg-white shrink-0 min-w-0">
                {{-- Left: Title & Subtitle + Language Switcher --}}
                <div class="flex items-center gap-3 min-w-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="font-extrabold text-sm sm:text-base text-slate-900 tracking-tight truncate" x-text="activeTemplateName"></h3>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 block">Recruiter-Tested Official A4 Standard</span>
                    </div>

                    <div class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold shrink-0 ml-1">
                        <button type="button" @click="setSampleLang('ko')"
                                :class="sampleLang === 'ko' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-md transition cursor-pointer">🇰🇷 한국어</button>
                        <button type="button" @click="setSampleLang('en')"
                                :class="sampleLang === 'en' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-md transition cursor-pointer">🌐 English</button>
                    </div>
                </div>

                {{-- Center: Template Tabs --}}
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold shrink-0">
                    <button type="button" @click="switchModalTemplate('korean_standard', 'Korean Standard (이력서)')"
                            :class="activeTemplateId === 'korean_standard' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-950'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer whitespace-nowrap">Korean Standard</button>
                    <button type="button" @click="switchModalTemplate('modern', 'Modern Professional')"
                            :class="activeTemplateId === 'modern' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-950'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer whitespace-nowrap">Modern ATS</button>
                    <button type="button" @click="switchModalTemplate('classic', 'Executive Classic ATS')"
                            :class="activeTemplateId === 'classic' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-950'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer whitespace-nowrap">Classic ATS</button>
                    <button type="button" @click="switchModalTemplate('creative', 'Creative & Impact')"
                            :class="activeTemplateId === 'creative' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-950'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer whitespace-nowrap">Creative</button>
                </div>

                {{-- Right: Zoom Mode, Full Tab Link, Use CTA, Close --}}
                <div class="flex items-center gap-2 shrink-0">
                    <div class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold">
                        <button type="button" @click="setZoom('fit')"
                                :class="zoomMode === 'fit' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-md transition cursor-pointer">Fit</button>
                        <button type="button" @click="setZoom(100)"
                                :class="zoomMode === 100 ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                                class="px-2.5 py-1 rounded-md transition cursor-pointer">100%</button>
                    </div>

                    <a :href="currentFullUrl" target="_blank" title="Open full sheet in new tab"
                       class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>

                    <a :href="currentCreateUrl"
                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white text-xs font-bold uppercase tracking-wider transition shadow-sm whitespace-nowrap">
                        Use This Template →
                    </a>

                    <button @click="previewModalOpen = false" title="Close (Esc)"
                            class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition flex items-center justify-center font-bold cursor-pointer text-base">✕</button>
                </div>
            </div>

            {{-- MOBILE & TABLET TOOLBAR (< lg) --}}
            <div class="flex lg:hidden flex-col bg-white border-b border-slate-200 shrink-0">
                {{-- Mobile Top Row: Title, Language, Close --}}
                <div class="px-3.5 py-2.5 flex items-center justify-between gap-2 border-b border-slate-100">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <h3 class="font-bold text-xs sm:text-sm text-slate-900 truncate" x-text="activeTemplateName"></h3>
                        </div>
                        <span class="text-[10px] text-slate-500 block">A4 Sheet Preview</span>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Language switch --}}
                        <div class="inline-flex p-0.5 rounded-lg bg-slate-100 border border-slate-200 text-[11px] font-bold">
                            <button type="button" @click="setSampleLang('ko')"
                                    :class="sampleLang === 'ko' ? 'bg-white text-slate-950 font-bold shadow-xs' : 'text-slate-600'"
                                    class="px-2 py-0.5 rounded transition">🇰🇷 KO</button>
                            <button type="button" @click="setSampleLang('en')"
                                    :class="sampleLang === 'en' ? 'bg-white text-slate-950 font-bold shadow-xs' : 'text-slate-600'"
                                    class="px-2 py-0.5 rounded transition">🌐 EN</button>
                        </div>

                        {{-- Open in new tab --}}
                        <a :href="currentFullUrl" target="_blank"
                           title="Open in new tab"
                           class="p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        {{-- Close --}}
                        <button @click="previewModalOpen = false"
                                class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 hover:text-slate-950 flex items-center justify-center font-bold text-xs cursor-pointer">✕</button>
                    </div>
                </div>

                {{-- Mobile Second Row: Scrollable Template Pills + Fit/100% + Use CTA --}}
                <div class="px-3 py-1.5 bg-slate-50 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar">
                    <div class="flex items-center gap-1 overflow-x-auto no-scrollbar shrink-0 text-[11px] font-bold">
                        <button type="button" @click="switchModalTemplate('korean_standard', 'Korean Standard (이력서)')"
                                :class="activeTemplateId === 'korean_standard' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200'"
                                class="px-2.5 py-1 rounded-md transition whitespace-nowrap shrink-0">Korean Standard</button>
                        <button type="button" @click="switchModalTemplate('modern', 'Modern Professional')"
                                :class="activeTemplateId === 'modern' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200'"
                                class="px-2.5 py-1 rounded-md transition whitespace-nowrap shrink-0">Modern ATS</button>
                        <button type="button" @click="switchModalTemplate('classic', 'Executive Classic ATS')"
                                :class="activeTemplateId === 'classic' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200'"
                                class="px-2.5 py-1 rounded-md transition whitespace-nowrap shrink-0">Classic ATS</button>
                        <button type="button" @click="switchModalTemplate('creative', 'Creative & Impact')"
                                :class="activeTemplateId === 'creative' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200'"
                                class="px-2.5 py-1 rounded-md transition whitespace-nowrap shrink-0">Creative</button>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <div class="inline-flex p-0.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold">
                            <button type="button" @click="setZoom('fit')"
                                    :class="zoomMode === 'fit' ? 'bg-slate-900 text-white' : 'text-slate-600'"
                                    class="px-2 py-0.5 rounded transition">Fit</button>
                            <button type="button" @click="setZoom(100)"
                                    :class="zoomMode === 100 ? 'bg-slate-900 text-white' : 'text-slate-600'"
                                    class="px-2 py-0.5 rounded transition">100%</button>
                        </div>

                        <a :href="currentCreateUrl"
                           class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white text-[11px] font-bold uppercase tracking-wider transition shadow-sm whitespace-nowrap">
                            Use →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Full Height Seamless Canvas Container --}}
            <div class="flex-1 bg-slate-100 relative min-h-0 overflow-hidden">
                <div x-show="iframeLoading"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100/90 z-20 backdrop-blur-xs">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-xs font-bold shadow-lg">
                        <svg class="animate-spin w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading Live A4 Sheet...</span>
                    </div>
                </div>

                <iframe x-ref="previewFrame"
                        :src="currentSampleUrl"
                        @load="onPreviewLoad()"
                        title="Live A4 resume preview"
                        class="w-full h-full border-0 block bg-slate-100"
                        style="width: 100%; height: 100%; min-height: 100%; border: none;"></iframe>
            </div>

        </div>
    </div>

</div>
</body>
</html>
