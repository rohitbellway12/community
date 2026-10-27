<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $resume ? $resume->title : 'New Resume' }} • REIAC Resume Studio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased selection:bg-amber-500 selection:text-slate-950">

<div class="min-h-screen bg-slate-100 text-slate-900 flex flex-col" x-data="resumeEditorApp(@js($initialData))">

    {{-- TOP STUDIO NAVIGATION BAR (EXECUTIVE REIAC THEME) --}}
    <header class="sticky top-0 z-40 bg-[#0b1329]/95 backdrop-blur-md border-b border-slate-800/80 px-3 sm:px-6 py-2.5 shadow-md shrink-0">
        <div class="max-w-[1780px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">

            {{-- Left: Back & Title --}}
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <a href="{{ route('resume-builder.my-resumes') }}"
                   class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center shrink-0 border border-slate-700/80"
                   title="Back to My Resumes">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>

                <div class="hidden sm:flex items-center gap-2 pr-3 border-r border-slate-800 shrink-0">
                    <a href="{{ route('community.index') }}" class="flex items-center gap-1.5" title="Community Home">
                        <span class="text-sm font-black tracking-wider text-amber-400">REIAC</span>
                        <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded">STUDIO</span>
                    </a>
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="resume.title" placeholder="Resume Title (e.g. Full Stack Developer CV)"
                               class="text-xs sm:text-sm md:text-base font-black text-white border-b border-transparent hover:border-slate-600 focus:border-amber-400 bg-transparent focus:ring-0 p-0 w-36 sm:w-60 md:w-72 truncate transition">
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full shrink-0"
                              :class="isSaving ? 'bg-amber-400/20 text-amber-300 animate-pulse border border-amber-400/30' : (hasUnsavedChanges ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30')"
                              x-text="isSaving ? 'Saving...' : (hasUnsavedChanges ? 'Unsaved' : 'Saved')">
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-400 block font-medium truncate">Professional Resume Builder</span>
                </div>
            </div>

            {{-- Right: Template, Language, and Action Buttons --}}
            <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2 shrink-0">
                {{-- Template Dropdown --}}
                <div class="relative">
                    <select x-model="resume.template"
                            class="text-xs font-bold bg-slate-900 border border-slate-700 hover:border-slate-600 rounded-xl px-2.5 sm:px-3 py-1.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer shadow-xs">
                        <option value="korean_standard">🇰🇷 Korean Standard (이력서/자기소개서)</option>
                        <option value="modern">Modern Professional ATS</option>
                        <option value="classic">Executive Classic ATS (No Photo)</option>
                        <option value="creative">Creative &amp; Impact</option>
                    </select>
                </div>

                {{-- Language Selector (English / Korean) --}}
                <div class="flex items-center bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs font-bold">
                    <button type="button" @click="resume.language = 'en'"
                            :class="resume.language === 'en' ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2 sm:px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                        <span>🌐</span>
                        <span>English</span>
                    </button>
                    <button type="button" @click="resume.language = 'ko'"
                            :class="resume.language === 'ko' ? 'bg-emerald-500 text-white font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2 sm:px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                        <span>🇰🇷</span>
                        <span>한국어</span>
                    </button>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-1.5 shrink-0 ml-auto sm:ml-0">
                    {{-- Quick Save --}}
                    <button type="button" @click="saveResume(false)" :disabled="isSaving"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                        <span x-show="!isSaving">💾 Save</span>
                        <span x-show="isSaving">Saving...</span>
                    </button>

                    {{-- Preview in New Tab --}}
                    <button type="button" @click="openPreview()"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                            title="Open standalone preview in new browser tab">
                        <span>👁️</span>
                        <span>Preview</span>
                    </button>

                    {{-- Download PDF --}}
                    <button type="button" @click="downloadPdf()"
                            class="px-3.5 sm:px-4 py-1.5 rounded-xl bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm hover:shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="hidden sm:inline">Export PDF</span>
                        <span class="sm:hidden">PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN WORKSPACE CONTAINER --}}
    <main class="flex-1 w-full max-w-4xl mx-auto p-3 sm:p-5 lg:p-6 transition-all duration-200">
        <div class="w-full space-y-4">

            <div class="bg-white border border-slate-200/90 rounded-2xl p-3 sm:p-4 shadow-sm space-y-3">
                {{-- Active Template Header Banner --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 border-b border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 font-semibold text-[11px] uppercase tracking-wider">Template:</span>
                        <span class="font-black text-slate-900 px-2.5 py-0.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-1.5 shadow-2xs"
                              x-text="resume.template === 'korean_standard' ? '🇰🇷 Korean Standard (이력서/자기소개서)' : (resume.template === 'modern' ? '📄 Modern Professional ATS' : (resume.template === 'classic' ? '🏛️ Executive Classic ATS' : '🎨 Creative & Impact'))"></span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px]">
                        <span class="text-slate-400 font-medium">Active Step:</span>
                        <span class="font-black text-amber-800 bg-amber-100/70 border border-amber-200 px-2.5 py-0.5 rounded-md uppercase tracking-wider" x-text="activeStep"></span>
                    </div>
                </div>

                {{-- WORKFLOW A: KOREAN STANDARD STEPS (BILINGUAL KOREAN & ENGLISH) --}}
                <div x-show="resume.template === 'korean_standard'" class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar sm:flex-wrap py-0.5">
                    <button type="button" @click="activeStep = 'personal'"
                            :class="activeStep === 'personal' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '👤 1. 인적사항/사진' : '👤 1. Personal & Photo'"></span>
                    </button>
                    <button type="button" @click="activeStep = 'education'"
                            :class="activeStep === 'education' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '🎓 2. 학력사항' : '🎓 2. Education'"></span>
                    </button>
                    <button type="button" @click="activeStep = 'experience'"
                            :class="activeStep === 'experience' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '💼 3. 경력사항' : '💼 3. Experience'"></span>
                    </button>
                    <button type="button" @click="activeStep = 'tests_military'"
                            :class="activeStep === 'tests_military' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '🎖️ 4. 어학/자격/병역' : '🎖️ 4. Tests & Military'"></span>
                    </button>
                    <button type="button" @click="activeStep = 'self_intro'"
                            :class="activeStep === 'self_intro' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '✍️ 5. 자기소개서 (4항목)' : '✍️ 5. Statement (4 Prompts)'"></span>
                        <span class="text-[9px] bg-amber-400 text-slate-950 px-1 py-0.2 rounded font-black">AI</span>
                    </button>
                    <button type="button" @click="activeStep = 'declaration'"
                            :class="activeStep === 'declaration' ? 'bg-emerald-600 text-white font-extrabold shadow-sm ring-2 ring-emerald-500/30' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.language === 'ko' ? '🖋️ 6. 서약, 미리보기 & 다운로드' : '🖋️ 6. Review & Download'"></span>
                    </button>
                </div>

                {{-- WORKFLOW B: GLOBAL ATS RESUMES (MODERN / CLASSIC / CREATIVE) --}}
                <div x-show="resume.template !== 'korean_standard'" class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto no-scrollbar sm:flex-wrap py-0.5">
                    <button type="button" @click="activeStep = 'personal'"
                            :class="activeStep === 'personal' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span x-text="resume.template === 'classic' ? '👤 1. Contact Info' : '👤 1. Contact & Photo'"></span>
                    </button>
                    <button type="button" @click="activeStep = 'summary'"
                            :class="activeStep === 'summary' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>📝 2. Summary</span>
                        <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                    </button>
                    <button type="button" @click="activeStep = 'experience'"
                            :class="activeStep === 'experience' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>💼 3. Experience</span>
                        <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                    </button>
                    <button type="button" @click="activeStep = 'education'"
                            :class="activeStep === 'education' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>🎓 4. Education</span>
                    </button>
                    <button type="button" @click="activeStep = 'skills'"
                            :class="activeStep === 'skills' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>⚡ 5. Skills &amp; Tools</span>
                        <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                    </button>
                    <button type="button" @click="activeStep = 'projects'"
                            :class="activeStep === 'projects' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>🚀 6. Projects &amp; Certs</span>
                    </button>
                    <button type="button" @click="activeStep = 'declaration'"
                            :class="activeStep === 'declaration' ? 'bg-amber-500 text-slate-950 font-black shadow-sm ring-2 ring-amber-400/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border border-slate-200/80'"
                            class="px-3 sm:px-3.5 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                        <span>🖋️ 7. Review &amp; Download</span>
                    </button>
                </div>
            </div>

            {{-- ===================== STEP 1: PERSONAL & PHOTO ===================== --}}
            <div x-show="activeStep === 'personal'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                            <span x-text="resume.template === 'korean_standard' ? 'Step 1 of 6' : 'Step 1 of 7'"></span>
                            <span>•</span>
                            <span>Candidate Information</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight"
                            x-text="resume.template === 'classic' ? 'Candidate Contact Information' : (resume.language === 'ko' ? '인적사항 및 사진 (Personal Information & Photo)' : 'Personal Information & Photo')"></h2>
                        <p class="text-xs text-slate-500 mt-0.5">Your contact information will appear on your resume header.</p>
                    </div>
                    <span class="self-start sm:self-auto text-[11px] font-bold px-3 py-1.5 rounded-xl bg-slate-100/80 text-slate-700 border border-slate-200 shadow-2xs">
                        <span x-text="resume.template === 'korean_standard' ? '🇰🇷 Official Table' : (resume.template === 'classic' ? '🏛️ Text-Only ATS' : 'Universal Profile')"></span>
                    </span>
                </div>

                {{-- Candidate Photo Upload Box (For templates that use photos: Korean Standard, Modern, Creative) --}}
                <div x-show="resume.template !== 'classic'" class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex flex-col sm:flex-row items-center gap-5 transition hover:border-slate-300">
                    <div class="w-24 h-32 rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden flex flex-col items-center justify-center text-center p-1 shrink-0 relative shadow-xs">
                        <template x-if="resume.personal_info.photo_url">
                            <img :src="resume.personal_info.photo_url" class="w-full h-full object-cover rounded-lg" alt="Candidate Photo">
                        </template>
                        <template x-if="!resume.personal_info.photo_url">
                            <div class="text-slate-400 text-[10px] space-y-1">
                                <span class="text-2xl block">📷</span>
                                <span class="font-bold block text-slate-600">3cm × 4cm</span>
                                <span class="text-[9px] text-slate-400">증명사진 / Photo</span>
                            </div>
                        </template>

                        {{-- Loading Spinner during Upload --}}
                        <div x-show="isUploadingPhoto" x-cloak class="absolute inset-0 bg-white/90 backdrop-blur-xs flex flex-col items-center justify-center text-amber-600">
                            <svg class="animate-spin h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span class="text-[9px] font-bold">Uploading...</span>
                        </div>
                    </div>

                    <div class="flex-1 space-y-2 w-full text-xs text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                            <label class="font-bold text-slate-800 text-xs sm:text-sm">Candidate Portrait / ID Photo (증명사진 업로드)</label>
                            <span class="text-[10px] font-medium text-slate-500">JPG, PNG, WebP (Max 5MB)</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Upload your photo from your device. It automatically positions on your resume preview &amp; PDF download.
                        </p>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                            <input type="file" x-ref="photoFileInput" @change="uploadPhotoFile($event)" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">

                            <button type="button" @click="$refs.photoFileInput.click()" :disabled="isUploadingPhoto"
                                    class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50 active:scale-95">
                                <span>📤</span>
                                <span x-text="resume.personal_info.photo_url ? 'Change Photo (사진 변경)' : 'Upload Photo (사진 업로드)'"></span>
                            </button>

                            <button type="button" @click="resume.personal_info.photo_url = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80'"
                                    class="px-3 py-2 rounded-xl bg-slate-200/80 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                                Use Demo Photo
                            </button>

                            <template x-if="resume.personal_info.photo_url">
                                <button type="button" @click="resume.personal_info.photo_url = ''"
                                        class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1 cursor-pointer">
                                    <span>🗑️</span>
                                    <span>Remove (삭제)</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>


                <div x-show="resume.template === 'classic'" class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200 flex items-center gap-3.5 text-xs text-slate-700">
                    <div class="w-12 h-12 rounded-xl bg-slate-200/80 flex items-center justify-center text-2xl shrink-0">
                        🏛️
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="font-black text-slate-900 text-xs sm:text-sm"
                            x-text="resume.language === 'ko' ? 'Executive Classic ATS 서식 (사진 미사용)' : 'Executive Classic ATS Standard (No Photo)'"></h4>
                        <p class="text-[11.5px] text-slate-600 leading-relaxed"
                           x-text="resume.language === 'ko' ? '하버드/와튼스쿨 글로벌 ATS 표준에 따라 자동 서류 전형 시스템(ATS)과의 100% 호환성을 위해 사진을 삽입하지 않습니다. 단일 열 텍스트 기반으로 평가관과 스캐너에 최적화됩니다.' : 'To ensure 100% compliance with automated Applicant Tracking Systems (ATS), candidate photos are omitted from the Executive Classic template (Harvard & Wharton ATS standard).'">
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Full Name (성명) <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="resume.personal_info.full_name" placeholder="e.g. 홍길동 (or Alex Morgan)"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">English Full Name (영문 성명)</label>
                        <input type="text" x-model="resume.personal_info.english_name" placeholder="e.g. GILDONG HONG"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Date of Birth &amp; Age (생년월일 및 나이)</label>
                        <input type="text" x-model="resume.personal_info.date_of_birth" placeholder="e.g. 1998.05.14 (만 26세)"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Gender (성별)</label>
                        <select x-model="resume.personal_info.gender"
                                class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none cursor-pointer shadow-2xs">
                            <option value="">Select Gender</option>
                            <option value="남 (Male)">남 (Male)</option>
                            <option value="여 (Female)">여 (Female)</option>
                            <option value="Not Specified">Not Specified</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Target Job Title / Applied Field (지원분야) <span class="text-rose-500">*</span></label>
                        <input type="text" x-model="resume.personal_info.job_title" placeholder="e.g. 백엔드 개발자 (Backend Engineer)"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Total Experience (총 경력)</label>
                        <input type="text" x-model="resume.personal_info.total_experience" placeholder="e.g. 신입, 2년 6개월, or 3 Years"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Email Address (이메일) <span class="text-rose-500">*</span></label>
                        <input type="email" x-model="resume.personal_info.email" placeholder="gildong@example.com"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Mobile Phone (휴대폰 연락처)</label>
                        <input type="text" x-model="resume.personal_info.phone" placeholder="010-1234-5678"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Emergency Contact (비상연락처)</label>
                        <input type="text" x-model="resume.personal_info.emergency_phone" placeholder="010-9876-5432 (부/모/배우자)"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">Current Address / Location (현주소)</label>
                        <input type="text" x-model="resume.personal_info.location" placeholder="e.g. 서울특별시 서초구 반포대로 14"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">LinkedIn Profile URL</label>
                        <input type="text" x-model="resume.personal_info.linkedin" placeholder="linkedin.com/in/username"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1.5 tracking-tight">GitHub / Portfolio URL</label>
                        <input type="text" x-model="resume.personal_info.portfolio" placeholder="portfolio.dev or github.com/username"
                               class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-900 transition-all outline-none placeholder:text-slate-400 shadow-2xs">
                    </div>
                </div>

                {{-- Bottom Action Bar --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                    <button type="button" @click="saveResume(false)" :disabled="isSaving"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                    </button>
                    <button type="button"
                            @click="activeStep = (resume.template === 'korean_standard' ? 'education' : 'summary')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                        <span x-text="resume.template === 'korean_standard' ? (resume.language === 'ko' ? '다음: 학력사항' : 'Next: Education') : 'Next: Summary & Objective'"></span>
                        <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                    </button>
                </div>
            </div>

                {{-- ===================== STEP 2: SUMMARY & OBJECTIVE ===================== --}}
                <div x-show="activeStep === 'summary'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 2 of 7</span>
                                <span>•</span>
                                <span>Professional Overview</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Career Objective &amp; Summary (전문 요약)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Write a 2-4 sentence pitch. You can type rough notes and click "Polish with AI".</p>
                        </div>

                        {{-- AI Polish Button --}}
                        <button type="button" @click="aiPolishSummary()" :disabled="isPolishingSummary || !resume.summary"
                                class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-2 cursor-pointer disabled:opacity-40 active:scale-95">
                            <span x-show="!isPolishingSummary">✨ Polish with AI</span>
                            <span x-show="isPolishingSummary" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span>AI Refining...</span>
                            </span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <textarea rows="7" x-model="resume.summary"
                                  placeholder="Write a few lines about your experience, technical core strengths, and future career goals. Click 'Polish with AI' to refine your wording into corporate prose..."
                                  class="w-full text-xs sm:text-sm bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-2xl p-4 leading-relaxed font-medium text-slate-800 transition-all outline-none placeholder:text-slate-400 shadow-2xs"></textarea>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-slate-500">
                            <span class="flex items-center gap-1.5 text-teal-700 font-medium">
                                <span>🛡️</span>
                                <span>Strictly preserves your facts; never invents qualifications or fake experience.</span>
                            </span>
                            <span class="font-bold text-slate-400" x-text="(resume.summary ? resume.summary.length : 0) + ' characters'"></span>
                        </div>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button" @click="activeStep = 'personal'"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span>Back</span>
                            </button>
                            <button type="button" @click="activeStep = 'experience'"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span>Next: Work Experience</span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 3: EDUCATION ===================== --}}
                <div x-show="activeStep === 'education'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span x-text="resume.template === 'korean_standard' ? 'Step 2 of 6' : 'Step 4 of 7'"></span>
                                <span>•</span>
                                <span>Academic Background</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Education History (학력사항)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">List your university, college, high school, or degrees in reverse chronological order.</p>
                        </div>

                        <button type="button" @click="addEducation()"
                                class="self-start sm:self-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                            <span>+ Add Degree</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(edu, index) in resume.education" :key="index">
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 text-xs space-y-3 relative group transition hover:border-slate-300">
                                <button type="button" @click="removeEducation(index)"
                                        class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold text-xs transition px-2 py-1 rounded-lg hover:bg-rose-50 flex items-center gap-1 cursor-pointer">
                                    <span>✕</span>
                                    <span>Remove</span>
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Institution / School (학교명) <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="edu.institution" placeholder="e.g. 서울대학교 (Seoul National Univ)"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-bold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Degree / Level (학위/구분) <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="edu.degree" placeholder="e.g. 학사 (Bachelor of Science)"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Major / Field of Study (전공/학과)</label>
                                        <input type="text" x-model="edu.field" placeholder="e.g. 컴퓨터공학 (Computer Science)"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">GPA / Grade (학점/성적)</label>
                                        <input type="text" x-model="edu.grade" placeholder="e.g. 3.8 / 4.5"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Period From (입학년월)</label>
                                        <input type="text" x-model="edu.start_date" placeholder="e.g. 2018.03"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Period To (졸업년월)</label>
                                        <input type="text" x-model="edu.end_date" placeholder="e.g. 2022.02" :disabled="edu.is_current"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs disabled:opacity-40 disabled:bg-slate-100">
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-slate-200/80">
                                    <input type="checkbox" :id="'edu_curr_' + index" x-model="edu.is_current" class="rounded bg-white border-slate-300 text-amber-500 focus:ring-0 cursor-pointer">
                                    <label :for="'edu_curr_' + index" class="text-xs text-slate-700 font-semibold cursor-pointer">
                                        Currently Enrolled / In Progress (재학 중)
                                    </label>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.education.length === 0" class="py-8 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No degrees added yet. Click <strong class="text-slate-700">+ Add Degree</strong> above.
                        </div>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button"
                                    @click="activeStep = (resume.template === 'korean_standard' ? 'personal' : 'experience')"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span x-text="resume.template === 'korean_standard' ? (resume.language === 'ko' ? '이전: 인적사항' : 'Back to Personal') : 'Back to Experience'"></span>
                            </button>
                            <button type="button"
                                    @click="activeStep = (resume.template === 'korean_standard' ? 'experience' : 'skills')"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span x-text="resume.template === 'korean_standard' ? (resume.language === 'ko' ? '다음: 경력사항' : 'Next: Work Experience') : 'Next: Skills & Tools'"></span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 4: WORK EXPERIENCE ===================== --}}
                <div x-show="activeStep === 'experience'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 3 of 7</span>
                                <span>•</span>
                                <span>Career History</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Work Experience &amp; Internships (경력사항)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">List professional roles, internships, or freelance work. Use AI to format accomplishments into high-impact bullets.</p>
                        </div>

                        <button type="button" @click="addExperience()"
                                class="self-start sm:self-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition flex items-center gap-1.5 cursor-pointer active:scale-95">
                            <span>+ Add Position</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(exp, index) in resume.experience" :key="index">
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 text-xs space-y-3 relative group transition hover:border-slate-300">
                                <button type="button" @click="removeExperience(index)"
                                        class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold text-xs transition px-2 py-1 rounded-lg hover:bg-rose-50 flex items-center gap-1 cursor-pointer">
                                    <span>✕</span>
                                    <span>Remove</span>
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Job Title / Position (직급/직책) <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="exp.title" placeholder="e.g. 주임 연구원 / Senior Backend Engineer"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-bold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Company / Organization (회사명) <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="exp.company" placeholder="e.g. 네이버 (Naver Corp)"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-bold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Department / Location (부서/근무지)</label>
                                        <input type="text" x-model="exp.location" placeholder="e.g. 플랫폼 개발실 (Seoul, Korea)"
                                               class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block font-bold text-slate-700 text-xs mb-1.5">Start (시작)</label>
                                            <input type="text" x-model="exp.start_date" placeholder="2022.03"
                                                   class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-xs mb-1.5">End (종료)</label>
                                            <input type="text" x-model="exp.end_date" placeholder="2024.01" :disabled="exp.is_current"
                                                   class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs disabled:opacity-40 disabled:bg-slate-100">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <input type="checkbox" :id="'exp_curr_' + index" x-model="exp.is_current" class="rounded bg-white border-slate-300 text-amber-500 focus:ring-0 cursor-pointer">
                                    <label :for="'exp_curr_' + index" class="text-xs text-slate-700 font-semibold cursor-pointer">
                                        Currently Working Here (현재 재직 중)
                                    </label>
                                </div>

                                {{-- AI Bullet Enhancer --}}
                                <div class="pt-3 border-t border-slate-200/80 space-y-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                                        <label class="font-bold text-slate-800 text-xs">Key Responsibilities &amp; Achievements (주요업무 및 성과)</label>
                                        <button type="button" @click="aiPolishExperience(index)" :disabled="exp.isPolishing || (!exp.rawNotes && (!exp.points || exp.points.length === 0))"
                                                class="self-start sm:self-auto px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-40 active:scale-95">
                                            <span x-show="!exp.isPolishing">✨ AI Enhance Bullets</span>
                                            <span x-show="exp.isPolishing">Formatting...</span>
                                        </button>
                                    </div>

                                    <textarea rows="3" x-model="exp.rawNotes"
                                              placeholder="Type rough notes about what you built or accomplished here, then click 'AI Enhance Bullets' to transform them into recruiter-ready bullet points..."
                                              class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3 text-slate-800 text-xs sm:text-sm leading-relaxed outline-none transition shadow-2xs"></textarea>

                                    {{-- Active Bullets List --}}
                                    <div class="space-y-1.5" x-show="exp.points && exp.points.length > 0">
                                        <span class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Formatted Bullet Points:</span>
                                        <template x-for="(pt, pidx) in exp.points" :key="pidx">
                                            <div class="flex items-start gap-2 bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                                                <span class="text-amber-500 text-xs mt-0.5">•</span>
                                                <input type="text" x-model="exp.points[pidx]" class="flex-1 bg-transparent border-none text-xs text-slate-800 p-0 focus:ring-0">
                                                <button type="button" @click="exp.points.splice(pidx, 1)" class="text-slate-400 hover:text-rose-600 text-xs px-1">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.experience.length === 0" class="py-8 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No work experience added yet. Click <strong class="text-slate-700">+ Add Position</strong> above (or skip if entry level).
                        </div>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button"
                                    @click="activeStep = (resume.template === 'korean_standard' ? 'education' : 'summary')"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span x-text="resume.template === 'korean_standard' ? (resume.language === 'ko' ? '이전: 학력사항' : 'Back to Education') : 'Back to Summary'"></span>
                            </button>
                            <button type="button"
                                    @click="activeStep = (resume.template === 'korean_standard' ? 'tests_military' : 'education')"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span x-text="resume.template === 'korean_standard' ? (resume.language === 'ko' ? '다음: 어학/자격/병역' : 'Next: Tests & Military') : 'Next: Education'"></span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 5: TESTS, MILITARY & COMPUTER SKILLS ===================== --}}
                <div x-show="activeStep === 'tests_military'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-emerald-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 4 of 6</span>
                                <span>•</span>
                                <span>Korean Corporate Standard</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Language Tests, Military &amp; IT Skills (어학/병역/전산)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Mandatory fields for Korean job applications and major conglomerates.</p>
                        </div>
                    </div>

                    {{-- 1. Military Service Box --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-3.5 transition hover:border-slate-300">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>🎖️</span>
                                <span>1. Military Service (병역사항)</span>
                            </h3>
                            <span class="text-[11px] font-bold text-slate-500 bg-white px-2.5 py-0.5 rounded-lg border border-slate-200">군필 / 면제 / 비해당</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">Status (병역구분) <span class="text-rose-500">*</span></label>
                                <select x-model="resume.military_service.status"
                                        class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition cursor-pointer shadow-2xs">
                                    <option value="군필 (Completed)">군필 (Completed)</option>
                                    <option value="면제 (Exempted)">면제 (Exempted)</option>
                                    <option value="미필 (Unfulfilled)">미필 (Unfulfilled)</option>
                                    <option value="비해당 (Not Applicable)">비해당 (Not Applicable)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">Branch (군별)</label>
                                <input type="text" x-model="resume.military_service.branch" placeholder="e.g. 육군 (ROK Army)"
                                       class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">Final Rank (계급)</label>
                                <input type="text" x-model="resume.military_service.rank" placeholder="e.g. 병장 (Sergeant)"
                                       class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">Service Period (복무기간)</label>
                                <input type="text" x-model="resume.military_service.period" placeholder="e.g. 2018.06 ~ 2020.02"
                                       class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 text-xs mb-1.5">Discharge / Exemption Reason (전역구분 / 면제사유)</label>
                                <input type="text" x-model="resume.military_service.discharge_reason" placeholder="e.g. 만기전역 (Normal Discharge)"
                                       class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Official Language Tests Box --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-3.5 transition hover:border-slate-300">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>🗣️</span>
                                <span>2. Language Proficiency Tests (공인어학성적)</span>
                            </h3>
                            <button type="button" @click="addLanguageTest()"
                                    class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition cursor-pointer shadow-2xs active:scale-95">
                                + Add Score
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(test, index) in resume.language_tests" :key="index">
                                <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 text-xs space-y-2 relative shadow-2xs">
                                    <button type="button" @click="removeLanguageTest(index)" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-rose-600 font-bold p-1">✕</button>
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5 pr-6">
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Test Name *</label>
                                            <input type="text" x-model="test.test_name" placeholder="TOEIC / OPIc / JLPT" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 font-bold outline-none focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Score / Grade *</label>
                                            <input type="text" x-model="test.score" placeholder="950 / AL / N1" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-emerald-700 font-black outline-none focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Date</label>
                                            <input type="text" x-model="test.acquisition_date" placeholder="2023.09.15" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Authority</label>
                                            <input type="text" x-model="test.organization" placeholder="ETS / ACTFL" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-amber-500">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="resume.language_tests.length === 0" class="py-5 text-center bg-white rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                                No test scores added yet. Click <strong>+ Add Score</strong> (e.g. TOEIC, OPIc, TOEFL).
                            </div>
                        </div>
                    </div>

                    {{-- 3. Computer & IT Skills (상/중/하) --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-3.5 transition hover:border-slate-300">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2.5">
                            <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>💻</span>
                                <span>3. Computer &amp; IT Competencies (전산 및 OA 활용능력)</span>
                            </h3>
                            <button type="button" @click="addComputerSkill()"
                                    class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer shadow-2xs active:scale-95">
                                + Add Tool
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(skill, index) in resume.computer_skills" :key="index">
                                <div class="p-3.5 rounded-xl bg-white border border-slate-200/90 text-xs space-y-2.5 relative shadow-2xs">
                                    <button type="button" @click="removeComputerSkill(index)" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-rose-600 font-bold p-1">✕</button>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pr-6">
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Category (분류)</label>
                                            <input type="text" x-model="skill.category" placeholder="프로그래밍 / OA / DB" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 font-bold outline-none focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Tool / Language</label>
                                            <input type="text" x-model="skill.tool_name" placeholder="Python, Java, Excel" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 font-bold outline-none focus:border-amber-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Proficiency Level</label>
                                            <select x-model="skill.proficiency" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-amber-500 cursor-pointer">
                                                <option value="상 (Advanced)">상 (Advanced)</option>
                                                <option value="중상 (Upper-Intermediate)">중상 (Upper-Intermediate)</option>
                                                <option value="중 (Intermediate)">중 (Intermediate)</option>
                                                <option value="초급 (Beginner)">초급 (Beginner)</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-bold text-slate-700 text-[11px] mb-1">Description (활용 내용)</label>
                                            <input type="text" x-model="skill.description" placeholder="e.g. 대규모 데이터 처리 및 분산 시스템 아키텍처 구현" class="w-full bg-slate-50/70 border border-slate-200 rounded-lg px-2.5 py-2 text-slate-900 outline-none focus:border-amber-500">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button" @click="activeStep = 'experience'"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span x-text="resume.language === 'ko' ? '이전: 경력사항' : 'Back to Experience'"></span>
                            </button>
                            <button type="button" @click="activeStep = 'self_intro'"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span x-text="resume.language === 'ko' ? '다음: 자기소개서 (4항목)' : 'Next: Self-Introduction (4 Prompts)'"></span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 6: KOREAN 4-PART SELF-INTRODUCTION ===================== --}}
                <div x-show="activeStep === 'self_intro'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 5 of 6</span>
                                <span>•</span>
                                <span>Official 4-Question Statement</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Korean Self-Introduction (자기소개서 4개 항목)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Standard questions used by JobKorea, Saramin, and major enterprises. Click "AI Polish" on any item to elevate your writing.</p>
                        </div>
                    </div>

                    {{-- 1. 성장과정 --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-2.5 transition hover:border-slate-300">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-black text-slate-900">
                                1. 성장과정 (Growth Background &amp; Upbringing)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('growth')" :disabled="isPolishingSelfIntro === 'growth' || !resume.self_introduction.growth"
                                    class="self-start sm:self-auto px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-40 active:scale-95 shadow-2xs">
                                <span x-show="isPolishingSelfIntro !== 'growth'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'growth'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.growth"
                                  placeholder="어릴 적 가치관 형성, 학창 시절 관심 분야 탐구 과정 등을 서술하세요..."
                                  class="w-full text-xs sm:text-sm bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3.5 text-slate-900 leading-relaxed outline-none transition shadow-2xs"></textarea>
                    </div>

                    {{-- 2. 성격소개 및 장단점 --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-2.5 transition hover:border-slate-300">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-black text-slate-900">
                                2. 성격소개 및 장단점 (Personality &amp; Strengths/Weaknesses)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('personality')" :disabled="isPolishingSelfIntro === 'personality' || !resume.self_introduction.personality"
                                    class="self-start sm:self-auto px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-40 active:scale-95 shadow-2xs">
                                <span x-show="isPolishingSelfIntro !== 'personality'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'personality'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.personality"
                                  placeholder="자신의 성격적 강점, 위기 대처 방식, 협업 태도 및 단점 보완 노력을 서술하세요..."
                                  class="w-full text-xs sm:text-sm bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3.5 text-slate-900 leading-relaxed outline-none transition shadow-2xs"></textarea>
                    </div>

                    {{-- 3. 주요경력 및 직무역량 --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-2.5 transition hover:border-slate-300">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-black text-slate-900">
                                3. 주요경력 및 직무수행 역량 (Key Projects &amp; Job Competencies)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('strengths')" :disabled="isPolishingSelfIntro === 'strengths' || !resume.self_introduction.strengths"
                                    class="self-start sm:self-auto px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-40 active:scale-95 shadow-2xs">
                                <span x-show="isPolishingSelfIntro !== 'strengths'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'strengths'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.strengths"
                                  placeholder="실무 인턴십, 동아리 프로젝트, 오픈소스 기여 등 핵심 직무 경험을 구체적 성과와 함께 서술하세요..."
                                  class="w-full text-xs sm:text-sm bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3.5 text-slate-900 leading-relaxed outline-none transition shadow-2xs"></textarea>
                    </div>

                    {{-- 4. 지원동기 및 입사 후 포부 --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 space-y-2.5 transition hover:border-slate-300">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-xs sm:text-sm font-black text-slate-900">
                                4. 지원동기 및 입사 후 포부 (Motivation &amp; Future Aspirations)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('motivation_and_aspirations')" :disabled="isPolishingSelfIntro === 'motivation_and_aspirations' || !resume.self_introduction.motivation_and_aspirations"
                                    class="self-start sm:self-auto px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs transition flex items-center gap-1 cursor-pointer disabled:opacity-40 active:scale-95 shadow-2xs">
                                <span x-show="isPolishingSelfIntro !== 'motivation_and_aspirations'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'motivation_and_aspirations'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.motivation_and_aspirations"
                                  placeholder="지원 회사/직무에 지원하게 된 계기와 입사 후 1년/3년 차에 달성하고자 하는 비전을 서술하세요..."
                                  class="w-full text-xs sm:text-sm bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3.5 text-slate-900 leading-relaxed outline-none transition shadow-2xs"></textarea>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button" @click="activeStep = 'tests_military'"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span x-text="resume.language === 'ko' ? '이전: 어학/자격/병역' : 'Back to Tests & Military'"></span>
                            </button>
                            <button type="button" @click="activeStep = 'declaration'"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span x-text="resume.language === 'ko' ? '다음: 서약 & 다운로드' : 'Next: Review & Download'"></span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 7: SKILLS & TOOLS ===================== --}}
                <div x-show="activeStep === 'skills'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 5 of 7</span>
                                <span>•</span>
                                <span>Keywords &amp; Competencies</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Skills &amp; Competencies (보유 기술)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Add hard and soft skills. Recruiters and ATS scanners match keywords to rank candidates.</p>
                        </div>

                        <button type="button" @click="aiSuggestSkills()" :disabled="isSuggestingSkills"
                                class="self-start sm:self-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50 active:scale-95">
                            <span x-show="!isSuggestingSkills">✨ AI Suggest Skills</span>
                            <span x-show="isSuggestingSkills">Suggesting...</span>
                        </button>
                    </div>

                    {{-- Add Skill Input --}}
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" x-model="newSkillInput" @keydown.enter.prevent="addSkill()"
                               placeholder="Type a skill (e.g. React, Node.js, Spring Boot, Figma) and press Enter..."
                               class="flex-1 text-xs sm:text-sm bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-4 py-2.5 text-slate-900 font-semibold outline-none transition shadow-2xs">
                        <button type="button" @click="addSkill()"
                                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition cursor-pointer active:scale-95 shadow-2xs">
                            + Add Skill
                        </button>
                    </div>

                    {{-- Active Skills Cloud --}}
                    <div class="flex flex-wrap gap-2.5 p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200 min-h-[90px]">
                        <template x-for="(skill, index) in resume.skills" :key="index">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 font-bold text-xs sm:text-sm shadow-2xs hover:border-slate-300 transition">
                                <span x-text="skill"></span>
                                <button type="button" @click="removeSkill(index)" class="text-slate-400 hover:text-rose-600 font-bold ml-1 text-xs">✕</button>
                            </span>
                        </template>
                        <div x-show="resume.skills.length === 0" class="text-slate-400 text-xs italic self-center">
                            No skills added yet. Type a skill above or click "✨ AI Suggest Skills".
                        </div>
                    </div>

                    {{-- Bottom Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button" @click="activeStep = 'education'"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span>Back to Education</span>
                            </button>
                            <button type="button" @click="activeStep = 'projects'"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span>Next: Projects &amp; Certs</span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP 8: PROJECTS & CERTIFICATIONS ===================== --}}
                <div x-show="activeStep === 'projects'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span>Step 6 of 7</span>
                                <span>•</span>
                                <span>Accomplishments</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Projects &amp; Certifications (프로젝트 및 자격증)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Highlight major software/academic projects and verified professional certifications.</p>
                        </div>
                    </div>

                    {{-- Projects Repeater --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs sm:text-sm font-black uppercase text-slate-900 tracking-wider">🚀 Featured Projects (주요 프로젝트)</h3>
                            <button type="button" @click="addProject()" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition cursor-pointer border border-slate-200">
                                + Add Project
                            </button>
                        </div>

                        <template x-for="(proj, index) in resume.projects" :key="index">
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/70 border border-slate-200/90 text-xs space-y-3 relative transition hover:border-slate-300">
                                <button type="button" @click="removeProject(index)" class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold px-2 py-1 rounded-lg hover:bg-rose-50 transition">✕ Remove</button>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Project Title <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="proj.title" placeholder="e.g. AI-Powered Resume Studio" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-bold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Tech Stack</label>
                                        <input type="text" x-model="proj.tech" placeholder="e.g. Laravel, Alpine.js, Tailwind, OpenAI" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3.5 py-2.5 text-slate-900 font-semibold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Project Description / Link</label>
                                        <textarea rows="2" x-model="proj.description" placeholder="Briefly describe what problem it solved and your role..." class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl p-3 text-slate-900 text-xs sm:text-sm leading-relaxed outline-none transition shadow-2xs"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.projects.length === 0" class="py-6 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No projects added yet. Click <strong class="text-slate-700">+ Add Project</strong> if applicable.
                        </div>
                    </div>

                    {{-- Certifications Repeater --}}
                    <div class="space-y-4 pt-5 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs sm:text-sm font-black uppercase text-slate-900 tracking-wider">📜 Licenses &amp; Certifications (자격증/면허)</h3>
                            <button type="button" @click="addCertification()" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition cursor-pointer border border-slate-200">
                                + Add Certification
                            </button>
                        </div>

                        <template x-for="(cert, index) in resume.certifications" :key="index">
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/70 border border-slate-200/90 text-xs space-y-2 relative transition hover:border-slate-300">
                                <button type="button" @click="removeCertification(index)" class="absolute top-2.5 right-3 text-slate-400 hover:text-rose-600 font-bold px-2 py-1 rounded-lg hover:bg-rose-50 transition">✕</button>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Certificate Name <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="cert.name" placeholder="정보처리기사 / AWS SAA" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3 py-2 text-slate-900 font-bold text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Issuer / Authority</label>
                                        <input type="text" x-model="cert.issuer" placeholder="한국산업인력공단 / Amazon" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3 py-2 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 text-xs mb-1.5">Year / Date</label>
                                        <input type="text" x-model="cert.year" placeholder="2023.11" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl px-3 py-2 text-slate-900 text-xs sm:text-sm outline-none transition shadow-2xs">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.certifications.length === 0" class="py-6 text-center bg-slate-50/70 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No certifications added. Click <strong class="text-slate-700">+ Add Certification</strong>.
                        </div>
                    </div>

                    {{-- Finish Action Bar --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition cursor-pointer hover:border-slate-300 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span x-text="isSaving ? 'Saving Draft...' : 'Save Draft'"></span>
                        </button>
                        <div class="flex items-center gap-2 justify-end">
                            <button type="button" @click="activeStep = 'skills'"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer">
                                <span>←</span>
                                <span>Back to Skills</span>
                            </button>
                            <button type="button" @click="activeStep = 'declaration'"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider shadow-sm hover:shadow-md transition-all cursor-pointer group active:scale-[0.98]">
                                <span>Next: Review &amp; Download</span>
                                <span class="transition-transform group-hover:translate-x-1 font-bold">→</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ===================== STEP: DECLARATION, DATE & SIGNATURE ===================== --}}
                <div x-show="activeStep === 'declaration'" x-cloak class="bg-white border border-slate-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-[11px] font-black uppercase tracking-wider mb-1">
                                <span x-text="resume.template === 'korean_standard' ? 'Step 6 of 6' : 'Step 7 of 7'"></span>
                                <span>•</span>
                                <span>Official Certification</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                <span x-text="resume.template === 'korean_standard' ? '서약 및 서명 (Declaration & Signature)' : 'Declaration, Date & Candidate Signature'"></span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Certifies the authenticity of all application details. Appears at the very bottom of your resume on print and PDF export.
                            </p>
                        </div>
                        <span class="self-start sm:self-auto text-[11px] font-bold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                            🖋️ Signature Block
                        </span>
                    </div>

                    {{-- Declaration Text Card --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl shrink-0">📜</span>
                            <div class="space-y-1">
                                <h4 class="text-xs font-black uppercase text-amber-950 tracking-wider">Formal Certification Statement</h4>
                                <p class="text-xs sm:text-sm text-amber-900 font-medium leading-relaxed italic"
                                   x-text="resume.language === 'ko' ? '위의 모든 기재사항은 사실과 다름없음을 확인합니다.' : 'I hereby declare that all the information furnished above is true and complete to the best of my knowledge.'">
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Date & Signature Inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Date Field --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 tracking-tight">
                                <span x-text="resume.language === 'ko' ? '작성일자 (Declaration Date)' : 'Declaration Date'"></span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   x-model="resume.personal_info.declaration_date"
                                   placeholder="e.g. {{ date('F d, Y') }}"
                                   class="w-full px-4 py-2.5 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 transition shadow-2xs outline-none">
                            <span class="text-[10.5px] text-slate-400 block">Default: Current date (e.g. {{ date('F d, Y') }})</span>
                        </div>

                        {{-- Signature Name Field --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 tracking-tight">
                                <span x-text="resume.language === 'ko' ? '지원자(작성자) 성명 (Signatory Name)' : 'Applicant Signature Name'"></span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   x-model="resume.personal_info.signature_name"
                                   :placeholder="resume.personal_info.full_name || 'Candidate Full Name'"
                                   class="w-full px-4 py-2.5 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-400/15 rounded-xl text-xs sm:text-sm font-semibold text-slate-900 transition shadow-2xs outline-none">
                            <span class="text-[10.5px] text-slate-400 block">Defaults to your full candidate name</span>
                        </div>
                    </div>

                    {{-- Live Appearance Preview Strip --}}
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200 text-xs space-y-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Live Appearance on Document:</span>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-2xs">
                            <div class="text-slate-600">
                                <strong class="text-slate-900 font-bold" x-text="resume.language === 'ko' ? '작성일자 : ' : 'Date : '"></strong>
                                <span class="font-semibold text-slate-900" x-text="resume.personal_info.declaration_date || '{{ date('F d, Y') }}'"></span>
                            </div>
                            <div class="text-slate-600">
                                <strong class="text-slate-900 font-bold" x-text="resume.language === 'ko' ? '지원자(작성자) : ' : 'Applicant Signature : '"></strong>
                                <span class="border-b-2 border-slate-800 px-3 font-black text-slate-950 inline-block"
                                      x-text="resume.personal_info.signature_name || resume.personal_info.full_name || 'Your Full Name'"></span>
                                <span class="text-slate-400 text-[11px] ml-1" x-text="resume.language === 'ko' ? '(인 / 서명)' : '(Signed)'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Final Bottom Actions --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-5 border-t border-slate-100">
                        <button type="button"
                                @click="activeStep = (resume.template === 'korean_standard' ? 'self_intro' : 'projects')"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition cursor-pointer">
                            <span>←</span>
                            <span>Previous Section</span>
                        </button>

                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="openPreview()"
                                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs transition cursor-pointer border border-slate-200 shadow-2xs">
                                <span>👁️ Preview / Print</span>
                            </button>
                            <button type="button" @click="saveResume(false)" :disabled="isSaving"
                                    class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                                <span>💾</span>
                                <span x-text="isSaving ? 'Saving...' : 'Save Resume'"></span>
                            </button>
                            <button type="button" @click="downloadPdf()"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs sm:text-sm uppercase tracking-wider transition cursor-pointer shadow-md hover:shadow-lg active:scale-95">
                                <span>📥</span>
                                <span>Download PDF</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Watermark Status Card (Only shown on Final Review & Download Step) --}}
                <div x-show="activeStep === 'declaration'" x-cloak
                     class="p-4 sm:p-5 rounded-2xl border text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs transition"
                     :class="resume.is_watermark_removed ? 'bg-emerald-50/80 border-emerald-200 text-emerald-950' : 'bg-amber-50/80 border-amber-200 text-amber-950'">
                    <div>
                        <div class="font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span x-text="resume.is_watermark_removed ? '✓ Clean Watermark-Free Export' : '🔒 Free Watermarked Export'"></span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed"
                           x-text="resume.is_watermark_removed ? 'Your PDF exports 100% clean without any REIAC watermark branding.' : 'Exports include the REIAC watermark. Share with friends to unlock a 100% clean PDF.'">
                        </p>
                    </div>
                    <template x-if="!resume.is_watermark_removed">
                        <a href="{{ route('resume-builder.my-resumes') }}" class="self-start sm:self-auto px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs whitespace-nowrap shrink-0 transition shadow-2xs active:scale-95">
                            Unlock Watermark →
                        </a>
                    </template>
                </div>

            </div>
        </div>
    </main>
{{-- ALPINE APPLICATION LOGIC --}}
<script>
function resumeEditorApp(initial) {
    return {
        activeStep: initial.template === 'korean_standard' ? 'personal' : 'personal',
        hasUnsavedChanges: false,
        resume: {
            id: initial.id || null,
            title: initial.title || 'My Professional Resume',
            template: initial.template || 'korean_standard',
            language: initial.language || 'en',
            personal_info: {
                full_name: initial.personal_info?.full_name || '',
                english_name: initial.personal_info?.english_name || '',
                date_of_birth: initial.personal_info?.date_of_birth || initial.personal_info?.dob || '',
                gender: initial.personal_info?.gender || '',
                photo_url: initial.personal_info?.photo_url || '',
                total_experience: initial.personal_info?.total_experience || '',
                emergency_phone: initial.personal_info?.emergency_phone || '',
                job_title: initial.personal_info?.job_title || '',
                email: initial.personal_info?.email || '',
                phone: initial.personal_info?.phone || '',
                location: initial.personal_info?.location || '',
                linkedin: initial.personal_info?.linkedin || '',
                github: initial.personal_info?.github || '',
                portfolio: initial.personal_info?.portfolio || '',
                declaration_date: initial.personal_info?.declaration_date || '{{ date('F d, Y') }}',
                signature_name: initial.personal_info?.signature_name || initial.personal_info?.full_name || '',
            },
            summary: initial.summary || '',
            education: initial.education || [],
            experience: (initial.experience || []).map(e => ({
                ...e,
                rawNotes: '',
                isPolishing: false,
                points: e.points || []
            })),
            skills: initial.skills || [],
            projects: initial.projects || [],
            certifications: initial.certifications || [],
            military_service: initial.military_service || {
                status: '군필 (Completed)',
                branch: '',
                rank: '',
                period: '',
                discharge_reason: ''
            },
            language_tests: initial.language_tests || [],
            computer_skills: initial.computer_skills || [],
            self_introduction: initial.self_introduction || {
                growth: '',
                personality: '',
                strengths: '',
                motivation_and_aspirations: ''
            },
            is_watermark_removed: initial.is_watermark_removed || false,
        },
        newSkillInput: '',
        isSaving: false,
        isUploadingPhoto: false,
        isPolishingSummary: false,
        isPolishingSelfIntro: null,
        isSuggestingSkills: false,

        init() {
            // Track changes
            this.$watch('resume', () => {
                this.hasUnsavedChanges = true;
            }, { deep: true });

            // Auto-switch steps if active step is not relevant to template
            this.$watch('resume.template', (newTpl) => {
                if (newTpl === 'korean_standard') {
                    if (['summary', 'skills', 'projects'].includes(this.activeStep)) {
                        this.activeStep = 'personal';
                    }
                } else {
                    if (['tests_military', 'self_intro'].includes(this.activeStep)) {
                        this.activeStep = 'summary';
                    }
                }
            });
        },

        // Photo Upload
        async uploadPhotoFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Instant Client-side base64 preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.resume.personal_info.photo_url = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload to server
            this.isUploadingPhoto = true;
            const formData = new FormData();
            formData.append('photo', file);

            try {
                const res = await fetch('{{ route('resume-builder.upload-photo') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success && data.url) {
                    this.resume.personal_info.photo_url = data.url;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.isUploadingPhoto = false;
                event.target.value = '';
            }
        },

        // Helpers for repeaters
        addEducation() {
            this.resume.education.push({
                institution: '',
                degree: '',
                field: '',
                grade: '',
                start_date: '',
                end_date: '',
                is_current: false
            });
        },
        removeEducation(index) {
            this.resume.education.splice(index, 1);
        },

        addExperience() {
            this.resume.experience.push({
                title: '',
                company: '',
                location: '',
                start_date: '',
                end_date: '',
                is_current: false,
                rawNotes: '',
                points: [],
                isPolishing: false
            });
        },
        removeExperience(index) {
            this.resume.experience.splice(index, 1);
        },

        addLanguageTest() {
            this.resume.language_tests.push({
                test_name: 'TOEIC',
                score: '',
                acquisition_date: '',
                organization: 'ETS'
            });
        },
        removeLanguageTest(index) {
            this.resume.language_tests.splice(index, 1);
        },

        addComputerSkill() {
            this.resume.computer_skills.push({
                category: 'Programming',
                tool_name: '',
                proficiency: '상 (Advanced)',
                description: ''
            });
        },
        removeComputerSkill(index) {
            this.resume.computer_skills.splice(index, 1);
        },

        addSkill() {
            const trimmed = this.newSkillInput.trim();
            if (trimmed && !this.resume.skills.includes(trimmed)) {
                this.resume.skills.push(trimmed);
            }
            this.newSkillInput = '';
        },
        removeSkill(index) {
            this.resume.skills.splice(index, 1);
        },

        addProject() {
            this.resume.projects.push({
                title: '',
                tech: '',
                description: ''
            });
        },
        removeProject(index) {
            this.resume.projects.splice(index, 1);
        },

        addCertification() {
            this.resume.certifications.push({
                name: '',
                issuer: '',
                year: ''
            });
        },
        removeCertification(index) {
            this.resume.certifications.splice(index, 1);
        },

        // AI Operations
        async aiPolishSummary() {
            if (!this.resume.summary) return;
            this.isPolishingSummary = true;
            try {
                const res = await fetch('{{ route('resume-builder.ai.summary') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        summary: this.resume.summary,
                        target_role: this.resume.personal_info.job_title
                    })
                });
                const data = await res.json();
                if (data.success && data.polished_summary) {
                    this.resume.summary = data.polished_summary;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isPolishingSummary = false;
            }
        },

        async aiPolishSelfIntro(section) {
            const raw = this.resume.self_introduction[section];
            if (!raw) return;
            this.isPolishingSelfIntro = section;
            try {
                const res = await fetch('{{ route('resume-builder.ai.summary') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        summary: raw,
                        target_role: this.resume.personal_info.job_title
                    })
                });
                const data = await res.json();
                if (data.success && data.polished_summary) {
                    this.resume.self_introduction[section] = data.polished_summary;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isPolishingSelfIntro = null;
            }
        },

        async aiPolishExperience(idx) {
            const exp = this.resume.experience[idx];
            const notes = exp.rawNotes || (exp.points ? exp.points.join('\n') : '');
            if (!notes) return;
            exp.isPolishing = true;

            try {
                const res = await fetch('{{ route('resume-builder.ai.experience') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        notes: notes,
                        job_title: exp.title,
                        company: exp.company
                    })
                });
                const data = await res.json();
                if (data.success && data.bullets) {
                    exp.points = data.bullets;
                    exp.rawNotes = '';
                }
            } catch (e) {
                console.error(e);
            } finally {
                exp.isPolishing = false;
            }
        },

        async aiSuggestSkills() {
            const role = this.resume.personal_info.job_title || 'Software Professional';
            this.isSuggestingSkills = true;

            try {
                const res = await fetch('{{ route('resume-builder.ai.skills') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ job_title: role })
                });
                const data = await res.json();
                if (data.success && data.skills) {
                    for (const s of data.skills) {
                        if (!this.resume.skills.includes(s)) {
                            this.resume.skills.push(s);
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isSuggestingSkills = false;
            }
        },

        // Save & PDF Export
        async saveResume(quiet = false) {
            if (!this.resume.personal_info.full_name || !this.resume.personal_info.email) {
                alert('Please provide your Full Name and Email in Step 1 before saving.');
                this.activeStep = 'personal';
                return false;
            }

            this.isSaving = true;
            try {
                const res = await fetch('{{ route('resume-builder.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.resume)
                });
                const data = await res.json();
                this.isSaving = false;

                if (data.success) {
                    this.resume.id = data.resume_id;
                    this.hasUnsavedChanges = false;
                    if (!quiet) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Resume saved successfully!',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                    return true;
                } else {
                    alert(data.message || 'Error saving resume');
                    return false;
                }
            } catch (e) {
                this.isSaving = false;
                alert('Failed to save resume. Please check your connection.');
                return false;
            }
        },

        async openPreview() {
            const saved = await this.saveResume(true);
            if (saved && this.resume.id) {
                const baseUrl = "{{ url('/resume-builder') }}";
                window.open(`${baseUrl}/${this.resume.id}/preview`, '_blank');
            }
        },

        async downloadPdf() {
            const saved = await this.saveResume(true);
            if (saved && this.resume.id) {
                const baseUrl = "{{ url('/resume-builder') }}";
                window.open(`${baseUrl}/${this.resume.id}/preview?print=1`, '_blank');
            }
        }
    }
}
</script>
</div>
</body>
</html>
