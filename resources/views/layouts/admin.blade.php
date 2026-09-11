<!DOCTYPE html>
<html lang="en" class="h-full bg-[#F4F6F9]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Dashboard') - REIAC Community</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        reiac: {
                            navy: '#0B132B',
                            slate: '#1C2541',
                            gold: '#F7B500',
                            'gold-hover': '#E0A400',
                            bg: '#F4F6F9'
                        }
                    }
                }
            }
        }
    </script>
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
            class="fixed inset-y-0 left-0 z-50 w-64 bg-reiac-navy text-white flex flex-col
                   transition-transform duration-300 ease-in-out
                   lg:translate-x-0 shrink-0"
        >

            {{-- ========================================================
                 BRAND HEADER
            ========================================================= --}}
            <div
                class="h-16 flex items-center justify-between px-5
                       bg-reiac-slate/50 border-b border-slate-700/50 shrink-0"
            >

                {{-- Brand --}}
                <div class="flex items-center gap-3 min-w-0">

                    {{-- Logo Container --}}
                    <div
                        class="w-10 h-10 rounded-xl bg-white/95
                               flex items-center justify-center
                               shrink-0 shadow-sm overflow-hidden"
                    >
                        <img
                            src="{{ asset('storage/logo.png') }}"
                            alt="REIAC Logo"
                            class="w-8 h-8 object-contain"
                        >
                    </div>

                    {{-- Brand Text --}}
                    <div class="min-w-0 leading-tight">

                        <p
                            class="text-sm font-bold text-white
                                   tracking-wide truncate"
                        >
                            REIAC
                        </p>

                        <p
                            class="text-[9px] text-slate-400
                                   uppercase tracking-[0.14em]
                                   font-semibold mt-0.5 truncate"
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
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6">

                {{-- Dashboard --}}
                <div>

                    <a
                        href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : '/admin/dashboard' }}"
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-lg
                               text-sm font-semibold
                               {{ request()->routeIs('admin.dashboard') || request()->is('admin/dashboard')
                                    ? 'bg-reiac-gold text-reiac-navy font-bold'
                                    : 'text-slate-300 hover:bg-reiac-slate hover:text-white' }}
                               transition-colors"
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
                     USER MANAGEMENT
                ===================================================== --}}
                @php
                    $sidebarTotalUsers = \App\Models\User::count();
                    $sidebarActiveUsers = \App\Models\User::where('status', 'active')->count();
                    $sidebarBlockedUsers = \App\Models\User::where('status', 'blocked')->count();
                    $isUserManagementActive = request()->routeIs('admin.users*') || request()->is('*admin/users*');
                @endphp
                <div x-data="{ open: {{ $isUserManagementActive ? 'true' : 'true' }} }">

                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-bold uppercase tracking-wider transition-colors
                               {{ $isUserManagementActive ? 'text-reiac-gold' : 'text-slate-400 hover:text-white' }}"
                    >
                        <span class="flex items-center space-x-2">
                            <svg class="w-3.5 h-3.5 {{ $isUserManagementActive ? 'text-reiac-gold' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>User Management</span>
                        </span>

                        <svg
                            class="w-3.5 h-3.5 transform transition-transform duration-200"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>


                    <div
                        x-show="open"
                        x-collapse
                        class="mt-2 space-y-1 pl-2"
                    >

                        {{-- All Users --}}
                        <a
                            href="{{ Route::has('admin.users') ? route('admin.users') : '/admin/users' }}"
                            class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors group
                                   {{ $isUserManagementActive && !request()->filled('status')
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-3">
                                <svg
                                    class="w-4 h-4 shrink-0 {{ $isUserManagementActive && !request()->filled('status') ? 'text-reiac-gold' : 'text-slate-400 group-hover:text-white' }}"
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
                                <span>All Users</span>
                            </span>

                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                                {{ $sidebarTotalUsers }}
                            </span>
                        </a>


                        {{-- Active Users --}}
                        <a
                            href="{{ route('admin.users', ['status' => 'active']) }}"
                            class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors group
                                   {{ $isUserManagementActive && request('status') === 'active'
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-3">
                                <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Active Users</span>
                            </span>

                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                {{ $sidebarActiveUsers }}
                            </span>
                        </a>


                        {{-- Blocked Users --}}
                        <a
                            href="{{ route('admin.users', ['status' => 'blocked']) }}"
                            class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-colors group
                                   {{ $isUserManagementActive && request('status') === 'blocked'
                                        ? 'bg-reiac-slate text-reiac-gold font-bold shadow-xs'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-3">
                                <svg class="w-4 h-4 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <span>Blocked Users</span>
                            </span>

                            @if($sidebarBlockedUsers > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    {{ $sidebarBlockedUsers }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/5 text-slate-400">
                                    0
                                </span>
                            @endif
                        </a>

                    </div>

                </div>


                {{-- ====================================================
                     CONTENT & MODERATION
                ===================================================== --}}
                <div x-data="{ open: true }">

                    <button
                        @click="open = !open"
                        type="button"
                        class="w-full flex items-center justify-between
                               px-3 py-1 text-xs font-semibold
                               text-slate-400 uppercase tracking-wider
                               hover:text-slate-200"
                    >

                        <span>Content &amp; Moderation</span>

                        <svg
                            class="w-4 h-4 transform transition-transform"
                            :class="open ? 'rotate-90' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>

                    </button>


                    <div
                        x-show="open"
                        x-collapse
                        class="mt-2 space-y-1 pl-2"
                    >

                        {{-- Posts --}}
                        <a
                            href="{{ Route::has('admin.posts') ? route('admin.posts') : '/admin/posts' }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.posts*') || request()->is('admin/posts*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
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
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                                />
                            </svg>

                            <span>Posts</span>

                        </a>


                        {{-- Comments --}}
                        <a
                            href="{{ Route::has('admin.comments') ? route('admin.comments') : '/admin/comments' }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.comments*') || request()->is('admin/comments*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
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
                                    d="M8 12h.01M12 12h.01M16 12h.01
                                       M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                                       C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                />
                            </svg>

                            <span>Comments</span>

                        </a>


                        {{-- Tags --}}
                        <a
                            href="{{ route('tags.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ (request()->routeIs('tags*') || request()->is('*admin/tags*'))
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
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
                                    d="M7 7h.01M3 6a2 2 0 012-2h6l10 10a2 2 0 010 3l-4 4a2 2 0 01-3 0L4 11V6z"
                                />
                            </svg>

                            <span>Tags</span>

                        </a>

                        {{-- Categories --}}
                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.categories*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>Categories</span>
                        </a>

                        {{-- Groups --}}
                        <a
                            href="{{ route('admin.groups.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.groups*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Groups</span>
                        </a>

                        {{-- Reports --}}
                        @php
                            $sidebarPendingReports = \App\Models\Report::where('status', 'pending')->count();
                        @endphp
                        <a
                            href="{{ route('admin.reports') }}"
                            class="flex items-center justify-between px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.reports*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <span class="flex items-center space-x-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Reports</span>
                            </span>
                            @if($sidebarPendingReports > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-reiac-navy">
                                    {{ $sidebarPendingReports }}
                                </span>
                            @endif
                        </a>

                        {{-- Guidelines --}}
                        <a
                            href="{{ route('admin.guidelines.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.guidelines*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Guidelines</span>
                        </a>

                        {{-- Banners --}}
                        <a
                            href="{{ route('admin.banners.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.banners*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Banners</span>
                        </a>


                    </div>

                </div>


                {{-- ====================================================
                     SETTINGS
                ===================================================== --}}
                <div>

                    <span
                        class="w-full flex items-center justify-between
                               px-3 py-1 text-xs font-semibold
                               text-slate-400 uppercase tracking-wider"
                    >
                        <span>Settings</span>
                    </span>


                    <div class="mt-2 space-y-1 pl-2">

                        {{-- Countries --}}
                        <a
                            href="{{ route('admin.countries.index') }}"
                            class="flex items-center space-x-3 px-3 py-2
                                   rounded-lg text-sm
                                   {{ request()->routeIs('admin.countries*')
                                        ? 'bg-reiac-slate text-reiac-gold font-semibold'
                                        : 'text-slate-300 hover:bg-reiac-slate/60 hover:text-white' }}"
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
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2
                                       2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2.5 2.5 0 012 2
                                       2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064
                                       M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>

                            <span>Countries</span>

                        </a>

                    </div>

                </div>

            </nav>


            {{-- ========================================================
                 SIDEBAR FOOTER / ADMIN PROFILE
            ========================================================= --}}
            <div
                class="p-4 bg-reiac-slate/80
                       border-t border-slate-700/50
                       flex items-center justify-between shrink-0"
            >

                <div class="flex items-center space-x-3 min-w-0">

                    @php
    $adminUser = auth()->user();
    $adminProfile = $adminUser?->profile;
    
    // Check if avatar exists in profile, otherwise use fallback placeholder
    $adminAvatar = $adminProfile?->avatar 
        ? asset('storage/' . $adminProfile->avatar) 
        : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
        
    $adminRole = $adminProfile?->role ?? $adminUser->role->value ?? 'Administrator';
@endphp

                    <img
                        src="{{ $adminAvatar }}"
                        class="w-9 h-9 rounded-full object-cover
                               ring-2 ring-reiac-gold/50 shrink-0"
                        alt="{{ auth()->user()->name ?? 'Admin User' }}"
                    >

                    <div class="truncate min-w-0">

                        <p
                            class="text-xs font-semibold text-white truncate"
                        >
                            {{ auth()->user()->name ?? 'Admin User' }}
                        </p>

                        <p
                            class="text-[10px] text-slate-400 truncate"
                        >
                            {{ auth()->user()->role ?? 'Super Administrator' }}
                        </p>

                    </div>

                </div>

            </div>

        </aside>


        {{-- ============================================================
             MAIN CONTENT WRAPPER
        ============================================================= --}}
        <div
            class="flex-1 flex flex-col min-w-0
                   overflow-hidden lg:pl-64"
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


                    {{-- Breadcrumb --}}
                    <nav
                        class="hidden sm:flex items-center
                               space-x-2 text-xs font-medium
                               text-slate-500"
                    >
                        <span>REIAC Admin</span>

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

    @stack('scripts')
</body>
</html>