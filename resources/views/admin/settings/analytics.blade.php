@extends('layouts.admin')

@section('title', 'Website & System Settings')

@section('content')
<div class="p-6 space-y-6 max-w-7xl mx-auto">

    {{-- FLASH MESSAGES --}}
    @if(isset($errors) && $errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Website &amp; System Settings</h1>
            <p class="text-xs text-slate-500 mt-1">Manage website integrations, analytics tracking, and system configurations.</p>
        </div>

        {{-- Overall Integration Status --}}
        <div class="flex items-center gap-2">
            @if($setting->is_enabled && !empty($setting->google_analytics_id))
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Google Analytics Connected
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Analytics Inactive
                </span>
            @endif
        </div>
    </div>

    {{-- ========================================================
         MAIN GRID (Google Analytics Card + Quick Guide)
    ======================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- MAIN CONFIGURATION CARD (2 Columns) --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.website-settings.update') }}" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                @csrf
                @method('PUT')

                {{-- Card Header with Google Analytics Branding --}}
                <div class="p-6 bg-gradient-to-r from-amber-50/40 via-white to-white border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        {{-- Official Google Analytics Logo Container --}}
                        <div class="w-13 h-13 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center p-2.5 shrink-0 shadow-xs">
                            <svg class="w-8 h-8" viewBox="0 0 192 192" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M152 144a20 20 0 0 0-20-20h-1a20 20 0 1 0 21 20z" fill="#E37400"/>
                                <path d="M106 144a20 20 0 0 0 20-20V60a20 20 0 1 0-40 0v64a20 20 0 0 0 20 20z" fill="#F9AB00"/>
                                <path d="M60 144a20 20 0 0 0 20-20v-20a20 20 0 1 0-40 0v20a20 20 0 0 0 20 20z" fill="#E37400"/>
                            </svg>
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-extrabold text-slate-900">Google Analytics 4 (GA4)</h2>
                                <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    Official Tag
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Track website visitors, live sessions, and user activity.</p>
                        </div>
                    </div>

                    {{-- Toggle Status Pill --}}
                    @if($setting->is_enabled && !empty($setting->google_analytics_id))
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Tracking
                        </span>
                    @else
                        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Off
                        </span>
                    @endif
                </div>

                {{-- Form Fields Body --}}
                <div class="p-6 space-y-6">

                    {{-- 1. Enable / Disable Toggle Switch --}}
                    <div class="flex items-center justify-between p-4.5 bg-slate-50/80 rounded-xl border border-slate-200/70 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 shadow-2xs">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <label for="is_enabled_toggle" class="text-sm font-bold text-slate-800 cursor-pointer">
                                    Enable Tracking on Website
                                </label>
                                <p class="text-xs text-slate-500 mt-0.5">Website ke sabhi public pages par Google Analytics code load hoga.</p>
                            </div>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                id="is_enabled_toggle"
                                name="is_enabled"
                                value="1"
                                {{ $setting->is_enabled ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-12 h-6.5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600 shadow-inner"></div>
                        </label>
                    </div>

                    {{-- 2. Measurement ID Input --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span>Measurement ID (Google Analytics Key)</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-semibold text-slate-400">
                                Format: <code class="font-mono text-amber-700 bg-amber-50 px-1 py-0.5 rounded">G-XXXXXXXXXX</code>
                            </span>
                        </div>

                        <div class="flex rounded-xl shadow-xs overflow-hidden border border-slate-300 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/20 bg-white transition">
                            <span class="inline-flex items-center gap-1.5 px-3.5 bg-slate-50 border-r border-slate-200 text-xs font-bold text-slate-500 select-none">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                KEY ID
                            </span>
                            <input
                                type="text"
                                name="google_analytics_id"
                                value="{{ old('google_analytics_id', $setting->google_analytics_id) }}"
                                placeholder="e.g. G-ABC123XYZ0"
                                class="w-full text-sm px-4 py-3 border-0 bg-transparent focus:outline-none focus:ring-0 font-mono font-bold text-slate-800 placeholder:font-sans placeholder:font-normal placeholder:text-slate-400"
                            >
                        </div>
                        <p class="text-[11.5px] text-slate-500 mt-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Google Analytics dashboard se <strong class="text-slate-700">Measurement ID</strong> (jaise <code>G-ABC123XYZ0</code>) yahan daalein.
                        </p>
                    </div>

                </div>

                {{-- Card Footer with Save Button --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500">Save karte hi website par changes live ho jayenge.</span>
                    <button
                        type="submit"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        {{-- SIDEBAR GUIDE CARD (1 Column) --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">How to get your Key</h3>
                </div>

                <ol class="space-y-3 text-xs text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[11px]">1</span>
                        <div>
                            Open <a href="https://analytics.google.com" target="_blank" class="font-bold text-amber-700 hover:underline">Google Analytics &rarr;</a> and log in.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[11px]">2</span>
                        <div>
                            Click <strong>Admin (Gear icon)</strong> at bottom left corner.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[11px]">3</span>
                        <div>
                            Go to <strong>Data Streams &rarr; Web</strong>.
                        </div>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center shrink-0 text-[11px]">4</span>
                        <div>
                            Copy the <strong>Measurement ID</strong> (e.g. <code>G-XXXXXXXXXX</code>) and paste it here.
                        </div>
                    </li>
                </ol>

                <div class="pt-3 border-t border-slate-100">
                    <a
                        href="https://analytics.google.com"
                        target="_blank"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200"
                    >
                        <span>Open Google Analytics Console</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
