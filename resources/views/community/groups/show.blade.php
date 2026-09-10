<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $group->name }} | Community</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">
  
  <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        x-show="mobileMenuOpen"
        class="fixed inset-0 z-50 lg:hidden flex"
        style="display: none;"
    >
        <div
            @click="mobileMenuOpen = false"
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto"
        >
            @auth
                @php
                    $mobileProfile = $user?->profile;
                    $mobileAvatar = $mobileProfile?->avatar
                        ? asset('storage/' . $mobileProfile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';
                @endphp

                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img
                        src="{{ $mobileAvatar }}"
                        alt="{{ $user?->name }}"
                        class="w-10 h-10 rounded-full object-cover"
                    >

                    <div>
                        <div class="font-bold text-slate-900 text-sm">
                            {{ $user?->name }}
                        </div>

                        <a
                            href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}"
                            class="text-xs text-amber-600 font-semibold hover:underline"
                        >
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a
                    href="{{ route('community.index') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50"
                >
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Community Home
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        <x-community.sidebar
            :top-contributors="$topContributors ?? collect()"
            :categories="$categories"
            :tags="$tags"
        />

        {{-- CENTER FEED --}}
        <section class="space-y-5 min-w-0" x-data="{ activeTab: '{{ request('tab', 'posts') }}' }">

            {{-- GROUP HEADER CARD --}}
            <div class="bg-white p-5 lg:p-6 rounded-2xl shadow-sm border border-slate-200/70 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-md">
                                Group
                            </span>
                            <span class="text-xs font-medium px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md capitalize">
                                {{ $group->visibility ?? 'Public' }} Group
                            </span>
                            <span class="text-xs text-slate-500 font-semibold cursor-pointer hover:text-amber-600 transition" @click="activeTab = 'members'">
                                {{ $members->count() }} Active {{ Str::plural('Member', $members->count()) }}
                            </span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight mt-1.5">
                            {{ $group->name }}
                        </h1>
                    </div>

                    {{-- MEMBERSHIP STATUS / ACTIONS --}}
                    <div class="shrink-0">
                        @if($user && (int) $group->owner_id === (int) $user->id)
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-800 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-amber-200/80">
                                👑 You are the Owner
                            </span>
                        @elseif($isMember)
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-emerald-200/60">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Active Member
                                </span>
                                <form method="POST" action="{{ route('community.groups.leave', $group) }}" onsubmit="return confirm('Are you sure you want to leave this group?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-rose-200 transition font-medium">
                                        Leave
                                    </button>
                                </form>
                            </div>
                        @elseif($hasPendingInvitation ?? false)
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1.5 bg-amber-50 text-amber-800 rounded-xl text-xs font-semibold inline-flex items-center gap-1.5 border border-amber-200">
                                    ✉️ You are invited
                                </span>
                                <form method="POST" action="{{ route('community.groups.invitation.accept', $group) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                        Accept
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('community.groups.invitation.reject', $group) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        @elseif($membership && ($membership->status ?? '') === 'pending')
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-amber-200/60">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Membership Pending Approval
                            </span>
                        @else
                            @auth
                                <form method="POST" action="{{ route('community.groups.join', $group) }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Join Group
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    Join Group
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>

                <p class="text-xs lg:text-sm text-slate-600 leading-relaxed">
                    {{ $group->description ?? 'No description provided for this group yet.' }}
                </p>

                {{-- CREATOR / OWNER SECTION & AVATAR PREVIEW --}}
                <div class="pt-3.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    @if($owner)
                        @php
                            $ownerProfile = $owner->profile;
                            $ownerAvatar = $ownerProfile?->avatar
                                ? asset('storage/' . $ownerProfile->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($owner->name) . '&background=0c1b33&color=fff';
                            $ownerUsername = $ownerProfile?->username ?? $owner->id;
                        @endphp
                        <div class="flex items-center gap-3">
                            <a href="{{ route('community.profile', $ownerUsername) }}" class="flex items-center gap-2.5 group">
                                <div class="relative">
                                    <img src="{{ $ownerAvatar }}" alt="{{ $owner->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-amber-400">
                                    @if($ownerProfile?->country?->iso_code)
                                        <img
                                            src="https://flagcdn.com/20x15/{{ strtolower($ownerProfile->country->iso_code) }}.png"
                                            class="absolute -bottom-0.5 -right-0.5 w-3.5 h-2.5 object-cover rounded-xs border border-white"
                                            alt="{{ $ownerProfile->country->name ?? 'Country' }}"
                                            title="{{ $ownerProfile->country->name ?? 'Country' }}"
                                        >
                                    @endif
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 group-hover:text-amber-600 flex items-center gap-1.5 transition">
                                        <span>{{ $owner->name }}</span>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-md">
                                            👑 Owner
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium">
                                        {{ '@' . ($ownerProfile?->username ?? 'user') }} • Created {{ $group->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif

                    {{-- Quick Member Avatar Stack --}}
                    <div
                        class="flex items-center gap-2 cursor-pointer bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200/60 transition self-start sm:self-auto"
                        @click="activeTab = 'members'"
                        title="Click to view all members"
                    >
                        <div class="flex -space-x-2 overflow-hidden">
                            @foreach($members->take(5) as $m)
                                @php
                                    $mAv = $m->profile?->avatar
                                        ? asset('storage/' . $m->profile->avatar)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($m->name) . '&background=0c1b33&color=fff';
                                @endphp
                                <img
                                    class="inline-block h-6 w-6 rounded-full ring-2 ring-white object-cover"
                                    src="{{ $mAv }}"
                                    alt="{{ $m->name }}"
                                    title="{{ $m->name }}"
                                >
                            @endforeach
                        </div>
                        <span class="text-xs font-bold text-slate-700 hover:text-amber-600 transition">
                            {{ $members->count() }} {{ Str::plural('Member', $members->count()) }}
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- NAVIGATION TABS --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-1.5 flex items-center gap-1.5">
                <button
                    type="button"
                    @click="activeTab = 'posts'"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
                    :class="activeTab === 'posts' ? 'bg-[#0b1329] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span>Discussions</span>
                    @if($isMember)
                        <span
                            class="px-1.5 py-0.5 rounded-full text-[10px]"
                            :class="activeTab === 'posts' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ method_exists($posts, 'total') ? $posts->total() : $posts->count() }}
                        </span>
                    @endif
                </button>

                <button
                    type="button"
                    @click="activeTab = 'members'"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
                    :class="activeTab === 'members' ? 'bg-[#0b1329] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Members</span>
                    <span
                        class="px-1.5 py-0.5 rounded-full text-[10px]"
                        :class="activeTab === 'members' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                    >
                        {{ $members->count() }}
                    </span>
                </button>

                @if($user && (int) $group->owner_id === (int) $user->id && $pendingMembers->isNotEmpty())
                    <button
                        type="button"
                        @click="activeTab = 'pending'"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
                        :class="activeTab === 'pending' ? 'bg-[#0b1329] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Requests</span>
                        <span
                            class="px-1.5 py-0.5 rounded-full text-[10px]"
                            :class="activeTab === 'pending' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ $pendingMembers->count() }}
                        </span>
                    </button>
                @endif

                @if($user && (int) $group->owner_id === (int) $user->id && isset($invitedMembers) && $invitedMembers->isNotEmpty())
                    <button
                        type="button"
                        @click="activeTab = 'invited'"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer"
                        :class="activeTab === 'invited' ? 'bg-[#0b1329] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Invited</span>
                        <span
                            class="px-1.5 py-0.5 rounded-full text-[10px]"
                            :class="activeTab === 'invited' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600'"
                        >
                            {{ $invitedMembers->count() }}
                        </span>
                    </button>
                @endif
            </div>

            {{-- TAB CONTENT: MEMBERS LIST --}}
            <div x-show="activeTab === 'members'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">
                            Group Members ({{ $members->count() }})
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            All members who have joined '{{ $group->name }}'
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">
                        {{ $members->count() }} Total
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @forelse($members as $member)
                        @php
                            $mProfile = $member->profile;
                            $mAvatar = $mProfile?->avatar
                                ? asset('storage/' . $mProfile->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=0c1b33&color=fff';
                            $mUsername = $mProfile?->username ?? $member->id;
                            $isGroupOwner = (int) $group->owner_id === (int) $member->id || $member->pivot->role === 'owner';
                        @endphp

                        <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $isGroupOwner ? 'border-amber-200/80 bg-amber-50/20' : 'border-slate-100 bg-white' }} hover:border-slate-200 hover:shadow-2xs transition gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="relative shrink-0">
                                    <img
                                        src="{{ $mAvatar }}"
                                        alt="{{ $member->name }}"
                                        class="w-11 h-11 rounded-full object-cover ring-2 {{ $isGroupOwner ? 'ring-amber-400' : 'ring-slate-100' }}"
                                    >
                                    @if($mProfile?->country?->iso_code)
                                        <img
                                            src="https://flagcdn.com/20x15/{{ strtolower($mProfile->country->iso_code) }}.png"
                                            class="absolute -bottom-0.5 -right-0.5 w-4 h-3 object-cover rounded-xs border border-white shadow-2xs"
                                            alt="{{ $mProfile->country->name ?? 'Country' }}"
                                            title="{{ $mProfile->country->name ?? 'Country' }}"
                                        >
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a
                                            href="{{ route('community.profile', $mUsername) }}"
                                            class="font-bold text-xs text-slate-900 hover:text-amber-600 hover:underline truncate"
                                        >
                                            {{ $member->name }}
                                        </a>
                                    </div>

                                    <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <span class="truncate max-w-[120px]">{{ '@' . ($mProfile?->username ?? 'user') }}</span>
                                        <span>•</span>
                                        <span class="shrink-0">{{ $member->pivot->created_at ? \Carbon\Carbon::parse($member->pivot->created_at)->diffForHumans() : 'Active' }}</span>
                                    </div>

                                    <div class="mt-1">
                                        @if($isGroupOwner)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-md">
                                                👑 Owner
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-semibold rounded-md">
                                                Member
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <a
                                href="{{ route('community.profile', $mUsername) }}"
                                class="shrink-0 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-amber-600 hover:bg-slate-50 rounded-lg border border-slate-200 transition"
                            >
                                Profile
                            </a>
                        </div>
                    @empty
                        <div class="col-span-2 py-8 text-center text-xs text-slate-400">
                            No active members found.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB CONTENT: PENDING REQUESTS (OWNER ONLY) --}}
            @if($user && (int) $group->owner_id === (int) $user->id)
                <div x-show="activeTab === 'pending'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Pending Join Requests ({{ $pendingMembers->count() }})
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Users waiting for your approval to join this group
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-amber-50 text-amber-800 rounded-xl text-xs font-bold">
                            {{ $pendingMembers->count() }} Pending
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($pendingMembers as $pendingUser)
                            @php
                                $pProfile = $pendingUser->profile;
                                $pAvatar = $pProfile?->avatar
                                    ? asset('storage/' . $pProfile->avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($pendingUser->name) . '&background=0c1b33&color=fff';
                                $pUsername = $pProfile?->username ?? $pendingUser->id;
                            @endphp

                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-amber-200/60 bg-amber-50/20 gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0">
                                        <img
                                            src="{{ $pAvatar }}"
                                            alt="{{ $pendingUser->name }}"
                                            class="w-11 h-11 rounded-full object-cover ring-2 ring-amber-300"
                                        >
                                        @if($pProfile?->country?->iso_code)
                                            <img
                                                src="https://flagcdn.com/20x15/{{ strtolower($pProfile->country->iso_code) }}.png"
                                                class="absolute -bottom-0.5 -right-0.5 w-4 h-3 object-cover rounded-xs border border-white"
                                                alt="{{ $pProfile->country->name ?? 'Country' }}"
                                            >
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <a
                                            href="{{ route('community.profile', $pUsername) }}"
                                            class="font-bold text-xs text-slate-900 hover:text-amber-600 hover:underline truncate block"
                                        >
                                            {{ $pendingUser->name }}
                                        </a>

                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ '@' . ($pProfile?->username ?? 'user') }} • Requested {{ $pendingUser->pivot->created_at ? \Carbon\Carbon::parse($pendingUser->pivot->created_at)->diffForHumans() : 'Recently' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <form method="POST" action="{{ route('community.groups.requests.accept', [$group, $pendingUser]) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-xs"
                                        >
                                            Accept
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('community.groups.requests.reject', [$group, $pendingUser]) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow-xs"
                                        >
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-xs text-slate-400">
                                No pending join requests for this group.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- TAB CONTENT: INVITED USERS (OWNER ONLY) --}}
            @if($user && (int) $group->owner_id === (int) $user->id && isset($invitedMembers) && $invitedMembers->isNotEmpty())
                <div x-show="activeTab === 'invited'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-200/70 p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">
                                Invited Members ({{ $invitedMembers->count() }})
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Users you invited who haven't accepted yet
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-sky-50 text-sky-800 rounded-xl text-xs font-bold">
                            {{ $invitedMembers->count() }} Invited
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($invitedMembers as $invitedUser)
                            @php
                                $iProfile = $invitedUser->profile;
                                $iAvatar = $iProfile?->avatar
                                    ? asset('storage/' . $iProfile->avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($invitedUser->name) . '&background=0c1b33&color=fff';
                                $iUsername = $iProfile?->username ?? $invitedUser->id;
                            @endphp

                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-sky-200/60 bg-sky-50/20 gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0">
                                        <img
                                            src="{{ $iAvatar }}"
                                            alt="{{ $invitedUser->name }}"
                                            class="w-11 h-11 rounded-full object-cover ring-2 ring-sky-300"
                                        >
                                        @if($iProfile?->country?->iso_code)
                                            <img
                                                src="https://flagcdn.com/20x15/{{ strtolower($iProfile->country->iso_code) }}.png"
                                                class="absolute -bottom-0.5 -right-0.5 w-4 h-3 object-cover rounded-xs border border-white"
                                                alt="{{ $iProfile->country->name ?? 'Country' }}"
                                            >
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <a
                                            href="{{ route('community.profile', $iUsername) }}"
                                            class="font-bold text-xs text-slate-900 hover:text-amber-600 hover:underline truncate block"
                                        >
                                            {{ $invitedUser->name }}
                                        </a>

                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ '@' . ($iProfile?->username ?? 'user') }} • Invited {{ $invitedUser->pivot->created_at ? \Carbon\Carbon::parse($invitedUser->pivot->created_at)->diffForHumans() : 'Recently' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold border border-amber-200/80">
                                        Waiting for User to Accept
                                    </span>

                                    <form method="POST" action="{{ route('community.groups.requests.reject', [$group, $invitedUser]) }}" onsubmit="return confirm('Cancel this invitation?');">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="px-2.5 py-1 text-xs text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 transition font-medium cursor-pointer"
                                        >
                                            Cancel Invite
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- TAB CONTENT: DISCUSSIONS / POSTS --}}
            <div x-show="activeTab === 'posts'" x-cloak class="space-y-5">
            @if($isMember)
                @forelse($posts as $post)
                    @php
                        $author = $post->user;
                        $profile = $author?->profile;

                        $authorAvatar = $profile?->avatar
                            ? asset('storage/' . $profile->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($author?->name ?? 'User') . '&background=0c1b33&color=fff';

                        $authorUsername = $profile?->username;

                        $authorUrl = $authorUsername
                            ? route('community.profile', $authorUsername)
                            : route('community.profile', $author?->id);
                    @endphp

                    <div
                        class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-3.5"
                        x-data="{
                            likesCount: {{ $post->likes_count ?? $post->likes->count() }},
                            liked: {{ auth()->check() && $post->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                            copied: false,
                            commentsCount: {{ $post->comments()->whereNull('parent_id')->count() }},
                            showAllComments: false,
                            expandedContent: false,
                            expandedTags: false,
                            newCommentText: '',
                            comments: {{ Js::from($post->comments->whereNull('parent_id')->take(4)->map(fn($c) => [
                                'id' => $c->id,
                                'content' => $c->content,
                                'created_at_human' => $c->created_at->diffForHumans(),
                                'showReplies' => true,
                                'user' => [
                                    'name' => $c->user?->name ?? 'User',
                                    'avatar' => $c->user?->profile?->avatar
                                        ? asset('storage/' . $c->user->profile->avatar)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($c->user?->name ?? 'User') . '&background=0c1b33&color=fff'
                                ],
                                'replies' => $c->replies->map(fn($r) => [
                                    'id' => $r->id,
                                    'content' => $r->content,
                                    'created_at_human' => $r->created_at->diffForHumans(),
                                    'user' => [
                                        'name' => $r->user?->name ?? 'User',
                                        'avatar' => $r->user?->profile?->avatar
                                            ? asset('storage/' . $r->user->profile->avatar)
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($r->user?->name ?? 'User') . '&background=0c1b33&color=fff'
                                    ]
                                ])->values()
                            ])->values()) }},
                            hasMoreComments: {{ $post->comments()->whereNull('parent_id')->count() > 4 ? 'true' : 'false' }},
                            loadingMore: false,

                            toggleLike() {
                                @guest
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                @endguest

                                fetch('{{ route('community.posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(async res => {
                                    if (res.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return;
                                    }
                                    return res.json();
                                })
                                .then(data => {
                                    if (data && data.success) {
                                        this.liked = data.liked;
                                        this.likesCount = data.likes_count;
                                    }
                                });
                            },

                            sharePost() {
                                navigator.clipboard.writeText('{{ route('community.posts.show', $post) }}')
                                    .then(() => {
                                        this.copied = true;
                                        setTimeout(() => { this.copied = false; }, 2000);
                                    });
                            },

                            submitComment() {
                                @guest
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                @endguest

                                if (!this.newCommentText.trim()) return;

                                fetch('{{ route('community.posts.comment.store', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ content: this.newCommentText })
                                })
                                .then(async res => {
                                    if (res.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return;
                                    }
                                    return res.json();
                                })
                                .then(data => {
                                    if (data && data.success) {
                                        this.comments.unshift(data.comment);
                                        this.commentsCount++;
                                        this.newCommentText = '';
                                    }
                                });
                            },

                            loadMoreComments() {
                                if (this.loadingMore || !this.hasMoreComments) return;
                                this.loadingMore = true;
                                fetch(`{{ route('community.posts.comments', $post) }}?skip=` + this.comments.length, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data && data.success && Array.isArray(data.comments)) {
                                            const newComments = data.comments.map(c => ({
                                                ...c,
                                                showReplies: true
                                            }));
                                            this.comments = this.comments.concat(newComments);
                                            this.hasMoreComments = Boolean(data.has_more);
                                        } else {
                                            this.hasMoreComments = false;
                                        }
                                        this.loadingMore = false;
                                    })
                                    .catch(() => {
                                        this.loadingMore = false;
                                    });
                            }
                        }"
                    >
                        {{-- POST HEADER --}}
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <img
                                    src="{{ $authorAvatar }}"
                                    alt="{{ $author?->name ?? 'User' }}"
                                    class="w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-slate-100"
                                >

                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 text-xs truncate flex items-center gap-1.5">
                                        @if($author)
                                            <a href="{{ $authorUrl }}" class="hover:underline">
                                                {{ $author->name }}
                                            </a>

                                            @if($profile?->country && $profile->country->iso_code)
                                                <img 
                                                    src="https://flagcdn.com/20x15/{{ strtolower($profile->country->iso_code) }}.png" 
                                                    srcset="https://flagcdn.com/40x30/{{ strtolower($profile->country->iso_code) }}.png 2x"
                                                    width="20"
                                                    height="15"
                                                    class="w-4.5 h-3.5 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle" 
                                                    alt="{{ $profile->country->name }}"
                                                    title="{{ $profile->country->name }}"
                                                    loading="lazy"
                                                    onerror="this.style.display='none'"
                                                >
                                            @endif
                                        @else
                                            User
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-400 flex flex-wrap items-center gap-x-1.5 gap-y-0.5 mt-0.5 leading-tight">
                                        @if($authorUsername)
                                            <span class="truncate max-w-[120px] sm:max-w-[200px] font-medium text-slate-500">
                                                {{ '@' . $authorUsername }}
                                            </span>
                                            <span class="text-slate-300">•</span>
                                        @endif

                                        <span class="whitespace-nowrap shrink-0 text-slate-400">
                                            {{ $post->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TITLE / CONTENT --}}
                        <h3 class="font-bold text-slate-900 text-sm leading-snug">
                            <a href="{{ route('community.posts.show', $post) }}" class="hover:underline">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <div class="text-xs text-slate-600 leading-relaxed">
                            <p :class="expandedContent ? '' : 'line-clamp-3'">
                                {{ $post->content ?? $post->body ?? '' }}
                            </p>

                            @if(mb_strlen($post->content ?? $post->body ?? '') > 160)
                                <button
                                    type="button"
                                    @click="expandedContent = !expandedContent"
                                    class="text-xs font-bold text-amber-600 hover:text-amber-700 hover:underline mt-1.5 inline-block focus:outline-none cursor-pointer"
                                >
                                    <span x-text="expandedContent ? 'Show less' : '...See more'"></span>
                                </button>
                            @endif
                        </div>

                        {{-- MEDIA --}}
                        @if($post->media && $post->media->isNotEmpty())
                            <div class="space-y-3 pt-1">
                                @foreach($post->media as $mediaItem)
                                    <div class="w-full max-h-[550px] bg-slate-900 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                                        @if(($mediaItem->type ?? '') === 'video' || str_starts_with($mediaItem->mime_type ?? '', 'video') || str_starts_with($mediaItem->file_type ?? '', 'video'))
                                            <video class="w-full max-h-[550px] object-contain" controls preload="metadata" playsinline>
                                                <source src="{{ asset('storage/' . $mediaItem->file_path) }}" type="{{ $mediaItem->mime_type ?: 'video/mp4' }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <img src="{{ asset('storage/' . $mediaItem->file_path) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[550px] object-contain">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- ACTION BAR --}}
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                            <div class="flex items-center gap-5">
                                <button
                                    type="button"
                                    @click="toggleLike()"
                                    class="flex items-center gap-1.5 transition"
                                    :class="liked ? 'text-red-500' : 'hover:text-red-500'"
                                >
                                    <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span x-text="likesCount"></span>
                                </button>

                                <button
                                    type="button"
                                    @click="showAllComments = !showAllComments"
                                    class="flex items-center gap-1.5 hover:text-blue-500 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span x-text="commentsCount"></span>
                                </button>

                                <button
                                    type="button"
                                    @click="sharePost()"
                                    class="flex items-center gap-1.5 transition"
                                    :class="copied ? 'text-emerald-600' : 'hover:text-emerald-500'"
                                >
                                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    <span x-text="copied ? 'Copied!' : 'Share'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- COMMENTS SECTION --}}
                        <div class="pt-3 border-t border-slate-100 space-y-3" x-show="showAllComments" x-cloak>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-model="newCommentText"
                                    @keydown.enter="submitComment()"
                                    placeholder="Write a comment..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400"
                                >
                                <button
                                    type="button"
                                    @click="submitComment()"
                                    class="px-3 py-2 bg-[#0b1329] text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition"
                                >
                                    Post
                                </button>
                            </div>

                            <div class="space-y-2.5 pt-1">
                                <template x-for="comment in comments" :key="comment.id">
                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                            <img :src="comment.user.avatar" class="w-7 h-7 rounded-full object-cover shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-900 text-xs" x-text="comment.user.name"></span>
                                                    <span class="text-[10px] text-slate-400" x-text="comment.created_at_human"></span>
                                                </div>
                                                <p class="text-xs text-slate-700 mt-0.5 leading-relaxed" x-text="comment.content"></p>
                                                <template x-if="comment.replies && comment.replies.length">
                                                    <div class="mt-1">
                                                        <button type="button" @click="comment.showReplies = !comment.showReplies"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 hover:bg-amber-100/80 text-[9px] font-bold text-amber-700 transition">
                                                            <span x-text="comment.showReplies ? 'Hide replies' : (comment.replies.length + (comment.replies.length === 1 ? ' reply' : ' replies'))"></span>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                        <template x-if="comment.replies && comment.replies.length">
                                            <div x-show="comment.showReplies !== false" class="ml-7 pl-3 border-l-2 border-amber-200 space-y-2">
                                                <template x-for="reply in comment.replies" :key="reply.id">
                                                    <div class="flex items-start gap-2 bg-slate-50/70 p-2 rounded-xl border border-slate-100">
                                                        <img :src="reply.user.avatar" class="w-6 h-6 rounded-full object-cover shrink-0">
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex items-center justify-between">
                                                                <span class="font-bold text-slate-800 text-[11px]" x-text="reply.user.name"></span>
                                                                <span class="text-[9px] text-slate-400" x-text="reply.created_at_human"></span>
                                                            </div>
                                                            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed" x-text="reply.content"></p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="text-center pt-1" x-show="hasMoreComments">
                                <button type="button" @click="loadMoreComments()" :disabled="loadingMore"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 hover:bg-amber-50 border border-slate-200 text-xs font-semibold text-amber-600 hover:text-amber-700 transition disabled:opacity-50">
                                    <span x-text="loadingMore ? 'Loading comments...' : 'Read more'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-slate-200/70">
                        <p class="text-xs text-slate-500">No posts found in this group yet. Be the first to post!</p>
                    </div>
                @endforelse

                {{-- PAGINATION --}}
                @if(method_exists($posts, 'links'))
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                {{-- RESTRICTED ACCESS NOTICE --}}
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-xs lg:text-sm font-semibold text-amber-800">
                        You must be an active member of this group to view and interact with its posts.
                    </p>
                </div>
            @endif

            </div>{{-- End activeTab === 'posts' --}}

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar
            :user="$user"
            :trending-topics="collect()"
        />

    </main>

</body>
</html>