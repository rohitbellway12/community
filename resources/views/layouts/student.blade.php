<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F6F9]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Online Test') - REIAC Portal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
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
<body class="h-full font-sans antialiased text-slate-800 flex flex-col min-h-screen">

    {{-- STUDENT HEADER (HIDDEN DURING ACTIVE EXAMS) --}}
    @unless(View::hasSection('hide_header'))
    <header class="bg-reiac-navy text-white border-b border-slate-800 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('community.index') }}" class="flex items-center gap-2 text-white hover:text-reiac-gold transition">
                    <span class="text-base font-black tracking-wider text-reiac-gold">REIAC</span>
                    <span class="text-xs uppercase tracking-widest text-slate-300 font-semibold hidden sm:inline">| Test Center</span>
                </a>
            </div>

            <nav class="flex items-center gap-4 text-xs font-semibold">
                <a href="{{ route('tests.student.index') }}" class="text-slate-300 hover:text-white transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>All Tests</span>
                </a>
                <a href="{{ route('community.index') }}" class="text-slate-400 hover:text-slate-200 transition">
                    ← Back to Community
                </a>
                @auth
                    <div class="flex items-center gap-2 pl-3 border-l border-slate-700">
                        <span class="w-7 h-7 rounded-full bg-reiac-gold text-reiac-navy font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="text-xs text-slate-300 font-medium hidden md:inline">{{ auth()->user()->name }}</span>
                    </div>
                @endauth
            </nav>
        </div>
    </header>
    @endunless

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER (HIDDEN DURING ACTIVE EXAMS) --}}
    @unless(View::hasSection('hide_header'))
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} REIAC Community Test Portal. All rights reserved.</p>
    </footer>
    @endunless

</body>
</html>
