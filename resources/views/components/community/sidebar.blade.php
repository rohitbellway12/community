@props([
    'topContributors' => collect(),
    'categories' => collect(),
    'tags' => collect(),
])

@php
    $joinedGroups = \App\Models\Group::query()
        ->where(function ($query) {
            $query->where('owner_id', auth()->id())->orWhereHas('users', function ($userQuery) {
                $userQuery->where('users.id', auth()->id())
                    ->where('group_user.status', 'active');
            });
        })
        ->with(['users:id,name'])
        ->orderBy('name')
        ->get();
@endphp

{{-- LEFT SIDEBAR (Desktop) --}}
<aside
    class="hidden lg:block sticky top-20 max-h-[calc(100vh-5rem)] overflow-y-auto space-y-6 pb-6 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
    x-data="{
        showLoginPrompt: false,
        selectedGroup: '{{ request('group') }}'
    }">

    <div class="bg-[#0c1b33] text-white p-6 rounded-2xl shadow-sm relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-600/10 rounded-full blur-2xl pointer-events-none">
        </div>
        <h2 class="text-xl font-bold tracking-tight">REIAC Community</h2>
        <p class="text-xs text-amber-400 font-medium mt-1">Connect. Share. Get Solutions.</p>
        <p class="text-[12px] text-slate-300 leading-relaxed mt-3">Ask questions, share experiences and help others in
            their journey.</p>

        @auth
            <button @click.prevent="$dispatch('open-post-modal')" type="button"
                class="w-full mt-5 bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-amber-400/10 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Post
            </button>
        @else
            <button @click.prevent="$dispatch('open-login-modal')" type="button"
                class="w-full mt-5 bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-xs py-2.5 rounded-xl transition flex items-center justify-center gap-1.5 shadow-md shadow-amber-400/10 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Post
            </button>
        @endauth
    </div>

 {{-- JOINED GROUPS & MANAGEMENT SECTION --}}
@auth
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/70 space-y-3">

        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-900 tracking-tight uppercase">
                My Groups
            </span>

            <button
                @click="$dispatch('open-create-group-modal')"
                type="button"
                class="text-[11px] font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Group
            </button>
        </div>

        <div class="space-y-2">

            {{-- Group Selector --}}
            <div class="relative">
                <select
                    x-model="selectedGroup"
                    @change="if(selectedGroup) {
                        window.location.href = '{{ route('community.index') }}?group=' + selectedGroup
                    } else {
                        window.location.href = '{{ route('community.index') }}'
                    }"
                    class="w-full text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50 cursor-pointer appearance-none">

                    <option value="">
                        All Community Posts (Public)
                    </option>

                    @foreach ($joinedGroups as $group)
                        <option value="{{ $group->slug ?? $group->id }}">
                            {{ $group->name }}
                        </option>
                    @endforeach

                </select>

                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>


            {{-- Group Management List --}}
            @if ($joinedGroups->isNotEmpty())

                <div class="pt-2 border-t border-slate-100 space-y-1.5">

                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            Manage Your Groups
                        </span>

                        {{-- View All Groups --}}
                        <a href="{{ route('community.groups.index') }}"
                            class="text-[10px] font-bold text-amber-600 hover:text-amber-700 transition flex items-center gap-1">
                            View All Groups
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </a>
                    </div>


                    {{-- ONLY LATEST 5 GROUPS --}}
                    @foreach ($joinedGroups->take(5) as $group)

                        @php
                            $isOwner =
                                $group->owner_id === auth()->id() ||
                                optional($group->pivot)->role === 'owner' ||
                                optional($group->pivot)->role == 2;
                        @endphp

                        <div
                            class="flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 px-2.5 py-1.5 rounded-xl border border-slate-200/60 transition">

                            {{-- Group Name --}}
                            <span
                                class="text-xs font-medium text-slate-800 truncate max-w-[140px]"
                                title="{{ $group->name }}">
                                {{ $group->name }}
                            </span>


                            @if ($isOwner)

                                {{-- 3-Dot Options --}}
                                <div class="relative" x-data="{ open: false }">

                                    <button
                                        @click="open = !open"
                                        type="button"
                                        class="p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200/60 transition cursor-pointer focus:outline-none"
                                        aria-label="Group Options">

                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                        </svg>

                                    </button>


                                    <div
                                        x-show="open"
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-1 w-28 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-50 text-xs"
                                        style="display: none;">

                                        {{-- Edit --}}
                                        <button
                                            type="button"
                                            @click="open = false; $dispatch('open-edit-group-modal', {
                                                id: {{ $group->id }},
                                                slug: @js($group->slug),
                                                name: @js($group->name),
                                                description: @js($group->description),
                                                members: @js($group->users->pluck('id'))
                                            })"
                                            class="w-full text-left px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-100 flex items-center gap-1.5 transition cursor-pointer">

                                            <svg class="w-3.5 h-3.5 text-blue-500"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>

                                            Edit
                                        </button>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('community.groups.destroy', $group) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this group?');">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-full text-left px-3 py-1.5 font-medium text-red-600 hover:bg-red-50 flex items-center gap-1.5 transition cursor-pointer">

                                                <svg class="w-3.5 h-3.5 text-red-500"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>

                                                Delete
                                            </button>

                                        </form>

                                    </div>
                                </div>

                            @else

                                {{-- Leave Button --}}
                                <form
                                    action="{{ route('community.groups.leave', $group) }}"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure you want to leave this group?');">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-[11px] font-bold text-red-500 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded-lg transition cursor-pointer flex items-center gap-1"
                                        title="Leave Group">

                                        <svg class="w-3.5 h-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>

                                        Leave
                                    </button>

                                </form>

                            @endif

                        </div>

                    @endforeach


                    {{-- Show View All only when there are more than 5 --}}
                    @if ($joinedGroups->count() > 5)

                        <a href="{{ route('community.groups.index') }}"
                            class="mt-2 w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-[11px] font-bold text-slate-600 hover:text-amber-600 transition">

                            <span>
                                View All Groups
                            </span>

                            <span class="text-[10px] text-slate-400">
                                ({{ $joinedGroups->count() }})
                            </span>

                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5l7 7-7 7">
                                </path>
                            </svg>

                        </a>

                    @endif

                </div>

            @endif

        </div>
    </div>
