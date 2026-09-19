@extends('layouts.admin')

@section('title', 'Notification Templates')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{ tab: 'firebase' }">

    {{-- FLASH --}}
            @if($errors->any())
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
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-xs">
                    {{ session('error') }}
                </div>
            @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Notification Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage push toggles, message templates, and Firebase credentials.</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex gap-4 text-xs font-bold uppercase tracking-wider">

            <button
                @click="tab = 'firebase'"
                :class="tab === 'firebase' ? 'border-gold text-gold' : 'border-transparent text-slate-400 hover:text-slate-600'"
                class="border-b-2 pb-2 transition-colors"
                type="button"
            >
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 2a8 8 0 108 8A8 8 0 0110 2z" />
                    </svg>
                    Firebase Settings
                </span>
            </button>

            <button
                @click="tab = 'templates'"
                :class="tab === 'templates' ? 'border-gold text-gold' : 'border-transparent text-slate-400 hover:text-slate-600'"
                class="border-b-2 pb-2 transition-colors"
                type="button"
            >
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 17h.01M11 13h.01M11 9h.01M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7" />
                    </svg>
                    Message Templates
                </span>
            </button>
        </nav>
    </div>

    {{-- TAB: Firebase Settings --}}
    <div x-show="tab === 'firebase'" x-cloak>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Firebase Push Credentials</h2>

            <form action="{{ route('admin.notification-templates.firebase-update') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                @csrf

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_enabled" value="0">
                    <input type="checkbox" name="is_enabled" value="1"
                           {{ $firebase->is_enabled ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-gold focus:ring-gold">
                    <label class="text-sm font-bold text-slate-700">Enable Push Notifications</label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">FCM Project ID</label>
                    <input type="text" name="project_id" value="{{ $firebase->project_id }}"
                           placeholder="e.g. my-app-12345"
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold focus:outline-none">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Service Account JSON</label>
                    <textarea name="credentials_json" rows="4"
                              placeholder='Paste service-account JSON from Firebase Console → Project Settings → Service accounts'
                              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold focus:outline-none font-mono resize-y">{{ $firebase->credentials_json }}</textarea>
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                        Save Firebase Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TAB: Notification Templates --}}
    <div x-show="tab === 'templates'" x-cloak>

        {{-- HELP (placeholders) --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-xs mb-4">
            <strong>Available placeholders:</strong>
            <code>:user_name</code>, <code>:post_title</code>, <code>:group_name</code>, <code>:follower_name</code>,
            <code>:test_title</code>, <code>:level_name</code>, <code>:invited_by_name</code>, <code>:comment</code>,
            <code>:reply</code> — replaced automatically when the notification is sent.
        </div>

        <form action="{{ route('admin.notification-templates.bulk-update') }}" method="POST" class="space-y-4">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">Event</th>
                            <th class="py-3.5 px-4">Label</th>
                            <th class="py-3.5 px-3">Push</th>
                            <th class="py-3.5 px-3">Active</th>
                            <th class="py-3.5 px-4">Title</th>
                            <th class="py-3.5 px-4">Message Body</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($templates as $template)
                            @php
                                $setting = $settings[$template->event] ?? null;
                                $pushEnabled = $setting?->is_push_enabled ?? true;
                                $active = $setting?->is_active ?? true;
                            @endphp
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 align-top">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 font-mono">
                                        {{ $template->event }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 align-top text-slate-900 font-semibold">{{ $template->label }}</td>

                                {{-- Push toggle --}}
                                <td class="py-3 px-3 align-top">
                                    <input type="checkbox"
                                           name="push_enabled[]"
                                           value="{{ $template->event }}"
                                           {{ $pushEnabled ? 'checked' : '' }}
                                           class="w-4 h-4 rounded text-gold focus:ring-gold">
                                </td>

                                {{-- Active toggle --}}
                                <td class="py-3 px-3 align-top">
                                    <input type="checkbox"
                                           name="active[]"
                                           value="{{ $template->event }}"
                                           {{ $active ? 'checked' : '' }}
                                           class="w-4 h-4 rounded text-gold focus:ring-gold">
                                </td>

                                {{-- Title --}}
                                <td class="py-3 px-4 align-top">
                                    <input type="text"
                                           name="templates[{{ $template->event }}][title]"
                                           value="{{ $template->title }}"
                                           class="w-full text-xs px-2 py-1.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-gold focus:outline-none">
                                </td>

                                {{-- Body --}}
                                <td class="py-3 px-4 align-top">
                                    <textarea
                                           name="templates[{{ $template->event }}][body]"
                                           rows="2"
                                           class="w-full text-xs px-2 py-1.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-gold focus:outline-none resize-y">{{ $template->body }}</textarea>
                                    <input type="hidden" name="templates[{{ $template->event }}][is_active]" value="0">
                                    <input type="checkbox"
                                           name="templates[{{ $template->event }}][is_active]"
                                           value="1"
                                           {{ $template->is_active ? 'checked' : '' }}
                                           class="mt-1 w-3.5 h-3.5 rounded text-gold focus:ring-gold">
                                    <label class="ml-1 text-[10px] text-slate-500">active</label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-5 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                    Save All Templates &amp; Settings
                </button>
            </div>
        </form>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    </div>

</div>
@endsection
