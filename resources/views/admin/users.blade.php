@extends('layouts.admin')

@section('title', 'All Users')

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
    class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center space-x-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium rounded-xl space-y-1 shadow-sm">
            @foreach($errors->all() as $error)
                <p class="flex items-center space-x-2">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                    <span>{{ $error }}</span>
                </p>
            @endforeach
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/60 pb-5">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Community Members</h1>
            <p class="text-sm text-slate-500 mt-1">Manage platform accounts, user verification status, roles, and media attachments.</p>
        </div>
    </div>

    <!-- Filters & Counter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, handle..." 
                       class="w-full text-sm pl-9 pr-4 py-2 bg-slate-50/50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

     
        </form>

        <div class="text-sm text-slate-500">
            Showing <span class="font-semibold text-slate-700">{{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $users->total() }}</span> members
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 text-xs font-semibold tracking-wider text-slate-500 border-b border-slate-200/60">
                        <th class="py-4 px-5">User Details</th>
                        <th class="py-4 px-5">Email</th>
                        <th class="py-4 px-5">Location</th>
                        <th class="py-4 px-5 text-center">Posts</th>
                        <th class="py-4 px-5 text-center">Comments</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        @php
                            $rawStatus = is_object($user->status) ? $user->status->value : $user->status;
                            $userStatus = strtolower(trim((string)$rawStatus));
                            $avatarUrl = $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name);
                            $coverUrl = $user->profile?->cover_image ? asset('storage/' . $user->profile->cover_image) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3.5">
                                    <img src="{{ $avatarUrl }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-xs shrink-0" alt="{{ $user->name }}">
                                    <div>
                                        <p class="font-semibold text-slate-900 leading-tight">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">@ {{ $user->profile?->username ?? 'no-handle' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                {{ $user->email }}
                            </td>

                            <td class="py-4 px-5 text-slate-600">
                                {{ $user->profile?->location ?? 'Not specified' }}
                            </td>

                            <td class="py-4 px-5 text-center font-semibold text-slate-700">
                                {{ $user->posts->count() }}
                            </td>

                            <td class="py-4 px-5 text-center font-semibold text-slate-700">
                                {{ $user->comments->count() }}
                            </td>

                            <td class="py-4 px-5 text-center">
                                @if($userStatus === 'active')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/60">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-rose-500 rounded-full"></span> Blocked
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-5 text-right whitespace-nowrap">
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
                                            role: '{{ $user->role ?? 'user' }}',
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
                                        class="px-3.5 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 hover:bg-indigo-100/80 rounded-xl transition-all shadow-2xs">
                                    View Profile
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-sm">No members found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-slate-200/60 bg-slate-50/35">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Detailed Modal Overlay -->
    <div x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
         x-cloak>
        
        <div @click.outside="modalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-100 flex flex-col transition-all">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg font-mono">ID: #<span x-text="currentUser.id"></span></span>
                    <h3 class="text-base font-bold text-slate-900" x-text="isEditing ? 'Edit Profile & Account Settings' : 'Member Overview'"></h3>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button type="button" 
                            @click="isEditing = !isEditing" 
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-xl border transition-all flex items-center space-x-1.5"
                            :class="isEditing ? 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200' : 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700 shadow-xs'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span x-text="isEditing ? 'Switch to View Mode' : 'Edit Profile'"></span>
                    </button>
                    
                    <button @click="modalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Form Wrapper -->
            <form :action="'/admin/users/' + currentUser.id" method="POST" enctype="multipart/form-data" class="flex flex-col">
                @csrf
                @method('PUT')

                <input type="hidden" name="remove_avatar" :value="removeAvatar ? '1' : '0'">
                <input type="hidden" name="remove_cover" :value="removeCover ? '1' : '0'">

                <!-- Profile Hero Cover & Avatar Header -->
                <div class="relative bg-slate-900">
                    <div class="h-40 sm:h-48 w-full relative overflow-hidden bg-slate-950 flex items-center justify-center">
                        <template x-if="coverPreview && !removeCover">
                            <img :src="coverPreview" class="w-full h-full object-cover" />
                        </template>
                        <template x-if="!coverPreview || removeCover">
                            <div class="w-full h-full bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 flex items-center justify-center text-slate-400 text-xs font-medium tracking-wider uppercase">
                                No Custom Cover Image Set
                            </div>
                        </template>

                        <!-- Edit/Delete Banner Actions -->
                        <div x-show="isEditing" class="absolute top-3 right-3 flex items-center space-x-2 bg-slate-950/70 p-1.5 rounded-xl backdrop-blur-md">
                            <label class="px-3 py-1 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg cursor-pointer transition-colors shadow-xs">
                                Change Banner
                                <input type="file" name="cover_image" accept="image/*" class="hidden" @change="handleCoverChange">
                            </label>
                            <button type="button" x-show="coverPreview && !removeCover" @click="removeCover = true; coverPreview = null" class="px-3 py-1 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Avatar + Primary Metadata Info -->
                    <div class="px-6 pb-5 flex flex-col sm:flex-row sm:items-end justify-between -mt-12 relative z-10 gap-4">
                        <div class="flex items-end space-x-4">
                            <div class="relative">
                                <template x-if="avatarPreview && !removeAvatar">
                                    <img :src="avatarPreview" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-white shadow-lg bg-white shrink-0" />
                                </template>
                                <template x-if="!avatarPreview || removeAvatar">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-4 border-white shadow-lg bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs shrink-0">
                                        No Image
                                    </div>
                                </template>

                                <!-- Avatar Edit Trigger -->
                                <div x-show="isEditing" class="absolute -bottom-1 -right-1">
                                    <label class="p-2 text-white bg-slate-900 hover:bg-indigo-600 rounded-xl cursor-pointer shadow-md transition-colors block">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <input type="file" name="avatar" accept="image/*" class="hidden" @change="handleAvatarChange">
                                    </label>
                                </div>
                            </div>

                            <div class="pb-1">
                                <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight" x-text="currentUser.name"></h2>
                                <p class="text-xs text-indigo-600 font-medium mt-0.5" x-text="currentUser.username ? '@' + currentUser.username : '@no-handle'"></p>
                            </div>
                        </div>

                        <!-- Stats Card Badge Group -->
                        <div class="flex items-center space-x-3 bg-white p-2.5 rounded-2xl border border-slate-200/80 shadow-xs">
                            <div class="px-3 border-r border-slate-100 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Posts</span>
                                <span class="text-sm font-bold text-slate-800" x-text="currentUser.postsCount"></span>
                            </div>
                            <div class="px-3 border-r border-slate-100 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Comments</span>
                                <span class="text-sm font-bold text-slate-800" x-text="currentUser.commentsCount"></span>
                            </div>
                            <div class="px-3 text-center">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Joined</span>
                                <span class="text-xs font-semibold text-slate-700" x-text="currentUser.createdAt"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Inner Content Body -->
                <div class="p-6 space-y-5 bg-slate-50/50">

                    <!-- View Mode Layout -->
                    <div x-show="!isEditing" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Email Address</span>
                                <span class="font-semibold text-slate-800 break-all text-xs" x-text="currentUser.email"></span>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Email Verification</span>
                                <span class="font-semibold text-slate-800 text-xs" x-text="currentUser.emailVerifiedAt"></span>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Account Role</span>
                                <span class="font-semibold text-slate-800 uppercase text-xs" x-text="currentUser.role"></span>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Current Status</span>
                                <span :class="currentUser.status === 'active' ? 'text-emerald-600 font-bold' : 'text-rose-600 font-bold'" class="capitalize text-xs" x-text="currentUser.status"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Location & Region</span>
                                <p class="font-semibold text-slate-800 text-xs" x-text="(currentUser.location || 'Not Specified') + ' ' + (currentUser.flag || '')"></p>
                                <span class="text-[11px] text-slate-400 font-medium block mt-0.5" x-text="currentUser.countryName"></span>
                            </div>

                            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs md:col-span-2">
                                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">User Biography</span>
                                <p class="text-slate-700 font-normal mt-0.5 leading-relaxed text-xs" x-text="currentUser.bio || 'No biography details provided by this user.'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode Form Fields Layout -->
                    <div x-show="isEditing" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Full Name *</label>
                                <input type="text" name="name" x-model="currentUser.name" maxlength="100" required class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Email Address *</label>
                                <input type="email" name="email" x-model="currentUser.email" required class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                                @error('email')
                                    <span class="text-rose-500 text-[10px] font-semibold mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Username Handle</label>
                                <input type="text" name="username" x-model="currentUser.username" maxlength="50" 
                                       class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium"
                                       :class="{'border-rose-500 focus:ring-rose-500': '{{ $errors->has('username') }}'}">
                                @error('username')
                                    <span class="text-rose-500 text-[10px] font-semibold mt-1 block">Username already taken</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Location City</label>
                                <input type="text" name="location" x-model="currentUser.location" maxlength="100" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Country Region</label>
                                <select name="country_id" x-model="currentUser.country_id" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                                    <option value="">Select Country</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{ $country->id }}">{{ $country->flag }} {{ $country->name }} ({{ $country->iso_code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">System Access Role</label>
                                <select name="role" x-model="currentUser.role" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                    <option value="moderator">Moderator</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Account Status</label>
                                <select name="status" x-model="currentUser.status" class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium">
                                    <option value="active">Active</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block font-semibold text-slate-700">Biography</label>
                                    <span class="text-[10px] font-semibold text-slate-400" x-text="(currentUser.bio ? currentUser.bio.length : 0) + '/300 max characters'"></span>
                                </div>
                                <textarea name="bio" x-model="currentUser.bio" maxlength="300" rows="3" placeholder="Write biography overview..." class="w-full p-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none font-medium resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer Action Bar -->
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400" x-text="'Last updated: ' + currentUser.updatedAt"></span>
                    
                    <div class="flex items-center space-x-2.5">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors">
                            Close
                        </button>
                        <button x-show="isEditing" type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection