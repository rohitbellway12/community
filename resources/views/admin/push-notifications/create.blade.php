@extends('layouts.admin')

@section('title', 'Compose Push Notification')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="pushNotificationComposer()">

    {{-- ALERT MESSAGES / ERRORS --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs shadow-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span class="w-5 h-5 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center font-black">!</span>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside pl-7 space-y-1 text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.push-notifications.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-wider">← Back to Notifications</a>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">New Campaign</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-0.5">Compose Push Notification</h1>
            <p class="text-xs text-slate-500 mt-0.5">Craft rich media push notifications with custom targeting and delivery scheduling.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.push-notifications.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition">
                Cancel
            </a>

            <button type="button" @click="openConfirmModal()"
                    :disabled="!isValid"
                    :class="isValid ? 'bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/35 hover:-translate-y-0.5 active:scale-95 cursor-pointer ring-1 ring-amber-400/50' : 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none border border-slate-300/60'"
                    class="px-6 py-2.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span x-text="sendType === 'now' ? 'Send Broadcast Now' : 'Schedule Notification'"></span>
            </button>
        </div>
    </div>

    {{-- MAIN FORM --}}
    <form id="pushNotificationForm" method="POST" action="{{ route('admin.push-notifications.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- CARD 1: NOTIFICATION CONTENT --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-700 font-black text-xs flex items-center justify-center">1</span>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Notification Content</h2>
                    <p class="text-[11px] text-slate-400">Specify the message title, body text, and destination screen.</p>
                </div>
            </div>

            {{-- Title --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-800">Notification Title <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-md"
                          :class="title.length > 65 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-500'">
                        <span x-text="title.length"></span>/65
                    </span>
                </div>
                <input type="text" name="title" x-model="title" maxlength="65" required
                       placeholder="e.g. New Online Test Available Today!"
                       class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 transition">
                <p class="text-[11px] text-slate-400 mt-1">Displayed as bold primary header on Android & iOS notification trays.</p>
            </div>

            {{-- Description --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-800">Notification Message Body <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-md"
                          :class="description.length > 240 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-500'">
                        <span x-text="description.length"></span>/240
                    </span>
                </div>
                <textarea name="description" x-model="description" rows="3" maxlength="240" required
                          placeholder="e.g. Check out the new test series launched for IELTS preparation. Click to attempt before 8 PM."
                          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white py-2.5 px-3.5 transition"></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Short and concise summary (maximum 240 characters).</p>
            </div>

            {{-- Simple Tap Action (Where to open in App on Click) --}}
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3" x-data="{ redirectType: 'home' }">
                <div>
                    <label class="text-xs font-bold text-slate-800 block mb-1">When user taps notification, open in app:</label>
                    <p class="text-[11px] text-slate-400 mb-2">Select which section of the mobile app to open when this notification is clicked.</p>
                    
                    <select name="redirect_type" x-model="redirectType"
                            class="w-full text-xs font-semibold bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 py-2.5 px-3 transition">
                        <option value="home">🏠 App Home Screen</option>
                        <option value="test">📝 Online Tests Screen</option>
                        <option value="group">👥 Community Groups Screen</option>
                        <option value="event">🏆 Events & Contest Screen</option>
                        <option value="web_url">🔗 External Website / Web Link</option>
                    </select>
                </div>

                {{-- If External Link is selected --}}
                <div x-show="redirectType === 'web_url'" x-cloak class="pt-2 border-t border-slate-200/80">
                    <label class="text-xs font-bold text-slate-700 block mb-1">Enter Website / Play Store URL:</label>
                    <input type="url" name="redirect_url" placeholder="https://example.com/page"
                           class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 py-2 px-3">
                </div>
            </div>

            {{-- Banner Upload (Full width) --}}
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-slate-800">Notification Banner Image (Optional)</label>
                        <p class="text-[11px] text-slate-400">Large expanded image shown on device (Ratio 2:1, Recommended 1024x512 px) • Max 2 MB</p>
                    </div>
                    <span class="text-[10px] text-slate-400 font-semibold bg-white border border-slate-200 px-2 py-0.5 rounded-md">JPG, PNG, WebP</span>
                </div>

                <input type="file" name="banner" id="bannerInput" accept="image/png,image/jpeg,image/webp" class="hidden"
                       @change="handleFileChange($event)">

                <template x-if="bannerUrl">
                    <div class="relative w-full h-44 rounded-xl overflow-hidden border border-slate-200 shadow-2xs group">
                        <img :src="bannerUrl" class="w-full h-full object-cover">
                        <button type="button" @click="removeBanner()"
                                class="absolute inset-0 bg-slate-900/60 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-lg bg-rose-600/90 hover:bg-rose-600">Remove Image</span>
                        </button>
                    </div>
                </template>

                <template x-if="!bannerUrl">
                    <button type="button" onclick="document.getElementById('bannerInput').click()"
                            class="w-full py-4 px-3 bg-white border border-dashed border-slate-300 hover:border-amber-400 rounded-xl text-xs font-bold text-slate-700 shadow-2xs flex flex-col items-center justify-center gap-1.5 transition">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Click to Upload Banner Image</span>
                        <span class="text-[10px] text-slate-400 font-normal">Supports JPEG, PNG, WebP up to 2MB</span>
                    </button>
                </template>
            </div>
        </div>

        {{-- CARD 2: AUDIENCE TARGETING (WITH DROPDOWNS & SEARCH) --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-700 font-black text-xs flex items-center justify-center">2</span>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Target Audience Selection</h2>
                    <p class="text-[11px] text-slate-400">Select audience type using the dropdown below.</p>
                </div>
            </div>

            {{-- 1. Audience Type 4 Segmented Tabs --}}
            <div>
                <label class="text-xs font-bold text-slate-800 block mb-2">Who should receive this notification? <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/60">
                    {{-- Tab 1: All Students --}}
                    <button type="button" @click="setAudienceType('all_students')"
                            :class="audienceType === 'all_students'
                                ? 'bg-white text-slate-950 font-extrabold shadow-xs border border-slate-200/80 ring-1 ring-amber-500/40'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <span>👥</span>
                        <span>All Students</span>
                    </button>

                    {{-- Tab 2: Specific Groups --}}
                    <button type="button" @click="setAudienceType('group')"
                            :class="audienceType === 'group'
                                ? 'bg-white text-slate-950 font-extrabold shadow-xs border border-slate-200/80 ring-1 ring-amber-500/40'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <span>🏢</span>
                        <span>Groups</span>
                        <span x-show="selectedGroups.length > 0" class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black" x-text="selectedGroups.length"></span>
                    </button>

                    {{-- Tab 3: Individual Students --}}
                    <button type="button" @click="setAudienceType('individual')"
                            :class="audienceType === 'individual'
                                ? 'bg-white text-slate-950 font-extrabold shadow-xs border border-slate-200/80 ring-1 ring-amber-500/40'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <span>👤</span>
                        <span>Students</span>
                        <span x-show="selectedUsers.length > 0" class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black" x-text="selectedUsers.length"></span>
                    </button>

                    {{-- Tab 4: Target Countries --}}
                    <button type="button" @click="setAudienceType('country')"
                            :class="audienceType === 'country'
                                ? 'bg-white text-slate-950 font-extrabold shadow-xs border border-slate-200/80 ring-1 ring-amber-500/40'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <span>🌍</span>
                        <span>By Country</span>
                        <span x-show="selectedCountries.length > 0" class="px-1.5 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black" x-text="selectedCountries.length"></span>
                    </button>
                </div>
                <input type="hidden" name="audience_type" :value="audienceType">
            </div>

            {{-- Target Device / Operating System Filter (Visible ONLY for All Students) --}}
            <div class="pt-1" x-show="audienceType === 'all_students'" x-cloak>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span>Target Mobile Device / Platform</span>
                        <span class="text-[10px] text-amber-700 bg-amber-50 border border-amber-200/60 font-semibold px-2 py-0.5 rounded-full">Device Filter</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">Filter recipients by mobile OS</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/60">
                    {{-- Option 1: All Devices --}}
                    <button type="button" @click="setTargetPlatform('all')"
                            :class="targetPlatform === 'all'
                                ? 'bg-white text-slate-950 font-extrabold shadow-xs border border-slate-200/80 ring-1 ring-amber-500/40'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>📱</span>
                        <span>All Devices</span>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded-full">({{ number_format($activeDeviceTokensCount) }})</span>
                    </button>

                    {{-- Option 2: Android Only --}}
                    <button type="button" @click="setTargetPlatform('android')"
                            :class="targetPlatform === 'android'
                                ? 'bg-white text-emerald-800 font-extrabold shadow-xs border border-emerald-200 ring-1 ring-emerald-500/50'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>🤖</span>
                        <span>Android (Play Store)</span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded-full">
                            {{ number_format($androidDevicesCount) }}
                        </span>
                    </button>

                    {{-- Option 3: iOS Only --}}
                    <button type="button" @click="setTargetPlatform('ios')"
                            :class="targetPlatform === 'ios'
                                ? 'bg-white text-sky-800 font-extrabold shadow-xs border border-sky-200 ring-1 ring-sky-500/50'
                                : 'text-slate-600 font-bold hover:text-slate-900'"
                            class="py-2.5 px-3 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>🍎</span>
                        <span>iOS (App Store)</span>
                        <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-100 px-1.5 py-0.5 rounded-full">
                            {{ number_format($iosDevicesCount) }}
                        </span>
                    </button>
                </div>
                <input type="hidden" name="target_platform" :value="targetPlatform">
            </div>

            {{-- All Students Notice --}}
            <div x-show="audienceType === 'all_students'" x-cloak class="p-3.5 rounded-xl border border-purple-200/80 bg-purple-50/60 text-xs flex items-center gap-2.5 text-purple-900 font-medium">
                <span class="text-base">📱</span>
                <span x-show="targetPlatform === 'all'">Broadcasting to all registered mobile app devices (<strong>{{ number_format($activeDeviceTokensCount) }} active devices</strong>). Web-only users without mobile app are skipped automatically.</span>
                <span x-show="targetPlatform === 'android'">Broadcasting strictly to <strong>Android (Google Play Store)</strong> devices (<strong>{{ number_format($androidDevicesCount) }} active devices</strong>). iOS devices are excluded.</span>
                <span x-show="targetPlatform === 'ios'">Broadcasting strictly to <strong>iOS (Apple App Store)</strong> devices (<strong>{{ number_format($iosDevicesCount) }} active devices</strong>). Android devices are excluded.</span>
            </div>

            {{-- 2. Specific Groups Searchable Multi-Select Dropdown --}}
            <div x-show="audienceType === 'group'" x-cloak class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800">Select Target Groups <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-2 text-[11px]">
                        <button type="button" @click="selectAllGroups()" class="text-amber-600 hover:text-amber-700 font-bold">Select All</button>
                        <span class="text-slate-300">•</span>
                        <button type="button" @click="clearAllGroups()" class="text-slate-500 hover:text-slate-700 font-bold">Clear All</button>
                    </div>
                </div>

                {{-- Dropdown Container --}}
                <div class="relative" @click.away="groupDropdownOpen = false">
                    {{-- Dropdown Toggle Bar --}}
                    <button type="button" @click="groupDropdownOpen = !groupDropdownOpen"
                            class="w-full text-left bg-white border border-slate-200 rounded-xl py-2 px-3.5 text-xs font-medium flex items-center justify-between shadow-2xs hover:border-slate-300 transition">
                        <span class="text-slate-700" x-text="selectedGroups.length > 0 ? selectedGroups.length + ' Groups Selected' : 'Click to choose groups...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="groupDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu with Search --}}
                    <div x-show="groupDropdownOpen" x-cloak
                         class="absolute z-30 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5 space-y-2 max-h-72 flex flex-col">
                        {{-- Search Filter --}}
                        <div class="relative">
                            <input type="text" x-model="groupSearchQuery" placeholder="Search groups..."
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        {{-- Groups List with Checkboxes --}}
                        <div class="overflow-y-auto space-y-1 divide-y divide-slate-50 flex-1 pr-1">
                            @foreach($groups as $grp)
                                <label x-show="matchesGroupSearch('{{ addslashes(strtolower($grp->name)) }}')"
                                       class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer text-xs transition">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="groups[]" value="{{ $grp->id }}"
                                               @change="toggleGroup({{ $grp->id }}, '{{ addslashes($grp->name) }}')"
                                               :checked="selectedGroups.includes({{ $grp->id }})"
                                               class="rounded text-amber-500 focus:ring-amber-500">
                                        <span class="font-bold text-slate-800">{{ $grp->name }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                        {{ $grp->active_members_count }} members
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Selected Group Chips --}}
                <div class="flex flex-wrap gap-1.5 pt-1" x-show="selectedGroupItems.length > 0">
                    <template x-for="g in selectedGroupItems" :key="g.id">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 text-slate-800 rounded-lg text-xs font-bold shadow-2xs">
                            <span x-text="g.name"></span>
                            <button type="button" @click="toggleGroup(g.id, g.name)" class="text-slate-400 hover:text-rose-600 font-bold">✕</button>
                        </span>
                    </template>
                </div>
            </div>

            {{-- 3. Individual Students Searchable Multi-Select Dropdown --}}
            <div x-show="audienceType === 'individual'" x-cloak class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800">Search & Select Individual Students <span class="text-rose-500">*</span></label>
                    <button type="button" @click="clearAllUsers()" x-show="selectedUsers.length > 0" class="text-[11px] text-slate-500 hover:text-slate-700 font-bold">
                        Clear Selected (<span x-text="selectedUsers.length"></span>)
                    </button>
                </div>

                {{-- Search Box & Live Dropdown --}}
                <div class="relative" @click.away="userDropdownOpen = false">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @focus="userDropdownOpen = true" @input.debounce.250ms="searchStudents()"
                               placeholder="Type student name or email to search..."
                               class="w-full text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 pl-9 pr-3 py-2 shadow-2xs transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    {{-- Search Results Dropdown List --}}
                    <div x-show="userDropdownOpen && (searchResults.length > 0 || isSearching)" x-cloak
                         class="absolute z-30 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-slate-100">
                        <template x-if="isSearching">
                            <div class="p-3 text-center text-xs text-slate-400">Searching students...</div>
                        </template>

                        <template x-for="item in searchResults" :key="item.id">
                            <div @click="addUser(item)" class="p-2.5 hover:bg-amber-50/80 cursor-pointer flex items-center justify-between text-xs transition">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs"
                                         x-text="item.name.charAt(0)"></div>
                                    <div>
                                        <span class="font-bold text-slate-900 block" x-text="item.name"></span>
                                        <span class="text-[11px] text-slate-400 block" x-text="item.email"></span>
                                    </div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded font-semibold"
                                      :class="item.device_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                      x-text="item.device_count > 0 ? item.device_count + ' device' : 'No device token'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Selected Student Chips --}}
                <div class="flex flex-wrap gap-1.5 pt-1" x-show="selectedUsers.length > 0">
                    <template x-for="u in selectedUsers" :key="u.id">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 text-slate-800 rounded-lg text-xs font-bold shadow-2xs">
                            <span x-text="u.name"></span>
                            <button type="button" @click="removeUser(u.id)" class="text-slate-400 hover:text-rose-600 font-bold">✕</button>
                            <input type="hidden" name="users[]" :value="u.id">
                        </span>
                    </template>
                </div>
            </div>

            {{-- 4. Target Countries Searchable Multi-Select Dropdown --}}
            <div x-show="audienceType === 'country'" x-cloak class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800">Select Target Countries <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-2 text-[11px]">
                        <button type="button" @click="selectAllCountries()" class="text-amber-600 hover:text-amber-700 font-bold">Select All</button>
                        <span class="text-slate-300">•</span>
                        <button type="button" @click="clearAllCountries()" class="text-slate-500 hover:text-slate-700 font-bold">Clear All</button>
                    </div>
                </div>

                {{-- Dropdown Container --}}
                <div class="relative" @click.away="countryDropdownOpen = false">
                    {{-- Dropdown Toggle Bar --}}
                    <button type="button" @click="countryDropdownOpen = !countryDropdownOpen"
                            class="w-full text-left bg-white border border-slate-200 rounded-xl py-2 px-3.5 text-xs font-medium flex items-center justify-between shadow-2xs hover:border-slate-300 transition">
                        <span class="text-slate-700" x-text="selectedCountries.length > 0 ? selectedCountries.length + ' Countries Selected' : 'Click to choose countries...'"></span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="countryDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Menu with Search --}}
                    <div x-show="countryDropdownOpen" x-cloak
                         class="absolute z-30 left-0 right-0 mt-1.5 bg-white border border-slate-200 rounded-xl shadow-xl p-2.5 space-y-2 max-h-72 flex flex-col">
                        {{-- Search Filter --}}
                        <div class="relative">
                            <input type="text" x-model="countrySearchQuery" placeholder="Search countries..."
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        {{-- Countries List with Checkboxes --}}
                        <div class="overflow-y-auto space-y-1 divide-y divide-slate-50 flex-1 pr-1">
                            @foreach($countries as $country)
                                <label x-show="matchesCountrySearch('{{ addslashes(strtolower($country->name)) }}', '{{ strtolower($country->code ?? '') }}')"
                                       class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer text-xs transition">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" name="countries[]" value="{{ $country->id }}"
                                               @change="toggleCountry({{ $country->id }}, '{{ addslashes($country->name) }}', '{{ $country->flag ?? '🌍' }}')"
                                               :checked="selectedCountries.includes({{ $country->id }})"
                                               class="rounded text-amber-500 focus:ring-amber-500">
                                        <span class="text-sm">{{ $country->flag ?? '🌍' }}</span>
                                        <span class="font-bold text-slate-800">{{ $country->name }}</span>
                                        @if($country->code)
                                            <span class="text-[10px] text-slate-400 font-semibold uppercase">({{ $country->code }})</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                        {{ $country->students_count ?? 0 }} active students
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Selected Country Chips --}}
                <div class="flex flex-wrap gap-1.5 pt-1" x-show="selectedCountryItems.length > 0">
                    <template x-for="c in selectedCountryItems" :key="c.id">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 text-slate-800 rounded-lg text-xs font-bold shadow-2xs">
                            <span x-text="c.flag || '🌍'"></span>
                            <span x-text="c.name"></span>
                            <button type="button" @click="toggleCountry(c.id, c.name, c.flag)" class="text-slate-400 hover:text-rose-600 font-bold">✕</button>
                        </span>
                    </template>
                </div>
            </div>

            {{-- Recipient Estimator Banner --}}
            <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200/80 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold">👥</span>
                    <div>
                        <span class="font-extrabold text-slate-900 block" x-text="recipientMessage">Estimating recipients...</span>
                        <span class="text-[11px] text-slate-500 block">Deduplicated active accounts with registered FCM tokens.</span>
                    </div>
                </div>
                <button type="button" @click="refreshRecipientCount()" class="text-xs font-bold text-amber-700 hover:text-amber-900">
                    Refresh Count
                </button>
            </div>
        </div>

        {{-- CARD 3: DELIVERY SCHEDULE --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <span class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-700 font-black text-xs flex items-center justify-center">3</span>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Delivery Timing</h2>
                    <p class="text-[11px] text-slate-400">Choose whether to send right now or schedule for a specific date and time.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                       :class="sendType === 'now' ? 'border-amber-500 bg-amber-50/30 ring-1 ring-amber-500' : 'border-slate-200 hover:bg-slate-50'">
                    <input type="radio" name="send_type" value="now" x-model="sendType" class="text-amber-500 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Send Immediately (Now)</span>
                        <span class="text-[11px] text-slate-500 block">Deliver to all device tokens via background queue</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                       :class="sendType === 'schedule' ? 'border-amber-500 bg-amber-50/30 ring-1 ring-amber-500' : 'border-slate-200 hover:bg-slate-50'">
                    <input type="radio" name="send_type" value="schedule" x-model="sendType" class="text-amber-500 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Schedule for Later</span>
                        <span class="text-[11px] text-slate-500 block">Broadcast automatically at a future date and time</span>
                    </div>
                </label>
            </div>

            <div x-show="sendType === 'schedule'" x-cloak class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-2.5">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 block">Select Date and Time (KST) <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-lg border border-purple-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                        <span>Current KST: <strong x-text="currentKstTime"></strong></span>
                    </span>
                </div>
                <input type="datetime-local" name="scheduled_at" x-model="scheduledAt" :min="minDateTime"
                       class="text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 py-2.5 px-3.5 shadow-2xs font-bold text-slate-900">
                <p class="text-[11px] text-slate-500">The entire system operates in <strong>KST (Korea Standard Time, UTC+9)</strong>. The notification will broadcast automatically at this scheduled KST time.</p>
            </div>
        </div>

        {{-- BOTTOM SUBMIT ACTION --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.push-notifications.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition">
                Discard
            </a>

            <button type="button" @click="openConfirmModal()"
                    :disabled="!isValid"
                    :class="isValid ? 'bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-lg shadow-amber-500/30 hover:shadow-xl hover:shadow-amber-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer ring-1 ring-amber-400/50' : 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none border border-slate-300/60'"
                    class="px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider transition-all duration-200 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span x-text="sendType === 'now' ? 'Send Broadcast Now' : 'Schedule Notification'"></span>
            </button>
        </div>
    </form>

    {{-- CONFIRMATION MODAL --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4 transition-all">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 transform transition-all" @click.away="showConfirmModal = false">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500/20 to-amber-500/5 text-amber-600 ring-8 ring-amber-500/10 flex items-center justify-center mx-auto mb-3.5">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </div>

            <h3 class="text-lg font-black text-slate-900 text-center tracking-tight">Confirm Notification Broadcast</h3>
            <p class="text-xs text-slate-500 text-center mt-1">Please review the campaign details below before broadcasting.</p>

            <div class="my-5 p-4 bg-slate-50/90 rounded-2xl border border-slate-200/80 space-y-3 text-xs">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60">
                    <span class="text-slate-500 font-bold flex items-center gap-1.5">
                        <span class="text-slate-400">🎯</span> Target Audience
                    </span>
                    <span class="font-extrabold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-2xs" x-text="audienceLabel"></span>
                </div>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60" x-show="audienceType === 'all_students'">
                    <span class="text-slate-500 font-bold flex items-center gap-1.5">
                        <span class="text-slate-400">📱</span> Target Device
                    </span>
                    <span class="font-extrabold text-slate-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-2xs" x-text="platformLabel"></span>
                </div>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60">
                    <span class="text-slate-500 font-bold flex items-center gap-1.5">
                        <span class="text-slate-400">👥</span> Recipients
                    </span>
                    <span class="font-extrabold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/70" x-text="recipientCountText"></span>
                </div>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/60">
                    <span class="text-slate-500 font-bold flex items-center gap-1.5">
                        <span class="text-slate-400">⏰</span> Delivery Mode
                    </span>
                    <span class="font-bold text-slate-800" x-text="sendType === 'now' ? 'Immediate Broadcast' : 'Scheduled (' + (scheduledAt || 'N/A') + ' KST)'"></span>
                </div>
                <div class="pt-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Title Preview</span>
                    <p class="font-extrabold text-slate-900 text-xs bg-white p-2.5 rounded-xl border border-slate-200 line-clamp-2" x-text="title"></p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <button type="button" @click="showConfirmModal = false"
                        class="w-1/2 py-3 px-4 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200/80 text-slate-700 font-extrabold text-xs transition duration-150 flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                    Cancel & Edit
                </button>
                <button type="button" @click="submitBroadcastForm()"
                        :disabled="isSubmitting"
                        class="w-1/2 py-3 px-4 rounded-xl font-black text-xs uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/25 hover:shadow-lg hover:shadow-amber-500/35 active:scale-98 transition duration-150 flex items-center justify-center gap-2 ring-1 ring-amber-400/60 cursor-pointer">
                    <span x-show="!isSubmitting" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="sendType === 'now' ? 'Yes, Send Now' : 'Yes, Schedule'"></span>
                    </span>
                    <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Broadcasting...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function pushNotificationComposer() {
    return {
        title: '',
        description: '',
        bannerUrl: null,
        audienceType: 'all_students',
        targetPlatform: 'all',
        groupDropdownOpen: false,
        groupSearchQuery: '',
        selectedGroups: [],
        selectedGroupItems: [],
        countryDropdownOpen: false,
        countrySearchQuery: '',
        selectedCountries: [],
        selectedCountryItems: [],
        userDropdownOpen: false,
        searchQuery: '',
        searchResults: [],
        selectedUsers: [],
        isSearching: false,
        sendType: 'now',
        scheduledAt: '',
        currentKstTime: '',
        minDateTime: '',
        showConfirmModal: false,
        isSubmitting: false,
        recipientMessage: 'Estimating active recipients...',
        recipientCountText: 'Calculating...',

        allGroups: @json($groups),
        allCountries: @json($countries),

        setTargetPlatform(platform) {
            this.targetPlatform = platform;
            this.refreshRecipientCount();
        },

        get platformLabel() {
            if (this.targetPlatform === 'android') return '🤖 Android (Play Store) Only';
            if (this.targetPlatform === 'ios') return '🍎 iOS (App Store) Only';
            return '📱 All Devices (Android & iOS)';
        },

        get isAudienceValid() {
            if (this.audienceType === 'all_students') return true;
            if (this.audienceType === 'country') return this.selectedCountries.length > 0;
            if (this.audienceType === 'group') return this.selectedGroups.length > 0;
            if (this.audienceType === 'individual') return this.selectedUsers.length > 0;
            return false;
        },

        get isValid() {
            const hasTitle = this.title.trim().length > 0 && this.title.length <= 65;
            const hasDesc = this.description.trim().length > 0 && this.description.length <= 240;
            const hasAudience = this.isAudienceValid;
            const hasSchedule = (this.sendType === 'now') || (this.sendType === 'schedule' && this.scheduledAt);
            return hasTitle && hasDesc && hasAudience && hasSchedule;
        },

        get audienceLabel() {
            if (this.audienceType === 'all_students') return 'All Active Students';
            if (this.audienceType === 'country') return this.selectedCountries.length + ' Countries Selected';
            if (this.audienceType === 'group') return this.selectedGroups.length + ' Groups Selected';
            if (this.audienceType === 'individual') return this.selectedUsers.length + ' Students Selected';
            return 'None';
        },

        getKstDateTime(d = new Date()) {
            return d.toLocaleString('sv-SE', { timeZone: 'Asia/Seoul' }).replace(' ', 'T').slice(0, 16);
        },

        init() {
            this.refreshRecipientCount();

            // Set min datetime to current KST time
            const now = new Date();
            this.minDateTime = this.getKstDateTime(now);
            this.currentKstTime = now.toLocaleTimeString('en-US', { timeZone: 'Asia/Seoul', hour: '2-digit', minute: '2-digit' });

            // If empty, pre-fill scheduledAt with current KST + 30 minutes
            if (!this.scheduledAt) {
                const inThirtyMins = new Date(Date.now() + 30 * 60 * 1000);
                this.scheduledAt = this.getKstDateTime(inThirtyMins);
            }
        },

        setAudienceType(type) {
            this.audienceType = type;
            if (type !== 'all_students') {
                this.targetPlatform = 'all';
            }
            this.groupDropdownOpen = false;
            this.userDropdownOpen = false;
            this.countryDropdownOpen = false;
            this.refreshRecipientCount();
        },

        onAudienceTypeChange() {
            this.groupDropdownOpen = false;
            this.userDropdownOpen = false;
            this.countryDropdownOpen = false;
            this.refreshRecipientCount();
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.bannerUrl = URL.createObjectURL(file);
        },

        removeBanner() {
            this.bannerUrl = null;
            const input = document.getElementById('bannerInput');
            if (input) input.value = '';
        },

        // Group Handlers
        matchesGroupSearch(name) {
            if (!this.groupSearchQuery.trim()) return true;
            return name.includes(this.groupSearchQuery.toLowerCase().trim());
        },

        toggleGroup(groupId, groupName) {
            if (this.selectedGroups.includes(groupId)) {
                this.selectedGroups = this.selectedGroups.filter(id => id !== groupId);
                this.selectedGroupItems = this.selectedGroupItems.filter(g => g.id !== groupId);
            } else {
                this.selectedGroups.push(groupId);
                this.selectedGroupItems.push({ id: groupId, name: groupName });
            }
            this.refreshRecipientCount();
        },

        selectAllGroups() {
            this.selectedGroups = this.allGroups.map(g => g.id);
            this.selectedGroupItems = this.allGroups.map(g => ({ id: g.id, name: g.name }));
            this.refreshRecipientCount();
        },

        clearAllGroups() {
            this.selectedGroups = [];
            this.selectedGroupItems = [];
            this.refreshRecipientCount();
        },

        // Country Handlers
        matchesCountrySearch(name, code) {
            if (!this.countrySearchQuery.trim()) return true;
            const q = this.countrySearchQuery.toLowerCase().trim();
            return name.includes(q) || (code && code.includes(q));
        },

        toggleCountry(countryId, countryName, countryFlag) {
            if (this.selectedCountries.includes(countryId)) {
                this.selectedCountries = this.selectedCountries.filter(id => id !== countryId);
                this.selectedCountryItems = this.selectedCountryItems.filter(c => c.id !== countryId);
            } else {
                this.selectedCountries.push(countryId);
                this.selectedCountryItems.push({ id: countryId, name: countryName, flag: countryFlag });
            }
            this.refreshRecipientCount();
        },

        selectAllCountries() {
            this.selectedCountries = this.allCountries.map(c => c.id);
            this.selectedCountryItems = this.allCountries.map(c => ({ id: c.id, name: c.name, flag: c.flag }));
            this.refreshRecipientCount();
        },

        clearAllCountries() {
            this.selectedCountries = [];
            this.selectedCountryItems = [];
            this.refreshRecipientCount();
        },

        // Student Handlers
        addUser(user) {
            if (!this.selectedUsers.some(u => u.id === user.id)) {
                this.selectedUsers.push(user);
            }
            this.searchResults = [];
            this.searchQuery = '';
            this.userDropdownOpen = false;
            this.refreshRecipientCount();
        },

        removeUser(userId) {
            this.selectedUsers = this.selectedUsers.filter(u => u.id !== userId);
            this.refreshRecipientCount();
        },

        clearAllUsers() {
            this.selectedUsers = [];
            this.refreshRecipientCount();
        },

        searchStudents() {
            if (this.searchQuery.trim().length < 2) {
                this.searchResults = [];
                this.isSearching = false;
                return;
            }

            this.isSearching = true;
            fetch(`{{ route('admin.push-notifications.search-users') }}?q=${encodeURIComponent(this.searchQuery)}`)
                .then(r => r.json())
                .then(data => {
                    this.searchResults = data.items || [];
                    this.isSearching = false;
                })
                .catch(() => {
                    this.searchResults = [];
                    this.isSearching = false;
                });
        },

        refreshRecipientCount() {
            this.recipientMessage = 'Estimating recipients...';

            const params = new URLSearchParams();
            params.append('audience_type', this.audienceType);
            params.append('target_platform', this.targetPlatform);

            if (this.audienceType === 'country') {
                this.selectedCountries.forEach(id => params.append('countries[]', id));
            } else if (this.audienceType === 'group') {
                this.selectedGroups.forEach(id => params.append('groups[]', id));
            } else if (this.audienceType === 'individual') {
                this.selectedUsers.forEach(u => params.append('users[]', u.id));
            }

            fetch(`{{ route('admin.push-notifications.recipient-count') }}?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.recipientMessage = data.message;
                        this.recipientCountText = `${data.user_count} Students (${data.token_count} Devices)`;
                    } else {
                        this.recipientMessage = 'Could not calculate recipients.';
                    }
                })
                .catch(() => {
                    this.recipientMessage = 'Recipient estimation ready.';
                });
        },

        openConfirmModal() {
            if (!this.isValid) return;
            this.showConfirmModal = true;
        },

        submitBroadcastForm() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            document.getElementById('pushNotificationForm').submit();
        }
    }
}
</script>
@endsection
