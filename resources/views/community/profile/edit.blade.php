@props([
    'title' => 'Edit Profile | REIAC',
    'active' => 'profile',
    'user' => auth()->user(),
    'profile' => null,
    'countries' => [],
    'topContributors' => [],
    'notificationsCount' => 0,
])

@php
    $avatarUrl = $profile?->avatar
        ? asset('storage/' . $profile->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=e2e8f0&color=0f172a&size=200';

    $coverUrl = $profile?->cover_image
        ? asset('storage/' . $profile->cover_image)
        : 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1200&h=400&fit=crop';
@endphp

@vite(['resources/css/app.css', 'resources/js/app.js'])

<div class="min-h-screen bg-slate-100/60 text-slate-800 antialiased selection:bg-amber-400 selection:text-slate-950">
    <div class="sticky top-0 z-50">
        <x-community.topbar :notifications-count="$notificationsCount" :user="$user" />
    </div>

    <div class="max-w-[1520px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-start">
            
            <x-community.sidebar :top-contributors="$topContributors" />

            <main class="min-w-0 w-full space-y-6">
                <div>
                    <a
                        href="{{ route('community.profile', $profile->username) }}"
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

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 overflow-hidden p-6 sm:p-8">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#0b1329] tracking-tight mb-6">
                        Edit Profile
                    </h1>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                            {{ session('success') }}
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Avatar Image
                                </label>
                                <div class="flex items-center gap-4">
                                    <img
                                        src="{{ $avatarUrl }}"
                                        alt="Avatar preview"
                                        class="w-16 h-16 rounded-full object-cover border border-slate-200 shadow-sm"
                                    >
                                    <input
                                        type="file"
                                        name="avatar"
                                        accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer"
                                    >
                                </div>
                                @error('avatar')
                                    <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Cover Image
                                </label>
                                <div class="flex items-center gap-4">
                                    <div class="w-24 h-12 rounded-lg overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                        <img
                                            src="{{ $coverUrl }}"
                                            alt="Cover preview"
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    <input
                                        type="file"
                                        name="cover_image"
                                        accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer"
                                    >
                                </div>
                                @error('cover_image')
                                    <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"
                                    required
                                >
                                @error('name')
                                    <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Username
                                </label>
                                <input
                                    type="text"
                                    name="username"
                                    value="{{ old('username', $profile?->username) }}"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"
                                    required
                                >
                                @error('username')
                                    <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                           

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                    Country
                                </label>
                                <select
                                    name="country_id"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none bg-white"
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
                                    <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Bio
                            </label>
                            <textarea
                                name="bio"
                                rows="4"
                                placeholder="Tell us about yourself..."
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs text-slate-800 focus:border-amber-400 focus:ring-1 focus:ring-amber-400 outline-none"
                            >{{ old('bio', $profile?->bio) }}</textarea>
                            @error('bio')
                                <p class="text-red-500 text-[10px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                           <a
    href="{{ route('community.profile', $profile->username) }}"
    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition"
>
    Cancel
</a>
                            <button
                                type="submit"
                                class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs transition shadow-sm cursor-pointer"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</div>