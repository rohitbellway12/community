@props([
    'title' => 'Groups | REIAC',
    'user' => auth()->user(),
    'notificationsCount' => 0,
    'trendingTopics' => collect(),
    'categories' => collect(),
    'tags' => collect(),
    'topContributors' => collect(),
    'groups' => collect(),
    'users' => collect(),
])

@php
    /*
    |--------------------------------------------------------------------------
    | Prepare Groups For Alpine
    |--------------------------------------------------------------------------
    */

    $groupData = collect($groups ?? [])
        ->map(function ($group) use ($user) {
            $isOwner = (bool) ($group->is_owner ?? (int) $group->user_id === (int) $user->id);
            $isMember = $group->users->contains($user->id) ?? false;

            return [
                'id' => $group->id,
                'slug' => $group->slug,
                'name' => $group->name,
                'description' => $group->description ?? '',
                'members' => (int) ($group->users_count ?? 0),
                'owner' => $isOwner,
                'is_member' => $isMember,
            ];
        })
        ->values()
        ->all();
@endphp


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</head>


<body
    class="min-h-screen
           bg-[#f3f4f6]
           font-sans
           text-slate-800
           antialiased
           selection:bg-amber-500
           selection:text-white
           pb-20 lg:pb-0"
    x-data="groupManagement()">


    {{-- ============================================================
        TOPBAR
    ============================================================= --}}

    <x-community.topbar :notifications-count="$notificationsCount" />


    {{-- ============================================================
        MAIN LAYOUT
    ============================================================= --}}

    <main
        class="max-w-[1520px]
               mx-auto
               px-4
               lg:px-6
               py-6

               grid
               grid-cols-1

               lg:grid-cols-[280px_minmax(0,1fr)_330px]

               xl:grid-cols-[300px_minmax(0,1fr)_360px]

               gap-6

               items-start">


        {{-- ========================================================
            LEFT SIDEBAR
        ========================================================= --}}

        <x-community.sidebar :top-contributors="$topContributors" :categories="$categories" :tags="$tags" :user="$user" />


        {{-- ========================================================
            CENTER CONTENT
        ========================================================= --}}

        <section class="min-w-0">


            {{-- ====================================================
                SUCCESS MESSAGE
            ===================================================== --}}

            @if (session('success'))
                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-emerald-200
                           bg-emerald-50
                           px-4
                           py-3
                           text-xs
                           font-semibold
                           text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif


            {{-- ====================================================
                ERROR MESSAGE
            ===================================================== --}}

            @if (session('error'))
                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-red-200
                           bg-red-50
                           px-4
                           py-3
                           text-xs
                           font-semibold
                           text-red-700">
                    {{ session('error') }}
                </div>
            @endif


            {{-- ====================================================
                VALIDATION ERRORS
            ===================================================== --}}

            @if ($errors->any())

                <div
                    class="mb-5
                           rounded-xl
                           border
                           border-red-200
                           bg-red-50
                           px-4
                           py-3">

                    <ul
                        class="list-disc
                               list-inside
                               space-y-1
                               text-xs
                               text-red-700">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- ====================================================
                PAGE HEADER
            ===================================================== --}}

            <div
                class="bg-white
                       rounded-2xl
                       border
                       border-slate-200/70
                       shadow-sm
                       p-5
                       lg:p-6
                       mb-5">

                <div
                    class="flex
                           flex-col
                           sm:flex-row
                           sm:items-center
                           sm:justify-between
                           gap-4">

                    <div>

                        <h1
                            class="text-xl
                                   lg:text-2xl
                                   font-extrabold
                                   text-slate-900
                                   tracking-tight">
                            My Groups
                        </h1>

                        <p
                            class="text-xs
                                   text-slate-500
                                   mt-1">
                            Manage your groups and explore
                            group discussions.
                        </p>

                    </div>


                    {{-- CREATE GROUP BUTTON --}}

                    <button type="button" @click="createModal = true"
                        class="shrink-0
                               inline-flex
                               items-center
                               justify-center
                               gap-2
                               px-4
                               py-2.5
                               rounded-xl
                               bg-[#0b1329]
                               text-white
                               text-xs
                               font-bold
                               hover:bg-slate-800
                               transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                        </svg>

                        Create Group

                    </button>

                </div>

            </div>


            {{-- ====================================================
                GROUPS CARD
            ===================================================== --}}

            <div
                class="bg-white
                       rounded-2xl
                       border
                       border-slate-200/70
                       shadow-sm
                       overflow-hidden">


                {{-- GROUPS HEADER --}}

                <div
                    class="px-5
                           py-4
                           border-b
                           border-slate-100
                           flex
                           items-center
                           justify-between
                           gap-3">

                    <div>

                        <h2
                            class="text-sm
                                   font-extrabold
                                   text-slate-900">
                            Groups
                        </h2>

                        <p
                            class="text-[11px]
                                   text-slate-400
                                   mt-0.5">
                            Groups you are a member of
                        </p>

                    </div>


                    <span
                        class="shrink-0
                               px-2.5
                               py-1
                               rounded-lg
                               bg-amber-50
                               text-amber-700
                               text-[10px]
                               font-bold">
                        {{ $groups->count() }}

                        {{ $groups->count() === 1 ? 'Group' : 'Groups' }}
                    </span>

                </div>


                {{-- =================================================
                    GROUP LIST
                ================================================== --}}

                @if ($groups->count())

                    <div class="divide-y
                               divide-slate-100">

                        @foreach ($groups as $group)
                            @php
                                $isOwner = (bool) ($group->is_owner ?? (int) $group->user_id === (int) $user->id);
                                $isMember = $group->users->contains($user->id);

                                $groupUrl = route('community.groups.show', [
                                    'group' => $group->slug,
                                ]);
                            @endphp


                            {{-- =================================================
                                GROUP ROW
                            ================================================== --}}

                            <div
                                class="group
                                       relative
                                       flex
                                       items-center
                                       gap-3
                                       p-4
                                       lg:p-5
                                       hover:bg-slate-50
                                       transition">


                                {{-- CLICKABLE GROUP AREA --}}

                                <a href="{{ $groupUrl }}"
                                    class="absolute
                                           inset-0
                                           z-0"
                                    aria-label="Open {{ $group->name }}"></a>


                                {{-- GROUP ICON --}}

                                <div
                                    class="relative
                                           z-10
                                           pointer-events-none
                                           w-11
                                           h-11
                                           rounded-xl
                                           bg-[#0b1329]
                                           text-white
                                           flex
                                           items-center
                                           justify-center
                                           font-extrabold
                                           shrink-0">
                                    {{ strtoupper(substr($group->name ?? 'G', 0, 1)) }}
                                </div>


                                {{-- GROUP DETAILS --}}

                                <div
                                    class="relative
                                           z-10
                                           pointer-events-none
                                           min-w-0
                                           flex-1">

                                    <div
                                        class="font-bold
                                               text-sm
                                               text-slate-900
                                               truncate">
                                        {{ $group->name }}
                                    </div>


                                    <div
                                        class="text-[11px]
                                               text-slate-500
                                               mt-0.5
                                               flex
                                               items-center
                                               gap-1">

                                        <span>
                                            {{ $group->users_count ?? 0 }}
                                            {{ ($group->users_count ?? 0) == 1 ? 'member' : 'members' }}
                                        </span>


                                        <span>
                                            •
                                        </span>


                                        <span>
                                            {{ $group->posts_count ?? ($group->posts->count() ?? 0) }}
                                            {{ ($group->posts_count ?? $group->posts->count()) == 1 ? 'post' : 'posts' }}
                                        </span>

                                    </div>

                                </div>


                                {{-- OWNER BADGE --}}

                                @if ($isOwner)
                                    <span
                                        class="relative
                                               z-10
                                               pointer-events-none
                                               hidden
                                               sm:inline-flex
                                               px-2
                                               py-1
                                               rounded-md
                                               bg-amber-50
                                               text-amber-700
                                               text-[9px]
                                               font-bold">
                                        OWNER
                                    </span>
                                @endif


                                {{-- ARROW --}}

                                <div
                                    class="relative
                                           z-10
                                           pointer-events-none
                                           w-8
                                           h-8
                                           flex
                                           items-center
                                           justify-center
                                           shrink-0">

                                    <svg class="w-4 h-4
                                               text-slate-300
                                               group-hover:text-slate-500
                                               transition"
                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>

                                </div>


                                {{-- =================================================
                                    OWNER ACTION BUTTONS / LEAVE OPTION
                                ================================================== --}}

                                <div
                                    class="relative
                                           z-20
                                           flex
                                           items-center
                                           gap-1">

                                    @if ($isOwner)
                                        {{-- EDIT BUTTON --}}

                                        <button type="button"
                                            @click.stop="
                                                openEditGroup({
                                                    id: {{ $group->id }},
                                                    slug: @js($group->slug),
                                                    name: @js($group->name),
                                                    description: @js($group->description ?? '')
                                                })
                                            "
                                            class="p-2
                                                   rounded-lg
                                                   bg-white
                                                   border
                                                   border-slate-200
                                                   text-slate-400
                                                   hover:text-blue-600
                                                   hover:border-blue-200
                                                   hover:bg-blue-50
                                                   transition"
                                            title="Edit Group">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>

                                        </button>


                                        {{-- DELETE BUTTON --}}

                                        <button type="button"
                                            @click.stop="
                                                openDeleteGroup({
                                                    id: {{ $group->id }},
                                                    slug: @js($group->slug),
                                                    name: @js($group->name)
                                                })
                                            "
                                            class="p-2
                                                   rounded-lg
                                                   bg-white
                                                   border
                                                   border-slate-200
                                                   text-slate-400
                                                   hover:text-red-600
                                                   hover:border-red-200
                                                   hover:bg-red-50
                                                   transition"
                                            title="Delete Group">

                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 11v6M14 11v6M4 7h16M9 7V4h6v3" />
                                            </svg>

                                        </button>
                                    @else
                                        {{-- LEAVE GROUP BUTTON --}}
                                        @if ($isMember)
                                            <form action="{{ route('community.groups.leave', $group->slug) }}" method="POST" @click.stop class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-[11px] font-bold transition cursor-pointer border border-red-200/60"
                                                    title="Leave Group"
                                                >
                                                    Leave
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>

                            </div>
                        @endforeach

                    </div>
                @else
                    {{-- =================================================
                        EMPTY STATE
                    ================================================== --}}

                    <div class="p-12
                               text-center">

                        <div
                            class="w-14
                                   h-14
                                   mx-auto
                                   rounded-2xl
                                   bg-slate-100
                                   text-slate-400
                                   flex
                                   items-center
                                   justify-center">

                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.7"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5A2.5 2.5 0 015.5 5h4l2 2h7A2.5 2.5 0 0121 9.5v8a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 17.5v-10z" />
                            </svg>

                        </div>


                        <h3
                            class="mt-4
                                   text-sm
                                   font-extrabold
                                   text-slate-900">
                            No groups yet
                        </h3>


                        <p
                            class="mt-1
                                   text-xs
                                   text-slate-400">
                            Create your first group to
                            start a discussion.
                        </p>


                        <button type="button" @click="createModal = true"
                            class="mt-4
                                   inline-flex
                                   items-center
                                   gap-2
                                   px-4
                                   py-2.5
                                   rounded-xl
                                   bg-[#0b1329]
                                   text-white
                                   text-xs
                                   font-bold
                                   hover:bg-slate-800">
                            + Create Group
                        </button>

                    </div>

                @endif

            </div>

        </section>


        {{-- ========================================================
            RIGHT SIDEBAR
        ========================================================= --}}

        <x-community.rightbar :user="$user" :trending-topics="$trendingTopics" />

    </main>


    {{-- ============================================================
        CREATE GROUP MODAL
    ============================================================= --}}
    @auth
        @php
            $modalUsersList = auth()
                ->user()
                ->following()
                ->with('profile:id,user_id,username,avatar')
                ->select('users.id', 'users.name', 'users.email')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->profile?->username,
                        'avatar' => $user->profile?->avatar
                            ? asset('storage/' . $user->profile->avatar)
                            : 'https://ui-avatars.com/api/?name=' .
                                urlencode($user->name ?? 'User') .
                                '&background=0c1b33&color=fff&size=100',
                    ];
                })
                ->values();
        @endphp

        <div x-data="{
            searchMember: '',
            selectedMembers: [],
            usersList: @js($modalUsersList),
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
        }" x-show="createModal" x-cloak x-transition.opacity
            class="fixed
                   inset-0
                   z-[100]
                   flex
                   items-center
                   justify-center
                   bg-slate-900/60
                   backdrop-blur-sm
                   px-4
                   overflow-y-auto">
            <div @click.outside="createModal = false"
                class="bg-white
                       w-full
                       max-w-lg
                       max-h-[90vh]
                       rounded-3xl
                       shadow-2xl
                       border
                       border-slate-100
                       flex
                       flex-col
                       overflow-hidden"
                x-transition.scale.95>
                {{-- HEADER --}}
                <div
                    class="px-6
                           py-4
                           border-b
                           border-slate-100
                           flex
                           items-center
                           justify-between
                           shrink-0">
                    <h3
                        class="text-base
                               font-bold
                               text-slate-900
                               tracking-tight">
                        Create New Group
                    </h3>

                    <button type="button" @click="createModal = false"
                        class="text-slate-400
                               hover:text-slate-600
                               cursor-pointer
                               transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- FORM --}}
                <form method="POST" action="{{ route('community.groups.store') }}" enctype="multipart/form-data"
                    class="flex
                           flex-col
                           overflow-hidden
                           flex-1">
                    @csrf

                    <div
                        class="p-6
                               space-y-4
                               overflow-y-auto
                               flex-1">
                        {{-- NAME --}}
                        <div>
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5">
                                Group Name
                            </label>

                            <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                                placeholder="e.g. Real Estate Investors Hub"
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2.5
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50">
                        </div>

                        {{-- DESCRIPTION --}}
                        <div>
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5">
                                Description
                            </label>

                            <textarea name="description" rows="3" maxlength="2000" placeholder="What is this group about?"
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2.5
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50
                                       resize-none">{{ old('description') }}</textarea>
                        </div>

                        {{-- ADD MEMBERS SEARCH & SELECT LIST --}}
                        <div>
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5">
                                Add Members
                            </label>

                            <input type="text" x-model="searchMember" placeholder="Search users to add..."
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       mb-2
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50">

                            <div
                                class="max-h-40
                                       overflow-y-auto
                                       space-y-1.5
                                       bg-slate-50
                                       border
                                       border-slate-200/80
                                       rounded-xl
                                       p-2">
                                <template x-for="user in filteredUsers" :key="user.id">
                                    <div @click="toggleMember(user.id)"
                                        class="flex
                                               items-center
                                               justify-between
                                               p-2
                                               rounded-lg
                                               cursor-pointer
                                               transition"
                                        :class="selectedMembers.includes(user.id) ? 'bg-amber-50 border border-amber-200' :
                                            'hover:bg-slate-100 border border-transparent'">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <img :src="user.avatar" class="w-7 h-7 rounded-full object-cover shrink-0">
                                            <span class="text-xs font-semibold text-slate-800 truncate"
                                                x-text="user.name"></span>
                                        </div>

                                        <span class="text-xs font-bold px-2 py-0.5 rounded shrink-0 transition"
                                            :class="selectedMembers.includes(user.id) ? 'bg-amber-400 text-slate-950' :
                                                'bg-slate-200 text-slate-600'"
                                            x-text="selectedMembers.includes(user.id) ? 'Added' : '+ Add'"></span>
                                    </div>
                                </template>

                                <div x-show="filteredUsers.length === 0" class="text-center py-3 text-xs text-slate-400">
                                    No users found
                                </div>
                            </div>

                            {{-- Hidden inputs to submit selected members array --}}
                            <template x-for="userId in selectedMembers" :key="userId">
                                <input type="hidden" name="members[]" :value="userId">
                            </template>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div
                        class="px-6
                               py-4
                               border-t
                               border-slate-100
                               bg-white
                               flex
                               items-center
                               justify-end
                               gap-3
                               shrink-0">
                        <button type="button" @click="createModal = false"
                            class="px-4
                                   py-2.5
                                   rounded-xl
                                   bg-slate-100
                                   hover:bg-slate-200
                                   text-slate-700
                                   text-xs
                                   font-bold
                                   transition
                                   cursor-pointer">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5
                                   py-2.5
                                   rounded-xl
                                   bg-amber-400
                                   hover:bg-amber-500
                                   text-slate-950
                                   text-xs
                                   font-bold
                                   transition
                                   shadow-md
                                   shadow-amber-400/10
                                   cursor-pointer">
                            Create Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth


    {{-- ============================================================
        EDIT GROUP MODAL
    ============================================================= --}}
    @auth
        @php
            $editUsersListModal = auth()
                ->user()
                ->following()
                ->with('profile:id,user_id,username,avatar')
                ->select('users.id', 'users.name', 'users.email')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->profile?->username,
                        'avatar' => $user->profile?->avatar
                            ? asset('storage/' . $user->profile->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=0c1b33&color=fff&size=100',
                    ];
                })
                ->values();
        @endphp

        <div
            x-show="editModal"
            x-cloak
            x-transition.opacity
            class="fixed
                   inset-0
                   z-[100]
                   flex
                   items-center
                   justify-center
                   bg-slate-900/60
                   backdrop-blur-sm
                   px-4
                   overflow-y-auto"
        >
            <div
                @click.outside="editModal = false"
                class="bg-white
                       w-full
                       max-w-lg
                       max-h-[90vh]
                       rounded-3xl
                       shadow-2xl
                       border
                       border-slate-100
                       flex
                       flex-col
                       overflow-hidden"
                x-transition.scale.95
            >
                {{-- HEADER --}}
                <div
                    class="px-6
                           py-4
                           border-b
                           border-slate-100
                           flex
                           items-center
                           justify-between
                           shrink-0"
                >
                    <h3
                        class="text-base
                               font-bold
                               text-slate-900
                               tracking-tight"
                    >
                        Edit Group
                    </h3>

                    <button
                        type="button"
                        @click="editModal = false"
                        class="text-slate-400
                               hover:text-slate-600
                               cursor-pointer
                               transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- FORM --}}
                <form
                    :action="editAction"
                    method="POST"
                    enctype="multipart/form-data"
                    class="flex
                           flex-col
                           overflow-hidden
                           flex-1"
                >
                    @csrf
                    @method('PUT')

                    <div
                        class="p-6
                               space-y-4
                               overflow-y-auto
                               flex-1"
                    >
                        {{-- NAME --}}
                        <div>
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5"
                            >
                                Group Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                x-model="editGroup.name"
                                required
                                maxlength="255"
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2.5
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50"
                            >
                        </div>

                        {{-- DESCRIPTION --}}
                        <div>
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5"
                            >
                                Description
                            </label>

                            <textarea
                                name="description"
                                x-model="editGroup.description"
                                rows="3"
                                maxlength="2000"
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2.5
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50
                                       resize-none"
                            ></textarea>
                        </div>

                      
                        {{-- MANAGE MEMBERS SEARCH & SELECT LIST --}}
                        <div
                            x-data="{
                                searchEditMember: '',
                                usersList: @js($editUsersListModal),
                                groupMembers: [],
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
                        >
                            <label
                                class="block
                                       text-xs
                                       font-bold
                                       text-slate-700
                                       mb-1.5"
                            >
                                Manage Members
                            </label>

                            <input
                                type="text"
                                x-model="searchEditMember"
                                placeholder="Search users..."
                                class="w-full
                                       rounded-xl
                                       border
                                       border-slate-200
                                       bg-slate-50
                                       px-3.5
                                       py-2
                                       text-xs
                                       font-medium
                                       text-slate-800
                                       mb-2
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-amber-400/50"
                            >

                            <div
                                class="max-h-40
                                       overflow-y-auto
                                       space-y-1.5
                                       bg-slate-50
                                       border
                                       border-slate-200/80
                                       rounded-xl
                                       p-2"
                            >
                                <template x-for="user in filteredEditUsers" :key="user.id">
                                    <div
                                        @click="toggleEditMember(user.id)"
                                        class="flex
                                               items-center
                                               justify-between
                                               p-2
                                               rounded-lg
                                               cursor-pointer
                                               transition"
                                        :class="groupMembers.includes(user.id) ? 'bg-amber-50 border border-amber-200' : 'hover:bg-slate-100 border border-transparent'"
                                    >
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <img
                                                :src="user.avatar"
                                                class="w-7 h-7 rounded-full object-cover shrink-0"
                                            >
                                            <span
                                                class="text-xs font-semibold text-slate-800 truncate"
                                                x-text="user.name"
                                            ></span>
                                        </div>

                                        <span
                                            class="text-xs font-bold px-2 py-0.5 rounded shrink-0 transition"
                                            :class="groupMembers.includes(user.id) ? 'bg-amber-400 text-slate-950' : 'bg-slate-200 text-slate-600'"
                                            x-text="groupMembers.includes(user.id) ? 'Added' : '+ Add'"
                                        ></span>
                                    </div>
                                </template>

                                <div
                                    x-show="filteredEditUsers.length === 0"
                                    class="text-center py-3 text-xs text-slate-400"
                                >
                                    No users found
                                </div>
                            </div>

                            {{-- Hidden inputs for updating members array --}}
                            <template x-for="userId in groupMembers" :key="userId">
                                <input type="hidden" name="members[]" :value="userId">
                            </template>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div
                        class="px-6
                               py-4
                               border-t
                               border-slate-100
                               bg-white
                               flex
                               items-center
                               justify-end
                               gap-3
                               shrink-0"
                    >
                        <button
                            type="button"
                            @click="editModal = false"
                            class="px-4
                                   py-2.5
                                   rounded-xl
                                   bg-slate-100
                                   hover:bg-slate-200
                                   text-slate-700
                                   text-xs
                                   font-bold
                                   transition
                                   cursor-pointer"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="px-5
                                   py-2.5
                                   rounded-xl
                                   bg-amber-400
                                   hover:bg-amber-500
                                   text-slate-950
                                   text-xs
                                   font-bold
                                   transition
                                   shadow-md
                                   shadow-amber-400/10
                                   cursor-pointer"
                        >
                            Update Group
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth


    {{-- ============================================================
        DELETE GROUP MODAL
    ============================================================= --}}

    <div x-show="deleteModal" x-cloak x-transition.opacity
        class="fixed
               inset-0
               z-[100]
               flex
               items-center
               justify-center
               bg-slate-900/60
               backdrop-blur-sm
               px-4">

        <div @click.outside="deleteModal = false"
            class="bg-white
                   w-full
                   max-w-sm
                   rounded-2xl
                   shadow-2xl
                   p-6
                   text-center">


            {{-- ICON --}}

            <div
                class="w-12
                       h-12
                       mx-auto
                       rounded-full
                       bg-red-50
                       text-red-600
                       flex
                       items-center
                       justify-center
                       mb-4">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7" />

                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 11v6M14 11v6M4 7h16M9 7V4h6v3" />
                </svg>

            </div>


            <h3 class="font-extrabold
                       text-slate-900">
                Delete Group?
            </h3>


            <p class="text-xs
                       text-slate-500
                       mt-2">

                Are you sure you want to delete

                <strong class="text-slate-700"
                    x-text="
                        deleteTarget?.name
                            || 'this group'
                    "></strong>?

                <br>

                This action cannot be undone.

            </p>


            {{-- DELETE FORM --}}

            <form method="POST" :action="deleteAction"
                class="flex
                       justify-center
                       gap-2
                       mt-5">

                @csrf

                @method('DELETE')


                <button type="button" @click="deleteModal = false"
                    class="px-4
                           py-2.5
                           rounded-xl
                           text-xs
                           font-semibold
                           text-slate-600
                           bg-slate-100
                           hover:bg-slate-200">
                    Cancel
                </button>


                <button type="submit" :disabled="!deleteTarget?.slug"
                    class="px-4
                           py-2.5
                           rounded-xl
                           bg-red-600
                           text-white
                           text-xs
                           font-bold
                           hover:bg-red-700
                           disabled:opacity-50
                           disabled:cursor-not-allowed">
                    Delete
                </button>

            </form>

        </div>

    </div>


    {{-- ============================================================
        ALPINE
    ============================================================= --}}

    <script>
        function groupManagement() {

            return {

                /*
                |--------------------------------------------------------------------------
                | Groups
                |--------------------------------------------------------------------------
                */

                groups: @js($groupData),


                /*
                |--------------------------------------------------------------------------
                | Modals
                |--------------------------------------------------------------------------
                */

                createModal: false,

                editModal: false,

                deleteModal: false,


                /*
                |--------------------------------------------------------------------------
                | Edit Group
                |--------------------------------------------------------------------------
                */

                editGroup: {
                    id: null,
                    slug: '',
                    name: '',
                    description: ''
                },


                /*
                |--------------------------------------------------------------------------
                | Delete Target
                |--------------------------------------------------------------------------
                */

                deleteTarget: null,


                /*
                |--------------------------------------------------------------------------
                | Edit Form Action
                |--------------------------------------------------------------------------
                */

                get editAction() {

                    if (!this.editGroup.slug) {
                        return '#';
                    }

                    return '{{ url('/community/groups') }}/' +
                        encodeURIComponent(
                            this.editGroup.slug
                        );
                },


                /*
                |--------------------------------------------------------------------------
                | Delete Form Action
                |--------------------------------------------------------------------------
                */

                get deleteAction() {

                    if (!this.deleteTarget?.slug) {
                        return '#';
                    }

                    return '{{ url('/community/groups') }}/' +
                        encodeURIComponent(
                            this.deleteTarget.slug
                        );
                },


                /*
                |--------------------------------------------------------------------------
                | Open Edit Modal
                |--------------------------------------------------------------------------
                */

                openEditGroup(group) {

                    this.editGroup = {

                        id: group.id,

                        slug: group.slug || '',

                        name: group.name || '',

                        description: group.description || ''

                    };

                    this.editModal = true;
                },


                /*
                |--------------------------------------------------------------------------
                | Open Delete Modal
                |--------------------------------------------------------------------------
                */

                openDeleteGroup(group) {

                    this.deleteTarget = {

                        id: group.id,

                        slug: group.slug || '',

                        name: group.name || ''

                    };

                    this.deleteModal = true;
                }

            };

        }
    </script>


</body>

</html>