@endauth

    <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200/70">
        <nav class="space-y-0.5 text-[13px] font-medium text-slate-600">
            <a href="{{ route('community.index') }}"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ !request('category') && !request()->routeIs('community.posts.show') && !request('group') ? 'bg-slate-100 text-slate-900 font-semibold' : 'hover:bg-slate-50 transition' }}">
                <svg class="w-4 h-4 {{ !request('category') && !request()->routeIs('community.posts.show') && !request('group') ? 'text-amber-500' : 'text-slate-400' }}"
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Community Home
            </a>
           
            <a href="{{ route('community.index', ['category' => 'general']) }}"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request('category') === 'general' ? 'bg-slate-100 text-slate-900 font-semibold' : 'hover:bg-slate-50 transition' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
                General
            </a>
            <a href="{{ route('community.index', ['category' => 'ask-a-solution']) }}"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request('category') === 'ask-a-solution' ? 'bg-slate-100 text-slate-900 font-semibold' : 'hover:bg-slate-50 transition' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                Ask a Solution
            </a>
            <a href="{{ route('community.index', ['category' => 'jobs-info']) }}"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request('category') === 'jobs-info' ? 'bg-slate-100 text-slate-900 font-semibold' : 'hover:bg-slate-50 transition' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                Jobs Info
            </a>
            <div class="border-t border-slate-100 my-1"></div>
            <a href="{{ route('community.index', ['sort' => 'trending']) }}"
                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl {{ request('sort') === 'trending' ? 'bg-slate-100 text-slate-900 font-semibold' : 'hover:bg-slate-50 transition' }}">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Trending
            </a>
         
        </nav>
    </div>

    {{-- TOP CONTRIBUTORS SECTION --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70">
        <div class="flex items-center gap-1.5 font-bold text-slate-900 text-[13px] mb-4">
            Top Contributor
            <svg class="w-4 h-4 fill-amber-400 text-amber-500" viewBox="0 0 24 24">
                <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
        </div>

        <div class="space-y-3.5">
            @forelse($topContributors ?? [] as $index => $contributor)
                <div class="flex items-center justify-between text-[13px]">
                    @php
                        $cProfile = $contributor->profile;
                        $cName = $contributor->name ?: 'User';
                        $cDefault = 'https://ui-avatars.com/api/?name=' . urlencode($cName) . '&background=0c1b33&color=fff&size=100';
                        $cAvatar = null;
                        $cRaw = trim((string)($cProfile?->avatar ?? ''));
                        if ($cRaw !== '' && $cRaw !== 'null' && $cRaw !== '0') {
                            if (str_starts_with($cRaw, 'http://') || str_starts_with($cRaw, 'https://')) {
                                $cAvatar = $cRaw;
                            } elseif (str_starts_with($cRaw, 'storage/')) {
                                $cAvatar = asset($cRaw);
                            } else {
                                $cAvatar = asset('storage/' . ltrim($cRaw, '/'));
                            }
                        }
                        if (!$cAvatar) {
                            $cAvatar = $cDefault;
                        }
                    @endphp

                    <a href="{{ $cProfile?->username ? route('community.profile', $cProfile->username) : '#' }}"
                        class="flex items-center gap-2.5 min-w-0 group hover:opacity-90 transition">
                        <span class="font-bold text-slate-400 text-xs w-3 shrink-0">
                            {{ $index + 1 }}
                        </span>

                        <img src="{{ $cAvatar }}"
                            alt="{{ $cName }}"
                            class="w-7 h-7 rounded-full object-cover shrink-0 bg-slate-100 ring-1 ring-slate-200 group-hover:ring-amber-400 transition"
                            onerror="this.onerror=null; this.src='{{ $cDefault }}';">

                        <span class="font-semibold text-slate-800 text-xs truncate group-hover:text-amber-600 transition">
                            {{ $cName }}
                        </span>
                    </a>

                    <span class="text-amber-500 font-bold text-xs flex items-center gap-0.5 shrink-0">
                        <svg class="w-3 h-3 fill-amber-400" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                        {{ $contributor->contributor_points ?? ($contributor->posts_count ?? 0) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-xs text-slate-400">
                        No contributors yet.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</aside>

@auth
    @php
        $user = auth()->user();
        $userGroups = $joinedGroups;
    @endphp
    
    <div x-data="createDiscussionModal({{ $errors->any() ? 'true' : 'false' }})"
        @open-post-modal.window="openModal = true; groupId = $event.detail?.groupId || null;"
        x-show="openModal"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/65 backdrop-blur-sm p-4 sm:p-0"
        x-transition.opacity style="display: none;" x-cloak>
        
        <div @click.away="openModal = false"
            class="bg-white w-full max-w-2xl sm:rounded-2xl shadow-2xl flex flex-col max-h-[90vh]" x-transition.scale.95>
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <h3 class="font-bold text-slate-900 text-lg">
                    Create a discussion
                </h3>

                <button type="button" @click="openModal = false"
                    class="p-2 -mr-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('community.posts.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col overflow-hidden h-full">
                @csrf

                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 flex flex-col gap-4">
                    @if ($errors->any())
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-semibold space-y-1">
                            @foreach ($errors->all() as $error)
                                <div>• {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    @php
                        $modalProfile = $user?->profile;
                        $mName = $user?->name ?: 'User';
                        $mDefault = 'https://ui-avatars.com/api/?name=' . urlencode($mName) . '&background=0c1b33&color=fff&size=100';
                        $modalAvatar = null;
                        $mRaw = trim((string)($modalProfile?->avatar ?? ''));
                        if ($mRaw !== '' && $mRaw !== 'null' && $mRaw !== '0') {
                            if (str_starts_with($mRaw, 'http://') || str_starts_with($mRaw, 'https://')) {
                                $modalAvatar = $mRaw;
                            } elseif (str_starts_with($mRaw, 'storage/')) {
                                $modalAvatar = asset($mRaw);
                            } else {
                                $modalAvatar = asset('storage/' . ltrim($mRaw, '/'));
                            }
                        }
                        if (!$modalAvatar) {
                            $modalAvatar = $mDefault;
                        }
                    @endphp

                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <img src="{{ $modalAvatar }}" alt="{{ $mName }}"
                                class="w-12 h-12 rounded-full object-cover bg-slate-100 ring-1 ring-slate-200"
                                onerror="this.onerror=null; this.src='{{ $mDefault }}';">

                            <div>
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $user?->name }}
                                </div>

                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    {{-- CATEGORY DROPDOWN --}}
                                    <div class="relative inline-block">
                                        <select name="category_id" required
                                            class="appearance-none bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-1.5 pl-3 pr-8 rounded-full border-0 focus:ring-2 focus:ring-amber-400 cursor-pointer transition shadow-sm">
                                            <option value="" disabled selected>
                                                Select Category
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <svg class="w-3.5 h-3.5 absolute right-2.5 top-2 pointer-events-none text-slate-500"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    {{-- GROUP DROPDOWN --}}
                                    <div class="relative inline-block">
                                        <select name="group_id" x-model="groupId"
                                            class="appearance-none bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-1.5 pl-3 pr-8 rounded-full border-0 focus:ring-2 focus:ring-amber-400 cursor-pointer transition shadow-sm">
                                            <option value="">General Feed (No Group)</option>
                                            @foreach ($userGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-3.5 h-3.5 absolute right-2.5 top-2 pointer-events-none text-slate-500"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    {{-- VISIBILITY DROPDOWN --}}
                                    <div class="relative inline-block">
                                        <select name="visibility" x-model="visibility" :disabled="!groupId"
                                            :class="!groupId ? 'opacity-50 cursor-not-allowed bg-slate-200' : 'bg-slate-100 hover:bg-slate-200 cursor-pointer'"
                                            class="appearance-none text-slate-700 text-xs font-bold py-1.5 pl-3 pr-8 rounded-full border-0 focus:ring-2 focus:ring-amber-400 transition shadow-sm">
                                            <option value="public">Public</option>
                                            <option value="private">Private</option>
                                        </select>
                                        <svg class="w-3.5 h-3.5 absolute right-2.5 top-2 pointer-events-none text-slate-500"
                                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TITLE + CONTENT --}}
                    <div class="flex flex-col gap-2 mt-2 flex-1">
                        <input type="text" name="title" required placeholder="Give your discussion a clear title..."
                            class="w-full text-base font-bold text-slate-900 placeholder-slate-400 border-0 p-0 focus:ring-0 bg-transparent">

                        <textarea name="content" rows="4" required placeholder="What do you want to talk about?"
                            class="w-full text-sm text-slate-800 placeholder-slate-400 border-0 p-0 focus:ring-0 bg-transparent resize-none flex-1 min-h-[100px]"></textarea>
                    </div>

                    {{-- MEDIA PREVIEW --}}
                    <template x-if="previews.length > 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                            <template x-for="(preview, index) in previews" :key="index">
                                <div class="relative group rounded-lg overflow-hidden bg-slate-900 aspect-video flex items-center justify-center border border-slate-200">
                                    <template x-if="preview.type === 'image'">
                                        <img :src="preview.url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="preview.type === 'video'">
                                        <video :src="preview.url" class="w-full h-full object-cover" controls></video>
                                    </template>
                                    <button type="button" @click="removeFile(index)"
                                        class="absolute top-1.5 right-1.5 bg-slate-900/80 hover:bg-red-600 text-white p-1 rounded-full transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- TAG PICKER --}}
                    <div class="mt-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Select Relevant Tags (Optional)
                        </label>

                        @if ($tags->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($tags as $tag)
                                    <button type="button" @click="toggleTag({{ $tag->id }})"
                                        :class="selectedTags.includes({{ $tag->id }}) ?
                                            'bg-[#0b1329] text-white border-[#0b1329]' :
                                            'bg-white text-slate-700 border-slate-200 hover:border-slate-300'"
                                        class="text-xs font-semibold px-3.5 py-1.5 rounded-full border transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer">
                                        <span x-text="selectedTags.includes({{ $tag->id }}) ? '✓' : '+'" class="text-[10px]"></span>
                                        {{ $tag->name }}
                                    </button>
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden"
                                        :checked="selectedTags.includes({{ $tag->id }})">
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">No tags available</span>
                        @endif
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 shrink-0 bg-white">
                    <div class="flex items-center gap-2">
                        <label class="relative p-2.5 rounded-full hover:bg-slate-100 cursor-pointer transition group" title="Add Media">
                            <svg class="w-6 h-6 text-slate-500 group-hover:text-slate-700" fill="none"
                                stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z">
                                </path>
                            </svg>
                            <input x-ref="createMediaInput" type="file" name="media[]" @change="handleFiles($event)" multiple
                                accept="image/*,video/*" class="hidden">
                        </label>
                        <span class="text-[10px] font-medium text-slate-400">Max 100MB per file</span>
                    </div>

                    <button type="submit"
                        class="bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-sm px-6 py-2.5 rounded-full transition shadow-sm cursor-pointer">
                        Publish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function createDiscussionModal(hasErrors = false) {
            return {
                openModal: hasErrors,
                groupId: null,
                visibility: 'public',
                files: [],
                previews: [],
                selectedTags: [],

                init() {
                    this.$watch('groupId', value => {
                        if (!value) {
                            this.visibility = 'public';
                        }
                    });
                },

                handleFiles(e) {
                    const incoming = Array.from(e.target.files || []);
                    if (!incoming.length) return;

                    const MAX_SIZE = 100 * 1024 * 1024; // 100MB
                    for (const file of incoming) {
                        if (file.size > MAX_SIZE) {
                            alert('"' + file.name + '" exceeds the 100MB maximum file limit.');
                            return;
                        }
                    }

                    // Revoke old blob URLs
                    this.previews.forEach(p => {
                        if (p.url && p.url.startsWith('blob:')) {
                            URL.revokeObjectURL(p.url);
                        }
                    });

                    this.files = incoming;
                    this.previews = incoming.map(file => ({
                        url: URL.createObjectURL(file),
                        type: (file.type && file.type.startsWith('video/')) || /\.(mp4|mov|avi|webm|mkv|m4v)$/i.test(file.name) ? 'video' : 'image',
                        name: file.name
                    }));
                },

                removeFile(index) {
                    const p = this.previews[index];
                    if (p && p.url && p.url.startsWith('blob:')) {
                        URL.revokeObjectURL(p.url);
                    }
                    this.files.splice(index, 1);
                    this.previews.splice(index, 1);

                    const input = this.$refs.createMediaInput;
                    if (input && typeof DataTransfer !== 'undefined') {
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    }
                },

                toggleTag(id) {
                    if (this.selectedTags.includes(id)) {
                        this.selectedTags = this.selectedTags.filter(tag => tag !== id);
                    } else {
                        this.selectedTags.push(id);
                    }
                }
            };
        }
    </script>
@endauth

{{-- GLOBAL CREATE GROUP MODAL --}}
@auth
    @php
        $editUsersList = auth()
            ->user()
            ->followers()
            ->with('profile:id,user_id,username,avatar')
            ->select('users.id', 'users.name', 'users.email')
            ->get()
            ->map(function ($user) {
                $uProfile = $user->profile;
                $uName = $user->name ?: 'User';
                $uDefault = 'https://ui-avatars.com/api/?name=' . urlencode($uName) . '&background=0c1b33&color=fff&size=100';
                $uAvatar = null;
                $uRaw = trim((string)($uProfile?->avatar ?? ''));
                if ($uRaw !== '' && $uRaw !== 'null' && $uRaw !== '0') {
                    if (str_starts_with($uRaw, 'http://') || str_starts_with($uRaw, 'https://')) {
                        $uAvatar = $uRaw;
                    } elseif (str_starts_with($uRaw, 'storage/')) {
                        $uAvatar = asset($uRaw);
                    } else {
                        $uAvatar = asset('storage/' . ltrim($uRaw, '/'));
                    }
                }
                if (!$uAvatar) {
                    $uAvatar = $uDefault;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $uProfile?->username,
                    'avatar' => $uAvatar,
                ];
            })
            ->values();
    @endphp
    <div x-data="{
        openGroupModal: false,
        searchMember: '',
        selectedMembers: [],
        usersList: @js($editUsersList),
    
        get filteredUsers() {
            if (!this.searchMember) return this.usersList;
            return this.usersList.filter(user =>
                user.name.toLowerCase().includes(this.searchMember.toLowerCase()) ||
                (user.email && user.email.toLowerCase().includes(this.searchMember.toLowerCase()))
            );
        },
    
        toggleMember(userId) {
            if (this.selectedMembers.includes(userId)) {
                this.selectedMembers = this.selectedMembers.filter(id => id !== userId);
            } else {
                this.selectedMembers.push(userId);
            }
        }
    }" @open-create-group-modal.window="openGroupModal = true" x-show="openGroupModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
        style="display: none;" x-cloak>
        <div @click.away="openGroupModal = false"
            class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-100 transform transition-all my-8 max-h-[90vh] flex flex-col"
            x-transition.scale.95>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Create New Group</h3>
                <button @click="openGroupModal = false" type="button"
                    class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('community.groups.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 pt-4 overflow-y-auto flex-1 pr-1">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Group Name</label>
                    <input type="text" name="name" required placeholder="e.g. Real Estate Investors Hub"
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" placeholder="What is this group about?"
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50"></textarea>
                </div>

                {{-- ADD MEMBERS SECTION --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Add Members</label>
                    <input type="text" x-model="searchMember" placeholder="Search users to add..."
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-amber-400/50">

                    <div
                        class="max-h-40 overflow-y-auto space-y-1.5 bg-slate-50 border border-slate-200/80 rounded-xl p-2">
                        <template x-for="user in filteredUsers" :key="user.id">
                            <div @click="toggleMember(user.id)"
                                class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition"
                                :class="selectedMembers.includes(user.id) ? 'bg-amber-50 border border-amber-200' :
                                    'hover:bg-slate-100 border border-transparent'">
                                <div class="flex items-center gap-2.5">
                                    <img :src="user.avatar" class="w-7 h-7 rounded-full object-cover bg-slate-100 ring-1 ring-slate-200"
                                        x-on:error="$event.target.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=0c1b33&color=fff&size=100'">
                                    <span class="text-xs font-semibold text-slate-800" x-text="user.name"></span>
                                </div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded"
                                    :class="selectedMembers.includes(user.id) ? 'bg-amber-400 text-slate-950' :
                                        'bg-slate-200 text-slate-600'"
                                    x-text="selectedMembers.includes(user.id) ? 'Added' : '+ Add'"></span>
                            </div>
                        </template>

                        <div x-show="filteredUsers.length === 0" class="text-center py-3 text-xs text-slate-400">
                            No users found
                        </div>
                    </div>

                    <template x-for="userId in selectedMembers" :key="userId">
                        <input type="hidden" name="members[]" :value="userId">
                    </template>
                </div>

                <div
                    class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="button" @click="openGroupModal = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-bold rounded-xl transition shadow-md shadow-amber-400/10 cursor-pointer">
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>
@endauth

{{-- GLOBAL EDIT GROUP MODAL --}}
@auth
    @php
        $editUsersListForModal = auth()
            ->user()
            ->followers()
            ->with('profile:id,user_id,username,avatar')
            ->select('users.id', 'users.name', 'users.email')
            ->get()
            ->map(function ($user) {
                $uProfile = $user->profile;
                $uName = $user->name ?: 'User';
                $uDefault = 'https://ui-avatars.com/api/?name=' . urlencode($uName) . '&background=0c1b33&color=fff&size=100';
                $uAvatar = null;
                $uRaw = trim((string)($uProfile?->avatar ?? ''));
                if ($uRaw !== '' && $uRaw !== 'null' && $uRaw !== '0') {
                    if (str_starts_with($uRaw, 'http://') || str_starts_with($uRaw, 'https://')) {
                        $uAvatar = $uRaw;
                    } elseif (str_starts_with($uRaw, 'storage/')) {
                        $uAvatar = asset($uRaw);
                    } else {
                        $uAvatar = asset('storage/' . ltrim($uRaw, '/'));
                    }
                }
                if (!$uAvatar) {
                    $uAvatar = $uDefault;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $uProfile?->username,
                    'avatar' => $uAvatar,
                ];
            })
            ->values();
    @endphp

    <div x-data="{
        openEditModal: false,
        groupSlug: '',
        groupName: '',
        groupDescription: '',
        groupMembers: [],
        searchEditMember: '',
        usersList: @js($editUsersListForModal),
    
        get filteredEditUsers() {
            if (!this.searchEditMember) return this.usersList;
            return this.usersList.filter(user =>
                user.name.toLowerCase().includes(this.searchEditMember.toLowerCase()) ||
                (user.email && user.email.toLowerCase().includes(this.searchEditMember.toLowerCase()))
            );
        },
    
        toggleEditMember(userId) {
            if (this.groupMembers.includes(userId)) {
                this.groupMembers = this.groupMembers.filter(id => id !== userId);
            } else {
                this.groupMembers.push(userId);
            }
        }
    }"
        @open-edit-group-modal.window="
            openEditModal = true; 
            groupSlug = $event.detail.slug;
            groupName = $event.detail.name;
            groupDescription = $event.detail.description || '';
            groupMembers = $event.detail.members || [];
        "
        x-show="openEditModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
        style="display: none;" x-cloak>
        <div @click.away="openEditModal = false"
            class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 border border-slate-100 transform transition-all my-8 max-h-[90vh] flex flex-col"
            x-transition.scale.95>
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
                <h3 class="text-base font-bold text-slate-900 tracking-tight">Edit Group</h3>
                <button @click="openEditModal = false" type="button"
                    class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form :action="'{{ route('community.groups.index') }}/' + groupSlug" method="POST" enctype="multipart/form-data"
                class="space-y-4 pt-4 overflow-y-auto flex-1 pr-1">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Group Name</label>
                    <input type="text" name="name" x-model="groupName" required
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" x-model="groupDescription"
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400/50"></textarea>
                </div>

                {{-- MEMBERS SELECTION FOR EDIT (Following users only) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Manage Members</label>
                    <input type="text" x-model="searchEditMember" placeholder="Search followed users..."
                        class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-amber-400/50">

                    <div
                        class="max-h-40 overflow-y-auto space-y-1.5 bg-slate-50 border border-slate-200/80 rounded-xl p-2">
                        <template x-for="user in filteredEditUsers" :key="user.id">
                            <div @click="toggleEditMember(user.id)"
                                class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition"
                                :class="groupMembers.includes(user.id) ? 'bg-amber-50 border border-amber-200' :
                                    'hover:bg-slate-100 border border-transparent'">
                                <div class="flex items-center gap-2.5">
                                    <img :src="user.avatar" class="w-7 h-7 rounded-full object-cover bg-slate-100 ring-1 ring-slate-200"
                                        x-on:error="$event.target.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=0c1b33&color=fff&size=100'">
                                    <span class="text-xs font-semibold text-slate-800" x-text="user.name"></span>
                                </div>
                                <span class="text-xs font-bold px-2 py-0.5 rounded"
                                    :class="groupMembers.includes(user.id) ? 'bg-amber-400 text-slate-950' :
                                        'bg-slate-200 text-slate-600'"
                                    x-text="groupMembers.includes(user.id) ? 'Added' : '+ Add'"></span>
                            </div>
                        </template>

                        <div x-show="filteredEditUsers.length === 0" class="text-center py-3 text-xs text-slate-400">
                            No followed users found
                        </div>
                    </div>

                    <template x-for="userId in groupMembers" :key="userId">
                        <input type="hidden" name="members[]" :value="userId">
                    </template>
                </div>

                <div
                    class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 sticky bottom-0 bg-white pb-2">
                    <button type="button" @click="openEditModal = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-amber-400 hover:bg-amber-500 text-slate-950 text-xs font-bold rounded-xl transition shadow-md shadow-amber-400/10 cursor-pointer">
                        Update Group
                    </button>
                </div>
            </form>
        </div>
    </div>
@endauth

{{-- GLOBAL PROFESSIONAL LOGIN PROMPT MODAL --}}
<div x-data="{ globalLoginModal: false }"
    @open-login-modal.window="globalLoginModal = true"
    @keydown.escape.window="globalLoginModal = false"
    x-show="globalLoginModal"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4"
    style="display: none;" x-cloak>
    <div @click.away="globalLoginModal = false"
        class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center border border-slate-100 transform transition-all"
        x-transition.scale.95>
        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
        </div>

        <h3 class="text-lg font-bold text-slate-900 tracking-tight">
            Authentication Required
        </h3>

        <p class="text-xs text-slate-500 mt-2 leading-relaxed px-2">
            We're sorry, but you need to be logged in to create discussions, interact with posts, and access protected
            community features in REIAC Community.
        </p>

        <div class="flex items-center gap-3 mt-6">
            <button type="button" @click="globalLoginModal = false"
                class="flex-1 py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                Cancel / Go Back
            </button>

            <a href="{{ route('login') }}"
                class="flex-1 py-2.5 px-4 bg-[#0b1329] hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-md text-center">
                Login Now
            </a>
        </div>
    </div>
</div>