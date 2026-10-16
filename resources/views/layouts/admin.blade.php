<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F4F6F9]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - REIAC Community</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Modern SweetAlert2 Styling */
        .swal2-popup {
            border-radius: 1.5rem !important;
            padding: 1.75rem !important;
            font-family: inherit !important;
            border: 1px solid #f1f5f9 !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        }
        .swal2-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            padding-top: 0.5rem !important;
        }
        .swal2-html-container {
            font-size: 0.8125rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
            margin: 0.75rem 0 0 !important;
        }
        .swal2-actions {
            display: flex !important;
            gap: 0.75rem !important;
            margin-top: 1.5rem !important;
            width: 100% !important;
            justify-content: center !important;
        }
        .swal2-styled.swal2-confirm {
            border-radius: 0.75rem !important;
            font-weight: 800 !important;
            font-size: 0.8125rem !important;
            letter-spacing: 0.025em !important;
            padding: 0.7rem 1.4rem !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12) !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
            cursor: pointer !important;
        }
        .swal2-styled.swal2-cancel {
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 0.8125rem !important;
            padding: 0.7rem 1.4rem !important;
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
            cursor: pointer !important;
        }
        .swal2-styled.swal2-confirm:hover,
        .swal2-styled.swal2-cancel:hover {
            transform: translateY(-1px) !important;
        }
    </style>
</head>

<body
    class="h-full font-sans antialiased text-slate-800"
    x-data="{
        mobileSidebarOpen: false,
        notificationsOpen: false,
        profileOpen: false,

        notifications: [
            {
                id: 1,
                text: '12 new posts were reported in Jobs Info category',
                time: '10m ago',
                read: false
            },
            {
                id: 2,
                text: 'Rahul Sharma commented on Immigration Discussion',
                time: '25m ago',
                read: false
            },
            {
                id: 3,
                text: '5 new users joined the community today',
                time: '1h ago',
                read: true
            },
            {
                id: 4,
                text: 'Post #2841 was flagged for potential spam',
                time: '2h ago',
                read: true
            }
        ]
    }"
