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
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased selection:bg-amber-500 selection:text-slate-950 overflow-hidden">

<div class="h-screen bg-slate-100/70 text-slate-800 flex flex-col overflow-hidden" x-data="resumeEditorApp(@js($initialData))">

    {{-- TOP STUDIO NAVIGATION BAR (EXECUTIVE REIAC THEME) --}}
    <header class="sticky top-0 z-40 bg-[#0b1329] border-b border-slate-800 px-3 sm:px-6 py-2.5 shadow-md shrink-0">
        <div class="max-w-[1780px] mx-auto flex flex-col lg:flex-row lg:items-center justify-between gap-3">

            {{-- Left: Back & Title --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('resume-builder.my-resumes') }}"
                   class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white transition flex items-center justify-center shrink-0 border border-slate-700/80"
                   title="Back to My Resumes">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>

                <div class="hidden sm:flex items-center gap-2 pr-3 border-r border-slate-800">
                    <a href="{{ route('community.index') }}" class="flex items-center gap-1.5" title="Community Home">
                        <span class="text-sm font-black tracking-wider text-amber-400">REIAC</span>
                        <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 bg-slate-800 px-1.5 py-0.5 rounded">STUDIO</span>
                    </a>
                </div>

                <div>
                    <div class="flex items-center gap-2">
                        <input type="text" x-model="resume.title" placeholder="Resume Title (e.g. Full Stack Developer CV)"
                               class="text-sm sm:text-base font-black text-white border-b border-transparent hover:border-slate-600 focus:border-amber-400 bg-transparent focus:ring-0 p-0 w-44 sm:w-72 truncate transition">
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full"
                              :class="isSaving ? 'bg-amber-400/20 text-amber-300 animate-pulse border border-amber-400/30' : (hasUnsavedChanges ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30')"
                              x-text="isSaving ? 'Saving...' : (hasUnsavedChanges ? 'Unsaved' : 'Saved')">
                        </span>
                    </div>
                    <span class="text-[10px] text-slate-400 block font-medium">Real-time Live A4 Document Engine</span>
                </div>
            </div>

            {{-- Center: View Mode & Template & Language Controls --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- View Mode Switcher (Split / Form / Preview) --}}
                <div class="flex items-center bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs font-bold">
                    <button type="button" @click="viewMode = 'split'"
                            :class="viewMode === 'split' ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer">
                        <span>🌗</span>
                        <span class="hidden sm:inline">Split View</span>
                    </button>
                    <button type="button" @click="viewMode = 'form'"
                            :class="viewMode === 'form' ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer">
                        <span>📝</span>
                        <span class="hidden sm:inline">Form Only</span>
                    </button>
                    <button type="button" @click="viewMode = 'preview'"
                            :class="viewMode === 'preview' ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2.5 py-1 rounded-lg transition flex items-center gap-1 cursor-pointer">
                        <span>👁️</span>
                        <span class="hidden sm:inline">Preview Only</span>
                    </button>
                </div>

                {{-- Template Dropdown --}}
                <div class="relative">
                    <select x-model="resume.template"
                            class="text-xs font-bold bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer shadow-xs">
                        <option value="korean_standard">🇰🇷 Korean Standard (이력서/자기소개서)</option>
                        <option value="modern">Modern Professional ATS</option>
                        <option value="classic">Executive Classic ATS</option>
                        <option value="creative">Creative &amp; Impact</option>
                    </select>
                </div>

                {{-- Language Selector --}}
                <div class="flex items-center bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs font-bold">
                    <button type="button" @click="resume.language = 'en'"
                            :class="resume.language === 'en' ? 'bg-amber-400 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                        <span>🌐</span>
                        <span>English</span>
                    </button>
                    <button type="button" @click="resume.language = 'ko'"
                            :class="resume.language === 'ko' ? 'bg-emerald-500 text-slate-950 font-black shadow-xs' : 'text-slate-400 hover:text-white'"
                            class="px-2 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                        <span>🇰🇷</span>
                        <span>한국어</span>
                    </button>
                </div>
            </div>

            {{-- Right: Actions (Save, Popout, PDF) --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Quick Save --}}
                <button type="button" @click="saveResume(false)" :disabled="isSaving"
                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <span x-show="!isSaving">💾 Save</span>
                    <span x-show="isSaving">Saving...</span>
                </button>

                {{-- Pop-out Tab Preview --}}
                <button type="button" @click="openPreview()"
                        class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                        title="Open standalone preview in new browser tab">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span class="hidden xl:inline">New Tab</span>
                </button>

                {{-- Download PDF --}}
                <button type="button" @click="downloadPdf()"
                        class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export PDF</span>
                </button>
            </div>
        </div>
    </header>

    {{-- MAIN WORKSPACE CONTAINER --}}
    <main class="flex-1 max-w-[1780px] w-full mx-auto p-3 sm:p-5 lg:p-6">
        <div :class="{
                'grid grid-cols-1 lg:grid-cols-12 gap-6 items-start': viewMode === 'split',
                'max-w-4xl mx-auto': viewMode === 'form',
                'max-w-5xl mx-auto': viewMode === 'preview'
             }">

            {{-- ========================================================================= --}}
            {{-- LEFT PANE: GUIDED STEP-BY-STEP FORM (LIGHT THEME) --}}
            {{-- ========================================================================= --}}
            <div x-show="viewMode === 'split' || viewMode === 'form'"
                 :class="viewMode === 'split' ? 'lg:col-span-6 xl:col-span-6' : 'w-full'"
                 class="space-y-4">

                {{-- STEP NAVIGATION PILLS --}}
                <div class="bg-white border border-slate-200 rounded-2xl p-2 shadow-xs">
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin scrollbar-thumb-slate-300">
                        <button type="button" @click="activeStep = 'personal'"
                                :class="activeStep === 'personal' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>👤</span>
                            <span>1. Personal &amp; Photo</span>
                        </button>

                        <button type="button" @click="activeStep = 'summary'"
                                :class="activeStep === 'summary' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>📝</span>
                            <span>2. Summary</span>
                            <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                        </button>

                        <button type="button" @click="activeStep = 'education'"
                                :class="activeStep === 'education' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>🎓</span>
                            <span>3. Education (학력)</span>
                        </button>

                        <button type="button" @click="activeStep = 'experience'"
                                :class="activeStep === 'experience' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>💼</span>
                            <span>4. Experience (경력)</span>
                            <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                        </button>

                        <button type="button" @click="activeStep = 'tests_military'"
                                :class="activeStep === 'tests_military' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>🎖️</span>
                            <span>5. Tests &amp; Military</span>
                            <span class="text-[9px] bg-emerald-100 text-emerald-800 px-1 py-0.2 rounded font-black">KR</span>
                        </button>

                        <button type="button" @click="activeStep = 'self_intro'"
                                :class="activeStep === 'self_intro' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>✍️</span>
                            <span>6. Self-Intro (자기소개서)</span>
                            <span class="text-[9px] bg-amber-400 text-slate-950 px-1 py-0.2 rounded font-black">AI</span>
                        </button>

                        <button type="button" @click="activeStep = 'skills'"
                                :class="activeStep === 'skills' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>⚡</span>
                            <span>7. Skills &amp; Tools</span>
                            <span class="text-[9px] bg-amber-200 text-amber-900 px-1 py-0.2 rounded font-black">AI</span>
                        </button>

                        <button type="button" @click="activeStep = 'projects'"
                                :class="activeStep === 'projects' ? 'bg-amber-500 text-slate-950 font-black shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer shrink-0">
                            <span>🚀</span>
                            <span>8. Projects &amp; Certs</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 1: PERSONAL & PHOTO ===================== --}}
                <div x-show="activeStep === 'personal'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 1 of 8</span>
                                <span>•</span>
                                <span>Candidate Information</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Personal Information &amp; Photo (인적사항 및 사진)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Your photo and contact information will appear on all resume templates.</p>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 border border-slate-200">
                            <span x-text="resume.template === 'korean_standard' ? '🇰🇷 Official Table' : 'Universal Profile'"></span>
                        </span>
                    </div>

                    {{-- Candidate Photo Upload Box --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center gap-5">
                        <div class="w-24 h-32 rounded-xl border-2 border-dashed border-slate-300 bg-white overflow-hidden flex flex-col items-center justify-center text-center p-1 shrink-0 relative shadow-xs">
                            <template x-if="resume.personal_info.photo_url">
                                <img :src="resume.personal_info.photo_url" class="w-full h-full object-cover rounded-lg" alt="Candidate Photo">
                            </template>
                            <template x-if="!resume.personal_info.photo_url">
                                <div class="text-slate-400 text-[10px] space-y-1">
                                    <span class="text-2xl block">📷</span>
                                    <span class="font-bold block text-slate-600">3cm × 4cm</span>
                                    <span class="text-[9px] text-slate-400">증명사진</span>
                                </div>
                            </template>

                            {{-- Loading Spinner during Upload --}}
                            <div x-show="isUploadingPhoto" x-cloak class="absolute inset-0 bg-white/90 backdrop-blur-xs flex flex-col items-center justify-center text-amber-600">
                                <svg class="animate-spin h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span class="text-[9px] font-bold">Uploading...</span>
                            </div>
                        </div>

                        <div class="flex-1 space-y-2 w-full text-xs">
                            <div class="flex items-center justify-between">
                                <label class="font-bold text-slate-800 text-xs">Candidate Portrait / ID Photo (증명사진 업로드)</label>
                                <span class="text-[10px] font-medium text-slate-500">Appears on all 4 templates</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Upload your photo from your device (JPG, PNG, WebP up to 5MB). It automatically renders on all resume designs.
                            </p>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <input type="file" x-ref="photoFileInput" @change="uploadPhotoFile($event)" accept="image/png,image/jpeg,image/webp,image/jpg" class="hidden">

                                <button type="button" @click="$refs.photoFileInput.click()" :disabled="isUploadingPhoto"
                                        class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                                    <span>📤</span>
                                    <span x-text="resume.personal_info.photo_url ? 'Change Photo (사진 변경)' : 'Upload Photo (사진 업로드)'"></span>
                                </button>

                                <button type="button" @click="resume.personal_info.photo_url = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80'"
                                        class="px-3 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer">
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

                    {{-- Primary Fields Grid (Light Inputs) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Full Name (성명) *</label>
                            <input type="text" x-model="resume.personal_info.full_name" placeholder="e.g. 홍길동 (or Alex Morgan)"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">English Full Name (영문 성명)</label>
                            <input type="text" x-model="resume.personal_info.english_name" placeholder="e.g. GILDONG HONG"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Date of Birth &amp; Age (생년월일 및 나이)</label>
                            <input type="text" x-model="resume.personal_info.date_of_birth" placeholder="e.g. 1998.05.14 (만 26세)"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Gender (성별)</label>
                            <select x-model="resume.personal_info.gender"
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none cursor-pointer">
                                <option value="">Select Gender</option>
                                <option value="남 (Male)">남 (Male)</option>
                                <option value="여 (Female)">여 (Female)</option>
                                <option value="Not Specified">Not Specified</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Target Job Title / Applied Field (지원분야) *</label>
                            <input type="text" x-model="resume.personal_info.job_title" placeholder="e.g. 백엔드 개발자 (Backend Engineer)"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-bold text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Total Experience (총 경력)</label>
                            <input type="text" x-model="resume.personal_info.total_experience" placeholder="e.g. 신입, 2년 6개월, or 3 Years"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Email Address (이메일) *</label>
                            <input type="email" x-model="resume.personal_info.email" placeholder="gildong@example.com"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Mobile Phone (휴대폰 연락처)</label>
                            <input type="text" x-model="resume.personal_info.phone" placeholder="010-1234-5678"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Emergency Contact (비상연락처)</label>
                            <input type="text" x-model="resume.personal_info.emergency_phone" placeholder="010-9876-5432 (부/모/배우자)"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Current Address / Location (현주소)</label>
                            <input type="text" x-model="resume.personal_info.location" placeholder="e.g. 서울특별시 서초구 반포대로 14"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">LinkedIn Profile URL</label>
                            <input type="text" x-model="resume.personal_info.linkedin" placeholder="linkedin.com/in/username"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">GitHub / Portfolio URL</label>
                            <input type="text" x-model="resume.personal_info.portfolio" placeholder="portfolio.dev or github.com/username"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none">
                        </div>
                    </div>

                    {{-- Next Step Bar --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="saveResume(false)" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            💾 Save Progress
                        </button>
                        <button type="button" @click="activeStep = 'summary'"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Summary &amp; Objective →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 2: SUMMARY & OBJECTIVE ===================== --}}
                <div x-show="activeStep === 'summary'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 2 of 8</span>
                                <span>•</span>
                                <span>Professional Overview</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Career Objective &amp; Summary (전문 요약)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Write a 2-4 sentence pitch. You can type rough notes and click "Polish with AI".</p>
                        </div>

                        {{-- AI Polish Button --}}
                        <button type="button" @click="aiPolishSummary()" :disabled="isPolishingSummary || !resume.summary"
                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-2 cursor-pointer disabled:opacity-40">
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
                                  class="w-full text-xs sm:text-sm bg-slate-50 border border-slate-300 rounded-2xl p-4 leading-relaxed font-medium text-slate-800 focus:bg-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 outline-none"></textarea>
                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                            <span class="flex items-center gap-1 text-teal-700 font-medium">
                                <span>🛡️</span>
                                <span>Strictly preserves your facts; never invents qualifications or fake experience.</span>
                            </span>
                            <span x-text="(resume.summary ? resume.summary.length : 0) + ' characters'"></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'personal'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Personal
                        </button>
                        <button type="button" @click="activeStep = 'education'"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Education →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 3: EDUCATION ===================== --}}
                <div x-show="activeStep === 'education'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 3 of 8</span>
                                <span>•</span>
                                <span>Academic Background</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Education History (학력사항)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">List your university, college, high school, or degrees in reverse chronological order.</p>
                        </div>

                        <button type="button" @click="addEducation()"
                                class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-300 transition flex items-center gap-1.5 cursor-pointer">
                            <span>+ Add Degree</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(edu, index) in resume.education" :key="index">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-3 relative group">
                                <button type="button" @click="removeEducation(index)"
                                        class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold text-xs transition cursor-pointer">
                                    ✕ Remove
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Institution / School (학교명) *</label>
                                        <input type="text" x-model="edu.institution" placeholder="e.g. 서울대학교 (Seoul National Univ)"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Degree / Level (학위/구분) *</label>
                                        <input type="text" x-model="edu.degree" placeholder="e.g. 학사 (Bachelor of Science)"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Major / Field of Study (전공/학과)</label>
                                        <input type="text" x-model="edu.field" placeholder="e.g. 컴퓨터공학 (Computer Science)"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">GPA / Grade (학점/성적)</label>
                                        <input type="text" x-model="edu.grade" placeholder="e.g. 3.8 / 4.5"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Period From (입학년월)</label>
                                        <input type="text" x-model="edu.start_date" placeholder="e.g. 2018.03"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Period To (졸업년월)</label>
                                        <input type="text" x-model="edu.end_date" placeholder="e.g. 2022.02" :disabled="edu.is_current"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none disabled:opacity-40">
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1 border-t border-slate-200">
                                    <input type="checkbox" :id="'edu_curr_' + index" x-model="edu.is_current" class="rounded bg-white border-slate-300 text-amber-500 focus:ring-0">
                                    <label :for="'edu_curr_' + index" class="text-[11px] text-slate-700 font-medium cursor-pointer">
                                        Currently Enrolled / In Progress (재학 중)
                                    </label>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.education.length === 0" class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No degrees added yet. Click <strong>+ Add Degree</strong> above.
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'summary'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Summary
                        </button>
                        <button type="button" @click="activeStep = 'experience'"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Work Experience →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 4: WORK EXPERIENCE ===================== --}}
                <div x-show="activeStep === 'experience'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 4 of 8</span>
                                <span>•</span>
                                <span>Career History</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Work Experience &amp; Internships (경력사항)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">List professional roles, internships, or freelance work. Use AI to format accomplishments into high-impact bullets.</p>
                        </div>

                        <button type="button" @click="addExperience()"
                                class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-300 transition flex items-center gap-1.5 cursor-pointer">
                            <span>+ Add Position</span>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(exp, index) in resume.experience" :key="index">
                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-3 relative group">
                                <button type="button" @click="removeExperience(index)"
                                        class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold text-xs transition cursor-pointer">
                                    ✕ Remove
                                </button>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-8">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Job Title / Position (직급/직책) *</label>
                                        <input type="text" x-model="exp.title" placeholder="e.g. 주임 연구원 / Senior Backend Engineer"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Company / Organization (회사명) *</label>
                                        <input type="text" x-model="exp.company" placeholder="e.g. 네이버 (Naver Corp)"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold focus:border-amber-500 outline-none">
                                    </div>

                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Department / Location (부서/근무지)</label>
                                        <input type="text" x-model="exp.location" placeholder="e.g. 플랫폼 개발실 (Seoul, Korea)"
                                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-500 outline-none">
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Start (시작)</label>
                                            <input type="text" x-model="exp.start_date" placeholder="2022.03"
                                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">End (종료)</label>
                                            <input type="text" x-model="exp.end_date" placeholder="2024.01" :disabled="exp.is_current"
                                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none disabled:opacity-40">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" :id="'exp_curr_' + index" x-model="exp.is_current" class="rounded bg-white border-slate-300 text-amber-500 focus:ring-0">
                                    <label :for="'exp_curr_' + index" class="text-[11px] text-slate-700 font-medium cursor-pointer">
                                        Currently Working Here (현재 재직 중)
                                    </label>
                                </div>

                                {{-- AI Bullet Enhancer --}}
                                <div class="pt-2 border-t border-slate-200 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="font-bold text-slate-800">Key Responsibilities &amp; Achievements (주요업무 및 성과)</label>
                                        <button type="button" @click="aiPolishExperience(index)" :disabled="exp.isPolishing || (!exp.rawNotes && (!exp.points || exp.points.length === 0))"
                                                class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer disabled:opacity-40">
                                            <span x-show="!exp.isPolishing">✨ AI Enhance Bullets</span>
                                            <span x-show="exp.isPolishing">Formatting...</span>
                                        </button>
                                    </div>

                                    <textarea rows="3" x-model="exp.rawNotes"
                                              placeholder="Type rough notes about what you built or accomplished here, then click 'AI Enhance Bullets' to transform them into recruiter-ready bullet points..."
                                              class="w-full bg-white border border-slate-300 rounded-xl p-3 text-slate-800 text-xs focus:border-amber-500 outline-none"></textarea>

                                    {{-- Active Bullets List --}}
                                    <div class="space-y-1.5" x-show="exp.points && exp.points.length > 0">
                                        <span class="text-[10px] font-bold uppercase text-slate-500">Formatted Bullet Points:</span>
                                        <template x-for="(pt, pidx) in exp.points" :key="pidx">
                                            <div class="flex items-start gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                                <span class="text-amber-500 text-xs mt-0.5">•</span>
                                                <input type="text" x-model="exp.points[pidx]" class="flex-1 bg-transparent border-none text-xs text-slate-800 p-0 focus:ring-0">
                                                <button type="button" @click="exp.points.splice(pidx, 1)" class="text-slate-400 hover:text-rose-600 text-xs">✕</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.experience.length === 0" class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No work experience added yet. Click <strong>+ Add Position</strong> above (or skip if entry level).
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'education'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Education
                        </button>
                        <button type="button" @click="activeStep = 'tests_military'"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Tests &amp; Military (어학/병역) →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 5: TESTS, MILITARY & COMPUTER SKILLS ===================== --}}
                <div x-show="activeStep === 'tests_military'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-emerald-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 5 of 8</span>
                                <span>•</span>
                                <span>Korean Corporate Standard</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Language Tests, Military &amp; IT Skills (어학/병역/전산)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Mandatory fields for Korean job applications and major conglomerates.</p>
                        </div>
                    </div>

                    {{-- 1. Military Service Box --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>🎖️</span>
                                <span>1. Military Service (병역사항)</span>
                            </h3>
                            <span class="text-[10px] text-slate-400">군필 / 면제 / 비해당</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Status (병역구분) *</label>
                                <select x-model="resume.military_service.status"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-medium focus:border-amber-500 outline-none cursor-pointer">
                                    <option value="군필 (Completed)">군필 (Completed)</option>
                                    <option value="면제 (Exempted)">면제 (Exempted)</option>
                                    <option value="미필 (Unfulfilled)">미필 (Unfulfilled)</option>
                                    <option value="비해당 (Not Applicable)">비해당 (Not Applicable)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Branch (군별)</label>
                                <input type="text" x-model="resume.military_service.branch" placeholder="e.g. 육군 (ROK Army)"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Final Rank (계급)</label>
                                <input type="text" x-model="resume.military_service.rank" placeholder="e.g. 병장 (Sergeant)"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Service Period (복무기간)</label>
                                <input type="text" x-model="resume.military_service.period" placeholder="e.g. 2018.06 ~ 2020.02"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Discharge / Exemption Reason (전역구분 / 면제사유)</label>
                                <input type="text" x-model="resume.military_service.discharge_reason" placeholder="e.g. 만기전역 (Normal Discharge)"
                                       class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 focus:border-amber-500 outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Official Language Tests Box --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>🗣️</span>
                                <span>2. Language Proficiency Tests (공인어학성적)</span>
                            </h3>
                            <button type="button" @click="addLanguageTest()"
                                    class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition cursor-pointer">
                                + Add Score
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(test, index) in resume.language_tests" :key="index">
                                <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs space-y-2 relative shadow-2xs">
                                    <button type="button" @click="removeLanguageTest(index)" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 pr-6">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Test Name *</label>
                                            <input type="text" x-model="test.test_name" placeholder="TOEIC / OPIc / JLPT" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900 font-bold">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Score / Grade *</label>
                                            <input type="text" x-model="test.score" placeholder="950 / AL / N1" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-emerald-700 font-black">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Date</label>
                                            <input type="text" x-model="test.acquisition_date" placeholder="2023.09.15" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Authority</label>
                                            <input type="text" x-model="test.organization" placeholder="ETS / ACTFL" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="resume.language_tests.length === 0" class="py-4 text-center bg-white rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                                No test scores added yet. Click <strong>+ Add Score</strong> (e.g. TOEIC, OPIc, TOEFL).
                            </div>
                        </div>
                    </div>

                    {{-- 3. Computer & IT Skills (상/중/하) --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span>💻</span>
                                <span>3. Computer &amp; IT Competencies (전산 및 OA 활용능력)</span>
                            </h3>
                            <button type="button" @click="addComputerSkill()"
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition cursor-pointer">
                                + Add Tool
                            </button>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(skill, index) in resume.computer_skills" :key="index">
                                <div class="p-3 rounded-xl bg-white border border-slate-200 text-xs space-y-2 relative shadow-2xs">
                                    <button type="button" @click="removeComputerSkill(index)" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pr-6">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Category (분류)</label>
                                            <input type="text" x-model="skill.category" placeholder="프로그래밍 / OA / DB" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900 font-bold">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Tool / Language</label>
                                            <input type="text" x-model="skill.tool_name" placeholder="Python, Java, Excel" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900 font-bold">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Proficiency Level</label>
                                            <select x-model="skill.proficiency" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900">
                                                <option value="상 (Advanced)">상 (Advanced)</option>
                                                <option value="중상 (Upper-Intermediate)">중상 (Upper-Intermediate)</option>
                                                <option value="중 (Intermediate)">중 (Intermediate)</option>
                                                <option value="초급 (Beginner)">초급 (Beginner)</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block font-bold text-slate-700 mb-1">Description (활용 내용)</label>
                                            <input type="text" x-model="skill.description" placeholder="e.g. 대규모 데이터 처리 및 분산 시스템 아키텍처 구현" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2.5 py-1.5 text-slate-900">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'experience'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Experience
                        </button>
                        <button type="button" @click="activeStep = 'self_intro'"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Self-Intro (자기소개서) →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 6: KOREAN 4-PART SELF-INTRODUCTION ===================== --}}
                <div x-show="activeStep === 'self_intro'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 6 of 8</span>
                                <span>•</span>
                                <span>Official 4-Question Statement</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Korean Self-Introduction (자기소개서 4개 항목)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Standard questions used by JobKorea, Saramin, and major enterprises. Click "AI Polish" on any item to elevate your writing.</p>
                        </div>
                    </div>

                    {{-- 1. 성장과정 --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-900">
                                1. 성장과정 (Growth Background &amp; Upbringing)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('growth')" :disabled="isPolishingSelfIntro === 'growth' || !resume.self_introduction.growth"
                                    class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer disabled:opacity-40">
                                <span x-show="isPolishingSelfIntro !== 'growth'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'growth'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.growth"
                                  placeholder="어릴 적 가치관 형성, 학창 시절 관심 분야 탐구 과정 등을 서술하세요..."
                                  class="w-full text-xs bg-white border border-slate-300 rounded-xl p-3 text-slate-900 leading-relaxed focus:border-amber-500 outline-none"></textarea>
                    </div>

                    {{-- 2. 성격소개 및 장단점 --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-900">
                                2. 성격소개 및 장단점 (Personality &amp; Strengths/Weaknesses)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('personality')" :disabled="isPolishingSelfIntro === 'personality' || !resume.self_introduction.personality"
                                    class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer disabled:opacity-40">
                                <span x-show="isPolishingSelfIntro !== 'personality'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'personality'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.personality"
                                  placeholder="자신의 성격적 강점, 위기 대처 방식, 협업 태도 및 단점 보완 노력을 서술하세요..."
                                  class="w-full text-xs bg-white border border-slate-300 rounded-xl p-3 text-slate-900 leading-relaxed focus:border-amber-500 outline-none"></textarea>
                    </div>

                    {{-- 3. 주요경력 및 직무역량 --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-900">
                                3. 주요경력 및 직무수행 역량 (Key Projects &amp; Job Competencies)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('strengths')" :disabled="isPolishingSelfIntro === 'strengths' || !resume.self_introduction.strengths"
                                    class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer disabled:opacity-40">
                                <span x-show="isPolishingSelfIntro !== 'strengths'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'strengths'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.strengths"
                                  placeholder="실무 인턴십, 동아리 프로젝트, 오픈소스 기여 등 핵심 직무 경험을 구체적 성과와 함께 서술하세요..."
                                  class="w-full text-xs bg-white border border-slate-300 rounded-xl p-3 text-slate-900 leading-relaxed focus:border-amber-500 outline-none"></textarea>
                    </div>

                    {{-- 4. 지원동기 및 입사 후 포부 --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-black text-slate-900">
                                4. 지원동기 및 입사 후 포부 (Motivation &amp; Future Aspirations)
                            </label>
                            <button type="button" @click="aiPolishSelfIntro('motivation_and_aspirations')" :disabled="isPolishingSelfIntro === 'motivation_and_aspirations' || !resume.self_introduction.motivation_and_aspirations"
                                    class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-[11px] transition flex items-center gap-1 cursor-pointer disabled:opacity-40">
                                <span x-show="isPolishingSelfIntro !== 'motivation_and_aspirations'">✨ AI Polish</span>
                                <span x-show="isPolishingSelfIntro === 'motivation_and_aspirations'">Refining...</span>
                            </button>
                        </div>
                        <textarea rows="4" x-model="resume.self_introduction.motivation_and_aspirations"
                                  placeholder="지원 회사/직무에 지원하게 된 계기와 입사 후 1년/3년 차에 달성하고자 하는 비전을 서술하세요..."
                                  class="w-full text-xs bg-white border border-slate-300 rounded-xl p-3 text-slate-900 leading-relaxed focus:border-amber-500 outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'tests_military'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Tests &amp; Military
                        </button>
                        <button type="button" @click="activeStep = 'skills'"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Skills &amp; Tools →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 7: SKILLS & TOOLS ===================== --}}
                <div x-show="activeStep === 'skills'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 7 of 8</span>
                                <span>•</span>
                                <span>Keywords &amp; Competencies</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Skills &amp; Competencies (보유 기술)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Add hard and soft skills. Recruiters and ATS scanners match keywords to rank candidates.</p>
                        </div>

                        <button type="button" @click="aiSuggestSkills()" :disabled="isSuggestingSkills"
                                class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                            <span x-show="!isSuggestingSkills">✨ AI Suggest Skills</span>
                            <span x-show="isSuggestingSkills">Suggesting...</span>
                        </button>
                    </div>

                    {{-- Add Skill Input --}}
                    <div class="flex gap-2">
                        <input type="text" x-model="newSkillInput" @keydown.enter.prevent="addSkill()"
                               placeholder="Type a skill (e.g. React, Node.js, Spring Boot, Figma) and press Enter..."
                               class="flex-1 text-xs bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 focus:bg-white focus:border-amber-500 outline-none">
                        <button type="button" @click="addSkill()"
                                class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition cursor-pointer">
                            + Add
                        </button>
                    </div>

                    {{-- Active Skills Cloud --}}
                    <div class="flex flex-wrap gap-2 p-4 rounded-2xl bg-slate-50 border border-slate-200 min-h-[90px]">
                        <template x-for="(skill, index) in resume.skills" :key="index">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-800 font-bold text-xs shadow-2xs">
                                <span x-text="skill"></span>
                                <button type="button" @click="removeSkill(index)" class="text-slate-400 hover:text-rose-600 font-bold ml-1">✕</button>
                            </span>
                        </template>
                        <div x-show="resume.skills.length === 0" class="text-slate-400 text-xs italic self-center">
                            No skills added yet. Type a skill or click "AI Suggest Skills".
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'self_intro'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Self-Intro
                        </button>
                        <button type="button" @click="activeStep = 'projects'"
                                class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                            <span>Next: Projects &amp; Certs →</span>
                        </button>
                    </div>
                </div>

                {{-- ===================== STEP 8: PROJECTS & CERTIFICATIONS ===================== --}}
                <div x-show="activeStep === 'projects'" class="bg-white border border-slate-200/90 rounded-3xl p-5 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-amber-600 text-xs font-black uppercase tracking-wider mb-1">
                                <span>Step 8 of 8</span>
                                <span>•</span>
                                <span>Accomplishments</span>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Projects &amp; Certifications (프로젝트 및 자격증)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Highlight major software/academic projects and verified professional certifications.</p>
                        </div>
                    </div>

                    {{-- Projects Repeater --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider">🚀 Featured Projects (주요 프로젝트)</h3>
                            <button type="button" @click="addProject()" class="text-xs font-bold text-amber-700 hover:underline">
                                + Add Project
                            </button>
                        </div>

                        <template x-for="(proj, index) in resume.projects" :key="index">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2 relative">
                                <button type="button" @click="removeProject(index)" class="absolute top-3 right-3 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-6">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Project Title *</label>
                                        <input type="text" x-model="proj.title" placeholder="e.g. AI-Powered Resume Studio" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Tech Stack</label>
                                        <input type="text" x-model="proj.tech" placeholder="e.g. Laravel, Alpine.js, Tailwind, OpenAI" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block font-bold text-slate-700 mb-1">Project Description / Link</label>
                                        <textarea rows="2" x-model="proj.description" placeholder="Briefly describe what problem it solved and your role..." class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-slate-900"></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.projects.length === 0" class="py-4 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No projects added. Click <strong>+ Add Project</strong> if applicable.
                        </div>
                    </div>

                    {{-- Certifications Repeater --}}
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase text-slate-900 tracking-wider">📜 Licenses &amp; Certifications (자격증/면허)</h3>
                            <button type="button" @click="addCertification()" class="text-xs font-bold text-amber-700 hover:underline">
                                + Add Certification
                            </button>
                        </div>

                        <template x-for="(cert, index) in resume.certifications" :key="index">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2 relative">
                                <button type="button" @click="removeCertification(index)" class="absolute top-2.5 right-3 text-slate-400 hover:text-rose-600 font-bold">✕</button>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pr-6">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Certificate Name *</label>
                                        <input type="text" x-model="cert.name" placeholder="정보처리기사 / AWS SAA" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900 font-bold">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Issuer / Authority</label>
                                        <input type="text" x-model="cert.issuer" placeholder="한국산업인력공단 / Amazon" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Year / Date</label>
                                        <input type="text" x-model="cert.year" placeholder="2023.11" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-900">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div x-show="resume.certifications.length === 0" class="py-4 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No certifications added. Click <strong>+ Add Certification</strong>.
                        </div>
                    </div>

                    {{-- Finish Action Bar --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <button type="button" @click="activeStep = 'skills'" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                            ← Back to Skills
                        </button>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="saveResume(false)"
                                    class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                                💾 Save Resume
                            </button>
                            <button type="button" @click="downloadPdf()"
                                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs uppercase tracking-wider shadow-sm transition">
                                📥 Export PDF
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Watermark Status Card --}}
                <div class="p-4 rounded-2xl border text-xs flex items-center justify-between gap-4"
                     :class="resume.is_watermark_removed ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900'">
                    <div>
                        <div class="font-black text-xs uppercase tracking-wider flex items-center gap-1.5">
                            <span x-text="resume.is_watermark_removed ? '✓ Clean Watermark-Free Export' : '🔒 Free Watermarked Export'"></span>
                        </div>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed"
                           x-text="resume.is_watermark_removed ? 'Your PDF exports 100% clean without any REIAC watermark branding.' : 'Exports include the REIAC watermark. Share with friends to unlock a 100% clean PDF.'">
                        </p>
                    </div>
                    <template x-if="!resume.is_watermark_removed">
                        <a href="{{ route('resume-builder.my-resumes') }}" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-[11px] whitespace-nowrap shrink-0 transition">
                            Unlock Watermark →
                        </a>
                    </template>
                </div>

            </div>


            {{-- ========================================================================= --}}
            {{-- RIGHT PANE: LIVE REAL-TIME STICKY A4 PAPER PREVIEW (CLEAN DESK LIGHT THEME) --}}
            {{-- ========================================================================= --}}
            <div x-show="viewMode === 'split' || viewMode === 'preview'"
                 :class="viewMode === 'split' ? 'lg:col-span-6 xl:col-span-6 sticky top-3' : 'w-full'"
                 class="h-[calc(100vh-4.5rem)] flex flex-col rounded-3xl bg-slate-200/70 border border-slate-300/80 shadow-inner overflow-hidden">

                {{-- Live Studio Toolbar --}}
                <div class="bg-white px-4 py-2.5 border-b border-slate-200 flex items-center justify-between gap-3 shrink-0 shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-black text-slate-800 tracking-wide">Live Real-Time Sync</span>
                        <span class="text-[10px] font-bold text-slate-400 hidden sm:inline">• A4 Document Engine</span>
                    </div>

                    {{-- Zoom Slider / Scale Controls --}}
                    <div class="flex items-center gap-1.5 text-xs">
                        <button type="button" @click="zoom = Math.max(50, zoom - 10)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-black flex items-center justify-center transition border border-slate-200">
                            -
                        </button>
                        <span class="w-12 text-center font-bold text-slate-800 text-xs" x-text="zoom + '%'"></span>
                        <button type="button" @click="zoom = Math.min(130, zoom + 10)"
                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-black flex items-center justify-center transition border border-slate-200">
                            +
                        </button>
                        <button type="button" @click="zoom = 85"
                                class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 hover:text-slate-900 text-[11px] font-medium transition ml-1 border border-slate-200">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- A4 Paper Viewport Scroll Container --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-slate-200/50 flex justify-center items-start scrollbar-thin scrollbar-thumb-slate-400">

                    {{-- Scaled Paper Container --}}
                    <div :style="'transform: scale(' + (zoom / 100) + '); transform-origin: top center; margin-bottom: ' + ((zoom - 100) * 8) + 'px;'"
                         class="w-full max-w-[760px] bg-white text-slate-900 rounded-xl shadow-xl p-6 sm:p-10 relative transition-transform duration-150 select-text font-sans border border-slate-300/70">

                        {{-- WATERMARK OVERLAY IF ACTIVE --}}
                        <template x-if="!resume.is_watermark_removed">
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none z-30 opacity-15 rotate-[-30deg]">
                                <div class="text-center font-black tracking-widest text-4xl sm:text-5xl text-slate-800 uppercase">
                                    <span>REIAC COMMUNITY</span>
                                    <span class="block text-sm tracking-widest font-bold mt-1">Watermarked Preview</span>
                                </div>
                            </div>
                        </template>

                        {{-- ========================================================================= --}}
                        {{-- TEMPLATE 1: KOREAN STANDARD OFFICIAL TABLE (이력서/자기소개서) --}}
                        {{-- ========================================================================= --}}
                        <div x-show="resume.template === 'korean_standard'" class="space-y-4 text-slate-900 text-[11px] leading-relaxed">
                            {{-- Document Title --}}
                            <div class="text-center border-b-2 border-slate-900 pb-3 mb-4">
                                <h1 class="text-2xl font-black tracking-widest text-slate-950 uppercase"
                                    x-text="resume.language === 'ko' ? '입 사 지 원 서' : 'APPLICATION FORM'"></h1>
                                <div class="text-[11px] text-slate-500 font-bold mt-1 tracking-wider"
                                     x-text="resume.language === 'ko' ? '(이 력 서 및 자 기 소 개 서)' : 'CURRICULUM VITAE & PERSONAL STATEMENT'"></div>
                            </div>

                            {{-- 1. 인적사항 Table (WITH CANDIDATE PHOTO) --}}
                            <div>
                                <div class="flex items-center justify-between pb-1 mb-1 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">1</span>
                                        <span x-text="resume.language === 'ko' ? '인적사항 (Personal Information)' : 'Personal Information'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'personal'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>

                                <table class="w-full border-collapse border border-slate-400 text-[11px]">
                                    <colgroup>
                                        <col style="width: 16%;">
                                        <col style="width: 28%;">
                                        <col style="width: 14%;">
                                        <col style="width: 24%;">
                                        <col style="width: 18%;">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '성 명' : 'Name'"></th>
                                            <td class="border border-slate-300 p-2">
                                                <strong class="text-xs" x-text="resume.personal_info.full_name || '-'"></strong>
                                                <template x-if="resume.personal_info.english_name">
                                                    <div class="text-[10px] text-slate-500 font-medium" x-text="'(Eng) ' + resume.personal_info.english_name"></div>
                                                </template>
                                            </td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '생년월일' : 'Date of Birth'"></th>
                                            <td class="border border-slate-300 p-2">
                                                <span x-text="resume.personal_info.date_of_birth || '-'"></span>
                                                <template x-if="resume.personal_info.gender">
                                                    <span class="text-[10px] text-slate-500 ml-1" x-text="'(' + resume.personal_info.gender + ')'"></span>
                                                </template>
                                            </td>
                                            <td rowspan="4" class="border border-slate-300 p-1.5 text-center align-middle bg-slate-50">
                                                <div class="w-20 h-28 mx-auto border border-dashed border-slate-300 bg-white rounded flex items-center justify-center overflow-hidden">
                                                    <template x-if="resume.personal_info.photo_url">
                                                        <img :src="resume.personal_info.photo_url" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!resume.personal_info.photo_url">
                                                        <div class="text-slate-400 text-[9px] leading-tight">
                                                            <span class="text-lg block">📷</span>
                                                            <span class="font-bold block" x-text="resume.language === 'ko' ? '사 진' : 'Photo'"></span>
                                                            <span class="text-[8px]">(3cm×4cm)</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '지원분야' : 'Applied Field'"></th>
                                            <td class="border border-slate-300 p-2 font-bold text-emerald-800" x-text="resume.personal_info.job_title || '-'"></td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '총 경력' : 'Total Exp'"></th>
                                            <td class="border border-slate-300 p-2 font-medium" x-text="resume.personal_info.total_experience || '-'"></td>
                                        </tr>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '휴대폰' : 'Mobile'"></th>
                                            <td class="border border-slate-300 p-2" x-text="resume.personal_info.phone || '-'"></td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '이메일' : 'Email'"></th>
                                            <td class="border border-slate-300 p-2 text-[10px]" x-text="resume.personal_info.email || '-'"></td>
                                        </tr>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '비상연락처' : 'Emergency'"></th>
                                            <td class="border border-slate-300 p-2" x-text="resume.personal_info.emergency_phone || '-'"></td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '포트폴리오' : 'Portfolio'"></th>
                                            <td class="border border-slate-300 p-2 text-[10px]" x-text="resume.personal_info.portfolio || '-'"></td>
                                        </tr>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '현 주 소' : 'Address'"></th>
                                            <td colspan="4" class="border border-slate-300 p-2 font-medium" x-text="resume.personal_info.location || '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- 2. 학력사항 Table --}}
                            <div>
                                <div class="flex items-center justify-between pb-1 mb-1 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">2</span>
                                        <span x-text="resume.language === 'ko' ? '학력사항 (Education)' : 'Education History'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'education'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>
                                <table class="w-full border-collapse border border-slate-400 text-[10px]">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-800">
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '재학기간' : 'Period'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-1/3" x-text="resume.language === 'ko' ? '학교명' : 'Institution'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center" x-text="resume.language === 'ko' ? '전공 / 학위' : 'Major / Degree'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-16" x-text="resume.language === 'ko' ? '학점' : 'GPA'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-14" x-text="resume.language === 'ko' ? '구분' : 'Status'"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(edu, idx) in resume.education" :key="idx">
                                            <tr>
                                                <td class="border border-slate-300 p-1.5 text-center font-medium" x-text="(edu.start_date || '') + ' ~ ' + (edu.is_current ? (resume.language === 'ko' ? '재학' : 'Present') : (edu.end_date || ''))"></td>
                                                <td class="border border-slate-300 p-1.5 font-bold" x-text="edu.institution || '-'"></td>
                                                <td class="border border-slate-300 p-1.5" x-text="(edu.degree || '') + (edu.field ? ' / ' + edu.field : '')"></td>
                                                <td class="border border-slate-300 p-1.5 text-center font-semibold" x-text="edu.grade || '-'"></td>
                                                <td class="border border-slate-300 p-1.5 text-center" x-text="edu.is_current ? '재학' : '졸업'"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="resume.education.length === 0">
                                            <td colspan="5" class="border border-slate-300 p-2 text-center text-slate-400 italic">No education recorded.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- 3. 경력사항 Table --}}
                            <div>
                                <div class="flex items-center justify-between pb-1 mb-1 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">3</span>
                                        <span x-text="resume.language === 'ko' ? '경력사항 (Work Experience)' : 'Work Experience'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'experience'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>
                                <table class="w-full border-collapse border border-slate-400 text-[10px]">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-800">
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '근무기간' : 'Period'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '회사명' : 'Company'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '직급 / 역할' : 'Position'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center" x-text="resume.language === 'ko' ? '담당업무 및 성과' : 'Responsibilities'"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(exp, idx) in resume.experience" :key="idx">
                                            <tr>
                                                <td class="border border-slate-300 p-1.5 text-center font-medium" x-text="(exp.start_date || '') + ' ~ ' + (exp.is_current ? (resume.language === 'ko' ? '현재 재직' : 'Present') : (exp.end_date || ''))"></td>
                                                <td class="border border-slate-300 p-1.5 font-bold" x-text="exp.company || '-'"></td>
                                                <td class="border border-slate-300 p-1.5 font-semibold text-emerald-800" x-text="exp.title || '-'"></td>
                                                <td class="border border-slate-300 p-1.5 text-[9.5px]">
                                                    <template x-if="exp.points && exp.points.length > 0">
                                                        <ul class="list-disc list-inside space-y-0.5">
                                                            <template x-for="(pt, pidx) in exp.points" :key="pidx">
                                                                <li x-text="pt"></li>
                                                            </template>
                                                        </ul>
                                                    </template>
                                                    <template x-if="!exp.points || exp.points.length === 0">
                                                        <span x-text="exp.rawNotes || '-'"></span>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="resume.experience.length === 0">
                                            <td colspan="4" class="border border-slate-300 p-2 text-center text-slate-400 italic">No work experience recorded.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- 4. 어학성적 및 자격증 Table --}}
                            <div>
                                <div class="flex items-center justify-between pb-1 mb-1 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">4</span>
                                        <span x-text="resume.language === 'ko' ? '공인외국어시험 및 자격증' : 'Language Tests & Certifications'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'tests_military'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>
                                <table class="w-full border-collapse border border-slate-400 text-[10px]">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-800">
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '구분 / 시험명' : 'Test / Cert Name'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '점수 / 등급' : 'Score / Level'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center w-1/4" x-text="resume.language === 'ko' ? '취득일자' : 'Date'"></th>
                                            <th class="border border-slate-300 p-1.5 text-center" x-text="resume.language === 'ko' ? '시행기관' : 'Authority'"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(test, idx) in resume.language_tests" :key="idx">
                                            <tr>
                                                <td class="border border-slate-300 p-1.5 font-bold" x-text="test.test_name"></td>
                                                <td class="border border-slate-300 p-1.5 font-black text-emerald-800" x-text="test.score"></td>
                                                <td class="border border-slate-300 p-1.5 text-center" x-text="test.acquisition_date"></td>
                                                <td class="border border-slate-300 p-1.5" x-text="test.organization"></td>
                                            </tr>
                                        </template>
                                        <template x-for="(cert, cidx) in resume.certifications" :key="cidx">
                                            <tr>
                                                <td class="border border-slate-300 p-1.5 font-bold" x-text="cert.name"></td>
                                                <td class="border border-slate-300 p-1.5 text-slate-600">자격취득</td>
                                                <td class="border border-slate-300 p-1.5 text-center" x-text="cert.year"></td>
                                                <td class="border border-slate-300 p-1.5" x-text="cert.issuer"></td>
                                            </tr>
                                        </template>
                                        <tr x-show="resume.language_tests.length === 0 && resume.certifications.length === 0">
                                            <td colspan="4" class="border border-slate-300 p-2 text-center text-slate-400 italic">None recorded.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- 5. 병역사항 Table --}}
                            <div>
                                <div class="flex items-center justify-between pb-1 mb-1 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">5</span>
                                        <span x-text="resume.language === 'ko' ? '병역사항 (Military Service)' : 'Military Service'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'tests_military'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>
                                <table class="w-full border-collapse border border-slate-400 text-[10px]">
                                    <tbody>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center w-1/5" x-text="resume.language === 'ko' ? '병역구분' : 'Status'"></th>
                                            <td class="border border-slate-300 p-2 font-bold w-1/3" x-text="resume.military_service.status"></td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center w-1/5" x-text="resume.language === 'ko' ? '군별 / 계급' : 'Branch / Rank'"></th>
                                            <td class="border border-slate-300 p-2" x-text="(resume.military_service.branch || '-') + ' / ' + (resume.military_service.rank || '-')"></td>
                                        </tr>
                                        <tr>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '복무기간' : 'Period'"></th>
                                            <td class="border border-slate-300 p-2 font-medium" x-text="resume.military_service.period || '-'"></td>
                                            <th class="border border-slate-300 bg-slate-100 p-2 font-bold text-center" x-text="resume.language === 'ko' ? '전역구분' : 'Discharge'"></th>
                                            <td class="border border-slate-300 p-2" x-text="resume.military_service.discharge_reason || '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- 6. 자기소개서 4항목 --}}
                            <div class="pt-2">
                                <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-300">
                                    <h3 class="text-xs font-black uppercase text-slate-900 flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] flex items-center justify-center font-black">6</span>
                                        <span x-text="resume.language === 'ko' ? '자 기 소 개 서 (Personal Statement)' : 'Personal Statement'"></span>
                                    </h3>
                                    <button type="button" @click="activeStep = 'self_intro'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>

                                <div class="space-y-2 text-[10px]">
                                    <div class="border border-slate-300 rounded p-2 bg-slate-50/50">
                                        <div class="font-bold text-slate-900 mb-0.5">1. 성장과정 (Growth Background)</div>
                                        <p class="leading-relaxed text-slate-700 whitespace-pre-line" x-text="resume.self_introduction.growth || '(Not entered)'"></p>
                                    </div>
                                    <div class="border border-slate-300 rounded p-2 bg-slate-50/50">
                                        <div class="font-bold text-slate-900 mb-0.5">2. 성격소개 및 장단점 (Personality &amp; Strengths)</div>
                                        <p class="leading-relaxed text-slate-700 whitespace-pre-line" x-text="resume.self_introduction.personality || '(Not entered)'"></p>
                                    </div>
                                    <div class="border border-slate-300 rounded p-2 bg-slate-50/50">
                                        <div class="font-bold text-slate-900 mb-0.5">3. 주요경력 및 직무수행 역량 (Key Competencies)</div>
                                        <p class="leading-relaxed text-slate-700 whitespace-pre-line" x-text="resume.self_introduction.strengths || '(Not entered)'"></p>
                                    </div>
                                    <div class="border border-slate-300 rounded p-2 bg-slate-50/50">
                                        <div class="font-bold text-slate-900 mb-0.5">4. 지원동기 및 입사 후 포부 (Motivation &amp; Aspirations)</div>
                                        <p class="leading-relaxed text-slate-700 whitespace-pre-line" x-text="resume.self_introduction.motivation_and_aspirations || '(Not entered)'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Formal Legal Pledge --}}
                            <div class="text-center pt-4 space-y-1.5 border-t border-slate-300 mt-4 text-[10px]">
                                <div class="font-bold tracking-wider"
                                     x-text="resume.language === 'ko' ? '위 기재사항은 사실과 틀림없음을 확인합니다.' : 'I hereby certify that all information stated above is true and accurate.'"></div>
                                <div class="text-slate-500 font-medium">
                                    {{ date('Y년 m월 d일') }}
                                </div>
                                <div class="font-black text-xs pt-1 tracking-wider">
                                    <span x-text="resume.language === 'ko' ? '지원자 : ' : 'Applicant : '"></span>
                                    <span x-text="resume.personal_info.full_name || 'Candidate Name'"></span>
                                    <span class="font-normal text-slate-500 ml-1">(인 / 서명)</span>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================================= --}}
                        {{-- TEMPLATE 2, 3, 4: MODERN / CLASSIC / CREATIVE ATS (WITH CANDIDATE PHOTO) --}}
                        {{-- ========================================================================= --}}
                        <div x-show="resume.template !== 'korean_standard'" class="space-y-6">

                            {{-- Header (WITH CANDIDATE PHOTO IN ALL TEMPLATES) --}}
                            <div class="pb-4"
                                 :class="{
                                     'border-b-2 border-slate-900': resume.template === 'modern',
                                     'border-b border-black text-center': resume.template === 'classic',
                                     'bg-slate-900 text-white -mx-6 -mt-6 sm:-mx-10 sm:-mt-10 p-6 sm:p-8 mb-4 rounded-t-xl': resume.template === 'creative'
                                 }">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1" :class="resume.template === 'classic' && !resume.personal_info.photo_url ? 'text-center' : 'text-left'">
                                        <div class="flex items-center justify-between">
                                            <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-slate-950"
                                                :class="resume.template === 'creative' ? 'text-amber-400' : ''"
                                                x-text="resume.personal_info.full_name || 'Candidate Full Name'"></h1>
                                            <button type="button" @click="activeStep = 'personal'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer sm:hidden">✏️</button>
                                        </div>

                                        <div class="text-xs sm:text-sm font-bold uppercase tracking-wider mt-1 text-amber-700"
                                             :class="resume.template === 'creative' ? 'text-slate-300' : ''"
                                             x-text="resume.personal_info.job_title || 'Target Job Title'"></div>

                                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2.5 text-xs text-slate-600"
                                             :class="{
                                                 'text-slate-700': resume.template === 'classic',
                                                 'text-slate-300': resume.template === 'creative'
                                             }">
                                            <template x-if="resume.personal_info.email">
                                                <span>✉️ <span x-text="resume.personal_info.email"></span></span>
                                            </template>
                                            <template x-if="resume.personal_info.phone">
                                                <span>📞 <span x-text="resume.personal_info.phone"></span></span>
                                            </template>
                                            <template x-if="resume.personal_info.location">
                                                <span>📍 <span x-text="resume.personal_info.location"></span></span>
                                            </template>
                                            <template x-if="resume.personal_info.portfolio">
                                                <span>🔗 <span x-text="resume.personal_info.portfolio"></span></span>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Photo Display in Western Templates --}}
                                    <template x-if="resume.personal_info.photo_url">
                                        <div class="w-20 h-26 rounded-lg overflow-hidden border border-slate-300 shadow-sm shrink-0 bg-slate-50 relative group">
                                            <img :src="resume.personal_info.photo_url" class="w-full h-full object-cover" alt="Candidate Photo">
                                            <button type="button" @click="activeStep = 'personal'" class="absolute inset-0 bg-black/40 text-white font-bold text-[10px] opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                                Change
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!resume.personal_info.photo_url">
                                        <button type="button" @click="activeStep = 'personal'"
                                                class="w-18 h-22 rounded-lg border-2 border-dashed border-slate-300 hover:border-amber-400 bg-slate-50 flex flex-col items-center justify-center text-slate-400 hover:text-slate-700 text-[9px] shrink-0 transition cursor-pointer p-1">
                                            <span class="text-base">📷</span>
                                            <span class="font-bold">+ Photo</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Professional Summary --}}
                            <div class="relative group">
                                <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-200">
                                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900"
                                        x-text="resume.language === 'ko' ? '전문 요약 (Professional Summary)' : 'Professional Summary'"></h3>
                                    <button type="button" @click="activeStep = 'summary'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>
                                <p class="text-xs leading-relaxed text-slate-700 text-justify"
                                   x-text="resume.summary || 'Click Edit to write or polish your professional summary.'"></p>
                            </div>

                            {{-- Work Experience --}}
                            <div class="relative group">
                                <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-200">
                                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900"
                                        x-text="resume.language === 'ko' ? '경력 사항 (Work Experience)' : 'Work Experience'"></h3>
                                    <button type="button" @click="activeStep = 'experience'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(exp, idx) in resume.experience" :key="idx">
                                        <div class="text-xs space-y-1">
                                            <div class="flex justify-between font-bold text-slate-900">
                                                <span x-text="exp.title || 'Job Title'"></span>
                                                <span class="text-slate-500 font-medium text-[11px]" x-text="(exp.start_date || '') + ' - ' + (exp.is_current ? 'Present' : (exp.end_date || ''))"></span>
                                            </div>
                                            <div class="text-[11px] font-semibold text-amber-800" x-text="exp.company || 'Company'"></div>
                                            <template x-if="exp.points && exp.points.length > 0">
                                                <ul class="list-disc list-inside space-y-0.5 text-slate-700 text-[11px] leading-relaxed">
                                                    <template x-for="(pt, pidx) in exp.points" :key="pidx">
                                                        <li x-text="pt"></li>
                                                    </template>
                                                </ul>
                                            </template>
                                        </div>
                                    </template>
                                    <div x-show="resume.experience.length === 0" class="text-slate-400 text-xs italic">
                                        No work experience added.
                                    </div>
                                </div>
                            </div>

                            {{-- Education --}}
                            <div class="relative group">
                                <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-200">
                                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900"
                                        x-text="resume.language === 'ko' ? '학력 사항 (Education)' : 'Education History'"></h3>
                                    <button type="button" @click="activeStep = 'education'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>

                                <div class="space-y-2">
                                    <template x-for="(edu, idx) in resume.education" :key="idx">
                                        <div class="text-xs flex justify-between items-baseline">
                                            <div>
                                                <strong x-text="edu.institution"></strong>
                                                <span class="text-slate-600 ml-1" x-text="'(' + (edu.degree || '') + (edu.field ? ' - ' + edu.field : '') + ')'"></span>
                                            </div>
                                            <div class="text-[11px] text-slate-500 font-medium" x-text="(edu.start_date || '') + ' ~ ' + (edu.end_date || (edu.is_current ? 'Present' : ''))"></div>
                                        </div>
                                    </template>
                                    <div x-show="resume.education.length === 0" class="text-slate-400 text-xs italic">
                                        No education recorded.
                                    </div>
                                </div>
                            </div>

                            {{-- Skills --}}
                            <div class="relative group">
                                <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-200">
                                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900"
                                        x-text="resume.language === 'ko' ? '보유 기술 및 역량 (Skills)' : 'Skills &amp; Competencies'"></h3>
                                    <button type="button" @click="activeStep = 'skills'" class="text-teal-700 font-bold text-[10px] hover:underline cursor-pointer">✏️ Edit</button>
                                </div>

                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <template x-for="(sk, sidx) in resume.skills" :key="sidx">
                                        <span class="px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-800 text-[11px] font-bold" x-text="sk"></span>
                                    </template>
                                    <div x-show="resume.skills.length === 0" class="text-slate-400 text-xs italic">
                                        No skills added.
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </main>

</div>

{{-- ALPINE APPLICATION LOGIC --}}
<script>
function resumeEditorApp(initial) {
    return {
        viewMode: 'split', // 'split' | 'form' | 'preview'
        activeStep: 'personal', // 'personal', 'summary', 'education', 'experience', 'tests_military', 'self_intro', 'skills', 'projects'
        zoom: 85,
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

            // Auto-adjust viewMode based on window width
            if (window.innerWidth < 1024) {
                this.viewMode = 'form';
            }
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
                window.location.href = `${baseUrl}/${this.resume.id}/download`;
            }
        }
    }
}
</script>
</div>
</body>
</html>
