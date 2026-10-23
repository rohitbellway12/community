@extends('layouts.admin')

@section('title', 'AI Resume Builder & Watermark Settings')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="{
    watermarkText: '{{ addslashes($setting->watermark_text ?: 'REIAC COMMUNITY • reiac.org') }}',
    watermarkEnabled: {{ $setting->watermark_enabled ? 'true' : 'false' }},
    verificationMode: '{{ $setting->verification_mode ?: 'both' }}',
    showApiKey: false
}">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    {{-- PAGE HEADER & ACTION BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.resumes.index') }}" class="text-xs font-bold text-slate-400 hover:text-amber-600 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    <span>Back to Resumes</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-reiac-gold uppercase tracking-wider">Engine Settings</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">AI Resume Builder &amp; Watermark Settings</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure OpenAI API connectivity, customize student watermark branding, and control social reshare rules.</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.resumes.verifications') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Verifications Queue</span>
            </a>

            <button type="submit" form="settingsForm"
                    class="px-6 py-2.5 rounded-xl bg-reiac-gold hover:bg-amber-500 text-reiac-navy font-black text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save All Changes</span>
            </button>
        </div>
    </div>

    {{-- SETTINGS FORM --}}
    <form id="settingsForm" method="POST" action="{{ route('admin.resumes.settings.update') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf

        {{-- LEFT COLUMN: OpenAI Integration & Watermark Engine (7 Cols) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- CARD 1: OPENAI CONFIGURATION --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-200/60 text-teal-700 font-black text-lg flex items-center justify-center shadow-xs">
                            🤖
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">OpenAI Engine Configuration</h2>
                            <p class="text-[11px] text-slate-500">Powers smart grammar polish, executive summaries, and action-oriented experience bullets.</p>
                        </div>
                    </div>

                    @if(!empty($setting->openai_api_key))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live API Connected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Smart Heuristic Fallback
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    {{-- Secret Key Field --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">
                                OpenAI API Secret Key
                            </label>
                            <button type="button" @click="showApiKey = !showApiKey"
                                    class="text-[11px] font-bold text-teal-700 hover:text-teal-900 transition flex items-center gap-1">
                                <span x-text="showApiKey ? 'Hide Key' : 'Reveal Key'"></span>
                            </button>
                        </div>

                        <div class="relative">
                            <input :type="showApiKey ? 'text' : 'password'"
                                   name="openai_api_key"
                                   value="{{ old('openai_api_key', $setting->openai_api_key) }}"
                                   placeholder="sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                                   class="w-full text-xs font-mono bg-white border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <div class="absolute right-3 top-2.5 text-slate-400 pointer-events-none">
                                🔑
                            </div>
                        </div>

                        <div class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-2.5 text-[11px] text-slate-500">
                            <span class="text-slate-400 mt-0.5">ℹ️</span>
                            <div>
                                Provide your key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-teal-700 font-bold underline hover:text-teal-900">platform.openai.com</a>. 
                                <strong>Safety guarantee:</strong> If no key is set or the API balance runs low, our built-in intelligent grammar engine operates seamlessly as a fallback so students never encounter an error.
                            </div>
                        </div>
                    </div>

                    {{-- OpenAI Model Selector --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            OpenAI Model Selection
                        </label>
                        <select name="openai_model"
                                class="w-full text-xs font-bold bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="gpt-4o-mini" {{ old('openai_model', $setting->openai_model) === 'gpt-4o-mini' ? 'selected' : '' }}>
                                gpt-4o-mini (Recommended • Lightning Fast, High Quality &amp; ~$0.0001 per resume)
                            </option>
                            <option value="gpt-4o" {{ old('openai_model', $setting->openai_model) === 'gpt-4o' ? 'selected' : '' }}>
                                gpt-4o (Flagship Model • Deepest contextual understanding)
                            </option>
                            <option value="gpt-3.5-turbo" {{ old('openai_model', $setting->openai_model) === 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                gpt-3.5-turbo (Legacy Model)
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- CARD 2: WATERMARK BRANDING & APPEARANCE --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-200/60 text-amber-700 font-black text-lg flex items-center justify-center shadow-xs">
                            🛡️
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Watermark Appearance &amp; Policy</h2>
                            <p class="text-[11px] text-slate-500">Controls default PDF export branding and diagonal background stamping.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Toggle Switch for Watermark --}}
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Enable PDF Watermark by Default</span>
                            <span class="text-[11px] text-slate-500 block">All new resumes will be generated with the diagonal watermark until verified via reshare.</span>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="watermark_enabled" value="1" x-model="watermarkEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </label>
                    </div>

                    {{-- Watermark Text Input --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-bold text-slate-800">
                                Watermark Display Text <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-semibold text-slate-400">Printed diagonally across all pages</span>
                        </div>
                        <input type="text" name="watermark_text"
                               x-model="watermarkText"
                               required
                               placeholder="e.g. REIAC COMMUNITY • reiac.org"
                               class="w-full text-xs font-black tracking-wider bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    {{-- Interactive Live Watermark Canvas Preview --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Live PDF Stamp Preview</span>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200" x-show="watermarkEnabled">
                                Stamp Active
                            </span>
                            <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded" x-show="!watermarkEnabled">
                                Disabled
                            </span>
                        </div>

                        <div class="relative h-28 rounded-2xl border-2 border-dashed border-slate-200 bg-white overflow-hidden flex items-center justify-center shadow-inner">
                            <div class="absolute inset-0 flex flex-col justify-around p-3 opacity-20 pointer-events-none">
                                <div class="h-2 bg-slate-400 rounded w-1/3"></div>
                                <div class="h-2 bg-slate-400 rounded w-full"></div>
                                <div class="h-2 bg-slate-400 rounded w-5/6"></div>
                                <div class="h-2 bg-slate-400 rounded w-2/3"></div>
                            </div>

                            <div class="relative z-10 transition-all duration-300"
                                 :class="watermarkEnabled ? 'opacity-85' : 'opacity-20 line-through'">
                                <div class="text-center transform -rotate-12 select-none">
                                    <span class="font-black text-lg sm:text-xl text-slate-400 uppercase tracking-widest block" x-text="watermarkText || 'REIAC COMMUNITY'"></span>
                                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider block">Share to Remove Watermark • reiac.org</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Verification Rules & Share Message (5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- CARD 3: VERIFICATION MODE --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 border border-purple-200/60 text-purple-700 font-black text-lg flex items-center justify-center shadow-xs">
                            🎯
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Verification &amp; Evidence Mode</h2>
                            <p class="text-[11px] text-slate-500">Determine how students qualify for clean watermark removal.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-3.5">
                    {{-- Option A: Both --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all duration-150"
                           :class="verificationMode === 'both' ? 'border-amber-500 bg-amber-50/30 ring-1 ring-amber-500 shadow-2xs' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="verification_mode" value="both" x-model="verificationMode"
                               class="text-amber-500 focus:ring-amber-500 mt-0.5">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-900">Both Methods (Recommended)</span>
                                <span class="text-[10px] font-extrabold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">Best Growth</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Students can choose 1-Click Instant Share OR upload a screenshot for admin review.</p>
                        </div>
                    </label>

                    {{-- Option B: Instant Only --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all duration-150"
                           :class="verificationMode === 'instant' ? 'border-amber-500 bg-amber-50/30 ring-1 ring-amber-500 shadow-2xs' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="verification_mode" value="instant" x-model="verificationMode"
                               class="text-amber-500 focus:ring-amber-500 mt-0.5">
                        <div class="flex-1">
                            <span class="text-xs font-black text-slate-900 block">Instant 1-Click Share Only</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Watermark unlocks automatically upon clicking WhatsApp/LinkedIn/Twitter buttons.</p>
                        </div>
                    </label>

                    {{-- Option C: Manual Review Only --}}
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all duration-150"
                           :class="verificationMode === 'manual' ? 'border-amber-500 bg-amber-50/30 ring-1 ring-amber-500 shadow-2xs' : 'border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="verification_mode" value="manual" x-model="verificationMode"
                               class="text-amber-500 focus:ring-amber-500 mt-0.5">
                        <div class="flex-1">
                            <span class="text-xs font-black text-slate-900 block">Strict Manual Review Only</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Students must upload a screenshot. Watermark remains until admin manually approves it in the queue.</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- CARD 4: SOCIAL SHARE GUIDELINES & COPY --}}
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-200/60 text-blue-700 font-black text-lg flex items-center justify-center shadow-xs">
                            📢
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Reshare Message &amp; Rules</h2>
                            <p class="text-[11px] text-slate-500">Copy presented to students in the unlock modal.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Target URL --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Target Platform Share URL
                        </label>
                        <input type="url" name="reshare_target_url"
                               value="{{ old('reshare_target_url', $setting->reshare_target_url) }}"
                               placeholder="https://community.reiac.org/resume-builder"
                               class="w-full text-xs bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    {{-- Pre-filled Content --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Pre-filled Social Share Text &amp; Hashtags
                        </label>
                        <textarea name="reshare_content" rows="3"
                                  class="w-full text-xs bg-white border border-slate-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 leading-relaxed font-medium text-slate-800">{{ old('reshare_content', $setting->reshare_content) }}</textarea>
                    </div>

                    {{-- Guidelines --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Guidelines Displayed in Unlock Modal
                        </label>
                        <textarea name="reshare_guidelines" rows="4"
                                  class="w-full text-xs bg-white border border-slate-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 leading-relaxed font-medium text-slate-800">{{ old('reshare_guidelines', $setting->reshare_guidelines) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- BOTTOM ACTION CARD --}}
            <div class="bg-gradient-to-br from-slate-900 to-reiac-navy p-6 rounded-3xl text-white shadow-md flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-reiac-gold">Need to Moderation Proofs?</h3>
                    <p class="text-[11px] text-slate-300 mt-0.5">Check pending screenshot submissions from students.</p>
                </div>

                <a href="{{ route('admin.resumes.verifications') }}"
                   class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition shrink-0">
                    Open Queue →
                </a>
            </div>

        </div>

    </form>

</div>
@endsection
