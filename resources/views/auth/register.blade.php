<x-guest-layout>
    <div class="w-full max-w-md mx-auto p-6 bg-white rounded-3xl shadow-xl border border-slate-100">

        <!-- Brand Header -->
        <div class="text-center mb-6">
            <img
                src="{{ asset('storage/logo.png') }}"
                alt="REIAC Logo"
                class="h-16 w-auto mx-auto object-contain"
            >

            <h3 class="text-xl font-bold text-slate-900 mt-4">
                Create Your Account
            </h3>

            <p class="text-xs text-slate-500 mt-1">
                Join REIAC Community today!
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label
                    for="name"
                    class="block text-xs font-bold text-slate-700 mb-1"
                >
                    Full Name
                </label>

                <input
                    id="name"
                    class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
                    type="text"
                    name="name"
                    :value="old('name')"
                    placeholder="Rahul Sharma"
                    required
                    autofocus
                    autocomplete="name"
                >

                <x-input-error
                    :messages="$errors->get('name')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Email Address -->
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
                    autocomplete="username"
                >

                <x-input-error
                    :messages="$errors->get('email')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Country -->
            <div>
                <label
                    for="country_id"
                    class="block text-xs font-bold text-slate-700 mb-1"
                >
                    Country
                </label>

                <select
                    id="country_id"
                    name="country_id"
                    class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
                    required
                >
                    <option value="">Select Country</option>

                    @foreach(\App\Models\Country::all() as $country)
                        <option
                            value="{{ $country->id }}"
                            {{ old('country_id') == $country->id ? 'selected' : '' }}
                        >
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                <x-input-error
                    :messages="$errors->get('country_id')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Password -->
            <div>
                <label
                    for="password"
                    class="block text-xs font-bold text-slate-700 mb-1"
                >
                    Password
                </label>

                <input
                    id="password"
                    class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
                    type="password"
                    name="password"
                    placeholder="••••••••••••"
                    required
                    autocomplete="new-password"
                >

                <x-input-error
                    :messages="$errors->get('password')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Confirm Password -->
            <div>
                <label
                    for="password_confirmation"
                    class="block text-xs font-bold text-slate-700 mb-1"
                >
                    Confirm Password
                </label>

                <input
                    id="password_confirmation"
                    class="w-full text-xs px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••••••"
                    required
                    autocomplete="new-password"
                >

                <x-input-error
                    :messages="$errors->get('password_confirmation')"
                    class="mt-1 text-[10px]"
                />
            </div>

            <!-- Terms & Conditions -->
            <div class="flex items-start">
                <label class="inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="terms"
                        id="terms"
                        value="1"
                        required
                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 mt-0.5"
                    >

                    <span class="ms-2 text-xs font-medium text-slate-600">
                        I agree to the

                        <button
                            type="button"
                            onclick="openTermsModal()"
                            class="text-indigo-600 hover:underline font-bold focus:outline-none"
                        >
                            Terms & Conditions and Privacy Policy
                        </button>.
                    </span>
                </label>
            </div>

            <x-input-error
                :messages="$errors->get('terms')"
                class="mt-1 text-[10px]"
            />

            <!-- Optional Updates -->
            <div class="flex items-start">
                <label class="inline-flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        name="announcements"
                        id="announcements"
                        value="1"
                        class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4 mt-0.5"
                    >

                    <span class="ms-2 text-xs font-medium text-slate-600">
                        I would like to receive important community updates and announcements.

                        <span class="text-slate-400 font-normal">
                            (Optional)
                        </span>
                    </span>
                </label>
            </div>

            <!-- Register Button -->
            <div>
                <button
                    type="submit"
                    class="w-full py-3 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Create Account
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-4">
                <p class="text-xs text-slate-500">
                    Already have an account?

                    <a
                        href="{{ route('login') }}"
                        class="font-bold text-indigo-600 hover:underline"
                    >
                        Login
                    </a>
                </p>
            </div>
        </form>
    </div>

    <!-- Terms & Conditions Modal -->
    <div
        id="termsModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden p-4"
    >
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[85vh]">

            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">
                    REIAC Community – Registration Consent
                </h3>

                <button
                    type="button"
                    onclick="closeTermsModal()"
                    class="text-slate-400 hover:text-slate-600 p-1 rounded-lg focus:outline-none"
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

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-4 text-xs text-slate-600 leading-relaxed">

                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">
                        Terms & Conditions
                    </h4>

                    <p>
                        By joining REIAC Community, you agree to use the community respectfully and legally, provide accurate information, and not post illegal, harmful, abusive, or misleading content. REIAC may remove content or suspend accounts that violate these rules.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-1">
                        Privacy Policy
                    </h4>

                    <p>
                        We collect basic information such as your name, email, country, and profile details to provide and improve our community services. We handle your information securely, do not sell your personal information, and may use it for account management, security, and important service notifications.
                    </p>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                <button
                    type="button"
                    onclick="closeTermsModal()"
                    class="px-5 py-2.5 bg-[#0B132B] hover:bg-[#1C2541] text-white text-xs font-bold rounded-xl shadow-md transition-colors"
                >
                    Got it
                </button>
            </div>

        </div>
    </div>

    <!-- Modal Script -->
    <script>
        function openTermsModal() {
            document.getElementById('termsModal').classList.remove('hidden');
        }

        function closeTermsModal() {
            document.getElementById('termsModal').classList.add('hidden');
        }

        window.onclick = function (event) {
            const modal = document.getElementById('termsModal');

            if (event.target === modal) {
                closeTermsModal();
            }
        };
    </script>
</x-guest-layout>