@extends('layouts.admin')

@section('title', 'Community Posts | REIAC Admin Panel')

@section('content')

<div
    x-data="{
        viewModalOpen: false,
        editModalOpen: false,
        currentPost: {
            id: null,
            title: '',
            content: '',
            category_id: '',
            category_name: '',
            status: 'published',
            is_solved: false,
            created_at: '',
            author_name: '',
            author_username: '',
            author_avatar: '',
            views_count: 0,
            likes_count: 0,
            comments_count: 0,
            media: []
        },

        openViewModal(post) {
            this.currentPost = { ...post };
            this.viewModalOpen = true;
        },

        openEditModal(post) {
            this.currentPost = { ...post };
            this.editModalOpen = true;
        },

        closeAllModals() {
            this.viewModalOpen = false;
            this.editModalOpen = false;
        }
    }"
    class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
    @keydown.escape.window="closeAllModals()"
>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl p-4 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl p-4 shadow-xs">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Posts & Discussions</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage community threads, content moderation, feature promotions, and engagement.</p>
        </div>

        <div class="flex items-center space-x-2.5">
            <a
                href="{{ route('community.posts.create') }}"
                target="_blank"
                class="px-4 py-2 bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5"
            >
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New Discussion</span>
            </a>
        </div>
    </div>

    {{-- SUMMARY STAT CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        
        {{-- Total Posts --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider">Total Discussions</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ number_format($totalPostsCount) }}</div>
        </div>

        {{-- Published --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider">Published</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($publishedPostsCount) }}</div>
        </div>

        {{-- Drafts --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider">Drafts</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($draftPostsCount) }}</div>
        </div>

        {{-- Archived --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider">Archived</span>
                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-600">{{ number_format($archivedPostsCount) }}</div>
        </div>

        {{-- Solved --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider">Solved</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($solvedPostsCount) }}</div>
        </div>

    </div>

    {{-- FILTER TABS & SEARCH BAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-4 space-y-3">
        
        {{-- Status Tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="inline-flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
                <a href="{{ route('admin.posts') }}"
                   class="px-3 py-1.5 rounded-lg transition {{ !request()->filled('status') ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    All Discussions ({{ $totalPostsCount }})
                </a>
                <a href="{{ route('admin.posts', ['status' => 'published'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'published' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Published ({{ $publishedPostsCount }})
                </a>
                <a href="{{ route('admin.posts', ['status' => 'draft'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'draft' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Drafts ({{ $draftPostsCount }})
                </a>
                <a href="{{ route('admin.posts', ['status' => 'archived'] + request()->except('status', 'page')) }}"
                   class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'archived' ? 'bg-white text-slate-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                    Archived ({{ $archivedPostsCount }})
                </a>
            </div>

            <span class="text-xs text-slate-400 font-medium">
                Showing {{ $posts->firstItem() ?? 0 }}–{{ $posts->lastItem() ?? 0 }} of {{ $posts->total() }} results
            </span>
        </div>

        {{-- Search & Category Filter Form --}}
        <form method="GET" action="{{ route('admin.posts') }}" class="flex flex-col sm:flex-row items-center gap-2.5">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            {{-- Search Bar --}}
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search discussions by title or author name..."
                       class="w-full text-xs pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
            </div>

            {{-- Category Filter --}}
            <div class="w-full sm:w-56">
                <select name="category_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition font-semibold text-slate-700">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit & Reset --}}
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Filter
                </button>
                @if(request('search') || request('category_id') || request('status'))
                    <a href="{{ route('admin.posts') }}" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

    </div>

    {{-- POSTS TABLE --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-4">#ID</th>
                        <th class="py-3.5 px-4 min-w-[280px]">Discussion Details</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Engagement</th>
                        <th class="py-3.5 px-4 text-right">Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($posts as $post)
                        @php
                            $rawStatus = $post->status ?? 'published';
                            $statusValue = is_object($rawStatus) && property_exists($rawStatus, 'value') 
                                ? $rawStatus->value 
                                : (string) $rawStatus;

                            $authorAvatar = optional($post->user?->profile)->avatar
                                ? asset('storage/' . $post->user->profile->avatar)
                                : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name ?? 'User') . '&background=0B132B&color=fff';

                            $mediaFiles = $post->media->map(fn($m) => [
                                'url' => asset('storage/' . $m->file_path),
                                'type' => $m->type ?? ($m->file_type ?? 'image')
                            ])->values()->all();

                            $postPayload = [
                                'id' => $post->id,
                                'title' => $post->title,
                                'content' => $post->content,
                                'category_id' => $post->category_id,
                                'category_name' => $post->category?->name ?? 'General',
                                'status' => $statusValue,
                                'is_solved' => (bool) ($post->is_solved ?? false),
                                'created_at' => $post->created_at ? $post->created_at->format('d M Y, h:i A') : 'N/A',
                                'author_name' => $post->user->name ?? 'Unknown',
                                'author_username' => optional($post->user?->profile)->username ?? 'user',
                                'author_avatar' => $authorAvatar,
                                'views_count' => (int) ($post->views_count ?? 0),
                                'likes_count' => (int) ($post->likes_count ?? 0),
                                'comments_count' => (int) ($post->comments_count ?? 0),
                                'media' => $mediaFiles
                            ];
                        @endphp

                        <tr class="hover:bg-slate-50/70 transition-colors">
                            
                            {{-- ID --}}
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-400 text-[11px]">
                                #{{ $post->id }}
                            </td>

                            {{-- Post Title & Content --}}
                            <td class="py-3.5 px-4 max-w-sm">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('community.posts.show', $post) }}" target="_blank"
                                           class="font-bold text-slate-900 hover:text-indigo-600 transition line-clamp-1">
                                            {{ $post->title }}
                                        </a>
                                        @if($post->is_solved)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 flex items-center space-x-0.5">
                                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                <span>Solved</span>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 line-clamp-1 leading-relaxed">
                                        {{ Str::limit(strip_tags($post->content), 90) }}
                                    </p>
                                </div>
                            </td>

                            {{-- Author --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2.5">
                                    <img src="{{ $authorAvatar }}" class="w-8 h-8 rounded-xl object-cover border border-slate-200 shrink-0">
                                    <div>
                                        <p class="font-bold text-slate-800 leading-tight">{{ $post->user->name ?? 'Deleted User' }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">@ {{ optional($post->user?->profile)->username ?? 'user' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $post->category->name ?? 'General' }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($statusValue === 'published')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Published</span>
                                    </span>
                                @elseif($statusValue === 'draft')
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Draft</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>{{ ucfirst($statusValue) }}</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Engagement Metrics --}}
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-3 text-slate-500 text-[11px] font-bold">
                                    <span class="flex items-center space-x-1" title="Views">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>{{ number_format($post->views_count ?? 0) }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1" title="Likes">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                        <span>{{ number_format($post->likes_count ?? 0) }}</span>
                                    </span>
                                    <span class="flex items-center space-x-1" title="Comments">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>{{ number_format($post->comments_count ?? 0) }}</span>
                                    </span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="py-3.5 px-4 text-right text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $post->created_at?->format('d M Y') }}
                            </td>

                            {{-- Action Buttons --}}
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-1.5">
                                    
                                    {{-- View Modal --}}
                                    <button
                                        type="button"
                                        @click='openViewModal(@json($postPayload))'
                                        class="p-1.5 text-slate-500 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 rounded-xl transition"
                                        title="View Details"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    {{-- Edit Modal --}}
                                    <button
                                        type="button"
                                        @click='openEditModal(@json($postPayload))'
                                        class="p-1.5 text-slate-500 hover:text-amber-600 bg-slate-100 hover:bg-amber-50 rounded-xl transition"
                                        title="Edit Post"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    {{-- Delete Post (SweetAlert2) --}}
                                    <form
                                        id="delete-post-{{ $post->id }}"
                                        method="POST"
                                        action="{{ route('admin.posts.destroy', $post) }}"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            onclick="confirmPostDelete('delete-post-{{ $post->id }}', '{{ addslashes($post->title) }}')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 rounded-xl transition"
                                            title="Delete Post"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14"/></svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold">No discussions found matching criteria.</p>
                                <p class="text-xs text-slate-400 mt-1">Try clearing your search query or status filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($posts->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    {{-- VIEW POST DETAIL MODAL --}}
    <div
        x-show="viewModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div @click.outside="viewModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full border border-slate-100 overflow-hidden flex flex-col my-auto max-h-[90vh]">
            
            {{-- Modal Topbar --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                    <h3 class="text-sm font-black text-slate-800">Discussion Overview</h3>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600" x-text="'#' + currentPost.id"></span>
                </div>
                <button @click="viewModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4">
                
                {{-- Author & Category Card --}}
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="flex items-center space-x-3">
                        <img :src="currentPost.author_avatar" class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                        <div>
                            <h4 class="text-xs font-bold text-slate-900 leading-tight" x-text="currentPost.author_name"></h4>
                            <p class="text-[10px] text-slate-400" x-text="'@' + currentPost.author_username"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700" x-text="currentPost.category_name"></span>
                        <p class="text-[10px] text-slate-400 mt-1" x-text="currentPost.created_at"></p>
                    </div>
                </div>

                {{-- Post Title --}}
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Title</span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 mt-0.5" x-text="currentPost.title"></h2>
                </div>

                {{-- Post Content --}}
                <div class="p-4 bg-slate-50/70 rounded-2xl border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line font-medium" x-text="currentPost.content"></div>

                {{-- Media Thumbnails (if any) --}}
                <template x-if="currentPost.media && currentPost.media.length > 0">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Attached Media (<span x-text="currentPost.media.length"></span>)</span>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <template x-for="item in currentPost.media" :key="item.url">
                                <a :href="item.url" target="_blank" class="rounded-xl overflow-hidden border border-slate-200 block aspect-video bg-slate-100 group relative">
                                    <img :src="item.url" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Engagement Bar --}}
                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-center">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Views</span>
                        <span class="text-xs font-black text-slate-800" x-text="currentPost.views_count"></span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-center">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Likes</span>
                        <span class="text-xs font-black text-rose-600" x-text="currentPost.likes_count"></span>
                    </div>
                    <div class="p-3 bg-white border border-slate-200 rounded-xl text-center">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">Comments</span>
                        <span class="text-xs font-black text-indigo-600" x-text="currentPost.comments_count"></span>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-between shrink-0">
                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                      :class="currentPost.status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                      x-text="currentPost.status"></span>

                <div class="flex items-center space-x-2">
                    <button type="button" @click="viewModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Close
                    </button>
                    <button type="button" @click="viewModalOpen = false; openEditModal(currentPost)" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                        Edit Discussion
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- EDIT POST MODAL --}}
    <div
        x-show="editModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div @click.outside="editModalOpen = false" class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full border border-slate-100 overflow-hidden flex flex-col my-auto max-h-[90vh]">
            
            {{-- Modal Topbar --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-black text-slate-800">Edit Discussion</h3>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600" x-text="'#' + currentPost.id"></span>
                </div>
                <button @click="editModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form :action="'{{ route('admin.posts') }}/' + currentPost.id" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-y-auto">
                @csrf
                @method('PATCH')

                <div class="p-6 space-y-4">
                    
                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Post Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" x-model="currentPost.title" required
                               class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold">
                    </div>

                    {{-- Category & Status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" x-model="currentPost.category_id" required
                                    class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold text-slate-800">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                            <select name="status" x-model="currentPost.status" required
                                    class="w-full text-xs px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Discussion Content <span class="text-rose-500">*</span></label>
                        <textarea name="content" x-model="currentPost.content" rows="6" required
                                  class="w-full text-xs p-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium text-slate-800 resize-none leading-relaxed"></textarea>
                    </div>

                    {{-- Solved Toggle --}}
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-800">Discussion Solved / Answered</label>
                            <p class="text-[11px] text-slate-400">Mark this question or community discussion thread as resolved.</p>
                        </div>
                        <input type="checkbox" name="is_solved" value="1" x-model="currentPost.is_solved"
                               class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                    </div>

                    {{-- Add Media --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Attach Additional Media</label>
                        <input type="file" name="media[]" multiple accept=".jpg,.jpeg,.png,.webp,.mp4"
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-end space-x-2.5 shrink-0">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script>
function confirmPostDelete(formId, postTitle) {
    Swal.fire({
        title: 'Delete Discussion?',
        html: `Are you sure you want to delete <b>"${postTitle}"</b>?<br><span class="text-xs text-slate-400">This action can be undone by admins via restore if needed.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#E11D48',
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