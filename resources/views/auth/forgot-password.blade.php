<x-guest-layout>
    <div class="w-full max-w-md mx-auto p-6 sm:p-7 bg-white rounded-3xl shadow-xl border border-slate-100">

        <!-- Brand Header -->
        <div class="text-center mb-6">
            <img
                src="{{ asset('storage/logo.png') }}"
                alt="REIAC Logo"
                class="h-14 w-auto mx-auto object-contain"
            >

            <h2 class="text-xl font-bold text-slate-900 mt-4">
                @if($step == 1)
                    Forgot Password?
                @elseif($step == 2)
                    Verify Your Email
                @else
                    Create New Password
                @endif
            </h2>

            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                @if($step == 1)
                    Enter your email and we'll send you an OTP to reset your password.
                @elseif($step == 2)
                    Enter the 6-digit OTP sent to your registered email address.
                @else
                    Create a strong new password for your account.
                @endif
            </p>
        </div>

        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-6">
            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold transition-colors
                    {{ $step >= 1 ? 'bg-[#0B132B] text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                    1
                </div>
            </div>

            <div class="w-12 sm:w-16 h-0.5 mx-2 transition-colors
                {{ $step >= 2 ? 'bg-[#0B132B]' : 'bg-slate-200' }}">
            </div>

            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold transition-colors
                    {{ $step >= 2 ? 'bg-[#0B132B] text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                    2
                </div>
            </div>

            <div class="w-12 sm:w-16 h-0.5 mx-2 transition-colors
                {{ $step >= 3 ? 'bg-[#0B132B]' : 'bg-slate-200' }}">
            </div>

            <!-- Step 3 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold transition-colors
                    {{ $step >= 3 ? 'bg-[#0B132B] text-white shadow-sm' : 'bg-slate-100 text-slate-400' }}">
                    3
                </div>
            </div>
        </div>

        <!-- Step Labels -->
        <div class="flex justify-between text-[11px] font-semibold text-slate-400 mb-6 px-1">
            <span class="{{ $step == 1 ? 'text-slate-900 font-bold' : '' }}">
                Email
            </span>

            <span class="{{ $step == 2 ? 'text-slate-900 font-bold' : '' }}">
                Verification
            </span>

            <span class="{{ $step == 3 ? 'text-slate-900 font-bold' : '' }}">
                New Password
            </span>
        </div>

        <!-- Session Status Alert (Single Instance) -->
        @if (session('status'))
            <div class="mb-5 flex items-start gap-2.5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs leading-relaxed">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs leading-relaxed">
                <div class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- STEP 1: EMAIL -->
        @if($step == 1)
            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Email Address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="rahulsharma@email.com"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full text-xs px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                    >

                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-[10px]" />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer"
                >
                    Send OTP
                </button>
            </form>
        @endif

        <!-- STEP 2: VERIFICATION CODE -->
        @if($step == 2)
            @if(session('reset_email'))
                <div class="mb-4 flex items-center justify-between p-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                    <div class="flex items-center gap-1.5 text-slate-600 truncate">
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate font-medium">{{ session('reset_email') }}</span>
                    </div>
                    <a href="{{ route('password.request', ['reset' => 1]) }}" class="text-[11px] font-bold text-indigo-600 hover:underline shrink-0 ml-2">
                        Change
                    </a>
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify.otp') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="otp" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Verification Code
                    </label>

                    <input
                        id="otp"
                        type="text"
                        name="otp"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        placeholder="Enter 6-digit OTP"
                        required
                        autofocus
                        class="w-full text-center tracking-[0.45em] text-base font-bold px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition"
                    >

                    <p class="text-[11px] text-slate-400 mt-2">
                        Check your email inbox and enter the 6-digit verification code.
                    </p>

                    <x-input-error :messages="$errors->get('otp')" class="mt-1.5 text-[10px]" />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer"
                >
                    Verify OTP
                </button>
            </form>

            @if(session('reset_email'))
                <form method="POST" action="{{ route('password.email') }}" class="mt-4 text-center">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('reset_email') }}">
                    <p class="text-xs text-slate-500">
                        Didn't receive the OTP?
                        <button type="submit" class="font-bold text-indigo-600 hover:underline cursor-pointer bg-transparent border-0 p-0 text-xs">
                            Resend OTP
                        </button>
                    </p>
                </form>
            @endif
        @endif

        <!-- STEP 3: NEW PASSWORD -->
        @if($step == 3)
            <form
                method="POST"
                action="{{ route('password.update.otp') }}"
                class="space-y-4"
                x-data="{ showPass: false, showConfirm: false }"
            >
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') }}">

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                        New Password
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            placeholder="••••••••••••"
                            required
                            autofocus
                            autocomplete="new-password"
                            class="w-full text-xs px-3.5 py-3 pr-10 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                        >
                        <button
                            type="button"
                            @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
                        >
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-[10px]" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Confirm New Password
                    </label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            placeholder="••••••••••••"
                            required
                            autocomplete="new-password"
                            class="w-full text-xs px-3.5 py-3 pr-10 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                        >
                        <button
                            type="button"
                            @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1 cursor-pointer"
                        >
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-[10px]" />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors cursor-pointer"
                >
                    Update Password
                </button>
            </form>
        @endif

        <!-- Back to Login -->
        <div class="text-center mt-6 pt-5 border-t border-slate-100">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 hover:underline transition"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Login
            </a>
        </div>

    </div>
</x-guest-layout>