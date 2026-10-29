@extends('layouts.admin')

@section('title', 'AI Resume Builder & Watermark Control')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
        currentTab: @js($activeTab ?? 'resumes'),
        proofModalOpen: false,
        proofImageUrl: '',
        rejectModalOpen: false,
        rejectFormAction: '',
        openProof(url) {
            this.proofImageUrl = url;
            this.proofModalOpen = true;
        },
        openReject(action) {
            this.rejectFormAction = action;
            this.rejectModalOpen = true;
        },
        watermarkEnabled: @js((bool)$setting->watermark_enabled),
        watermarkText: @js($setting->watermark_text),
        verificationMode: @js($setting->verification_mode),
        languageMode: @js($setting->language_mode ?: 'both'),
        showApiKey: false
     }">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- PAGE HEADER & QUICK STATUS --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 text-xs">
                <span class="text-[11px] font-extrabold text-teal-600 uppercase tracking-wider">Services &amp; Resources</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-500 font-medium">Student CV Engine</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">AI Resume Builder &amp; Watermark Control</h1>
            <p class="text-xs text-slate-500 mt-0.5">All-in-one control center for student CVs, watermark branding, and reshare verification.</p>
        </div>

        {{-- QUICK STATUS PILLS --}}
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            {{-- Watermark Status Badge --}}
            <div class="px-3.5 py-2 rounded-xl border flex items-center gap-2 text-xs font-bold transition
                {{ $setting->watermark_enabled ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                <span class="w-2 h-2 rounded-full {{ $setting->watermark_enabled ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                <span>Watermark: {{ $setting->watermark_enabled ? 'ON (' . ($setting->watermark_text ?: 'REIAC') . ')' : 'OFF (Clean Downloads)' }}</span>
            </div>

            {{-- Verification Mode Badge --}}
            <div class="px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5">
                <span>⚡ Mode:</span>
                <span class="text-slate-900 font-black">
                    @if($setting->verification_mode === 'instant')
                        1-Click Instant Share
                    @elseif($setting->verification_mode === 'manual')
                        Manual Screenshot Review
                    @else
                        Both (Instant + Screenshot)
                    @endif
                </span>
            </div>

            <div class="px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5">
                <span>🌐 Lang:</span>
                <span class="text-slate-900 font-black">
                    @if($setting->language_mode === 'en')
                        English Only
                    @elseif($setting->language_mode === 'ko')
                        Korean Only
                    @else
                        Bilingual (Both EN + KR)
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MASTER NAVIGATION TABS --}}
    {{-- ========================================================================= --}}
    <div class="flex items-center gap-2 border-b border-slate-200">
        {{-- TAB 1: ALL RESUMES --}}
        <button type="button" @click="currentTab = 'resumes'"
                :class="currentTab === 'resumes' ? 'border-b-2 border-slate-900 text-slate-900 font-black' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-semibold'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>📑 Resumes List</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-bold">
                {{ number_format($stats['total']) }}
            </span>
        </button>

        {{-- TAB 2: VERIFICATIONS QUEUE --}}
        <button type="button" @click="currentTab = 'verifications'"
                :class="currentTab === 'verifications' ? 'border-b-2 border-slate-900 text-slate-900 font-black' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-semibold'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>🛡️ Verifications Queue</span>
            @if($verificationCounts['pending'] > 0)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white animate-pulse">
                    {{ $verificationCounts['pending'] }} Pending
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-600 font-bold">
                    0 Pending
                </span>
            @endif
        </button>

        {{-- TAB 3: WATERMARK & AI SETTINGS --}}
        <button type="button" @click="currentTab = 'settings'"
                :class="currentTab === 'settings' ? 'border-b-2 border-slate-900 text-slate-900 font-black' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-semibold'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>⚙️ Watermark &amp; AI Settings</span>
            <span class="w-1.5 h-1.5 rounded-full {{ $setting->watermark_enabled ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
        </button>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 1: ALL RESUMES LIST --}}
    {{-- ========================================================================= --}}
    <div x-show="currentTab === 'resumes'" class="space-y-6">
        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Resumes</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</h3>
                    <span class="text-[11px] text-slate-400 font-medium">All templates created</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl font-black">
                    📄
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Clean / Watermark Removed</p>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['clean']) }}</h3>
                    <span class="text-[11px] text-slate-400 font-medium">Unlocked by sharing</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-black">
                    ✨
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between cursor-pointer hover:border-amber-300 transition"
                 @click="currentTab = 'verifications'">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Verifications</p>
                    <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($verificationCounts['pending']) }}</h3>
                    <span class="text-[11px] text-amber-600 font-bold">Click to review proofs →</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-black">
                    ⏳
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total PDF Downloads</p>
                    <h3 class="text-2xl font-black text-purple-600 mt-1">{{ number_format($stats['downloads']) }}</h3>
                    <span class="text-[11px] text-slate-400 font-medium">Downloaded by students</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-black">
                    📥
                </div>
            </div>
        </div>

        {{-- FILTER & SEARCH BAR --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.resumes.index') }}" class="flex flex-1 items-center gap-2">
                <input type="hidden" name="tab" value="resumes">
                <div class="relative flex-1 max-w-md">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by title, student name, or email..."
                           class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <select name="watermark" onchange="this.form.submit()"
                        class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">All Watermark Statuses</option>
                    <option value="clean" {{ $watermarkFilter === 'clean' ? 'selected' : '' }}>Clean (Unlocked)</option>
                    <option value="watermarked" {{ $watermarkFilter === 'watermarked' ? 'selected' : '' }}>Watermarked</option>
                </select>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>

                @if($search || $watermarkFilter)
                    <a href="{{ route('admin.resumes.index') }}?tab=resumes" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- RESUMES TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Student</th>
                            <th class="py-3.5 px-4">Resume Title</th>
                            <th class="py-3.5 px-4">Template</th>
                            <th class="py-3.5 px-4 text-center">Downloads</th>
                            <th class="py-3.5 px-4 text-center">Watermark Status</th>
                            <th class="py-3.5 px-4">Created Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($resumes as $resume)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-extrabold flex items-center justify-center text-xs">
                                            {{ substr($resume->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $resume->user->name ?? 'Unknown Student' }}</span>
                                            <span class="text-[11px] text-slate-400 block">{{ $resume->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $resume->title }}
                                    @if(!empty($resume->personal_info['job_title']))
                                        <span class="text-[11px] text-slate-400 font-normal block">{{ $resume->personal_info['job_title'] }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider
                                        {{ $resume->template === 'modern' ? 'bg-teal-50 text-teal-700 border border-teal-200' : '' }}
                                        {{ $resume->template === 'classic' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                        {{ $resume->template === 'creative' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                        {{ $resume->template === 'harvard' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                        {{ $resume->template === 'korean_standard' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}">
                                        {{ str_replace('_', ' ', $resume->template) }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                    {{ $resume->download_count }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @if($resume->is_watermark_removed || !$setting->watermark_enabled)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span>✓</span> Clean (No Watermark)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                            <span>🔒</span> Watermarked
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $resume->created_at->format('M d, Y h:i A') }}
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('resume-builder.preview', $resume->id) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[11px] transition">
                                            Preview
                                        </a>

                                        <a href="{{ route('resume-builder.preview', $resume->id) }}?print=1" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] transition">
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-4xl mb-2">📄</div>
                                    <p class="font-bold text-sm text-slate-600">No student resumes found</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Students can build resumes from the frontend /resume-builder page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($resumes->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $resumes->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 2: VERIFICATIONS QUEUE --}}
    {{-- ========================================================================= --}}
    <div x-show="currentTab === 'verifications'" class="space-y-6">
        {{-- QUEUE HEADER & SUB-TABS --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-black text-slate-900">Student Reshare Proof Verifications</h2>
                <p class="text-xs text-slate-500 mt-0.5">Approve screenshots to immediately remove the watermark or reject invalid posts.</p>
            </div>

            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('admin.resumes.index', ['tab' => 'verifications', 'verification_status' => 'pending']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $verificationStatus === 'pending' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Pending ({{ $verificationCounts['pending'] }})
                </a>
                <a href="{{ route('admin.resumes.index', ['tab' => 'verifications', 'verification_status' => 'approved']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $verificationStatus === 'approved' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Approved ({{ $verificationCounts['approved'] }})
                </a>
                <a href="{{ route('admin.resumes.index', ['tab' => 'verifications', 'verification_status' => 'rejected']) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ $verificationStatus === 'rejected' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Rejected ({{ $verificationCounts['rejected'] }})
                </a>
            </div>
        </div>

        {{-- VERIFICATIONS TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Student</th>
                            <th class="py-3.5 px-4">Resume Title</th>
                            <th class="py-3.5 px-4">Platform</th>
                            <th class="py-3.5 px-4">Proof Type</th>
                            <th class="py-3.5 px-4 text-center">Screenshot Proof</th>
                            <th class="py-3.5 px-4">Submitted Date</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($verifications as $v)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $v->user->name ?? 'User #' . $v->user_id }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $v->user->email ?? 'N/A' }}</span>
                                </td>

                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $v->resume->title ?? 'Resume #' . $v->resume_id }}
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-800">
                                        {{ $v->platform }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($v->verification_method === 'instant')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                            ⚡ 1-Click Instant
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-purple-50 text-purple-700 border border-purple-200">
                                            📸 Screenshot Proof
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    @php $proofUrls = $v->proof_urls; @endphp
                                    @if(!empty($proofUrls))
                                        <div class="flex flex-wrap items-center justify-center gap-1.5">
                                            @foreach($proofUrls as $idx => $url)
                                                <button type="button" @click="openProof('{{ $url }}')"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-[10.5px] font-bold text-slate-800 transition cursor-pointer">
                                                    <span>📸 Proof {{ count($proofUrls) > 1 ? '#' . ($idx + 1) : '' }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @elseif($v->post_url)
                                        <a href="{{ $v->post_url }}" target="_blank" class="text-indigo-600 hover:underline text-[11px] font-bold">Link</a>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Instant Share Link</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                    {{ $v->created_at->format('M d, Y h:i A') }}
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    @if($v->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Approve Form --}}
                                            <form method="POST" action="{{ route('admin.resumes.verifications.approve', $v->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-black text-[11px] shadow-2xs transition flex items-center gap-1 cursor-pointer">
                                                    <span>✓</span>
                                                    <span>Approve</span>
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <button type="button" @click="openReject('{{ route('admin.resumes.verifications.reject', $v->id) }}')"
                                                    class="px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] transition cursor-pointer">
                                                <span>✕ Reject</span>
                                            </button>
                                        </div>
                                    @elseif($v->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span>✓</span> Approved
                                        </span>
                                    @else
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                                <span>✕</span> Rejected
                                            </span>
                                            @if($v->rejection_reason)
                                                <p class="text-[10px] text-slate-400 mt-0.5 max-w-xs ml-auto line-clamp-1" title="{{ $v->rejection_reason }}">{{ $v->rejection_reason }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="text-4xl mb-2">🛡️</div>
                                    <p class="font-bold text-sm text-slate-600">No {{ $verificationStatus }} verifications</p>
                                    <p class="text-xs text-slate-400 mt-0.5">When students upload share screenshots, they will appear here for review.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($verifications->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $verifications->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- TAB 3: WATERMARK & AI SETTINGS --}}
    {{-- ========================================================================= --}}
    <div x-show="currentTab === 'settings'" class="space-y-6">
        <form method="POST" action="{{ route('admin.resumes.settings.update') }}" class="space-y-6">
            @csrf

            {{-- 1. WATERMARK BRANDING --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">1. PDF Watermark Stamp Control</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Toggle watermark on or off globally, and customize the diagonal stamp text.</p>
                    </div>
                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="watermark_enabled" value="1" x-model="watermarkEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-2.5 text-xs font-black text-slate-800" x-text="watermarkEnabled ? 'Watermark Enabled' : 'Watermark Disabled'"></span>
                        </label>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-7 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Watermark Stamp Text <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="watermark_text" x-model="watermarkText" required
                                       value="{{ old('watermark_text', $setting->watermark_text) }}"
                                       placeholder="REIAC COMMUNITY • reiac.org"
                                       class="w-full text-xs font-bold bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">
                                <p class="text-[11px] text-slate-400 mt-1">This text is printed diagonally across every page of student PDF downloads until shared.</p>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1">
                                <span class="font-bold text-slate-800 block">💡 How it works:</span>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    If you uncheck <strong>Watermark Enabled</strong>, no watermark will appear on any student resume. When enabled, students must share to remove it.
                                </p>
                            </div>
                        </div>

                        {{-- Live Stamp Preview --}}
                        <div class="lg:col-span-5">
                            <span class="block text-xs font-bold text-slate-700 mb-1.5">Live Document Stamp Preview</span>
                            <div class="relative h-40 rounded-xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center p-4 select-none shadow-2xs">
                                <div class="absolute inset-0 p-4 space-y-2.5 opacity-15 pointer-events-none">
                                    <div class="h-2.5 bg-slate-400 rounded w-1/3"></div>
                                    <div class="h-2 bg-slate-300 rounded w-full"></div>
                                    <div class="h-2 bg-slate-300 rounded w-4/5"></div>
                                    <div class="h-2 bg-slate-300 rounded w-2/3"></div>
                                    <div class="h-2 bg-slate-300 rounded w-3/4"></div>
                                </div>

                                <div class="relative z-10 text-center transform -rotate-12 transition-opacity"
                                     :class="watermarkEnabled ? 'opacity-80' : 'opacity-15 line-through'">
                                    <div class="text-sm font-black text-slate-700 uppercase tracking-widest"
                                         x-text="watermarkText || 'REIAC COMMUNITY'"></div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">
                                        Share to Remove Watermark
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. VERIFICATION MODE --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-black text-slate-900">2. Watermark Removal Rules &amp; Social Links</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Choose how students are allowed to unlock the clean watermark-free PDF.</p>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2.5">Verification Mode</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Mode 1: Instant --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="verificationMode === 'instant' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="instant" x-model="verificationMode" class="mt-0.5 text-emerald-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                        <span>⚡ 1-Click Instant Share</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] bg-emerald-100 text-emerald-800 font-bold">Fastest</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1">Watermark removes immediately when user clicks WhatsApp or LinkedIn. No admin approval needed!</p>
                                </div>
                            </label>

                            {{-- Mode 2: Both --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="verificationMode === 'both' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="both" x-model="verificationMode" class="mt-0.5 text-emerald-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900">🔄 Both Methods (Flexible)</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Students can choose instant 1-click share or upload a screenshot proof for admin review.</p>
                                </div>
                            </label>

                            {{-- Mode 3: Manual --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="verificationMode === 'manual' ? 'border-emerald-600 bg-emerald-50/50 ring-1 ring-emerald-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="manual" x-model="verificationMode" class="mt-0.5 text-emerald-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900">📸 Manual Review Only</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Students must upload screenshot proof. Watermark unlocks only after admin approves.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Language Mode Selection (Both vs Single) --}}
                    <div class="pt-3 border-t border-slate-100">
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Resume Language Support (Single vs Both)</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Lang: Both --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="languageMode === 'both' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="language_mode" value="both" x-model="languageMode" class="mt-0.5 text-indigo-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900 flex items-center gap-1.5">
                                        <span>🌐 Bilingual (Both EN + 한국어)</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] bg-indigo-100 text-indigo-800 font-bold">Recommended</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1">Students can create resumes in English or Korean, and switch anytime.</p>
                                </div>
                            </label>

                            {{-- Lang: EN only --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="languageMode === 'en' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="language_mode" value="en" x-model="languageMode" class="mt-0.5 text-indigo-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900">🇺🇸 English Only (Single)</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Only English templates and input fields enabled for international candidates.</p>
                                </div>
                            </label>

                            {{-- Lang: KO only --}}
                            <label class="p-4 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                   :class="languageMode === 'ko' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="language_mode" value="ko" x-model="languageMode" class="mt-0.5 text-indigo-600">
                                <div>
                                    <div class="text-xs font-black text-slate-900">🇰🇷 Korean Only (Single)</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Only Korean standard application forms (입사지원서) enabled.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Target Platform Share URL</label>
                            <input type="url" name="reshare_target_url"
                                   value="{{ old('reshare_target_url', $setting->reshare_target_url) }}"
                                   placeholder="https://community.reiac.org"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">The link attached to student social shares.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pre-filled Social Caption</label>
                            <textarea name="reshare_content" rows="2"
                                      class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">{{ old('reshare_content', $setting->reshare_content) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Caption pre-filled when sharing on WhatsApp or LinkedIn.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Guidelines in Unlock Modal</label>
                            <textarea name="reshare_guidelines" rows="3"
                                      class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">{{ old('reshare_guidelines', $setting->reshare_guidelines) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Instructions shown to students in the unlock popup.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. AI WRITING ENGINE --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">3. OpenAI AI Writing Assistant</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Powers grammar polish, career summary generator, and bullet points refinement.</p>
                    </div>
                    <div>
                        @if(!empty($setting->openai_api_key))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                API Key Connected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                Offline Fallback Active
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-slate-700">OpenAI API Secret Key</label>
                                <button type="button" @click="showApiKey = !showApiKey"
                                        class="text-xs text-slate-500 hover:text-slate-800 font-bold transition cursor-pointer">
                                    <span x-text="showApiKey ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                            <input :type="showApiKey ? 'text' : 'password'"
                                   name="openai_api_key"
                                   value="{{ old('openai_api_key', $setting->openai_api_key) }}"
                                   placeholder="sk-..."
                                   class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">
                            <p class="text-[11px] text-slate-400 mt-1">If empty, built-in smart grammar rules are used automatically.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Model</label>
                            <select name="openai_model"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400">
                                <option value="gpt-6-luna" {{ old('openai_model', $setting->openai_model) === 'gpt-6-luna' ? 'selected' : '' }}>
                                    gpt-6-luna (Client Choice - Ultra Fast &amp; High Intelligence)
                                </option>
                                <option value="gpt-4o-mini" {{ old('openai_model', $setting->openai_model) === 'gpt-4o-mini' ? 'selected' : '' }}>
                                    gpt-4o-mini (Fast &amp; cheap)
                                </option>
                                <option value="gpt-4o" {{ old('openai_model', $setting->openai_model) === 'gpt-4o' ? 'selected' : '' }}>
                                    gpt-4o (Flagship)
                                </option>
                                <option value="gpt-3.5-turbo" {{ old('openai_model', $setting->openai_model) === 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                    gpt-3.5-turbo (Legacy)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SAVE BUTTON --}}
            <div class="flex items-center justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider shadow-md transition flex items-center gap-2 cursor-pointer">
                    <span>💾 Save All Settings</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: PROOF IMAGE VIEWER --}}
    {{-- ========================================================================= --}}
    <div x-show="proofModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 flex flex-col max-h-[92vh]" @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-black text-base text-slate-900">Screenshot Proof</h3>
                <button type="button" @click="proofModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
            </div>
            <div class="my-4 overflow-auto flex-1 flex items-center justify-center bg-slate-50 rounded-2xl p-2 border border-slate-100">
                <img :src="proofImageUrl" alt="Proof" class="max-h-[65vh] max-w-full rounded-xl object-contain shadow-xs">
            </div>
            <div class="pt-2 flex justify-end">
                <button type="button" @click="proofModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL: REJECT REASON --}}
    {{-- ========================================================================= --}}
    <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-xs">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100" @click.away="rejectModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-black text-base text-rose-600">Reject Reshare Verification</h3>
                <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
            </div>
            <form :action="rejectFormAction" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="e.g. Screenshot does not show REIAC link or hashtag..."
                              class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-900 focus:bg-white focus:outline-none focus:border-slate-400"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs shadow-xs transition">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
