@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div x-data="{ 
        modalOpen: false, 
        isEditing: false,
        currentUser: {},
        avatarPreview: null,
        coverPreview: null,
        removeAvatar: false,
        removeCover: false,
        
        openProfileModal(user, editMode = false) {
            this.currentUser = { ...user };
            this.avatarPreview = user.avatar;
            this.coverPreview = user.coverImage;
            this.removeAvatar = false;
            this.removeCover = false;
            this.isEditing = editMode;
            this.modalOpen = true;
        },
        handleAvatarChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.avatarPreview = URL.createObjectURL(file);
                this.removeAvatar = false;
            }
        },
        handleCoverChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.coverPreview = URL.createObjectURL(file);
                this.removeCover = false;
            }
        }
    }" 
    class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-2xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold rounded-2xl p-4 shadow-xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">User Management</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage community members, active/blocked access, user roles, and profiles.</p>
        </div>
        <div class="text-xs text-slate-500 font-semibold bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs">
            Total Users: <span class="font-bold text-slate-900">{{ number_format($stats['total'] ?? $users->total()) }}</span>
        </div>
    </div>

    {{-- SUMMARY STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('admin.users') }}"
           class="bg-white p-5 rounded-2xl border transition shadow-xs {{ !request('status') ? 'border-reiac-navy ring-2 ring-reiac-navy/10' : 'border-slate-200 hover:border-slate-300' }}">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">All Members</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($stats['total'] ?? $users->total()) }}</div>
            <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Total registered accounts</div>
        </a>

        <a href="{{ route('admin.users', ['status' => 'active']) }}"
           class="bg-white p-5 rounded-2xl border transition shadow-xs {{ request('status') === 'active' ? 'border-emerald-500 ring-2 ring-emerald-500/10' : 'border-slate-200 hover:border-emerald-300' }}">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Members</div>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1.5">{{ number_format($stats['active'] ?? 0) }}</div>
            <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">Can login, post & comment</div>
        </a>

        <a href="{{ route('admin.users', ['status' => 'blocked']) }}"
           class="bg-white p-5 rounded-2xl border transition shadow-xs {{ request('status') === 'blocked' ? 'border-rose-500 ring-2 ring-rose-500/10' : 'border-slate-200 hover:border-rose-300' }}">
            <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Blocked / Deactivated</div>
            <div class="text-2xl font-extrabold text-rose-600 mt-1.5">{{ number_format($stats['blocked'] ?? 0) }}</div>
            <div class="text-[11px] text-rose-700 font-semibold mt-0.5">Suspended accounts</div>
        </a>
    </div>

    {{-- FILTER TABS & SEARCH BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto">
            <a href="{{ route('admin.users', array_filter(['search' => request('search')])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !request('status') ? 'bg-reiac-slate text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                All ({{ $stats['total'] ?? $users->total() }})
            </a>
            <a href="{{ route('admin.users', array_filter(['status' => 'active', 'search' => request('search')])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'active' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Active ({{ $stats['active'] ?? 0 }})
            </a>
            <a href="{{ route('admin.users', array_filter(['status' => 'blocked', 'search' => request('search')])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ request('status') === 'blocked' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                Blocked ({{ $stats['blocked'] ?? 0 }})
            </a>
        </div>

        {{-- Search Input Form --}}
        <form method="GET" action="{{ route('admin.users') }}" class="w-full md:w-80 flex items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search name, email, username..."
                       class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request('search'))
                    <a href="{{ route('admin.users', array_filter(['status' => request('status')])) }}"
                       class="text-slate-400 hover:text-slate-600 absolute right-2.5 top-1.5 text-xs font-bold">✕</a>
                @endif
            </div>
        </form>
    </div>

    {{-- USERS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">User Details</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Location</th>
                        <th class="py-3.5 px-4 text-center">Activity</th>
                        <th class="py-3.5 px-4 text-center">Status Toggle</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($users as $user)
                        @php
                            $rawStatus = is_object($user->status) ? $user->status->value : $user->status;
                            $userStatus = strtolower(trim((string)$rawStatus));
                            $userRole = is_object($user->role) ? $user->role->value : ($user->role ?? 'user');
                            $avatarUrl = $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name);
                            $coverUrl = $user->profile?->cover_image ? asset('storage/' . $user->profile->cover_image) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- User Info --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="relative shrink-0">
                                        <img src="{{ $avatarUrl }}"
                                             class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs"
                                             alt="{{ $user->name }}">
                                        <span class="w-2.5 h-2.5 rounded-full absolute -bottom-0.5 -right-0.5 border-2 border-white {{ $userStatus === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate flex items-center gap-1.5">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-medium truncate">
                                            {{ $user->email }}
                                            @if($user->profile?->username)
                                                <span class="text-indigo-600 ml-1">@ {{ $user->profile->username }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($userRole === 'admin')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Admin</span>
                                @elseif($userRole === 'moderator')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Moderator</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">Member</span>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    @if($user->profile?->country?->flag)
                                        <span class="text-sm">{{ $user->profile->country->flag }}</span>
                                    @endif
                                    <span>{{ $user->profile?->location ?? ($user->profile?->country?->name ?? 'Not specified') }}</span>
                                </div>
                            </td>

                            {{-- Activity --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $user->posts->count() }} posts • {{ $user->comments->count() }} comments
                                </span>
                            </td>

                            {{-- Status Column with Clickable Toggle Switch --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <form id="user-status-form-{{ $user->id }}"
                                      action="{{ route('admin.users.updateStatus', $user) }}"
                                      method="POST"
                                      class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $userStatus === 'active' ? 'blocked' : 'active' }}">
                                    
                                    {{-- Toggle Switch Button with SweetAlert2 --}}
                                    <button type="button"
                                            title="Click to {{ $userStatus === 'active' ? 'Deactivate / Block' : 'Activate' }} user"
                                            onclick="confirmStatusToggle('user-status-form-{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $userStatus === 'active' ? 'Blocked' : 'Active' }}')"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $userStatus === 'active' ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $userStatus === 'active' ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>

                                    {{-- Status Label --}}
                                    <span class="text-xs font-bold {{ $userStatus === 'active' ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $userStatus === 'active' ? 'Active' : 'Blocked' }}
                                    </span>
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Edit Button --}}
                                    <button type="button"
                                            @click="openProfileModal({
                                                id: '{{ $user->id }}',
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ $user->email }}',
                                                emailVerifiedAt: '{{ $user->email_verified_at ? $user->email_verified_at->format('d M Y, h:i A') : 'Not Verified' }}',
                                                username: '{{ addslashes($user->profile?->username ?? '') }}',
                                                bio: '{{ addslashes($user->profile?->bio ?? '') }}',
                                                location: '{{ addslashes($user->profile?->location ?? '') }}',
                                                country_id: '{{ $user->profile?->country_id ?? '' }}',
                                                role: '{{ $userRole }}',
                                                status: '{{ $userStatus }}',
                                                createdAt: '{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}',
                                                updatedAt: '{{ $user->updated_at ? $user->updated_at->format('d M Y, h:i A') : 'N/A' }}',
                                                avatar: '{{ $avatarUrl }}',
                                                coverImage: '{{ $coverUrl }}',
                                                postsCount: '{{ $user->posts->count() }}',
                                                commentsCount: '{{ $user->comments->count() }}',
                                                countryName: '{{ addslashes($user->profile?->country?->name ?? 'Not Specified') }}',
                                                flag: '{{ $user->profile?->country?->flag ?? '' }}'
                                            }, true)"
                                            class="px-2.5 py-1 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                        Edit
                                    </button>

                                    {{-- View Button --}}
                                    <button type="button"
                                            @click="openProfileModal({
                                                id: '{{ $user->id }}',
                                                name: '{{ addslashes($user->name) }}',
                                                email: '{{ $user->email }}',
                                                emailVerifiedAt: '{{ $user->email_verified_at ? $user->email_verified_at->format('d M Y, h:i A') : 'Not Verified' }}',
                                                username: '{{ addslashes($user->profile?->username ?? '') }}',
                                                bio: '{{ addslashes($user->profile?->bio ?? '') }}',
                                                location: '{{ addslashes($user->profile?->location ?? '') }}',
                                                country_id: '{{ $user->profile?->country_id ?? '' }}',
                                                role: '{{ $userRole }}',
                                                status: '{{ $userStatus }}',
                                                createdAt: '{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}',
                                                updatedAt: '{{ $user->updated_at ? $user->updated_at->format('d M Y, h:i A') : 'N/A' }}',
                                                avatar: '{{ $avatarUrl }}',
                                                coverImage: '{{ $coverUrl }}',
                                                postsCount: '{{ $user->posts->count() }}',
                                                commentsCount: '{{ $user->comments->count() }}',
                                                countryName: '{{ addslashes($user->profile?->country?->name ?? 'Not Specified') }}',
                                                flag: '{{ $user->profile?->country?->flag ?? '' }}'
                                            }, false)"
                                            class="px-2.5 py-1 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition">
                                        View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-sm">
                                No members found matching the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- DETAILED PROFILE / EDIT MODAL --}}
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        
        <div @click.outside="modalOpen = false" 
             class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-100 flex flex-col transition-all my-auto max-h-[92vh]">
            
            {{-- Modal Top Header Bar --}}
            <div class="px-6 py-3.5 border-b border-slate-100 flex items-center justify-between bg-white z-20 shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <template x-if="!isEditing">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </template>
                        <template x-if="isEditing">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </template>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-sm font-black text-slate-800" x-text="isEditing ? 'Edit Member Profile' : 'Member Profile Overview'"></h3>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200" x-text="'ID #' + currentUser.id"></span>
                        </div>
                        <p class="text-[11px] text-slate-400" x-text="isEditing ? 'Update credentials, roles, and status' : 'View account activity, bio, and credentials'"></p>
                    </div>
                </div>
                
                {{-- Segmented Mode Switcher & Close --}}
                <div class="flex items-center space-x-2">
                    <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200/80">
                        <button type="button" 
                                @click="isEditing = false" 
                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center space-x-1.5"
                                :class="!isEditing ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-500 hover:text-slate-800'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Overview</span>
                        </button>
                        <button type="button" 
                                @click="isEditing = true" 
                                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center space-x-1.5"
                                :class="isEditing ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-500 hover:text-slate-800'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit</span>
                        </button>
                    </div>

                    <button @click="modalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Form Wrapper --}}
            <form :action="'{{ route('admin.users') }}/' + currentUser.id" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-y-auto">
                @csrf
                @method('PUT')

                <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">
                <input type="hidden" name="remove_cover" :value="removeCover ? '1' : '0'">

                {{-- Hero Banner & Profile Header --}}
                <div class="relative bg-slate-900 shrink-0">
                    <div class="h-36 sm:h-44 w-full relative overflow-hidden bg-slate-950 flex items-center justify-center">
                        <template x-if="coverPreview && !removeCover">
                            <img :src="coverPreview" class="w-full h-full object-cover" />
                        </template>
                        <template x-if="!coverPreview || removeCover">
                            <div class="w-full h-full bg-gradient-to-r from-[#0B132B] via-[#1C2541] to-indigo-950 flex flex-col items-center justify-center text-slate-400 relative">
                                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
                                <span class="text-xs font-bold tracking-widest text-slate-300 uppercase">REIAC Community</span>
                                <span class="text-[10px] text-slate-500 font-medium">Standard Header Banner</span>
                            </div>
                        </template>

                        {{-- Edit Cover Overlay Controls --}}
                        <div x-show="isEditing" class="absolute top-3 right-3 flex items-center space-x-2 bg-slate-950/75 p-1.5 rounded-xl backdrop-blur-md border border-white/10 shadow-lg">
                            <label class="px-2.5 py-1 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg cursor-pointer transition-colors shadow-xs flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Change Banner</span>
                                <input type="file" name="cover_image" accept="image/*" class="hidden" @change="handleCoverChange">
                            </label>
                            <button type="button" x-show="coverPreview && !removeCover" @click="removeCover = true; coverPreview = null" class="px-2 py-1 text-xs font-semibold text-rose-300 hover:text-white hover:bg-rose-600/80 rounded-lg transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>

                    {{-- Avatar & Identity Header Row --}}
                    <div class="px-6 pb-4 flex flex-col sm:flex-row sm:items-end justify-between -mt-12 sm:-mt-14 relative z-10 gap-3">
                        <div class="flex items-end space-x-4">
                            <div class="relative shrink-0">
                                <template x-if="avatarPreview && !removeAvatar">
                                    <img :src="avatarPreview" class="w-22 h-22 sm:w-26 sm:h-26 rounded-2xl object-cover ring-4 ring-white shadow-xl bg-white shrink-0" />
                                </template>
                                <template x-if="!avatarPreview || removeAvatar">
                                    <div class="w-22 h-22 sm:w-26 sm:h-26 rounded-2xl ring-4 ring-white shadow-xl bg-gradient-to-tr from-slate-200 to-slate-100 flex items-center justify-center text-slate-600 font-black text-xl shrink-0">
                                        <span x-text="currentUser.name ? currentUser.name.charAt(0).toUpperCase() : 'U'"></span>
                                    </div>
                                </template>

                                {{-- Avatar Edit Badge --}}
                                <div x-show="isEditing" class="absolute -bottom-1 -right-1 flex items-center space-x-1">
                                    <label class="p-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl cursor-pointer shadow-md transition-colors block ring-2 ring-white" title="Change Avatar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <input type="file" name="avatar" accept="image/*" class="hidden" @change="handleAvatarChange">
                                    </label>
                                </div>
                            </div>

                            <div class="pb-1">
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 leading-tight" x-text="currentUser.name"></h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-xs text-indigo-600 font-bold" x-text="currentUser.username ? '@' + currentUser.username : '@no-username'"></span>
                                    
                                    {{-- Role Pill --}}
                                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md border"
                                          :class="{
                                              'bg-amber-50 text-amber-700 border-amber-200': currentUser.role === 'admin',
                                              'bg-purple-50 text-purple-700 border-purple-200': currentUser.role === 'moderator',
                                              'bg-slate-100 text-slate-600 border-slate-200': currentUser.role !== 'admin' && currentUser.role !== 'moderator'
                                          }"
                                          x-text="currentUser.role">
                                    </span>

                                    {{-- Status Pill --}}
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border flex items-center space-x-1"
                                          :class="currentUser.status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="currentUser.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                        <span class="capitalize" x-text="currentUser.status"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Activity Counters --}}
                        <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-2xl border border-slate-200/80 shadow-xs self-start sm:self-end">
                            <div class="px-2.5 border-r border-slate-100 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Posts</span>
                                <span class="text-xs font-black text-slate-800" x-text="currentUser.postsCount || '0'"></span>
                            </div>
                            <div class="px-2.5 border-r border-slate-100 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Comments</span>
                                <span class="text-xs font-black text-slate-800" x-text="currentUser.commentsCount || '0'"></span>
                            </div>
                            <div class="px-2.5 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Joined</span>
                                <span class="text-[11px] font-bold text-slate-700 whitespace-nowrap" x-text="currentUser.createdAt"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Body Content --}}
                <div class="p-6 bg-slate-50/70 flex-1 space-y-4">
                    
                    {{-- ==================== VIEW MODE ==================== --}}
                    <div x-show="!isEditing" class="space-y-4">
                        {{-- Credentials & Contact Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            
                            {{-- Email & Security --}}
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        <span>Email Address</span>
                                    </span>
                                    <template x-if="currentUser.emailVerifiedAt && currentUser.emailVerifiedAt !== 'Not Verified'">
                                        <span class="inline-flex items-center space-x-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            <span>Verified</span>
                                        </span>
                                    </template>
                                    <template x-if="!currentUser.emailVerifiedAt || currentUser.emailVerifiedAt === 'Not Verified'">
                                        <span class="inline-flex items-center space-x-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                            <span>Unverified</span>
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs font-bold text-slate-800 break-all" x-text="currentUser.email"></p>
                                <p class="text-[11px] text-slate-400 font-medium" x-text="'Verified: ' + currentUser.emailVerifiedAt"></p>
                            </div>

                            {{-- Location & Country --}}
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2.5">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Location & Region</span>
                                </span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-base" x-text="currentUser.flag || '🌐'"></span>
                                    <span class="text-xs font-bold text-slate-800" x-text="currentUser.countryName || 'No Country Specified'"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium flex items-center space-x-1">
                                    <span>City:</span>
                                    <span class="font-bold text-slate-700" x-text="currentUser.location || 'Not Specified'"></span>
                                </p>
                            </div>
                        </div>

                        {{-- Biography Card --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                <span>About / Biography</span>
                            </span>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <template x-if="currentUser.bio">
                                    <p class="text-xs text-slate-700 leading-relaxed font-medium whitespace-pre-line" x-text="currentUser.bio"></p>
                                </template>
                                <template x-if="!currentUser.bio">
                                    <p class="text-xs text-slate-400 italic font-medium">No biography details provided by this user.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Action Switcher Card --}}
                        <div class="bg-indigo-50/60 p-3.5 rounded-2xl border border-indigo-100/80 flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-indigo-900">Need to update this user's information or credentials?</span>
                            </div>
                            <button type="button" @click="isEditing = true" class="px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                                Edit Details
                            </button>
                        </div>
                    </div>

                    {{-- ==================== EDIT MODE ==================== --}}
                    <div x-show="isEditing" class="space-y-4">
                        
                        {{-- Group 1: Basic Information --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 pb-2 border-b border-slate-100">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Basic Information</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" x-model="currentUser.name" required
                                           class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-semibold transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" x-model="currentUser.email" required
                                           class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-semibold transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Username Handle</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">@</span>
                                        <input type="text" name="username" x-model="currentUser.username"
                                               class="w-full text-xs pl-7 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-semibold transition"
                                               placeholder="username">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">City / Location</label>
                                    <input type="text" name="location" x-model="currentUser.location"
                                           class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-semibold transition"
                                           placeholder="e.g. Mumbai, Dubai, London">
                                </div>
                            </div>
                        </div>

                        {{-- Group 2: Role, Status & Region --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 pb-2 border-b border-slate-100">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Permissions & Location</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Role</label>
                                    <select name="role" x-model="currentUser.role" class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold text-slate-800">
                                        <option value="user">User / Member</option>
                                        <option value="moderator">Moderator</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Account Status</label>
                                    <select name="status" x-model="currentUser.status" class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold"
                                            :class="currentUser.status === 'active' ? 'text-emerald-700 font-bold' : 'text-rose-700 font-bold'">
                                        <option value="active">Active (Access Allowed)</option>
                                        <option value="blocked">Blocked (Access Restricted)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Country</label>
                                    <select name="country_id" x-model="currentUser.country_id" class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold text-slate-800">
                                        <option value="">Select Country</option>
                                        @foreach($countries ?? [] as $country)
                                            <option value="{{ $country->id }}">{{ $country->flag }} {{ $country->name }} ({{ $country->iso_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Group 3: Biography --}}
                        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-700">Biography / About User</label>
                                <span class="text-[10px] text-slate-400 font-medium">Max 300 characters</span>
                            </div>
                            <textarea name="bio" x-model="currentUser.bio" rows="3" maxlength="300"
                                      placeholder="Brief information about user's professional background, interests..."
                                      class="w-full text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium text-slate-800 resize-none transition leading-relaxed"></textarea>
                        </div>
                    </div>

                </div>

                {{-- Modal Bottom Actions Footer --}}
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-1.5 text-slate-400 text-[11px] font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Last updated: <span class="text-slate-600 font-semibold" x-text="currentUser.updatedAt"></span></span>
                    </div>
                    
                    <div class="flex items-center space-x-2.5">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Close
                        </button>
                        
                        <template x-if="!isEditing">
                            <button type="button" @click="isEditing = true" class="px-4 py-2 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Edit Profile</span>
                            </button>
                        </template>

                        <template x-if="isEditing">
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Changes</span>
                            </button>
                        </template>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function confirmStatusToggle(formId, userName, newStatus) {
    const isActivating = newStatus.toLowerCase() === 'active';
    Swal.fire({
        title: isActivating ? 'Activate User Account?' : 'Deactivate User Account?',
        html: `Are you sure you want to change status of <b>${userName}</b> to <span class="${isActivating ? 'text-emerald-600 font-black' : 'text-rose-600 font-black'}">${newStatus}</span>?`,
        icon: isActivating ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: isActivating ? 'Yes, Activate' : 'Yes, Deactivate',
        cancelButtonText: 'Cancel',
        confirmButtonColor: isActivating ? '#10B981' : '#E11D48',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
            confirmButton: 'rounded-xl font-bold px-5 py-2.5 shadow-xs',
            cancelButton: 'rounded-xl font-bold px-5 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}
</script>
@endsection