>

    <div class="min-h-full flex flex-col lg:flex-row">

        @php
            $isAdmin = auth()->check() && in_array(auth()->user()->role?->value ?? auth()->user()->role, ['admin', 'super_admin'], true);

            $adminUser = auth()->user();
            $adminProfile = $adminUser?->profile;
            $adminAvatar = $adminProfile?->avatar
                ? asset('storage/' . $adminProfile->avatar)
                : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
            $adminRole = $adminProfile?->role ?? $adminUser?->role?->value ?? 'Administrator';
        @endphp

        @if($isAdmin)
        {{-- ============================================================
             MOBILE BACKDROP
        ============================================================= --}}
        <div
            x-show="mobileSidebarOpen"
            x-cloak
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileSidebarOpen = false"
        ></div>


        {{-- ============================================================
             SIDEBAR
        ============================================================= --}}
        <aside
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-80 bg-reiac-navy text-white flex flex-col
                   transition-transform duration-300 ease-in-out
                   lg:translate-x-0 shrink-0"
        >

            {{-- ========================================================
                 BRAND HEADER
            ========================================================= --}}
            <div
                class="h-20 flex items-center justify-between px-6
                       bg-reiac-slate/50 border-b border-slate-700/50 shrink-0"
            >

                {{-- Brand --}}
                <div class="flex items-center gap-3.5 min-w-0">

                    {{-- Logo Container --}}
                    <div
                        class="w-11 h-11 rounded-xl bg-white/95
                               flex items-center justify-center
                               shrink-0 shadow-sm overflow-hidden"
                    >
                        <img
                            src="{{ asset('storage/logo.png') }}"
                            alt="REIAC Logo"
                            class="w-9 h-9 object-contain"
                        >
                    </div>

                    {{-- Brand Text --}}
                    <div class="min-w-0 leading-tight">

                        <p
                            class="text-base font-extrabold text-white
                                   tracking-wide truncate"
                        >
                            REIAC
                        </p>

                        <p
                            class="text-[11px] text-slate-300
                                   uppercase tracking-widest
                                   font-bold mt-0.5 truncate"
                        >
                            Community Admin
                        </p>

                    </div>

                </div>


                {{-- Mobile Close Button --}}
                <button
                    @click="mobileSidebarOpen = false"
                    type="button"
                    class="lg:hidden w-9 h-9 rounded-lg
                           flex items-center justify-center
                           text-slate-400 hover:text-white
                           hover:bg-white/10
                           transition-colors duration-200
                           shrink-0"
                    aria-label="Close sidebar"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

            </div>


            {{-- ========================================================
                 SIDEBAR NAVIGATION
            ========================================================= --}}
            <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-2">

                {{-- ====================================================
                     1. MAIN / DASHBOARD
                ===================================================== --}}
                <div>
                    <a
                        href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '/admin/dashboard' }}"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ request()->routeIs('admin.dashboard') || request()->is('admin/dashboard')
                                    ? 'bg-reiac-gold text-reiac-navy shadow-md font-extrabold'
                                    : 'text-slate-200 hover:bg-white/10 hover:text-white' }}"
                    >
                        <svg
                            class="w-5 h-5 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z
                                   M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z
                                   M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z
                                   M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                            />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>


                {{-- ====================================================
                     2. USER MANAGEMENT
                ===================================================== --}}
                @php
                    $sidebarTotalUsers = \App\Models\User::count();
                    $sidebarOnlineUsers = \App\Models\User::whereNotNull('last_seen_at')->where('last_seen_at', '>=', now()->subMinutes(5))->count();
                    $sidebarActiveUsers = \App\Models\User::where('status', 'active')->count();
                    $sidebarBlockedUsers = \App\Models\User::where('status', 'blocked')->count();
                    $isUserManagementActive = request()->routeIs('admin.users*') || request()->is('*admin/users*') || request()->routeIs('admin.profile-activities*');
                @endphp
                <div x-data="{ open: {{ $isUserManagementActive ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isUserManagementActive ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isUserManagementActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">User Management</span>
                        </span>

                        <svg
                            class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-collapse
                        class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60"
                    >
                        {{-- All Users --}}
                        <a
                            href="{{ Route::has('admin.users') ? route('admin.users') : '/admin/users' }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ $isUserManagementActive && !request()->filled('status') && !request()->routeIs('admin.profile-activities*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg
                                    class="w-4 h-4 shrink-0 {{ $isUserManagementActive && !request()->filled('status') && !request()->routeIs('admin.profile-activities*') ? 'text-reiac-gold' : 'text-slate-400 group-hover:text-white' }}"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <span class="truncate">All Users</span>
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarTotalUsers }}
                            </span>
                        </a>

                        {{-- Online Users --}}
                        <a
                            href="{{ route('admin.users', ['status' => 'online']) }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ $isUserManagementActive && request('status') === 'online'
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                <span class="truncate">Online Users</span>
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">
                                {{ $sidebarOnlineUsers }}
                            </span>
                        </a>

                        {{-- Active Accounts --}}
                        <a
                            href="{{ route('admin.users', ['status' => 'active']) }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ $isUserManagementActive && request('status') === 'active'
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate">Active Accounts</span>
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 shrink-0">
                                {{ $sidebarActiveUsers }}
                            </span>
                        </a>

                        {{-- Blocked Users --}}
                        <a
                            href="{{ route('admin.users', ['status' => 'blocked']) }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ $isUserManagementActive && request('status') === 'blocked'
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span class="truncate">Blocked Users</span>
                            </span>

                            @if($sidebarBlockedUsers > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 shrink-0">
                                    {{ $sidebarBlockedUsers }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/5 text-slate-400 shrink-0">
                                    0
                                </span>
                            @endif
                        </a>

                        {{-- Profile Activities & Progress Bar --}}
                        <a
                            href="{{ route('admin.profile-activities.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.profile-activities*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.profile-activities*') ? 'text-reiac-gold' : 'text-indigo-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="truncate">Profile Activities</span>
                            </span>

                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 shrink-0">
                                Settings
                            </span>
                        </a>
                    </div>
                </div>


                {{-- ====================================================
                     3. COMMUNITY & MODERATION
                ===================================================== --}}
                @php
                    $isCommunityActive = request()->routeIs('admin.posts*') || request()->is('*admin/posts*') ||
                                         request()->routeIs('admin.comments*') || request()->is('*admin/comments*') ||
                                         request()->routeIs('admin.groups*') || request()->is('*admin/groups*') ||
                                         request()->routeIs('admin.categories*') || request()->is('*admin/categories*') ||
                                         request()->routeIs('tags*') || request()->is('*tags*') ||
                                         request()->routeIs('admin.reports*') || request()->is('*admin/reports*') ||
                                         request()->routeIs('admin.guidelines*') || request()->is('*admin/guidelines*');
                    $sidebarPendingReports = \App\Models\Report::where('status', 'pending')->count();
                @endphp
                <div x-data="{ open: {{ $isCommunityActive ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isCommunityActive ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isCommunityActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span class="truncate">Community &amp; Moderation</span>
                        </span>

                        <div class="flex items-center space-x-2 shrink-0">
                            @if($sidebarPendingReports > 0)
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                            @endif
                            <svg
                                class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                                :class="open ? 'rotate-90' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>

                    <div
                        x-show="open"
                        x-collapse
                        class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60"
                    >
                        {{-- Posts --}}
                        <a
                            href="{{ Route::has('admin.posts') ? route('admin.posts') : '/admin/posts' }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.posts*') || request()->is('admin/posts*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <span>Posts</span>
                        </a>

                        {{-- Comments --}}
                        <a
                            href="{{ Route::has('admin.comments') ? route('admin.comments') : '/admin/comments' }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.comments*') || request()->is('admin/comments*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Comments</span>
                        </a>

                        {{-- Groups --}}
                        <a
                            href="{{ route('admin.groups.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.groups*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Groups</span>
                        </a>

                        {{-- Categories --}}
                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.categories*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>Categories</span>
                        </a>

                        {{-- Tags --}}
                        <a
                            href="{{ route('tags.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ (request()->routeIs('tags*') || request()->is('*admin/tags*'))
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M3 6a2 2 0 012-2h6l10 10a2 2 0 010 3l-4 4a2 2 0 01-3 0L4 11V6z" />
                            </svg>
                            <span>Tags</span>
                        </a>

                        {{-- Moderation Reports --}}
                        <a
                            href="{{ route('admin.reports') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.reports*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="truncate">Reports</span>
                            </span>
                            @if($sidebarPendingReports > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-reiac-navy animate-pulse shrink-0">
                                    {{ $sidebarPendingReports }} Pending
                                </span>
                            @endif
                        </a>

                        {{-- Guidelines --}}
                        <a
                            href="{{ route('admin.guidelines.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.guidelines*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Guidelines</span>
                        </a>
                    </div>
                </div>


                {{-- ====================================================
                     4. LANGUAGE TESTS (STEP-BY-STEP WORKFLOW)
                ===================================================== --}}
                @php
                    $isTestMenuOpen = request()->is('*admin/test-levels*') || request()->is('*admin/questions*') || request()->is('*admin/result-slabs*') || request()->is('*admin/tests*');
                    $sidebarLevelsCount = \App\Models\TestLevel::count();
                    $sidebarQuestionsCount = \App\Models\Question::count();
                    $sidebarSlabsCount = \App\Models\ResultSlab::count();
                    $sidebarTestsCount = \App\Models\Test::count();
                    $sidebarAttemptsCount = \App\Models\TestAttempt::count();
                @endphp
                <div x-data="{ open: {{ $isTestMenuOpen ? 'true' : 'false' }} }">

                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isTestMenuOpen ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isTestMenuOpen ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate">Language Tests</span>
                        </span>

                        <svg
                            class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-collapse
                        class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60"
                    >
                        {{-- STEP 1: Test Levels --}}
                        <a
                            href="{{ route('admin.test-levels.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->is('*admin/test-levels*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-5 h-5 rounded text-[11px] font-black flex items-center justify-center shrink-0 {{ request()->is('*admin/test-levels*') ? 'bg-reiac-gold text-reiac-navy' : 'bg-slate-700 text-slate-300' }}">1</span>
                                <span class="truncate">Test Levels</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarLevelsCount }}
                            </span>
                        </a>

                        {{-- STEP 2: Questions --}}
                        <a
                            href="{{ route('admin.questions.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->is('*admin/questions*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-5 h-5 rounded text-[11px] font-black flex items-center justify-center shrink-0 {{ request()->is('*admin/questions*') ? 'bg-reiac-gold text-reiac-navy' : 'bg-slate-700 text-slate-300' }}">2</span>
                                <span class="truncate">Question Bank</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarQuestionsCount }}
                            </span>
                        </a>

                        {{-- STEP 3: Result Slabs --}}
                        <a
                            href="{{ route('admin.result-slabs.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->is('*admin/result-slabs*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-5 h-5 rounded text-[11px] font-black flex items-center justify-center shrink-0 {{ request()->is('*admin/result-slabs*') ? 'bg-reiac-gold text-reiac-navy' : 'bg-slate-700 text-slate-300' }}">3</span>
                                <span class="truncate">Result Slabs</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarSlabsCount }}
                            </span>
                        </a>

                        {{-- STEP 4: Tests --}}
                        <a
                            href="{{ route('admin.tests.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ (request()->is('*admin/tests*') && !request()->is('*admin/tests/attempts*'))
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-5 h-5 rounded text-[11px] font-black flex items-center justify-center shrink-0 {{ (request()->is('*admin/tests*') && !request()->is('*admin/tests/attempts*')) ? 'bg-reiac-gold text-reiac-navy' : 'bg-slate-700 text-slate-300' }}">4</span>
                                <span class="truncate">Create Tests</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarTestsCount }}
                            </span>
                        </a>

                        {{-- STEP 5: Submissions --}}
                        <a
                            href="{{ route('admin.tests.attempts.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->is('*admin/tests/attempts*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <span class="w-5 h-5 rounded text-[11px] font-black flex items-center justify-center shrink-0 {{ request()->is('*admin/tests/attempts*') ? 'bg-reiac-gold text-reiac-navy' : 'bg-slate-700 text-slate-300' }}">5</span>
                                <span class="truncate">Submissions &amp; Results</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarAttemptsCount }}
                            </span>
                        </a>
                    </div>
                </div>


                {{-- ====================================================
                     5. SERVICES & RESOURCES
                ===================================================== --}}
                @php
                    $isServicesResourcesActive = request()->is('*admin/services*') || request()->is('*admin/service-categories*') || request()->is('*admin/documents*');
                    $sidebarServicesCount = \App\Models\Service::count();
                    $sidebarCategoriesCount = \App\Models\ServiceCategory::count();
                    $sidebarDocsCount = \App\Models\Document::count();
                @endphp
                <div x-data="{ open: {{ $isServicesResourcesActive ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isServicesResourcesActive ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isServicesResourcesActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">Services &amp; Resources</span>
                        </span>

                        <svg
                            class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60">
                        {{-- 1. Service Categories --}}
                        <a
                            href="{{ route('admin.service-categories.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.service-categories*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.service-categories*') ? 'text-reiac-gold' : 'text-cyan-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="truncate">Service Categories</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarCategoriesCount }}
                            </span>
                        </a>

                        {{-- 2. All Services --}}
                        <a
                            href="{{ route('admin.services.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.services.index') || (request()->routeIs('admin.services*') && !request()->routeIs('admin.services.create') && !request()->routeIs('admin.service-categories*'))
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ (request()->routeIs('admin.services.index') || (request()->routeIs('admin.services*') && !request()->routeIs('admin.services.create') && !request()->routeIs('admin.service-categories*'))) ? 'text-reiac-gold' : 'text-emerald-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                <span class="truncate">All Services</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarServicesCount }}
                            </span>
                        </a>

                        {{-- 3. Add Service --}}
                        <a
                            href="{{ route('admin.services.create') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.services.create')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.services.create') ? 'text-reiac-gold' : 'text-amber-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="truncate">Add New Service</span>
                            </span>
                        </a>

                        {{-- 4. Documents & Resources Library --}}
                        <a
                            href="{{ route('admin.documents.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.documents*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('admin.documents*') ? 'text-reiac-gold' : 'text-violet-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">Documents Library</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarDocsCount }}
                            </span>
                        </a>
                    </div>
                </div>


                {{-- ====================================================
                     6. MARKETING & ENGAGEMENT
                ===================================================== --}}
                @php
                    $isMarketingActive = request()->is('*admin/push-notifications*') || request()->is('*admin/events*') || request()->is('*admin/banners*');
                    $sidebarActiveBanners = \App\Models\Banner::where('status', 'active')->count();
                    $sidebarEventsCount = \App\Models\Event::count();
                @endphp
                <div x-data="{ open: {{ $isMarketingActive ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isMarketingActive ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isMarketingActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <span class="truncate">Marketing &amp; Growth</span>
                        </span>

                        <svg
                            class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60">
                        {{-- Push Notifications Module --}}
                        <a
                            href="{{ route('admin.push-notifications.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.push-notifications*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>Push Broadcasts</span>
                        </a>

                        {{-- Events & Referral Contests --}}
                        <a
                            href="{{ route('admin.events.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.events*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <span class="truncate">Events &amp; Contests</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarEventsCount }}
                            </span>
                        </a>

                        {{-- Banners --}}
                        <a
                            href="{{ route('admin.banners.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors group
                                   {{ request()->routeIs('admin.banners*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-2.5 min-w-0 truncate">
                                <svg class="w-4 h-4 shrink-0 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">Banners</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/10 text-slate-300 shrink-0">
                                {{ $sidebarActiveBanners }}
                            </span>
                        </a>
                    </div>
                </div>


                {{-- ====================================================
                     7. SYSTEM & SETTINGS
                ===================================================== --}}
                @php
                    $isSettingsActive = request()->routeIs('admin.countries*') || request()->routeIs('admin.notification-templates*') || request()->is('*admin/notification-templates*') || request()->is('*admin/countries*');
                @endphp
                <div x-data="{ open: {{ $isSettingsActive ? 'true' : 'false' }} }">
                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition-all duration-150
                               {{ $isSettingsActive ? 'text-reiac-gold bg-white/[0.08] shadow-xs' : 'text-slate-200 hover:text-white hover:bg-white/[0.06]' }}"
                    >
                        <span class="flex items-center space-x-3 min-w-0">
                            <svg class="w-5 h-5 shrink-0 {{ $isSettingsActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">Settings &amp; System</span>
                        </span>

                        <svg
                            class="w-4 h-4 shrink-0 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-1.5 space-y-1 ml-4 pl-3.5 border-l-2 border-slate-700/60">
                        {{-- Countries --}}
                        <a
                            href="{{ route('admin.countries.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.countries*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2.5 2.5 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Countries</span>
                        </a>

                        {{-- Notification Templates & Firebase settings --}}
                        <a
                            href="{{ route('admin.notification-templates.index') }}"
                            class="flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition-colors
                                   {{ request()->routeIs('admin.notification-templates*')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4 shrink-0 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17h.01M11 13h.01M11 9h.01M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7" />
                            </svg>
                            <span>Notification Templates &amp; Firebase</span>
                        </a>
                    </div>
                </div>

            </nav>


            {{-- ========================================================
                 SIDEBAR FOOTER / ADMIN PROFILE
            ========================================================= --}}
            <div
                class="p-4.5 px-5 bg-reiac-slate/90
                       border-t border-slate-700/50
                       flex items-center justify-between shrink-0"
            >

                <div class="flex items-center space-x-3.5 min-w-0">

                    <img
                        src="{{ $adminAvatar }}"
                        class="w-11 h-11 rounded-full object-cover
                               ring-2 ring-reiac-gold/50 shrink-0"
                        alt="{{ auth()->user()->name ?? 'Admin User' }}"
                    >

                    <div class="truncate min-w-0">

                        <p
                            class="text-sm font-bold text-white truncate"
                        >
                            {{ auth()->user()->name ?? 'Admin User' }}
                        </p>

                        <p
                            class="text-xs text-slate-300 font-medium truncate mt-0.5"
                        >
                            {{ auth()->user()->role ?? 'Super Administrator' }}
                        </p>

                    </div>

                </div>

            </div>

        </aside>
        @endif

        {{-- ============================================================
             MAIN CONTENT WRAPPER
        ============================================================= --}}
        <div
            class="flex-1 flex flex-col min-w-0 overflow-hidden
                   @if($isAdmin) lg:pl-80 @endif"
        >

            {{-- ========================================================
                 TOP NAVBAR
            ========================================================= --}}
            <header
                class="h-16 bg-white border-b border-slate-200
                       flex items-center justify-between
                       px-4 sm:px-6 z-10 shadow-sm"
            >

                {{-- Left --}}
                <div class="flex items-center space-x-4">

                    @if($isAdmin)
                    {{-- Mobile Menu --}}
                    <button
                        @click="mobileSidebarOpen = true"
                        type="button"
                        class="lg:hidden text-slate-500
                               hover:text-slate-700"
                        aria-label="Open sidebar"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                    @endif

                    {{-- Breadcrumb --}}
                    <nav
                        class="hidden sm:flex items-center
                               space-x-2 text-xs font-medium
                               text-slate-500"
                    >
                        <span>@if($isAdmin) REIAC Admin @else REIAC @endif</span>

                        <span>/</span>

                        <span class="text-slate-900 font-semibold">
                            @yield('title', 'Dashboard')
                        </span>
                    </nav>

                </div>


                {{-- Right --}}
                <div class="flex items-center space-x-3 sm:space-x-4">

                    {{-- ==================================================
                         SEARCH
                    =================================================== --}}
                    {{-- <div class="relative hidden md:block w-64">

                        <input
                            type="text"
                            placeholder="Search users, posts, reports..."
                            class="w-full text-xs pl-9 pr-4 py-2
                                   bg-slate-100 border border-transparent
                                   rounded-lg
                                   focus:bg-white
                                   focus:border-reiac-gold
                                   focus:outline-none
                                   transition-all"
                        >

                        <svg
                            class="w-4 h-4 text-slate-400
                                   absolute left-3 top-2.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>

                    </div> --}}


                    {{-- ==================================================
                         NOTIFICATIONS
                    =================================================== --}}
                    {{-- <div class="relative">

                        <button
                            @click="notificationsOpen = !notificationsOpen"
                            type="button"
                            class="relative p-2
                                   text-slate-500
                                   hover:text-reiac-navy
                                   rounded-lg
                                   hover:bg-slate-100
                                   transition-colors"
                            aria-label="Notifications"
                        >

                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-1.405-1.405
                                       A2.032 2.032 0 0118 14.158V11
                                       a6.002 6.002 0 00-4-5.659V5
                                       a2 2 0 10-4 0v.341
                                       C7.67 6.165 6 8.388 6 11v3.159
                                       c0 .538-.214 1.055-.595 1.436L4 17h5
                                       m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                />
                            </svg>

                            <span
                                class="absolute top-1.5 right-1.5
                                       w-2 h-2 bg-red-500 rounded-full
                                       ring-2 ring-white"
                            ></span>

                        </button> --}}


                        {{-- Notification Dropdown
                        <div
                            x-show="notificationsOpen"
                            x-cloak
                            @click.outside="notificationsOpen = false"
                            class="absolute right-0 mt-2
                                   w-80 sm:w-96
                                   bg-white
                                   border border-slate-200
                                   rounded-xl shadow-xl
                                   py-2 z-50
                                   divide-y divide-slate-100"
                        >

                            <div
                                class="px-4 py-2.5
                                       flex items-center justify-between"
                            >

                                <h3
                                    class="text-xs font-bold
                                           text-slate-900
                                           uppercase tracking-wider"
                                >
                                    Notifications
                                </h3>

                                <span
                                    class="text-[10px]
                                           bg-reiac-gold/20
                                           text-reiac-navy
                                           font-bold
                                           px-2 py-0.5
                                           rounded-full"
                                >
                                    4 New
                                </span>

                            </div>


                            <div
                                class="max-h-72 overflow-y-auto
                                       divide-y divide-slate-50"
                            >

                                <template
                                    x-for="n in notifications"
                                    :key="n.id"
                                >

                                    <div
                                        class="px-4 py-3
                                               flex items-start space-x-3
                                               hover:bg-slate-50
                                               transition-colors"
                                    >

                                        <div
                                            class="p-1.5 bg-amber-50
                                                   rounded-lg text-amber-600
                                                   shrink-0 mt-0.5"
                                        >
                                            <svg
                                                class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01
                                                       M21 12a9 9 0 11-18 0
                                                       9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>


                                        <div class="flex-1 min-w-0">

                                            <p
                                                class="text-xs text-slate-700
                                                       leading-snug"
                                                x-text="n.text"
                                            ></p>

                                            <span
                                                class="text-[10px]
                                                       text-slate-400
                                                       mt-1 block"
                                                x-text="n.time"
                                            ></span>

                                        </div>

                                    </div>

                                </template>

                            </div>

                        </div>

                    </div> --}}


                    {{-- ==================================================
                         PROFILE DROPDOWN
                    =================================================== --}}
                    <div class="relative">

                        <button
                            @click="profileOpen = !profileOpen"
                            type="button"
                            class="flex items-center space-x-2
                                   p-1 rounded-lg
                                   hover:bg-slate-100
                                   transition-colors"
                        >

                            <img
                                src="{{ $adminAvatar }}"
                                class="w-8 h-8 rounded-full
                                       object-cover border border-slate-300"
                                alt="{{ auth()->user()->name ?? 'Admin User' }}"
                            >

                            <span
                                class="hidden md:block
                                       text-xs font-semibold
                                       text-slate-700"
                            >
                                {{ auth()->user()->name ?? 'Admin User' }}
                            </span>

                        </button>


                        {{-- Profile Dropdown --}}
                        <div
                            x-show="profileOpen"
                            x-cloak
                            @click.outside="profileOpen = false"
                            class="absolute right-0 mt-2 w-48
                                   bg-white border border-slate-200
                                   rounded-xl shadow-xl
                                   py-1 z-50
                                   text-xs text-slate-700"
                        >

                            {{-- Admin Info --}}
                            <div class="px-4 py-3">

                                <p
                                    class="font-semibold
                                           text-slate-900 truncate"
                                >
                                    {{ auth()->user()->name ?? 'Admin User' }}
                                </p>

                                <p
                                    class="text-[10px]
                                           text-slate-400 mt-0.5"
                                >
                                    {{ auth()->user()->role ?? 'Super Administrator' }}
                                </p>

                            </div>


                            <div
                                class="border-t border-slate-100 my-1"
                            ></div>


                            {{-- Logout --}}
                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="w-full text-left
                                           block px-4 py-2
                                           hover:bg-red-50
                                           text-red-600
                                           font-semibold"
                                >
                                    Logout
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </header>


            {{-- ========================================================
                 DYNAMIC CONTENT
            ========================================================= --}}
            <main
                class="flex-1 overflow-y-auto
                       p-4 sm:p-6 lg:p-8"
            >
                @yield('content')
            </main>

        </div>

    </div>

    <script>
        function confirmDelete(form, itemName, itemType, questionCount = 0, testCount = 0) {
            if (!form) return;

            let message = `Are you sure you want to delete "${itemName}"? This action cannot be undone.`;
            if (questionCount > 0 || testCount > 0) {
                message += ` This will also permanently delete ${testCount} test(s) and ${questionCount} question(s).`;
            }

            Swal.fire({
                title: `Delete ${itemType}?`,
                html: `<span class="text-xs text-slate-600">${message}</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#E11D48',
                cancelButtonColor: '#64748B',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmPushResend(form, notificationTitle = '') {
            if (!form) return;
            Swal.fire({
                title: 'Re-broadcast Push Notification?',
                html: `Are you sure you want to re-broadcast ${notificationTitle ? `<b>"${notificationTitle}"</b>` : 'this notification'}?<br><span class="text-xs text-slate-500 mt-1 block">This will immediately deliver notifications to all registered device tokens.</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Re-broadcast Now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#64748B',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function confirmPushDelete(form, notificationTitle = '') {
            if (!form) return;
            Swal.fire({
                title: 'Delete Push Notification?',
                html: `Are you sure you want to delete ${notificationTitle ? `<b>"${notificationTitle}"</b>` : 'this notification'}?<br><span class="text-xs text-rose-500 mt-1 block font-medium">All associated delivery logs will be permanently deleted.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#E11D48',
                cancelButtonColor: '#64748B',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>