<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#F4F6F9]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Saved Resumes | REIAC Community</title>

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
<body class="min-h-screen bg-[#F4F6F9] text-slate-800 antialiased selection:bg-amber-500 selection:text-slate-950 flex flex-col">

    {{-- OFFICIAL REIAC COMMUNITY TOPBAR --}}
    <x-community.topbar :notifications-count="auth()->check() ? auth()->user()->unreadNotifications()->count() : 0" />

<div class="flex-1 py-6 sm:py-10 px-3.5 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6 sm:space-y-8 w-full" x-data="myResumesManager()">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl text-xs font-bold shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center text-[10px]">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold p-1">✕</button>
        </div>
    @endif

    {{-- RESPONSIVE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-slate-200/80 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('resume-builder.index') }}" class="text-xs font-bold text-slate-400 hover:text-amber-600 transition flex items-center gap-1">
                    <span>←</span>
                    <span>Templates &amp; Overview</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-[11px] font-extrabold text-amber-600 uppercase tracking-wider bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                    Account Library
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">My Saved Resumes</h1>
            <p class="text-xs sm:text-sm text-slate-500">Manage, edit, export, and unlock watermark-free PDFs for your resumes.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 pt-1 sm:pt-0">
            <a href="{{ route('resume-builder.index') }}#templates"
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs uppercase tracking-wider shadow-sm hover:shadow transition flex items-center justify-center gap-2 cursor-pointer">
                <span class="text-base leading-none">+</span>
                <span>Create New Resume</span>
            </a>
        </div>
    </div>

    {{-- QUICK OVERVIEW STATS (RESPONSIVE 2 COLS ON MOBILE, 4 ON TABLET/DESKTOP) --}}
    @php
        $totalResumes = $resumes->total();
        $cleanCount = $resumes->filter(fn($r) => $r->is_watermark_removed || !$setting->watermark_enabled)->count();
        $pendingCount = $resumes->filter(fn($r) => $r->latestVerification && $r->latestVerification->status === 'pending')->count();
        $totalDownloads = $resumes->sum('download_count');
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Resumes</span>
            <span class="text-lg sm:text-2xl font-black text-slate-900 mt-0.5 block">{{ $totalResumes }}</span>
        </div>
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-600 uppercase tracking-wider block">Clean Unlocked</span>
            <span class="text-lg sm:text-2xl font-black text-emerald-700 mt-0.5 block">{{ $cleanCount }}</span>
        </div>
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold text-amber-600 uppercase tracking-wider block">Under Review</span>
            <span class="text-lg sm:text-2xl font-black text-amber-700 mt-0.5 block">{{ $pendingCount }}</span>
        </div>
        <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-slate-200/80 shadow-xs">
            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider block">PDF Downloads</span>
            <span class="text-lg sm:text-2xl font-black text-slate-900 mt-0.5 block">{{ $totalDownloads }}</span>
        </div>
    </div>

    {{-- RESUMES GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($resumes as $resume)
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all duration-200 p-4 sm:p-6 flex flex-col justify-between space-y-4 sm:space-y-5">
                <div>
                    {{-- TOP TAGS: TEMPLATE + LANGUAGE + WATERMARK STATUS --}}
                    <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- Template Badge --}}
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider
                                {{ $resume->template === 'modern' ? 'bg-teal-50 text-teal-700 border border-teal-200' : '' }}
                                {{ $resume->template === 'classic' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                {{ $resume->template === 'creative' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                {{ $resume->template === 'korean_standard' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}">
                                {{ ucwords(str_replace('_', ' ', $resume->template)) }}
                            </span>

                            {{-- Language Badge --}}
                            <span class="px-2 py-0.5 rounded-md text-[9.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                {{ ($resume->language ?? 'en') === 'ko' ? '🇰🇷 KO' : '🌐 EN' }}
                            </span>
                        </div>

                        {{-- Watermark Status Badge / Button --}}
                        <div>
                            @if($resume->is_watermark_removed || !$setting->watermark_enabled)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span>✓</span>
                                    <span>Clean Copy</span>
                                </span>
                            @elseif($resume->latestVerification && $resume->latestVerification->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                    <span>⏳</span>
                                    <span>Review Pending</span>
                                </span>
                            @else
                                <button type="button" @click="openUnlockModal({{ $resume->id }}, '{{ addslashes($resume->title) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer shadow-2xs">
                                    <span>🔒</span>
                                    <span>Unlock Clean PDF</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- RESUME DETAILS --}}
                    <h3 class="text-base sm:text-lg font-black text-slate-900 line-clamp-1 hover:text-amber-600 transition">
                        <a href="{{ route('resume-builder.preview', $resume->id) }}" target="_blank">
                            {{ $resume->title }}
                        </a>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">
                        <span class="font-bold text-slate-700">{{ $resume->personal_info['full_name'] ?? 'Candidate' }}</span>
                        @if(!empty($resume->personal_info['job_title']))
                            <span class="text-slate-400">•</span>
                            <span class="text-amber-700 font-semibold">{{ $resume->personal_info['job_title'] }}</span>
                        @endif
                    </p>

                    <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Updated: {{ $resume->updated_at->diffForHumans() }}</span>
                        <span class="font-semibold text-slate-600">📥 {{ $resume->download_count }} downloads</span>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    {{-- Primary: Live A4 Preview --}}
                    <a href="{{ route('resume-builder.preview', $resume->id) }}" target="_blank"
                       class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs text-center transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                        <span>👁️</span>
                        <span>Preview Live A4 Sheet</span>
                        <span class="text-amber-400">↗</span>
                    </a>

                    {{-- Secondary: Edit & PDF --}}
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('resume-builder.edit', $resume->id) }}"
                           class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs text-center transition flex items-center justify-center gap-1.5 border border-slate-200/70">
                            <span>✏️</span>
                            <span>Edit Form</span>
                        </a>

                        <a href="{{ route('resume-builder.preview', $resume->id) }}?print=1" target="_blank"
                           class="py-2 px-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs text-center transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                            <span>📥</span>
                            <span>Print / PDF</span>
                        </a>
                    </div>

                    {{-- Card Footer: Direct Link & Delete --}}
                    <div class="flex items-center justify-between pt-1.5 text-[11px]">
                        <span class="text-slate-400 text-[10.5px]">ID: #{{ $resume->id }}</span>

                        <form method="POST" action="{{ route('resume-builder.destroy', $resume->id) }}"
                              onsubmit="return confirm('Are you sure you want to delete this resume? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold cursor-pointer text-[11px] flex items-center gap-1 hover:underline">
                                <span>🗑️</span>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 sm:py-16 text-center bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <div class="text-4xl sm:text-5xl mb-3">📄</div>
                <h3 class="text-lg font-black text-slate-900">No resumes saved yet</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">Pick a template and fill in your details to generate your first AI-enhanced professional resume.</p>
                <div class="mt-5">
                    <a href="{{ route('resume-builder.index') }}#templates"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs uppercase tracking-wider shadow-sm transition">
                        <span>+ Start Building Your Resume</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($resumes->hasPages())
        <div class="pt-4 flex justify-center">
            {{ $resumes->links() }}
        </div>
    @endif

    {{-- UNLOCK WATERMARK / SHARE MODAL (MOBILE RESPONSIVE DIALOG) --}}
    <div x-show="unlockModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-3 sm:p-4 backdrop-blur-xs">
        <div class="bg-white rounded-2xl sm:rounded-3xl max-w-lg w-full p-4 sm:p-7 shadow-2xl border border-slate-100 flex flex-col max-h-[92vh] overflow-y-auto" @click.away="unlockModalOpen = false">
            {{-- MODAL HEADER --}}
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white font-black text-base sm:text-lg flex items-center justify-center shadow-xs shrink-0">✨</span>
                    <div>
                        <h3 class="font-black text-sm sm:text-base text-slate-900">Download Clean PDF (No Watermark)</h3>
                        <p class="text-[10px] sm:text-[11px] text-slate-500">Share REIAC Community to permanently unlock your watermark-free CV.</p>
                    </div>
                </div>
                <button @click="unlockModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold p-1 cursor-pointer">✕</button>
            </div>

            {{-- RESHARE GUIDELINE TIP --}}
            <div class="mt-3.5 p-3 sm:p-3.5 rounded-xl sm:rounded-2xl bg-amber-50/80 border border-amber-200/90 text-xs">
                <div class="flex items-start gap-2">
                    <span class="text-amber-700 font-bold text-sm shrink-0">📋</span>
                    <div class="text-[10.5px] sm:text-[11px] text-amber-900 leading-relaxed">
                        <span class="font-extrabold">Guidelines: </span>
                        {{ $setting->reshare_guidelines }}
                    </div>
                </div>
            </div>

            {{-- MODE TABS (When admin allows 'both') --}}
            @if($setting->verification_mode === 'both')
                <div class="flex items-center p-1 bg-slate-100 rounded-xl my-3.5 gap-1">
                    <button type="button" @click="unlockTab = 'instant'"
                            :class="unlockTab === 'instant' ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-500 font-semibold hover:text-slate-700'"
                            class="flex-1 py-2 text-[11px] sm:text-xs rounded-lg transition flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer">
                        <span>⚡ 1-Click Share</span>
                        <span class="px-1.5 py-0.2 rounded text-[9px] bg-emerald-100 text-emerald-800 font-bold">Fast</span>
                    </button>
                    <button type="button" @click="unlockTab = 'manual'"
                            :class="unlockTab === 'manual' ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-500 font-semibold hover:text-slate-700'"
                            class="flex-1 py-2 text-[11px] sm:text-xs rounded-lg transition flex items-center justify-center gap-1 sm:gap-1.5 cursor-pointer">
                        <span>📸 Upload Proof</span>
                        <span class="px-1.5 py-0.2 rounded text-[9px] bg-amber-100 text-amber-800 font-bold">Review</span>
                    </button>
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- TAB 1: INSTANT 1-CLICK SHARE --}}
            {{-- ========================================================= --}}
            @if($setting->verification_mode === 'both' || $setting->verification_mode === 'instant')
                <div x-show="unlockTab === 'instant'" class="space-y-3.5 pt-1">
                    <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-50/70 border border-emerald-200/70 text-xs text-emerald-950">
                        <div class="flex items-center gap-1.5 font-black text-emerald-900">
                            <span>🚀</span>
                            <span>Instant Unlock — No Waiting Required!</span>
                        </div>
                        <p class="text-[10.5px] sm:text-[11px] text-emerald-800 mt-1 leading-relaxed">
                            Click any button below to share REIAC. As soon as you click, <strong>your watermark unlocks immediately</strong>!
                        </p>
                    </div>

                    <div class="space-y-2">
                        <span class="text-xs font-black text-slate-700 block">Choose Where to Share:</span>
                        
                        {{-- WhatsApp (Primary Highlight) --}}
                        <button type="button" @click="handleInstantShare('whatsapp')"
                                class="w-full py-3 px-3.5 sm:px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center justify-between shadow-xs transition cursor-pointer">
                            <span class="flex items-center gap-2 sm:gap-2.5">
                                <span class="text-base">💬</span>
                                <span>Share on WhatsApp</span>
                            </span>
                            <span class="px-2 py-0.5 rounded bg-emerald-700/60 text-[10px] uppercase font-bold tracking-wider">Instant Unlock →</span>
                        </button>

                        {{-- Grid for other socials --}}
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            {{-- LinkedIn --}}
                            <button type="button" @click="handleInstantShare('linkedin')"
                                    class="py-2.5 px-3 rounded-xl bg-[#0A66C2] hover:bg-[#084e96] text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <span>💼</span>
                                <span>LinkedIn</span>
                            </button>

                            {{-- Twitter / X --}}
                            <button type="button" @click="handleInstantShare('twitter')"
                                    class="py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <span>🐦</span>
                                <span>Twitter / X</span>
                            </button>

                            {{-- Facebook --}}
                            <button type="button" @click="handleInstantShare('facebook')"
                                    class="py-2.5 px-3 rounded-xl bg-[#1877F2] hover:bg-[#125ec7] text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition cursor-pointer">
                                <span>👥</span>
                                <span>Facebook</span>
                            </button>

                            {{-- Copy Share Link Button --}}
                            <button type="button" @click="copyShareLink()"
                                    class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center justify-center gap-1.5 transition cursor-pointer border border-slate-200">
                                <span>📋</span>
                                <span x-text="copiedLink ? '✓ Copied!' : 'Copy Link'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ========================================================= --}}
            {{-- TAB 2: UPLOAD SCREENSHOT PROOF (MANUAL) --}}
            {{-- ========================================================= --}}
            @if($setting->verification_mode === 'both' || $setting->verification_mode === 'manual')
                <div x-show="unlockTab === 'manual'" class="space-y-3.5 pt-1">
                    <div class="p-3 sm:p-3.5 rounded-xl sm:rounded-2xl bg-purple-50/70 border border-purple-200/70 text-xs text-purple-950">
                        <div class="flex items-center gap-1.5 font-black text-purple-900">
                            <span>📸</span>
                            <span>Upload Screenshot Proof</span>
                        </div>
                        <p class="text-[10.5px] sm:text-[11px] text-purple-800 mt-1 leading-relaxed">
                            Shared on Instagram Story or WhatsApp Status? Upload a screenshot of your post for admin review to unlock your clean PDF.
                        </p>
                    </div>

                    <form @submit.prevent="submitProofForm()" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Platform *</label>
                            <select x-model="proofPlatform" class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 focus:bg-white focus:outline-none focus:border-slate-400">
                                <option value="instagram">Instagram Story / Post</option>
                                <option value="whatsapp">WhatsApp Status</option>
                                <option value="linkedin">LinkedIn Post</option>
                                <option value="facebook">Facebook Story / Feed</option>
                                <option value="other">Other Platform</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Screenshot Proof Image *</label>
                            <input type="file" @change="proofFile = $event.target.files[0]" accept="image/*" required
                                   class="w-full text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 border border-slate-200 rounded-xl p-2 bg-slate-50">
                            <p class="text-[10px] text-slate-400 mt-1">Accepts PNG, JPG up to 5MB.</p>
                        </div>

                        <div class="pt-1.5">
                            <button type="submit" :disabled="isUploadingProof"
                                    class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs uppercase tracking-wider shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <span x-show="!isUploadingProof">Submit Proof for Review</span>
                                <span x-show="isUploadingProof">Uploading Proof...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- MODAL FOOTER --}}
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-[10.5px] sm:text-[11px] text-slate-400">Takes less than 10 seconds</span>
                <button type="button" @click="unlockModalOpen = false" class="font-bold text-slate-400 hover:text-slate-600 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function myResumesManager() {
    return {
        unlockModalOpen: false,
        activeResumeId: null,
        activeResumeTitle: '',
        unlockTab: '{{ $setting->verification_mode === "manual" ? "manual" : "instant" }}',
        proofPlatform: 'instagram',
        proofFile: null,
        isUploadingProof: false,
        copiedLink: false,
        shareText: @js($setting->reshare_content),
        shareUrl: @js($setting->reshare_target_url ?: url('/resume-builder')),

        openUnlockModal(id, title) {
            this.activeResumeId = id;
            this.activeResumeTitle = title;
            this.unlockModalOpen = true;
        },

        copyShareLink() {
            const fullText = `${this.shareText} ${this.shareUrl}`;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(fullText).then(() => {
                    this.copiedLink = true;
                    setTimeout(() => { this.copiedLink = false; }, 2500);
                });
            } else {
                alert('Share link: ' + fullText);
            }
        },

        async handleInstantShare(platform) {
            const encodedText = encodeURIComponent(this.shareText);
            const encodedUrl = encodeURIComponent(this.shareUrl);
            let targetWindowUrl = '';

            if (platform === 'whatsapp') {
                targetWindowUrl = `https://api.whatsapp.com/send?text=${encodedText}%20${encodedUrl}`;
            } else if (platform === 'linkedin') {
                targetWindowUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
            } else if (platform === 'twitter') {
                targetWindowUrl = `https://twitter.com/intent/tweet?text=${encodedText}&url=${encodedUrl}`;
            } else if (platform === 'facebook') {
                targetWindowUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            }

            if (targetWindowUrl) {
                window.open(targetWindowUrl, '_blank', 'width=600,height=500');
            }

            // Call backend instant verification
            try {
                const baseUrl = "{{ url('/resume-builder') }}";
                const response = await fetch(`${baseUrl}/${this.activeResumeId}/share`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        verification_method: 'instant',
                        platform: platform
                    })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        title: 'Watermark Removed!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Great!'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Notice', data.message, 'info');
                }
            } catch (err) {
                console.error(err);
            }
        },

        async submitProofForm() {
            if (!this.proofFile) {
                alert('Please select an image file first.');
                return;
            }

            this.isUploadingProof = true;
            const formData = new FormData();
            formData.append('verification_method', 'screenshot');
            formData.append('platform', this.proofPlatform);
            formData.append('proof_image', this.proofFile);

            try {
                const baseUrl = "{{ url('/resume-builder') }}";
                const response = await fetch(`${baseUrl}/${this.activeResumeId}/share`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                this.isUploadingProof = false;

                if (data.success) {
                    this.unlockModalOpen = false;
                    Swal.fire({
                        title: 'Proof Submitted!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to submit proof.', 'error');
                }
            } catch (err) {
                this.isUploadingProof = false;
                Swal.fire('Error', 'An unexpected error occurred while uploading proof.', 'error');
            }
        }
    }
}
</script>
</div>
</body>
</html>
