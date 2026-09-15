@props([
    'title' => 'Edit Profile | REIAC Community',
    'active' => 'profile',
    'user' => auth()->user(),
    'profile' => null,
    'countries' => [],
    'topContributors' => [],
    'notificationsCount' => 0,
])

@php
    $profile = $profile ?? $user?->profile;

    $avatarUrl = $profile?->avatar
        ? asset('storage/' . $profile->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=e2e8f0&color=0f172a&size=200';

    $coverUrl = $profile?->cover_image
        ? asset('storage/' . $profile->cover_image)
        : 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1200&h=400&fit=crop';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white">

    {{-- TOPBAR --}}
    <x-community.topbar :notifications-count="$notificationsCount" />

    {{-- MAIN CONTAINER WITH GENEROUS BOTTOM PADDING FOR MOBILE BOTTOM NAV CLEARANCE --}}
    <div class="max-w-[1520px] mx-auto px-3 sm:px-6 lg:px-8 pt-3 sm:pt-6 pb-32 sm:pb-24 lg:pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">

            {{-- DESKTOP SIDEBAR --}}
            <x-community.sidebar :top-contributors="$topContributors" />

            {{-- MAIN EDIT PROFILE FORM --}}
            <main class="min-w-0 w-full space-y-4 sm:space-y-5">
                {{-- BACK BUTTON --}}
                <div class="flex items-center justify-between">
                    <a
                        href="{{ route('community.profile', $profile?->username ?? auth()->user()->username ?? '') }}"
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition py-1"
                    >
                        <svg
                            class="w-4 h-4 shrink-0"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        <span>Back to profile</span>
                    </a>
                </div>

                {{-- CARD --}}
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-6 lg:p-8 overflow-hidden">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-[#0b1329] tracking-tight">
                            Edit Profile
                        </h1>
                        <p class="text-xs text-slate-500 mt-1">
                            Update your personal details, profile pictures, and community bio.
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                            <p class="font-bold mb-1">Please fix the following errors:</p>
                            <ul class="list-disc pl-4 space-y-0.5 font-normal">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('community.profile.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-6"
                    >
                        @csrf
                        @method('PATCH')

                        {{-- AVATAR & COVER UPLOAD SECTION --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 pb-6 border-b border-slate-100">
                            {{-- Avatar --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Avatar Photo
                                </label>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 p-3 bg-slate-50/70 border border-slate-200/70 rounded-xl">
                                    <div class="relative shrink-0">
                                        <img
                                            id="avatar-preview"
                                            src="{{ $avatarUrl }}"
                                            alt="Avatar preview"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200"
                                        >
                                    </div>
                                    <div class="min-w-0 w-full flex-1">
                                        <input
                                            type="file"
                                            id="avatar-input"
                                            name="avatar"
                                            accept="image/*"
                                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-slate-900 hover:file:bg-amber-200 cursor-pointer transition"
                                        >
                                        <p class="text-[11px] text-slate-400 mt-1">Square image (PNG, JPG, WEBP max 2MB)</p>
                                    </div>
                                </div>
                                @error('avatar')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cover Image --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Cover Banner
                                </label>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 p-3 bg-slate-50/70 border border-slate-200/70 rounded-xl">
                                    <div class="w-full sm:w-28 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                        <img
                                            id="cover-preview"
                                            src="{{ $coverUrl }}"
                                            alt="Cover preview"
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    <div class="min-w-0 w-full flex-1">
                                        <input
                                            type="file"
                                            id="cover-input"
                                            name="cover_image"
                                            accept="image/*"
                                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-slate-900 hover:file:bg-amber-200 cursor-pointer transition"
                                        >
                                        <p class="text-[11px] text-slate-400 mt-1">Landscape image 1200x400 (max 5MB)</p>
                                    </div>
                                </div>
                                @error('cover_image')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- NAME & USERNAME --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Full Name <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none transition"
                                    required
                                >
                                @error('name')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Username <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs text-slate-400 font-semibold select-none">@</span>
                                    <input
                                        type="text"
                                        name="username"
                                        value="{{ old('username', $profile?->username) }}"
                                        class="w-full rounded-xl border border-slate-200 pl-8 pr-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none transition"
                                        required
                                    >
                                </div>
                                @error('username')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- COUNTRY & LOCATION --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Country
                                </label>
                                <select
                                    name="country_id"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none bg-white transition"
                                >
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option
                                            value="{{ $country->id }}"
                                            {{ old('country_id', $profile?->country_id) == $country->id ? 'selected' : '' }}
                                        >
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    City / Location
                                </label>
                                <input
                                    type="text"
                                    name="location"
                                    placeholder="e.g. Toronto, Canada"
                                    value="{{ old('location', $profile?->location) }}"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none transition"
                                >
                                @error('location')
                                    <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- BIO --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Bio
                                </label>
                                <span class="text-[11px] text-slate-400">Up to 500 characters</span>
                            </div>
                            <textarea
                                name="bio"
                                rows="4"
                                placeholder="Tell us about yourself, your study or career goals, and your interests..."
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none transition"
                            >{{ old('bio', $profile?->bio) }}</textarea>
                            @error('bio')
                                <p class="text-rose-500 text-xs font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ACTIONS --}}
                        <div class="grid grid-cols-2 sm:flex sm:items-center sm:justify-end gap-3 pt-5 border-t border-slate-100">
                            <a
                                href="{{ route('community.profile', $profile?->username ?? auth()->user()->username ?? '') }}"
                                class="w-full sm:w-auto px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition text-center flex items-center justify-center cursor-pointer"
                            >
                                Cancel
                            </a>
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs transition shadow-sm cursor-pointer text-center flex items-center justify-center font-bold"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- DEDICATED EXTRA SCROLL CLEARANCE SPACER FOR MOBILE BOTTOM NAVIGATION --}}
                <div class="h-24 sm:h-12 lg:hidden" aria-hidden="true"></div>
            </main>
        </div>
    </div>

    {{-- LIVE IMAGE PREVIEW SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');
            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', (e) => {
                    const file = e.target.files?.[0];
                    if (file) {
                        avatarPreview.src = URL.createObjectURL(file);
                    }
                });
            }

            const coverInput = document.getElementById('cover-input');
            const coverPreview = document.getElementById('cover-preview');
            if (coverInput && coverPreview) {
                coverInput.addEventListener('change', (e) => {
                    const file = e.target.files?.[0];
                    if (file) {
                        coverPreview.src = URL.createObjectURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>