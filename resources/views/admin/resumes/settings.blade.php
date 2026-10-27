@extends('layouts.admin')

@section('title', 'AI Resume Builder Settings')

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6" x-data="{
    activeTab: 'ai',
    watermarkText: '{{ addslashes($setting->watermark_text ?: 'REIAC COMMUNITY • reiac.org') }}',
    watermarkEnabled: {{ $setting->watermark_enabled ? 'true' : 'false' }},
    verificationMode: '{{ $setting->verification_mode ?: 'both' }}',
    showApiKey: false
}">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-center justify-between text-emerald-800 text-xs font-medium">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('admin.resumes.index') }}" class="hover:text-slate-900 transition flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>Resumes</span>
                </a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Settings</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Resume Builder Settings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage AI writing assistant, PDF watermark branding, and social reshare verification rules.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.resumes.verifications') }}"
               class="px-3.5 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Verifications Queue</span>
                @if(!empty($pendingCount))
                    <span class="bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded text-[11px] font-bold">{{ $pendingCount }}</span>
                @endif
            </a>

            <button type="submit" form="settingsForm"
                    class="px-5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    {{-- TOP TABS NAVIGATION --}}
    <div class="border-b border-slate-200 flex items-center gap-1">
        <button type="button" @click="activeTab = 'ai'"
                :class="activeTab === 'ai' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>AI Writing Engine</span>
            @if(!empty($setting->openai_api_key))
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            @endif
        </button>

        <button type="button" @click="activeTab = 'watermark'"
                :class="activeTab === 'watermark' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>PDF Watermark</span>
            <span class="w-1.5 h-1.5 rounded-full" :class="watermarkEnabled ? 'bg-slate-900' : 'bg-slate-300'"></span>
        </button>

        <button type="button" @click="activeTab = 'social'"
                :class="activeTab === 'social' ? 'border-b-2 border-slate-900 text-slate-900 font-bold' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-800 font-medium'"
                class="px-5 py-3 text-xs transition cursor-pointer -mb-px flex items-center gap-2">
            <span>Social Unlock &amp; Verification</span>
        </button>
    </div>

    {{-- MAIN FORM --}}
    <form id="settingsForm" method="POST" action="{{ route('admin.resumes.settings.update') }}">
        @csrf

        {{-- ========================================================================= --}}
        {{-- TAB 1: AI WRITING ENGINE --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'ai'" class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">OpenAI Writing Assistant</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Powers grammar polish, summary refinement, and job bullet points in the student CV editor.</p>
                    </div>
                    <div>
                        @if(!empty($setting->openai_api_key))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                API Key Connected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Offline Fallback Active
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- OpenAI API Key --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-semibold text-slate-700">OpenAI API Secret Key</label>
                                <button type="button" @click="showApiKey = !showApiKey"
                                        class="text-xs text-slate-500 hover:text-slate-800 font-medium transition cursor-pointer">
                                    <span x-text="showApiKey ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                            <input :type="showApiKey ? 'text' : 'password'"
                                   name="openai_api_key"
                                   value="{{ old('openai_api_key', $setting->openai_api_key) }}"
                                   placeholder="sk-..."
                                   class="w-full text-xs font-mono bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-slate-400 transition">
                            <p class="text-[11px] text-slate-500 mt-1">If no key is provided, the system uses built-in smart grammar rules without showing any errors to students.</p>
                        </div>

                        {{-- OpenAI Model Selection --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Model</label>
                            <select name="openai_model"
                                    class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-slate-900 focus:outline-none focus:border-slate-400 transition">
                                <option value="gpt-4o-mini" {{ old('openai_model', $setting->openai_model) === 'gpt-4o-mini' ? 'selected' : '' }}>
                                    gpt-4o-mini (Recommended - Fast &amp; cost-efficient)
                                </option>
                                <option value="gpt-4o" {{ old('openai_model', $setting->openai_model) === 'gpt-4o' ? 'selected' : '' }}>
                                    gpt-4o (High capability flagship)
                                </option>
                                <option value="gpt-3.5-turbo" {{ old('openai_model', $setting->openai_model) === 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                    gpt-3.5-turbo (Legacy)
                                </option>
                            </select>
                            <p class="text-[11px] text-slate-500 mt-1"><code>gpt-4o-mini</code> provides the best response times and costs less than $0.0001 per resume polish.</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Configure watermark rules in the next tab.</span>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="activeTab = 'watermark'"
                                class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition cursor-pointer">
                            Next: Watermark Settings →
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 2: WATERMARK BRANDING --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'watermark'" class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">PDF Watermark Branding</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Control whether drafts include a diagonal watermark, and customize the stamp text.</p>
                    </div>
                    <div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="watermark_enabled" value="1" x-model="watermarkEnabled" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                            <span class="ml-2 text-xs font-medium text-slate-700" x-text="watermarkEnabled ? 'Watermark Enabled' : 'Watermark Disabled'"></span>
                        </label>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        {{-- Left: Text Input --}}
                        <div class="lg:col-span-7 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Watermark Main Text <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="watermark_text"
                                       x-model="watermarkText"
                                       required
                                       placeholder="REIAC COMMUNITY • reiac.org"
                                       class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-slate-900 focus:outline-none focus:border-slate-400 transition">
                                <p class="text-[11px] text-slate-500 mt-1">This text is stamped diagonally across all pages of exported PDF files.</p>
                            </div>

                            <div class="bg-slate-50 rounded-lg p-3.5 border border-slate-200/70 text-xs text-slate-600 space-y-1">
                                <div class="font-semibold text-slate-800">Behavior Overview</div>
                                <div class="text-[11px] text-slate-500 leading-relaxed">
                                    When enabled, student resumes include this diagonal stamp until the student reshares the platform link according to the policy in the next tab.
                                </div>
                            </div>
                        </div>

                        {{-- Right: Clean Document Preview --}}
                        <div class="lg:col-span-5">
                            <div class="text-xs font-semibold text-slate-700 mb-1.5">Document Stamp Preview</div>
                            <div class="relative h-36 rounded-lg border border-slate-200 bg-white overflow-hidden flex items-center justify-center p-3 select-none">
                                {{-- Background simulated lines --}}
                                <div class="absolute inset-0 p-3 space-y-2 opacity-15 pointer-events-none">
                                    <div class="h-2 bg-slate-400 rounded w-1/3"></div>
                                    <div class="h-1.5 bg-slate-300 rounded w-full"></div>
                                    <div class="h-1.5 bg-slate-300 rounded w-4/5"></div>
                                    <div class="h-1.5 bg-slate-300 rounded w-2/3"></div>
                                    <div class="h-1.5 bg-slate-300 rounded w-3/4"></div>
                                </div>

                                {{-- Watermark Text --}}
                                <div class="relative z-10 text-center transform -rotate-12 transition-opacity"
                                     :class="watermarkEnabled ? 'opacity-70' : 'opacity-15 line-through'">
                                    <div class="text-xs font-black text-slate-600 uppercase tracking-widest"
                                         x-text="watermarkText || 'REIAC COMMUNITY'"></div>
                                    <div class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                                        Share to Remove Watermark
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="activeTab = 'ai'"
                            class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition cursor-pointer">
                        ← Back to AI Engine
                    </button>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="activeTab = 'social'"
                                class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition cursor-pointer">
                            Next: Social Verification →
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition cursor-pointer">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: SOCIAL RESHARE & UNLOCK RULES --}}
        {{-- ========================================================================= --}}
        <div x-show="activeTab === 'social'" class="space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Social Reshare &amp; Unlock Policy</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Define verification rules and pre-filled copy for students removing the watermark.</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Mode Selection --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Verification Method</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Mode 1: Both --}}
                            <label class="p-3.5 rounded-lg border cursor-pointer transition flex items-start gap-2.5"
                                   :class="verificationMode === 'both' ? 'border-slate-900 bg-slate-50/60 ring-1 ring-slate-900' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="both" x-model="verificationMode" class="mt-0.5 text-slate-900 focus:ring-slate-900">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Both Methods (Flexible)</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Students can choose instant 1-click share or upload a screenshot for review.</p>
                                </div>
                            </label>

                            {{-- Mode 2: Instant --}}
                            <label class="p-3.5 rounded-lg border cursor-pointer transition flex items-start gap-2.5"
                                   :class="verificationMode === 'instant' ? 'border-slate-900 bg-slate-50/60 ring-1 ring-slate-900' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="instant" x-model="verificationMode" class="mt-0.5 text-slate-900 focus:ring-slate-900">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">1-Click Instant Share</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Watermark unlocks automatically upon clicking WhatsApp or LinkedIn.</p>
                                </div>
                            </label>

                            {{-- Mode 3: Manual --}}
                            <label class="p-3.5 rounded-lg border cursor-pointer transition flex items-start gap-2.5"
                                   :class="verificationMode === 'manual' ? 'border-slate-900 bg-slate-50/60 ring-1 ring-slate-900' : 'border-slate-200 hover:bg-slate-50'">
                                <input type="radio" name="verification_mode" value="manual" x-model="verificationMode" class="mt-0.5 text-slate-900 focus:ring-slate-900">
                                <div>
                                    <div class="text-xs font-bold text-slate-900">Manual Review Only</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Students must upload proof, which admin approves in the moderation queue.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Share URL & Pre-fill Content --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Target Platform Share URL</label>
                            <input type="url" name="reshare_target_url"
                                   value="{{ old('reshare_target_url', $setting->reshare_target_url) }}"
                                   placeholder="https://community.reiac.org"
                                   class="w-full text-xs bg-white border border-slate-200 rounded-lg px-3 py-2 text-slate-900 focus:outline-none focus:border-slate-400 transition">
                            <p class="text-[11px] text-slate-500 mt-1">The link that will be attached to student social shares.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pre-filled Social Caption</label>
                            <textarea name="reshare_content" rows="2"
                                      class="w-full text-xs bg-white border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:outline-none focus:border-slate-400 transition leading-relaxed">{{ old('reshare_content', $setting->reshare_content) }}</textarea>
                            <p class="text-[11px] text-slate-500 mt-1">Default caption pre-filled when sharing on WhatsApp, LinkedIn, or X.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Guidelines in Unlock Modal</label>
                            <textarea name="reshare_guidelines" rows="3"
                                      class="w-full text-xs bg-white border border-slate-200 rounded-lg p-2.5 text-slate-900 focus:outline-none focus:border-slate-400 transition leading-relaxed">{{ old('reshare_guidelines', $setting->reshare_guidelines) }}</textarea>
                            <p class="text-[11px] text-slate-500 mt-1">Instructions displayed to the student inside the unlock verification modal.</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="activeTab = 'watermark'"
                            class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition cursor-pointer">
                        ← Back to Watermark
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-xs transition cursor-pointer">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection
