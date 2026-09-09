@props([
    'title' => 'Community | REIAC',
    'active' => 'home',
    'user' => null,
    'sidebar' => true,
    'rightbar' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">

    {{-- TOPBAR --}}
    <header class="bg-[#0b1329] text-white sticky top-0 z-50">
        <div class="max-w-[1520px] mx-auto px-4 lg:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4 lg:gap-12">
                {{-- Hamburger for Mobile Sidebar --}}
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-slate-300 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="text-xl lg:text-2xl font-black tracking-wider text-white">REIAC Community</div>
                <nav class="hidden lg:flex items-center gap-7 text-[13px] font-medium text-slate-300">
                    <a href="#" class="hover:text-white transition">Home</a>
                    <a href="#" class="hover:text-white transition">About Us</a>
                    <a href="#" class="hover:text-white transition flex items-center gap-1">Services <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg></a>
                    <a href="#" class="hover:text-white transition">For Students</a>
                    <a href="#" class="text-white font-semibold border-b-2 border-amber-400 pb-1 mt-1">Community</a>
                    <a href="#" class="hover:text-white transition">F-2-7 Points Calculator</a>
                    <a href="#" class="hover:text-white transition">Contact Us</a>
                </nav>
            </div>
            <div class="flex items-center gap-3 lg:gap-4">
                <button class="hidden lg:flex w-9 h-9 rounded-full bg-[#1e293b] items-center justify-center text-slate-300 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                <div class="relative">
                    <button class="w-9 h-9 rounded-full bg-[#1e293b] flex items-center justify-center text-slate-300 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <span class="absolute -top-1 -right-1 bg-amber-400 text-slate-950 font-bold text-[10px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-[#0b1329]">12</span>
                </div>
                <div class="hidden lg:flex relative items-center pl-1">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop&crop=faces" alt="User" class="w-9 h-9 rounded-full object-cover ring-2 ring-amber-400">
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#0b1329]"></span>
                </div>
            </div>
        </div>
    </header>

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden flex">
        <div @click="mobileMenuOpen = false" x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">
            <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <div class="font-bold text-slate-900 text-sm">Rahul Sharma</div>
                    <a href="#" class="text-xs text-amber-600 font-semibold hover:underline">View Profile</a>
                </div>
            </div>
            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Community Home
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    All Discussions
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    General
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ask a Solution
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Jobs Info
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Trending
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Following
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    Saved Posts
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    My Activity
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
                <a href="#" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-red-600">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

       <x-community.sidebar />

        {{-- CENTER FEED --}}
        <section class="space-y-5 min-w-0">
            <div class="bg-white p-5 lg:p-6 rounded-2xl shadow-sm border border-slate-200/70">
                <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight">Community</h1>
                <p class="text-xs text-slate-500 mt-1">Ask questions. Share knowledge. Find solutions.</p>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 lg:gap-3 mt-4">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">All</div>
                            <div class="text-[10px] text-slate-500 truncate">All Discussions</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">General</div>
                            <div class="text-[10px] text-slate-500 truncate">Discuss anything</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">Ask a Solution</div>
                            <div class="text-[10px] text-slate-500 truncate">Get solutions</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">Jobs Info</div>
                            <div class="text-[10px] text-slate-500 truncate">Job & Career Info</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 relative flex items-center">
                    <span class="absolute left-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" placeholder="Search discussions, users, topics..." class="w-full bg-slate-50 border border-slate-200/80 rounded-xl pl-10 pr-20 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <button class="absolute right-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1 shadow-xs">
                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-1.414.707l-2-1A1 1 0 019 18.414v-4.172a1 1 0 00-.293-.707L2.293 7.293A1 1 0 012 6.586V4z"></path></svg> Filters
                    </button>
                </div>

                <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-5 font-semibold text-slate-500">
                        <a href="#" class="text-slate-900 border-b-2 border-amber-400 pb-1">Latest</a>
                        <a href="#" class="hover:text-slate-900 transition">Trending</a>
                        <a href="#" class="hover:text-slate-900 transition">Most Discussed</a>
                    </div>
                    <div class="text-slate-500 font-medium flex items-center gap-1 cursor-pointer">
                        Sort by: Latest <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            {{-- POST 1 --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=100&h=100&fit=crop&crop=faces" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Rahul Sharma</div>
                            <div class="text-[11px] text-slate-500">Canada Aspirant &bull; 2h ago</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md tracking-wide">GENERAL</span>
                        <button class="text-slate-400 hover:text-slate-600 font-bold px-1">&bull;&bull;&bull;</button>
                    </div>
                </div>

                <h3 class="font-bold text-slate-900 text-sm leading-snug">Which universities are best for MSc Data Science in Canada with a reasonable tuition fee?</h3>

                <div class="flex flex-col sm:flex-row gap-4">
                    <p class="text-xs text-slate-600 leading-relaxed flex-1">I'm planning for Fall 2025 intake. Please suggest universities that are good in academics and have affordable tuition fees.</p>
                    <div class="w-full sm:w-36 h-24 bg-slate-100 rounded-xl overflow-hidden shrink-0 border border-slate-100">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=300&h=200&fit=crop" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <span class="bg-slate-100 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-red-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Canada
                    </span>
                    <span class="bg-slate-100 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/></svg> MSc Data Science
                    </span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-5">
                        <button class="flex items-center gap-1.5 hover:text-red-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> 24
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-blue-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> 12
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-emerald-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg> Share
                        </button>
                    </div>
                    <button class="flex items-center gap-1.5 hover:text-amber-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg> Save
                    </button>
                </div>
            </div>

            {{-- POST 2 --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&h=100&fit=crop&crop=faces" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Sneha Kapoor</div>
                            <div class="text-[11px] text-slate-500">UK Aspirant &bull; 3h ago</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-sky-50 text-sky-600 text-[10px] font-bold px-2.5 py-1 rounded-md tracking-wide">ASK A SOLUTION</span>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg> Solved
                        </span>
                        <button class="text-slate-400 hover:text-slate-600 font-bold px-1">&bull;&bull;&bull;</button>
                    </div>
                </div>

                <h3 class="font-bold text-slate-900 text-sm leading-snug">How can I improve my chances of getting admission in top UK universities?</h3>

                <p class="text-xs text-slate-600 leading-relaxed">I have 7.0 in IELTS and 78% in my graduation. Please guide me on what else I can do to strengthen my profile.</p>

                <div class="flex items-center gap-2 pt-1">
                    <span class="bg-slate-100 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> 8 Answers
                    </span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-5">
                        <button class="flex items-center gap-1.5 hover:text-red-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> 15
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-blue-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> 0
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-emerald-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg> Share
                        </button>
                    </div>
                    <button class="flex items-center gap-1.5 hover:text-amber-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg> Save
                    </button>
                </div>
            </div>

            {{-- POST 3 --}}
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=faces" class="w-9 h-9 rounded-full object-cover">
                        <div>
                            <div class="font-bold text-slate-900 text-xs">Vikas Mehta</div>
                            <div class="text-[11px] text-slate-500">Community Member &bull; 5h ago</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-md tracking-wide">JOBS INFO</span>
                        <button class="text-slate-400 hover:text-slate-600 font-bold px-1">&bull;&bull;&bull;</button>
                    </div>
                </div>

                <h3 class="font-bold text-slate-900 text-sm leading-snug">Part-time job opportunity for students in Toronto</h3>

                <p class="text-xs text-slate-600 leading-relaxed">Looking for 2 part-time team members for a retail store in Toronto. Flexible hours and good pay. Interested students can DM.</p>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-5">
                        <button class="flex items-center gap-1.5 hover:text-red-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> 31
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-blue-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> 6
                        </button>
                        <button class="flex items-center gap-1.5 hover:text-emerald-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg> Share
                        </button>
                    </div>
                    <button class="flex items-center gap-1.5 hover:text-amber-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg> Save
                    </button>
                </div>
            </div>
        </section>

        <x-community.rightbar />

    </main>

    {{-- MOBILE BOTTOM NAVIGATION BAR --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 lg:hidden px-4 py-2 z-40 flex items-center justify-around shadow-lg">
        <a href="#" class="flex flex-col items-center gap-1 text-slate-500 hover:text-[#0b1329]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-[#0b1329] font-bold">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            <span class="text-[10px]">Community</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center -mt-5">
            <div class="w-12 h-12 bg-[#0b1329] rounded-full flex items-center justify-center text-white shadow-lg border-4 border-[#f3f4f6]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <span class="text-[10px] font-medium text-slate-700 mt-0.5">Create</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-slate-500 hover:text-[#0b1329] relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span class="absolute -top-0.5 right-0.5 bg-red-500 text-white text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center font-bold">12</span>
            <span class="text-[10px] font-medium">Notifications</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-slate-500 hover:text-[#0b1329]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-medium">Profile</span>
        </a>
    </div>

</body>

</html>