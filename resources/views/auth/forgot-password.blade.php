<x-guest-layout>
    <div class="w-full max-w-md mx-auto p-6 sm:p-7 bg-white rounded-3xl shadow-xl border border-slate-100">

        <!-- Brand Header -->
        <div class="text-center mb-7">
            <img
                src="{{ asset('storage/logo.png') }}"
                alt="REIAC Logo"
                class="h-16 w-auto mx-auto object-contain"
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
                    Enter the OTP sent to your registered email address.
                @else
                    Create a strong new password for your account.
                @endif
            </p>
        </div>

        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-7">

            <!-- Step 1 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                    {{ $step >= 1 ? 'bg-[#0B132B] text-white' : 'bg-slate-100 text-slate-400' }}">
                    1
                </div>
            </div>

            <div class="w-12 sm:w-16 h-px mx-2
                {{ $step >= 2 ? 'bg-[#0B132B]' : 'bg-slate-200' }}">
            </div>

            <!-- Step 2 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                    {{ $step >= 2 ? 'bg-[#0B132B] text-white' : 'bg-slate-100 text-slate-400' }}">
                    2
                </div>
            </div>

            <div class="w-12 sm:w-16 h-px mx-2
                {{ $step >= 3 ? 'bg-[#0B132B]' : 'bg-slate-200' }}">
            </div>

            <!-- Step 3 -->
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                    {{ $step >= 3 ? 'bg-[#0B132B] text-white' : 'bg-slate-100 text-slate-400' }}">
                    3
                </div>
            </div>

        </div>

        <!-- Step Labels -->
        <div class="flex justify-between text-[10px] font-semibold text-slate-400 mb-6 px-1">
            <span class="{{ $step == 1 ? 'text-slate-900' : '' }}">
                Email
            </span>

            <span class="{{ $step == 2 ? 'text-slate-900' : '' }}">
                Verification
            </span>

            <span class="{{ $step == 3 ? 'text-slate-900' : '' }}">
                New Password
            </span>
        </div>

        <!-- Session Status -->
        <x-auth-session-status
            class="mb-5"
            :status="session('status')"
        />
        <!-- Session Status -->
        <x-auth-session-status
            class="mb-5"
            :status="session('status')"
        />

        <!-- Step 1: Email -->
        @if($step == 1)

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label
                        for="email"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
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

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Send OTP
                </button>
            </form>

        @endif

        <!-- Step 2: OTP -->
        @if($step == 2)

            <form method="POST" action="{{ route('password.verify.otp') }}" class="space-y-5">
                @csrf

                <div>
                    <label
                        for="otp"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
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

                    <p class="text-[10px] text-slate-400 mt-2">
                        Check your email inbox and enter the 6-digit verification code.
                    </p>

                    <x-input-error
                        :messages="$errors->get('otp')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Verify OTP
                </button>
            </form>

        @endif

        <!-- Step 3: New Password -->
        @if($step == 3)

            <form method="POST" action="{{ route('password.update.otp') }}" class="space-y-5">
                @csrf
        <!-- Step 1: Email -->
        @if($step == 1)

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label
                        for="email"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
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

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Send OTP
                </button>
            </form>

        @endif

        <!-- Step 2: OTP -->
        @if($step == 2)

            <form method="POST" action="{{ route('password.verify.otp') }}" class="space-y-5">
                @csrf

                <div>
                    <label
                        for="otp"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
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

                    <p class="text-[10px] text-slate-400 mt-2">
                        Check your email inbox and enter the 6-digit verification code.
                    </p>

                    <x-input-error
                        :messages="$errors->get('otp')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Verify OTP
                </button>
            </form>

        @endif

        <!-- Step 3: New Password -->
        @if($step == 3)

            <form method="POST" action="{{ route('password.update.otp') }}" class="space-y-5">
                @csrf

                <!-- New Password -->
                <div>
                    <label
                        for="password"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
                        New Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        autofocus
                        autocomplete="new-password"
                        class="w-full text-xs px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                    >

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label
                        for="password_confirmation"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
                        Confirm New Password
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                        required
                        autocomplete="new-password"
                        class="w-full text-xs px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
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
                <svg
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>

                Back to Login
            </a>
                <!-- New Password -->
                <div>
                    <label
                        for="password"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
                        New Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter new password"
                        required
                        autofocus
                        autocomplete="new-password"
                        class="w-full text-xs px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                    >

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-1.5 text-[10px]"
                    />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label
                        for="password_confirmation"
                        class="block text-xs font-bold text-slate-700 mb-1.5"
                    >
                        Confirm New Password
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm new password"
                        required
                        autocomplete="new-password"
                        class="w-full text-xs px-3.5 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium transition"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
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
                <svg
                    class="w-3.5 h-3.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>

                Back to Login
            </a>
        </div>

    </div>
    </div>
</x-guest-layout>