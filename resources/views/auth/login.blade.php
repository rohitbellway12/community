<x-guest-layout>
    
    <div class="w-full max-w-md mx-auto p-6 bg-white rounded-3xl shadow-xl border border-slate-100">

        <!-- Logo -->
        <div class="text-center mb-6">
            <img
                src="{{ asset('storage/logo.png') }}"
                alt="REIAC Logo"
                class="h-16 w-auto mx-auto object-contain"
            >

            <h3 class="text-xl font-bold text-slate-900 mt-4 flex items-center justify-center gap-2">
                Welcome Back 👋
            </h3>

            <p class="text-xs text-slate-500 mt-1">
                Login to continue to REIAC Community
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 flex items-start gap-2.5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs leading-relaxed font-semibold">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label
                    for="email"
                    class="block text-xs font-bold text-slate-700 mb-1"
                >
                    Email Address
                </label>

                <input
                    id="email"
                    class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
                    type="email"
                    name="email"
                    :value="old('email')"
                    placeholder="rahulsharma@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label
                        for="password"
                        class="block text-xs font-bold text-slate-700"
                    >
                        Password
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            class="text-[11px] font-semibold text-indigo-600 hover:underline"
                            href="{{ route('password.request') }}"
                        >
                            Forgot Password?
                        </a>
                    @endif
                </div>

                <div class="relative">
                    <input
                        id="password"
                        class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium pr-10"
                        type="password"
                        name="password"
                        placeholder="••••••••••••"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <x-input-error
                    :messages="$errors->get('password')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <label
                    for="remember_me"
                    class="inline-flex items-center cursor-pointer"
                >
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4"
                        name="remember"
                    >

                    <span class="ms-2 text-xs font-medium text-slate-600">
                        Remember me
                    </span>
                </label>
            </div>

            <!-- Login Button -->
            <div>
                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Login
                </button>
            </div>

            <!-- Register -->
            <div class="text-center mt-4">
                <p class="text-xs text-slate-500">
                    Don't have an account?
                    <a
                        href="{{ route('register') }}"
                        class="font-bold text-indigo-600 hover:underline"
                    >
                        Register Now
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>