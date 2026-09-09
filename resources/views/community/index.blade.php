@props([
    'title' => 'Community | REIAC',
    'active' => 'home',
    'user' => auth()->user(),
    'sidebar' => true,
    'rightbar' => true,
    'posts' => collect(),
    'notificationsCount' => 0,
    'trendingTopics' => collect(),
    'categories' => collect(),
    'tags' => collect(),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>

    {{-- Prevent Alpine-controlled elements from flashing before Alpine initializes. --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">



    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden flex" style="display: none;">

        <div @click="mobileMenuOpen = false" x-show="mobileMenuOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">

            @auth
                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ $user?->profile?->avatar
                        ? asset('storage/' . $user->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff' }}"
                        alt="{{ $user?->name ?? 'User' }}" class="w-10 h-10 rounded-full object-cover">

                    <div>
                        <div class="font-bold text-slate-900 text-sm">
                            {{ $user?->name }}
                        </div>

                        <a href="{{ route('community.profile.me') }}"
                            class="text-xs text-amber-600 font-semibold hover:underline">
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">

                <a href="{{ route('community.index') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6">
                        </path>
                    </svg>
                    Community Home
                </a>

                <a href="{{ route('community.index') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                        </path>
                    </svg>
                    All Discussions
                </a>

                <a href="{{ route('community.index', ['category' => 'general']) }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    General
                </a>

                <a href="{{ route('community.index', ['category' => 'ask-a-solution']) }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Ask a Solution
                </a>

                <a href="{{ route('community.index', ['category' => 'jobs-info']) }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    Jobs Info
                </a>

                <div class="border-t border-slate-100 my-2"></div>

                <a href="{{ route('community.activity') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    My Activity
                </a>

                {{-- <a href="{{ route('community.saved') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Saved Posts
                </a> --}}

                <div class="border-t border-slate-100 my-2"></div>

                <a href="{{ route('community.profile.edit') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37-2.37a1.724 1.724 0 001.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                    Settings
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-red-600 text-left">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>

            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <main
        class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        <x-community.sidebar :top-contributors="$topContributors" :categories="$categories" :tags="$tags" />

        {{-- CENTER FEED --}}
        <section id="community-posts-feed" class="space-y-5 min-w-0">

            <div class="bg-white p-5 lg:p-6 rounded-2xl shadow-sm border border-slate-200/70">

                <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Community
                </h1>

                <p class="text-xs text-slate-500 mt-1">
                    Ask questions. Share knowledge. Find solutions.
                </p>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 lg:gap-3 mt-4">

                    <a href="{{ route('community.index') }}"
                        class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div
                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">
                                All
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                All Discussions
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('community.index', ['category' => 'general']) }}"
                        class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div
                            class="w-8 h-8 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">
                                General
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                Discuss anything
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('community.index', ['category' => 'ask-a-solution']) }}"
                        class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div
                            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                </path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">
                                Ask a Solution
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                Get solutions
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('community.index', ['category' => 'jobs-info']) }}"
                        class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center gap-2.5 cursor-pointer hover:bg-slate-100 transition">
                        <div
                            class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 text-xs truncate">
                                Jobs Info
                            </div>
                            <div class="text-[10px] text-slate-500 truncate">
                                Job & Career Info
                            </div>
                        </div>
                    </a>

                </div>

                {{-- SEARCH --}}
                <form method="GET" action="{{ route('community.index') }}"
                    class="mt-4 relative flex items-center">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif

                    @if (request('tag'))
                        <input type="hidden" name="tag" value="{{ request('tag') }}">
                    @endif

                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <span class="absolute left-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search discussions, users, topics..."
                        class="w-full bg-slate-50 border border-slate-200/80 rounded-xl pl-10 pr-20 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400">

                    <button type="submit"
                        class="absolute right-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1 shadow-xs">
                        Search
                    </button>
                </form>

                {{-- SORT --}}
                <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-100 text-xs">

                    <div class="flex items-center gap-5 font-semibold text-slate-500">

                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}"
                            class="{{ request('sort', 'latest') === 'latest' ? 'text-slate-900 border-b-2 border-amber-400 pb-1' : 'hover:text-slate-900 transition' }}">
                            Latest
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}"
                            class="{{ request('sort') === 'trending' ? 'text-slate-900 border-b-2 border-amber-400 pb-1' : 'hover:text-slate-900 transition' }}">
                            Trending
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'most_discussed']) }}"
                            class="{{ request('sort') === 'most_discussed' ? 'text-slate-900 border-b-2 border-amber-400 pb-1' : 'hover:text-slate-900 transition' }}">
                            Most Discussed
                        </a>

                    </div>

                </div>
            </div>

            {{-- POSTS --}}
            @php
                $postCollection = method_exists($posts, 'getCollection') ? $posts->getCollection() : collect($posts);

                $authorIds = $postCollection->pluck('user_id')->filter()->unique()->values();

                $followingAuthorIds =
                    auth()->check() && $authorIds->isNotEmpty()
                        ? \App\Models\Follow::where('follower_id', auth()->id())
                            ->whereIn('following_id', $authorIds)
                            ->pluck('following_id')
                            ->map(fn($id) => (int) $id)
                            ->toArray()
                        : [];
            @endphp

            @forelse($posts as $post)

                @php
                    $author = $post->user;
                    $profile = $author?->profile;
                    $isOwnPost = auth()->check() && auth()->id() === $post->user_id;
                    $isFollowingAuthor = auth()->check() && in_array((int) $post->user_id, $followingAuthorIds, true);

                    $authorAvatar = $profile?->avatar
                        ? asset('storage/' . $profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' .
                            urlencode($author?->name ?? 'User') .
                            '&background=0c1b33&color=fff';

                    $authorUsername = $profile?->username;

                    $authorUrl = $authorUsername
                        ? route('community.profile', $authorUsername)
                        : route('community.profile', $author?->id);

                    $shareUrl = route('community.posts.show', $post);
                    $shareText = trim(
                        $post->title . ' — ' . \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 180),
                    );
                    $shareImageUrl = $post->media?->first()
                        ? asset('storage/' . $post->media->first()->file_path)
                        : null;
                @endphp

                <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-4 min-w-0 overflow-hidden"
                    @follow-updated.window="
                    if (Number($event.detail.userId) === {{ (int) $post->user_id }}) {
                        following = Boolean($event.detail.following);
                    }
                "
                    x-data="{
                        currentUserId: {{ auth()->check() ? auth()->id() : 'null' }},
                        postMenuOpen: false,
                        postEditOpen: false,
                        postDeleteConfirm: false,
                        postActionLoading: false,
                        postTitle: @js($post->title),
                        postContent: @js($post->content),
                        postCategoryId: @js($post->category_id),
                        postCategoryName: @js($post->category?->name ?? ''),
                        availableTags: @js(collect($tags ?? [])->map(fn($tag) => ['id' => (int) $tag->id, 'name' => $tag->name])->values()),
                        selectedTags: @js($post->tags->pluck('id')->map(fn($id) => (int) $id)->values()),
                        savedPostTags: @js($post->tags->pluck('id')->map(fn($id) => (int) $id)->values()),
                        editFiles: [],
                        editFilePreviews: [],
                        savedPostTitle: @js($post->title),
                        savedPostContent: @js($post->content),
                        savedPostCategoryId: @js($post->category_id),
                        likesCount: {{ $post->likes_count ?? 0 }},
                        following: {{ $isFollowingAuthor ? 'true' : 'false' }},
                        followLoading: false,
                        liked: {{ auth()->check() &&$post->likes()->where('user_id', auth()->id())->exists()? 'true': 'false' }},
                        saved: {{ auth()->check() &&$post->savedBy()->where('user_id', auth()->id())->exists()? 'true': 'false' }},
                        copied: false,
                        commentsCount: {{ $post->comments_count ?? 0 }},
                        showAllComments: false,
                        showFullContent: false,
                        showAllTags: false,
                        mediaIndex: 0,
                        editExistingMediaIndex: 0,
                        editPreviewIndex: 0,
                        newCommentText: '',
                        replyingTo: null,
                        replyText: '',
                        editingCommentId: null,
                        editingCommentText: '',
                        commentActionLoading: false,
                        openCommentMenuId: null,
                        deleteConfirmComment: null,
                        deleteConfirmIsReply: false,
                        deleteConfirmParentComment: null,
                        commentToast: '',
                        commentToastType: 'success',
                        commentToastTimer: null,
                    
                        comments: {{ Js::from(
                            $post->comments->whereNull('parent_id')->map(
                                    fn($comment) => [
                                        'id' => $comment->id,
                                        'content' => $comment->content,
                                        'parent_id' => $comment->parent_id,
                        
                                        'created_at_human' => $comment->created_at->diffForHumans(),
                        
                                        'user' => [
                                            'id' => $comment->user?->id,
                        
                                            'name' => $comment->user?->name ?? 'User',
                        
                                            'avatar' => $comment->user?->profile?->avatar
                                                ? asset('storage/' . $comment->user->profile->avatar)
                                                : 'https://ui-avatars.com/api/?name=' .
                                                    urlencode($comment->user?->name ?? 'User') .
                                                    '&background=0c1b33&color=fff',
                                        ],
                        
                                        'replies' => $comment->replies->map(
                                                fn($reply) => [
                                                    'id' => $reply->id,
                                                    'content' => $reply->content,
                                                    'parent_id' => $reply->parent_id,
                        
                                                    'created_at_human' => $reply->created_at->diffForHumans(),
                        
                                                    'user' => [
                                                        'id' => $reply->user?->id,
                        
                                                        'name' => $reply->user?->name ?? 'User',
                        
                                                        'avatar' => $reply->user?->profile?->avatar
                                                            ? asset('storage/' . $reply->user->profile->avatar)
                                                            : 'https://ui-avatars.com/api/?name=' .
                                                                urlencode($reply->user?->name ?? 'User') .
                                                                '&background=0c1b33&color=fff',
                                                    ],
                                                ],
                                            )->values(),
                                    ],
                                )->values(),
                        ) }},
                        loadingMore: false,
                    
                        isPostOwner() {
                            return this.currentUserId !== null &&
                                Number(this.currentUserId) === Number({{ (int) $post->user_id }});
                        },
                    
                        openPostMenu() {
                            if (!this.isPostOwner()) return;
                            this.postMenuOpen = !this.postMenuOpen;
                        },
                    
                        toggleEditTag(id) {
                            id = Number(id);
                            if (this.selectedTags.includes(id)) {
                                this.selectedTags = this.selectedTags.filter(tagId => Number(tagId) !== id);
                            } else if (this.selectedTags.length < 5) {
                                this.selectedTags = [...this.selectedTags, id];
                            }
                        },

                        toggleEditTagFromInput(event, id) {
                            id = Number(id);
                            if (event.target.checked) {
                                if (this.selectedTags.includes(id)) return;
                                if (this.selectedTags.length >= 5) {
                                    event.target.checked = false;
                                    return;
                                }
                                this.selectedTags = [...this.selectedTags, id];
                            } else {
                                this.selectedTags = this.selectedTags.filter(tagId => Number(tagId) !== id);
                            }
                        },

                        handleEditFiles(event) {
                            const input = event.target;
                            const files = Array.from(input.files || []);

                            this.editFiles.forEach(file => {
                                const oldPreview = this.editFilePreviews.find(preview => preview.file === file);
                                if (oldPreview?.url) URL.revokeObjectURL(oldPreview.url);
                            });

                            this.editFiles = files;
                            this.editFilePreviews = files.map(file => ({
                                file,
                                name: file.name,
                                size: file.size,
                                type: file.type || '',
                                isVideo: (file.type || '').startsWith('video/'),
                                url: URL.createObjectURL(file)
                            }));
                        },

                        removeEditFile(index) {
                            const input = this.$refs.editMediaInput;
                            if (!input) return;

                            const preview = this.editFilePreviews[index];
                            if (preview?.url) URL.revokeObjectURL(preview.url);

                            const dataTransfer = new DataTransfer();
                            this.editFiles.forEach((file, fileIndex) => {
                                if (fileIndex !== index) dataTransfer.items.add(file);
                            });

                            input.files = dataTransfer.files;
                            this.editFiles = Array.from(input.files || []);
                            this.editFilePreviews = this.editFiles.map(file => ({
                                file,
                                name: file.name,
                                size: file.size,
                                type: file.type || '',
                                isVideo: (file.type || '').startsWith('video/'),
                                url: URL.createObjectURL(file)
                            }));

                            if (this.editPreviewIndex >= this.editFilePreviews.length) {
                                this.editPreviewIndex = Math.max(this.editFilePreviews.length - 1, 0);
                            }
                        },

                        clearEditFiles() {
                            this.editFilePreviews.forEach(preview => {
                                if (preview?.url) URL.revokeObjectURL(preview.url);
                            });
                            this.editFiles = [];
                            this.editFilePreviews = [];
                            this.editPreviewIndex = 0;
                            if (this.$refs.editMediaInput) {
                                this.$refs.editMediaInput.value = '';
                            }
                        },

                        startEditPost() {
                            if (!this.isPostOwner()) return;
                    
                            this.postMenuOpen = false;
                            this.postEditOpen = true;
                            this.postTitle = this.savedPostTitle;
                            this.postContent = this.savedPostContent;
                            this.postCategoryId = this.savedPostCategoryId;
                            this.selectedTags = [...this.savedPostTags];
                            this.clearEditFiles();
                            this.editExistingMediaIndex = 0;
                        },
                    
                        cancelEditPost() {
                            if (this.postActionLoading) return;
                    
                            this.postEditOpen = false;
                            this.postTitle = this.savedPostTitle;
                            this.postContent = this.savedPostContent;
                            this.postCategoryId = this.savedPostCategoryId;
                            this.selectedTags = [...this.savedPostTags];
                            this.clearEditFiles();
                        },
                    
                        requestDeletePost() {
                            if (!this.isPostOwner() || this.postActionLoading) return;
                    
                            this.postMenuOpen = false;
                            this.postDeleteConfirm = true;
                        },
                    
                        cancelDeletePost() {
                            if (this.postActionLoading) return;
                            this.postDeleteConfirm = false;
                        },

                        nextMedia(total) {
                            if (total < 2) return;
                            this.mediaIndex = (this.mediaIndex + 1) % total;
                        },

                        prevMedia(total) {
                            if (total < 2) return;
                            this.mediaIndex = (this.mediaIndex - 1 + total) % total;
                        },

                        nextEditExistingMedia(total) {
                            if (total < 2) return;
                            this.editExistingMediaIndex = (this.editExistingMediaIndex + 1) % total;
                        },

                        prevEditExistingMedia(total) {
                            if (total < 2) return;
                            this.editExistingMediaIndex = (this.editExistingMediaIndex - 1 + total) % total;
                        },
                    
                        toggleFollow() {
                            @guest
window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return; @endguest
                    
                            @if ($author && !$isOwnPost) if (this.followLoading) {
                                return;
                            }

                            this.followLoading = true;

                            fetch('{{ route('users.follow.toggle', $author) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(async response => {
                                if (response.status === 401) {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return null;
                                }

                                if (!response.ok) {
                                    throw new Error('Follow request failed');
                                }

                                return response.json();
                            })
                            .then(data => {
                                if (!data || !data.success || !data.data) {
                                    return;
                                }

                                const following = Boolean(data.data.following);

                                this.following = following;

                                window.dispatchEvent(
                                    new CustomEvent('follow-updated', {
                                        detail: {
                                            userId: {{ (int) $post->user_id }},
                                            following: following
                                        }
                                    })
                                );
                            })
                            .catch(error => {
                                console.error('Follow error:', error);
                            })
                            .finally(() => {
                                this.followLoading = false;
                            }); @endif
                        },
                    
                        toggleLike() {
                            @guest
window.dispatchEvent(new CustomEvent('open-login-modal'));
                                return; @endguest
                    
                            fetch('{{ route('community.posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(async response => {
                                    if (response.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return null;
                                    }
                                    if (!response.ok) {
                                        throw new Error('Like request failed');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data?.success) {
                                        this.liked = Boolean(data.liked);
                                        this.likesCount = data.likes_count;
                                    }
                                })
                                .catch(error => console.error('Like error:', error));
                        },
                    
                        toggleSave() {
                            @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                            @endguest
                    
                            fetch('{{ route('community.posts.save', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(async response => {
                                    if (response.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return null;
                                    }
                                    if (!response.ok) {
                                        throw new Error('Save request failed');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data?.success) {
                                        this.saved = Boolean(data.saved);
                                    }
                                })
                                .catch(error => console.error('Save error:', error));
                        },
                    
                        async sharePost() {
                            const shareUrl = @js($shareUrl);
                            const shareTitle = @js($post->title);
                            const shareText = @js($shareText);
                            const imageUrl = @js($shareImageUrl);
                    
                            try {
                                if (navigator.share) {
                                    const shareData = {
                                        title: shareTitle,
                                        text: shareText,
                                        url: shareUrl
                                    };
                    
                                    if (
                                        imageUrl &&
                                        navigator.canShare &&
                                        typeof File !== 'undefined'
                                    ) {
                                        try {
                                            const response = await fetch(imageUrl);
                    
                                            if (response.ok) {
                                                const blob = await response.blob();
                                                const imageFile = new File(
                                                    [blob],
                                                    'reiac-post.jpg', { type: blob.type || 'image/jpeg' }
                                                );
                    
                                                if (navigator.canShare({ files: [imageFile] })) {
                                                    shareData.files = [imageFile];
                                                }
                                            }
                                        } catch (imageError) {
                                            console.warn('Image share unavailable:', imageError);
                                        }
                                    }
                    
                                    await navigator.share(shareData);
                                    return;
                                }
                    
                                if (navigator.clipboard?.writeText) {
                                    await navigator.clipboard.writeText(shareUrl);
                                    this.copied = true;
                    
                                    setTimeout(() => {
                                        this.copied = false;
                                    }, 2000);
                    
                                    return;
                                }
                    
                                window.prompt('Copy this link:', shareUrl);
                            } catch (error) {
                                if (error?.name === 'AbortError') {
                                    return;
                                }
                    
                                console.error('Share failed:', error);
                            }
                        },
                    
                        showCommentToast(message, type = 'success') {
                            this.commentToast = message;
                            this.commentToastType = type;
                    
                            clearTimeout(this.commentToastTimer);
                            this.commentToastTimer = setTimeout(() => {
                                this.commentToast = '';
                            }, 2600);
                        },
                    
                        async submitComment() {
                            @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                            @endguest
                    
                            const content = this.newCommentText.trim();
                            if (!content) return;
                    
                            this.commentActionLoading = true;
                    
                            try {
                                const response = await fetch('{{ route('community.posts.comment.store', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({ content })
                                });
                    
                                if (response.status === 401) {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                }
                    
                                const data = await response.json();
                    
                                if (!response.ok || !data.success) {
                                    throw new Error(data.message || 'Unable to add comment.');
                                }
                    
                                const newComment = data.comment ?? data.data;
                                if (!newComment) {
                                    throw new Error('Invalid comment response.');
                                }
                    
                                newComment.user = newComment.user || {};
                                newComment.user.id = newComment.user.id ?? this.currentUserId;
                                newComment.user.name = newComment.user.name || 'You';
                    
                                this.comments.unshift(newComment);
                                this.commentsCount = Number(this.commentsCount) + 1;
                                this.newCommentText = '';
                                this.showAllComments = true;
                                this.showCommentToast('Comment added successfully.');
                            } catch (error) {
                                console.error('Comment error:', error);
                                this.showCommentToast(error.message || 'Unable to add comment.', 'error');
                            } finally {
                                this.commentActionLoading = false;
                            }
                        },
                    
                        startReply(commentId, userName) {
                            @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                            @endguest
                    
                            this.replyingTo = {
                                id: Number(commentId),
                                userName: userName
                            };
                    
                            this.replyText = '';
                    
                            this.$nextTick(() => {
                                this.$refs.replyInput?.focus();
                            });
                        },
                    
                        cancelReply() {
                            this.replyingTo = null;
                            this.replyText = '';
                        },
                    
                        async submitReply() {
                            @guest
                            window.dispatchEvent(new CustomEvent('open-login-modal'));
                            return;
                            @endguest
                    
                            if (!this.replyingTo || !this.replyText.trim()) return;
                    
                            const parentId = this.replyingTo.id;
                            const content = this.replyText.trim();
                            this.commentActionLoading = true;
                    
                            try {
                                const response = await fetch('{{ route('community.posts.comment.store', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({ content, parent_id: parentId })
                                });
                    
                                if (response.status === 401) {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                }
                    
                                const data = await response.json();
                    
                                if (!response.ok || !data.success) {
                                    throw new Error(data.message || 'Unable to add reply.');
                                }
                    
                                const parentComment = this.comments.find(
                                    comment => Number(comment.id) === Number(parentId)
                                );
                    
                                if (parentComment) {
                                    if (!Array.isArray(parentComment.replies)) parentComment.replies = [];
                                    parentComment.replies.push(data.comment);
                                }
                    
                                this.commentsCount = Number(this.commentsCount) + 1;
                                this.replyingTo = null;
                                this.replyText = '';
                                this.showCommentToast('Reply added successfully.');
                            } catch (error) {
                                console.error('Reply error:', error);
                                this.showCommentToast(error.message || 'Unable to add reply.', 'error');
                            } finally {
                                this.commentActionLoading = false;
                            }
                        },
                    
                        isCommentOwner(comment) {
                            return this.currentUserId !== null &&
                                Number(comment?.user?.id) === Number(this.currentUserId);
                        },
                    
                        startEditComment(comment) {
                            if (!this.isCommentOwner(comment)) return;
                    
                            this.editingCommentId = Number(comment.id);
                            this.editingCommentText = comment.content || '';
                    
                            this.$nextTick(() => {
                                this.$refs.editCommentInput?.focus();
                            });
                        },
                    
                        cancelEditComment() {
                            this.editingCommentId = null;
                            this.editingCommentText = '';
                        },
                    
                        async updateComment(comment) {
                            if (!this.isCommentOwner(comment) || !this.editingCommentText.trim()) return;
                    
                            this.commentActionLoading = true;
                    
                            try {
                                const response = await fetch(
                                    '{{ route('community.comments.update', ['comment' => '__COMMENT_ID__']) }}'
                                    .replace('__COMMENT_ID__', comment.id), {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: JSON.stringify({
                                            content: this.editingCommentText.trim()
                                        })
                                    }
                                );
                    
                                if (response.status === 401) {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                }
                    
                                const data = await response.json();
                    
                                if (!response.ok || !data.success) {
                                    throw new Error(data.message || 'Unable to update comment.');
                                }
                    
                                comment.content = data.comment?.content ?? this.editingCommentText.trim();
                                this.cancelEditComment();
                                this.showCommentToast('Comment updated successfully.');
                            } catch (error) {
                                console.error('Update comment error:', error);
                                this.showCommentToast(error.message || 'Unable to update comment.', 'error');
                            } finally {
                                this.commentActionLoading = false;
                            }
                        },
                    
                        requestDeleteComment(comment, isReply = false, parentComment = null) {
                            if (!this.isCommentOwner(comment) || this.commentActionLoading) return;
                    
                            this.openCommentMenuId = null;
                            this.deleteConfirmComment = comment;
                            this.deleteConfirmIsReply = isReply;
                            this.deleteConfirmParentComment = parentComment;
                        },
                    
                        cancelDeleteComment() {
                            if (this.commentActionLoading) return;
                    
                            this.deleteConfirmComment = null;
                            this.deleteConfirmIsReply = false;
                            this.deleteConfirmParentComment = null;
                        },
                    
                        async confirmDeleteComment() {
                            const comment = this.deleteConfirmComment;
                            const isReply = this.deleteConfirmIsReply;
                            const parentComment = this.deleteConfirmParentComment;
                    
                            if (!comment || !this.isCommentOwner(comment)) {
                                this.cancelDeleteComment();
                                return;
                            }
                    
                            this.commentActionLoading = true;
                    
                            try {
                                const response = await fetch(
                                    '{{ route('community.comments.destroy', ['comment' => '__COMMENT_ID__']) }}'
                                    .replace('__COMMENT_ID__', comment.id), {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                            'Accept': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    }
                                );
                    
                                if (response.status === 401) {
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                }
                    
                                const data = await response.json();
                    
                                if (!response.ok || !data.success) {
                                    throw new Error(data.message || 'Unable to delete comment.');
                                }
                    
                                if (isReply && parentComment) {
                                    parentComment.replies = (parentComment.replies || [])
                                        .filter(reply => Number(reply.id) !== Number(comment.id));
                                } else {
                                    this.comments = this.comments
                                        .filter(item => Number(item.id) !== Number(comment.id));
                                }
                    
                                this.commentsCount = Math.max(0, Number(this.commentsCount) - 1);
                                this.cancelDeleteComment();
                                this.showCommentToast(isReply ? 'Reply deleted successfully.' : 'Comment deleted successfully.');
                            } catch (error) {
                                console.error('Delete comment error:', error);
                                this.showCommentToast(error.message || 'Unable to delete comment.', 'error');
                            } finally {
                                this.commentActionLoading = false;
                            }
                        },
                    
                        loadMoreComments() {
                            this.loadingMore = true;
                    
                            fetch(
                                    `{{ route('community.posts.comments', $post) }}?skip=${this.comments.length}`
                                )
                                .then(async response => {
                                    if (!response.ok) {
                                        throw new Error('Unable to load comments.');
                                    }
                    
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success && Array.isArray(data.comments)) {
                                        this.comments = this.comments.concat(data.comments);
                                    }
                                })
                                .catch(error => {
                                    console.error('Load comments error:', error);
                                })
                                .finally(() => {
                                    this.loadingMore = false;
                                });
                        }
                    }">

                    {{-- GROUP INFO BADGE & JOIN BUTTON --}}
                    @if ($post->group)
                        @php
                            $group = $post->group;
                            $userMembership = auth()->check()
                                ? $group
                                    ->users()
                                    ->where('user_id', auth()->id())
                                    ->first()
                                : null;
                            $isMember = $userMembership && $userMembership->pivot->status === 'active';
                            $isPending = $userMembership && $userMembership->pivot->status === 'pending';
                            $isRejected = $userMembership && $userMembership->pivot->status === 'rejected';
                        @endphp
                        <div
                            class="flex items-center justify-between bg-slate-50 border border-slate-200/60 px-3 py-2 rounded-xl text-xs">
                            <div class="flex items-center gap-1.5 text-slate-700 font-semibold truncate">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>Group:</span>
                                <a href="{{ route('community.groups.show', $group->slug ?? $group->id) }}"
                                    class="text-amber-600 hover:underline truncate">
                                    {{ $group->name }}
                                </a>
                            </div>

                            @auth
                                @if ($isMember)
                                    <span
                                        class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        Member
                                    </span>
                                @elseif ($isPending)
                                    <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">
                                        Pending
                                    </span>
                                @else
                                    <form action="{{ route('community.groups.join', $group) }}" method="POST"
                                        class="shrink-0">

                                        @csrf
                                        <button type="submit"
                                            class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[10px] font-bold transition shadow-xs">
                                            {{ $isRejected ? 'Send Again' : 'Join Group' }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <button type="button" @click="window.dispatchEvent(new CustomEvent('open-login-modal'))"
                                    class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[10px] font-bold transition shadow-xs">
                                    Join Group
                                </button>
                            @endauth
                        </div>
                    @endif

                    {{-- POST HEADER --}}
                    <div class="flex flex-row items-start justify-between gap-2 sm:gap-3 min-w-0">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <img src="{{ $authorAvatar }}" alt="{{ $author?->name ?? 'User' }}"
                                class="w-9 h-9 rounded-full object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="font-bold text-slate-900 text-xs truncate max-w-full">
                                        @if ($author)
                                            <a href="{{ $authorUrl }}"
                                                class="hover:underline">{{ $author->name }}</a>
                                        @else
                                            User
                                        @endif
                                    </div>

                                    {{-- COUNTRY FLAG --}}
                                    @if ($profile?->country)
                                        @php
                                            $isoCode = strtolower(trim($profile->country->iso_code ?: ''));
                                            if (!$isoCode && !empty($profile->country->code) && strlen(trim($profile->country->code)) === 2) {
                                                $isoCode = strtolower(trim($profile->country->code));
                                            }
                                        @endphp
                                        @if ($isoCode)
                                            <img
                                                src="https://flagcdn.com/20x15/{{ $isoCode }}.png"
                                                srcset="https://flagcdn.com/40x30/{{ $isoCode }}.png 2x"
                                                width="20"
                                                height="15"
                                                alt="{{ $profile->country->name }}"
                                                title="{{ $profile->country->name }}"
                                                class="w-4.5 h-3.5 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle"
                                                loading="lazy"
                                                onerror="this.style.display='none'"
                                            >
                                        @elseif (!empty($profile->country->flag) && (str_starts_with($profile->country->flag, 'http://') || str_starts_with($profile->country->flag, 'https://')))
                                            <img
                                                src="{{ $profile->country->flag }}"
                                                alt="{{ $profile->country->name }}"
                                                title="{{ $profile->country->name }}"
                                                class="w-4.5 h-3.5 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle"
                                                loading="lazy"
                                                onerror="this.style.display='none'"
                                            >
                                        @endif
                                    @endif

                                    @auth
                                        @if ($author && !$isOwnPost)
                                            <button type="button" @click="toggleFollow()" :disabled="followLoading"
                                                class="shrink-0 inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                                :class="following
                                                    ?
                                                    'bg-slate-100 text-slate-600 border-slate-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200' :
                                                    'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-400 hover:text-slate-900 hover:border-amber-400'">
                                                <svg x-show="!following && !followLoading" class="w-3 h-3" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 5v14M5 12h14" />
                                                </svg>

                                                <svg x-show="following && !followLoading" x-cloak class="w-3 h-3"
                                                    fill="none" stroke="currentColor" stroke-width="2.5"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>

                                                <svg x-show="followLoading" x-cloak class="w-3 h-3 animate-spin"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" class="opacity-25">
                                                    </circle>
                                                    <path d="M21 12a9 9 0 01-9 9"></path>
                                                </svg>

                                                <span
                                                    x-text="followLoading ? '...' : (following ? 'Following' : 'Follow')"></span>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                                <div class="mt-0.5 flex items-center gap-1 text-[11px] text-slate-500 min-w-0">
                                    @if ($authorUsername)
                                        <a href="{{ $authorUrl }}"
                                            class="truncate max-w-[140px] sm:max-w-[220px] hover:text-slate-700 hover:underline">{{ '@' . $authorUsername }}</a>
                                        <span class="shrink-0">•</span>
                                    @endif
                                    <span
                                        class="shrink-0 whitespace-nowrap">{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <div class="flex items-center gap-2 flex-wrap justify-end">
                                @if ($post->category)
                                    <span x-text="String(postCategoryName || '').toUpperCase()"
                                        class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md tracking-wide whitespace-nowrap"></span>
                                @endif
                                @if ($post->is_solved)
                                    <span
                                        class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1 whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                        Solved
                                    </span>
                                @endif
                            </div>

                            @if ($isOwnPost)
                                <div class="relative" @click.outside="postMenuOpen = false">
                                    <button type="button" @click.stop="openPostMenu()"
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-slate-200"
                                        aria-label="Post options">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4zm0 6a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>

                                    <div x-show="postMenuOpen" x-cloak x-transition
                                        class="absolute right-0 top-9 z-40 w-36 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl"
                                        @click.stop>
                                        <button type="button" @click="startEditPost()"
                                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 text-left">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none"
                                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                            Edit post
                                        </button>
                                        <button type="button" @click="requestDeletePost()"
                                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-600 hover:bg-red-50 text-left">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h14" />
                                            </svg>
                                            Delete post
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- TITLE --}}
                    @php
                        $postTitleLength = mb_strlen(strip_tags($post->title ?? ''));
                        $postContentLength = mb_strlen(strip_tags($post->content ?? ''));
                        $mediaCount = $post->media?->count() ?? 0;
                    @endphp
                    <div class="text-sm font-bold text-slate-900 leading-snug break-words">
                        <h3
                            :class="showFullContent ? '' : 'line-clamp-2'"
                            class="break-words">
                            <a href="{{ route('community.posts.show', $post) }}" class="hover:underline">
                                <span x-text="postTitle"></span>
                            </a>
                        </h3>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="mt-1 text-xs text-slate-600 leading-relaxed break-words">
                        <p
                            :class="showFullContent ? '' : 'line-clamp-4'"
                            class="whitespace-pre-wrap break-words"
                            x-text="postContent"></p>

                        @if ($postTitleLength > 90 || $postContentLength > 280)
                            <button type="button" @click="showFullContent = !showFullContent"
                                class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 hover:text-amber-600 transition">
                                <span x-text="showFullContent ? 'Show less' : 'Show more'"></span>
                                <svg class="w-3 h-3 transition-transform" :class="showFullContent ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- MEDIA CAROUSEL --}}
                    @if ($mediaCount > 0)
                        <div class="pt-2">
                            <div class="relative w-full max-h-[550px] min-h-[220px] bg-slate-900 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                                @foreach ($post->media as $index => $mediaItem)
                                    <div x-show="mediaIndex === {{ $index }}" x-cloak class="w-full h-full flex items-center justify-center">
                                        @if (($mediaItem->type ?? '') === 'video' || str_starts_with($mediaItem->mime_type ?? '', 'video'))
                                            <video src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                class="w-full max-h-[550px] object-contain" controls preload="metadata"></video>
                                        @else
                                            <img src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                alt="{{ $post->title }}" class="w-full max-h-[550px] object-contain">
                                        @endif
                                    </div>
                                @endforeach

                                @if ($mediaCount > 1)
                                    <button type="button" @click="prevMedia({{ $mediaCount }})"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10"
                                        aria-label="Previous media">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                                    </button>
                                    <button type="button" @click="nextMedia({{ $mediaCount }})"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10"
                                        aria-label="Next media">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                    </button>
                                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-slate-950/65 text-white text-[10px] font-semibold px-2.5 py-1"
                                        x-text="(mediaIndex + 1) + ' / {{ $mediaCount }}'"></div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- TAGS --}}
                    @if ($post->tags && $post->tags->isNotEmpty())
                        @php
                            $visibleTags = $post->tags->take(3);
                            $remainingTags = max($post->tags->count() - 3, 0);
                        @endphp
                        <div class="flex flex-wrap items-center gap-2 pt-1 min-w-0">
                            @foreach ($visibleTags as $tag)
                                <a href="{{ route('community.index', ['tag' => $tag->slug]) }}"
                                    class="max-w-full truncate bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg transition-colors">#{{ $tag->name }}</a>
                            @endforeach
                            @if ($remainingTags > 0)
                                <template x-if="!showAllTags">
                                    <button type="button" @click="showAllTags = true"
                                        class="text-[11px] font-bold text-slate-500 hover:text-amber-600 transition whitespace-nowrap">+{{ $remainingTags }}
                                        more</button>
                                </template>
                                <template x-if="showAllTags">
                                    <div class="flex flex-wrap items-center gap-2 w-full">
                                        @foreach ($post->tags->slice(3) as $tag)
                                            <a href="{{ route('community.index', ['tag' => $tag->slug]) }}"
                                                class="max-w-full truncate bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium px-2.5 py-1 rounded-lg transition-colors">#{{ $tag->name }}</a>
                                        @endforeach
                                        <button type="button" @click="showAllTags = false"
                                            class="text-[11px] font-bold text-slate-500 hover:text-amber-600 transition whitespace-nowrap">Show
                                            less</button>
                                    </div>
                                </template>
                            @endif
                        </div>
                    @endif

                    {{-- ACTION BAR --}}
                    <div
                        class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">

                        <div class="flex items-center gap-5">

                            <button type="button" @click="toggleLike()" class="flex items-center gap-1.5 transition"
                                :class="liked ? 'text-red-500' : 'hover:text-red-500'">
                                <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>

                                <span x-text="likesCount"></span>
                            </button>

                            <button type="button"
                                @click="
                                    @guest
window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    @else
                                        showAllComments = !showAllComments; @endguest
                                "
                                class="flex items-center gap-1.5 hover:text-blue-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>

                                <span x-text="commentsCount"></span>
                            </button>

                            <button type="button" @click="sharePost()" class="flex items-center gap-1.5 transition"
                                :class="copied ? 'text-emerald-600' : 'hover:text-emerald-500'">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                    </path>
                                </svg>

                                <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor"
                                    stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>

                                <span x-text="copied ? 'Copied!' : 'Share'"></span>
                            </button>

                        </div>

                        <button type="button" @click="toggleSave()" class="flex items-center gap-1.5 transition"
                            :class="saved ? 'text-amber-500' : 'hover:text-amber-500'">
                            <svg class="w-4 h-4" :fill="saved ? 'currentColor' : 'none'" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>

                            <span x-text="saved ? 'Saved' : 'Save'"></span>
                        </button>

                    </div>

                    {{-- COMMENTS --}}
                    <div class="pt-4 border-t border-slate-100" x-show="showAllComments" x-cloak>

                        {{-- COMMENT HEADER --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 10h8M8 14h5m8-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">Discussion</p>
                                    <p class="text-[9px] text-slate-400"
                                        x-text="commentsCount + (commentsCount == 1 ? ' comment' : ' comments')"></p>
                                </div>
                            </div>
                        </div>

                        {{-- NEW COMMENT --}}
                        <div class="flex items-end gap-2.5 mb-4">
                            <img src="{{ auth()->check() && auth()->user()->profile?->avatar ? asset('storage/' . auth()->user()->profile->avatar) : 'https://ui-avatars.com/api/?name=User&background=0c1b33&color=fff' }}"
                                alt="Your avatar" class="w-8 h-8 rounded-full object-cover shrink-0 mt-1">
                            <div class="flex-1 min-w-0 relative">
                                <input type="text" x-model="newCommentText"
                                    @keydown.enter.prevent="submitComment()" maxlength="1000"
                                    placeholder="Write a comment..."
                                    class="w-full h-10 bg-slate-50 border border-slate-200 rounded-2xl pl-3 pr-16 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-amber-300 focus:ring-2 focus:ring-amber-100 transition">
                                <button type="button" @click="submitComment()"
                                    :disabled="commentActionLoading || !newCommentText.trim()"
                                    class="absolute right-1 top-1 h-8 px-3 rounded-xl bg-[#0b1329] text-white text-[10px] font-bold hover:bg-slate-800 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span x-show="!commentActionLoading">Post</span>
                                    <span x-show="commentActionLoading">...</span>
                                </button>
                            </div>
                        </div>

                        {{-- INLINE TOAST --}}
                        <div x-show="commentToast" x-cloak x-transition
                            class="mb-3 flex items-center gap-2 rounded-xl border px-3 py-2 text-[10px] font-semibold"
                            :class="commentToastType === 'error'
                                ?
                                'bg-red-50 border-red-100 text-red-600' :
                                'bg-emerald-50 border-emerald-100 text-emerald-700'">
                            <svg x-show="commentToastType !== 'error'" class="w-3.5 h-3.5 shrink-0" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="commentToastType === 'error'" class="w-3.5 h-3.5 shrink-0" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v4m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 3h15.58" />
                            </svg>
                            <span x-text="commentToast"></span>
                        </div>

                        {{-- COMMENTS LIST --}}
                        <div class="space-y-3">
                            <template x-for="comment in comments" :key="comment.id">
                                <div class="group relative">

                                    {{-- MAIN COMMENT --}}
                                    <div class="flex items-start gap-2.5">
                                        <img :src="comment.user.avatar" :alt="comment.user.name"
                                            class="w-8 h-8 rounded-full object-cover shrink-0 ring-2 ring-white shadow-sm">

                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="relative rounded-2xl rounded-tl-md bg-slate-50 border border-slate-100 px-3 py-2.5 hover:border-slate-200 transition">
                                                <div class="flex items-center gap-2 pr-7">
                                                    <span class="text-[11px] font-bold text-slate-900 truncate"
                                                        x-text="comment.user.name"></span>
                                                    <span x-show="Number(comment.user.id) === Number(currentUserId)"
                                                        class="shrink-0 rounded-full bg-amber-100 px-1.5 py-0.5 text-[8px] font-bold text-amber-700">You</span>
                                                    <span class="text-[9px] text-slate-400 shrink-0"
                                                        x-text="comment.created_at_human"></span>
                                                </div>

                                                <template x-if="editingCommentId === Number(comment.id)">
                                                    <div class="mt-2">
                                                        <textarea x-ref="editCommentInput" x-model="editingCommentText" rows="2" maxlength="1000"
                                                            @keydown.ctrl.enter="updateComment(comment)"
                                                            class="w-full bg-white border border-amber-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-100 resize-none"></textarea>
                                                        <div class="flex items-center gap-2 mt-2">
                                                            <button type="button" @click="updateComment(comment)"
                                                                :disabled="commentActionLoading || !editingCommentText.trim()"
                                                                class="px-3 py-1.5 rounded-lg bg-[#0b1329] text-white text-[9px] font-bold disabled:opacity-40">
                                                                <span x-show="!commentActionLoading">Save</span><span
                                                                    x-show="commentActionLoading">Saving...</span>
                                                            </button>
                                                            <button type="button" @click="cancelEditComment()"
                                                                class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[9px] font-bold">Cancel</button>
                                                        </div>
                                                    </div>
                                                </template>

                                                <template x-if="editingCommentId !== Number(comment.id)">
                                                    <p class="mt-1 text-[11px] leading-relaxed text-slate-700 whitespace-pre-wrap break-words"
                                                        x-text="comment.content"></p>
                                                </template>

                                                {{-- 3 DOT MENU --}}
                                                <template
                                                    x-if="isCommentOwner(comment) && editingCommentId !== Number(comment.id)">
                                                    <div class="absolute top-1.5 right-1.5">
                                                        <button type="button"
                                                            @click.stop="openCommentMenuId = openCommentMenuId === Number(comment.id) ? null : Number(comment.id)"
                                                            class="w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:bg-white hover:text-slate-700 transition"
                                                            aria-label="Comment options">
                                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24"
                                                                fill="currentColor">
                                                                <circle cx="5" cy="12" r="1.7" />
                                                                <circle cx="12" cy="12" r="1.7" />
                                                                <circle cx="19" cy="12" r="1.7" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="openCommentMenuId === Number(comment.id)" x-cloak
                                                            x-transition @click.outside="openCommentMenuId = null"
                                                            class="absolute right-0 top-7 z-40 w-28 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl p-1">
                                                            <button type="button"
                                                                @click="openCommentMenuId = null; startEditComment(comment)"
                                                                class="w-full flex items-center gap-2 rounded-lg px-2.5 py-2 text-left text-[10px] font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="1.8"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4 11.5-11.5z" />
                                                                </svg> Edit
                                                            </button>
                                                            <button type="button"
                                                                @click="requestDeleteComment(comment)"
                                                                :disabled="commentActionLoading"
                                                                class="w-full flex items-center gap-2 rounded-lg px-2.5 py-2 text-left text-[10px] font-semibold text-red-600 hover:bg-red-50 disabled:opacity-40">
                                                                <svg class="w-3.5 h-3.5" fill="none"
                                                                    stroke="currentColor" stroke-width="1.8"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 7h16m-10 4v6m4-6v6M9 7V4h6v3m-8 0l1 13h8l1-13" />
                                                                </svg> Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="flex items-center gap-3 px-1 pt-1">
                                                <button type="button"
                                                    @click="startReply(comment.id, comment.user.name)"
                                                    class="text-[9px] font-bold text-slate-400 hover:text-amber-600 transition">Reply</button>
                                                <span x-show="comment.replies && comment.replies.length"
                                                    class="text-[9px] text-slate-300">•</span>
                                                <span x-show="comment.replies && comment.replies.length"
                                                    class="text-[9px] text-slate-400"
                                                    x-text="comment.replies.length + (comment.replies.length === 1 ? ' reply' : ' replies')"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- REPLIES --}}
                                    <template x-if="comment.replies && comment.replies.length">
                                        <div class="ml-10 mt-2 pl-3 border-l-2 border-slate-100 space-y-2">
                                            <template x-for="reply in comment.replies" :key="reply.id">
                                                <div class="flex items-start gap-2">
                                                    <img :src="reply.user.avatar" :alt="reply.user.name"
                                                        class="w-6.5 h-6.5 w-7 h-7 rounded-full object-cover shrink-0 ring-2 ring-white">
                                                    <div class="min-w-0 flex-1">
                                                        <div
                                                            class="relative rounded-2xl rounded-tl-md bg-white border border-slate-100 px-2.5 py-2">
                                                            <div class="flex items-center gap-2 pr-6">
                                                                <span
                                                                    class="text-[10px] font-bold text-slate-800 truncate"
                                                                    x-text="reply.user.name"></span>
                                                                <span
                                                                    x-show="Number(reply.user.id) === Number(currentUserId)"
                                                                    class="shrink-0 rounded-full bg-amber-100 px-1.5 py-0.5 text-[7px] font-bold text-amber-700">You</span>
                                                                <span class="text-[8px] text-slate-400 shrink-0"
                                                                    x-text="reply.created_at_human"></span>
                                                            </div>

                                                            <template x-if="editingCommentId === Number(reply.id)">
                                                                <div class="mt-1.5">
                                                                    <textarea x-ref="editReplyInput" x-model="editingCommentText" rows="2" maxlength="1000"
                                                                        @keydown.ctrl.enter="updateComment(reply)"
                                                                        class="w-full bg-slate-50 border border-amber-200 rounded-xl px-2.5 py-2 text-[10px] focus:outline-none focus:ring-2 focus:ring-amber-100 resize-none"></textarea>
                                                                    <div class="flex items-center gap-2 mt-1.5">
                                                                        <button type="button"
                                                                            @click="updateComment(reply)"
                                                                            :disabled="commentActionLoading || !editingCommentText
                                                                                .trim()"
                                                                            class="px-2.5 py-1.5 rounded-lg bg-[#0b1329] text-white text-[8px] font-bold">Save</button>
                                                                        <button type="button"
                                                                            @click="cancelEditComment()"
                                                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[8px] font-bold">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </template>

                                                            <template x-if="editingCommentId !== Number(reply.id)">
                                                                <p class="mt-0.5 text-[10px] leading-relaxed text-slate-600 whitespace-pre-wrap break-words"
                                                                    x-text="reply.content"></p>
                                                            </template>

                                                            {{-- 3 DOT MENU --}}
                                                            <template
                                                                x-if="isCommentOwner(reply) && editingCommentId !== Number(reply.id)">
                                                                <div class="absolute top-1 right-1">
                                                                    <button type="button"
                                                                        @click.stop="openCommentMenuId = openCommentMenuId === Number(reply.id) ? null : Number(reply.id)"
                                                                        class="w-5.5 h-5.5 w-6 h-6 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition"
                                                                        aria-label="Reply options">
                                                                        <svg class="w-3 h-3" viewBox="0 0 24 24"
                                                                            fill="currentColor">
                                                                            <circle cx="5" cy="12"
                                                                                r="1.6" />
                                                                            <circle cx="12" cy="12"
                                                                                r="1.6" />
                                                                            <circle cx="19" cy="12"
                                                                                r="1.6" />
                                                                        </svg>
                                                                    </button>
                                                                    <div x-show="openCommentMenuId === Number(reply.id)"
                                                                        x-cloak x-transition
                                                                        @click.outside="openCommentMenuId = null"
                                                                        class="absolute right-0 top-7 z-40 w-28 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl p-1">
                                                                        <button type="button"
                                                                            @click="openCommentMenuId = null; startEditComment(reply)"
                                                                            class="w-full flex items-center gap-2 rounded-lg px-2.5 py-2 text-left text-[10px] font-semibold text-slate-600 hover:bg-slate-50">
                                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                                stroke="currentColor"
                                                                                stroke-width="1.8"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4 11.5-11.5z" />
                                                                            </svg> Edit
                                                                        </button>
                                                                        <button type="button"
                                                                            @click="requestDeleteComment(reply, true, comment)"
                                                                            :disabled="commentActionLoading"
                                                                            class="w-full flex items-center gap-2 rounded-lg px-2.5 py-2 text-left text-[10px] font-semibold text-red-600 hover:bg-red-50 disabled:opacity-40">
                                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                                stroke="currentColor"
                                                                                stroke-width="1.8"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="M4 7h16m-10 4v6m4-6v6M9 7V4h6v3m-8 0l1 13h8l1-13" />
                                                                            </svg> Delete
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- REPLY COMPOSER --}}
                        <div x-show="replyingTo" x-cloak x-transition class="mt-4 ml-10 flex items-center gap-2.5">
                            <div
                                class="w-7 h-7 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 10h10a4 4 0 014 4v1m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </div>
                            <div class="flex-1 relative min-w-0">
                                <input x-ref="replyInput" x-model="replyText" @keydown.enter.prevent="submitReply()"
                                    type="text" maxlength="1000"
                                    :placeholder="replyingTo ? 'Reply to ' + replyingTo.userName + '...' : 'Write a reply...'"
                                    class="w-full h-9 bg-slate-50 border border-slate-200 rounded-xl pl-3 pr-16 text-[10px] focus:outline-none focus:bg-white focus:border-amber-300 focus:ring-2 focus:ring-amber-100">
                                <button type="button" @click="submitReply()"
                                    :disabled="commentActionLoading || !replyText.trim()"
                                    class="absolute right-1 top-1 h-7 px-2.5 rounded-lg bg-[#0b1329] text-white text-[9px] font-bold disabled:opacity-40">Reply</button>
                            </div>
                            <button type="button" @click="cancelReply()"
                                class="text-[9px] font-bold text-slate-400 hover:text-slate-700">Cancel</button>
                        </div>

                        {{-- LOAD MORE --}}
                        <div class="text-center mt-4" x-show="comments.length < commentsCount">
                            <button type="button" @click="loadMoreComments()" :disabled="loadingMore"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-[9px] font-bold text-slate-500 hover:text-amber-600 hover:border-amber-200 transition disabled:opacity-50">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="loadingMore ? 'Loading...' : 'See more comments'"></span>
                            </button>
                        </div>

                        {{-- POST EDIT MODAL --}}
                        <template x-teleport="body">
                            <div x-show="postEditOpen" x-cloak x-transition.opacity
                                class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6">

                                {{-- Backdrop --}}
                                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                                    @click.stop="cancelEditPost()"></div>

                                {{-- SAME VISUAL STRUCTURE AS CREATE DISCUSSION MODAL --}}
                                <div x-show="postEditOpen" x-transition @click.stop
                                    class="relative w-full max-w-[672px] max-h-[calc(100vh-32px)] overflow-hidden rounded-2xl bg-white shadow-2xl">

                                    <form method="POST"
                                        action="{{ route('community.posts.update', ['post' => $post]) }}"
                                        enctype="multipart/form-data"
                                        @submit="postActionLoading = true">

                                        @csrf
                                        @method('PUT')

                                        {{-- HEADER --}}
                                        <div
                                            class="h-[68px] px-6 flex items-center justify-between border-b border-slate-200">
                                            <h2 class="text-[17px] font-bold text-slate-900">
                                                Edit a discussion
                                            </h2>

                                            <button type="button" @click.stop="cancelEditPost()"
                                                aria-label="Close edit discussion"
                                                class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition cursor-pointer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        {{-- BODY --}}
                                        <div class="px-6 pt-6 pb-5 overflow-y-auto max-h-[calc(100vh-168px)]">

                                            {{-- AUTHOR + CATEGORY + VISIBILITY --}}
                                            <div class="flex items-center gap-3 mb-5">
                                                <img src="{{ $authorAvatar }}" alt="{{ $author?->name ?? 'User' }}"
                                                    class="w-12 h-12 rounded-full object-cover shrink-0 border border-slate-100">

                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-bold text-slate-900 leading-tight">
                                                        {{ $author?->name ?? 'User' }}
                                                    </div>

                                                    <div class="flex flex-wrap items-center gap-2 mt-2">
                                                        <div class="relative">
                                                            <select name="category_id" x-model="postCategoryId"
                                                                required
                                                                class="appearance-none h-8 min-w-[132px] rounded-full bg-slate-100 border-0 pl-3 pr-8 text-[11px] font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-200 cursor-pointer">
                                                                @foreach ($categories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-500"
                                                                fill="none" stroke="currentColor" stroke-width="2"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 9l6 6 6-6" />
                                                            </svg>
                                                        </div>

                                                        <span
                                                            class="h-8 px-3 rounded-full bg-slate-100 inline-flex items-center text-[11px] font-semibold text-slate-500">
                                                            Public
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- TITLE --}}
                                            <input type="text" name="title" x-model="postTitle" maxlength="255"
                                                required autocomplete="off"
                                                placeholder="Give your discussion a clear title..."
                                                class="w-full border-0 bg-transparent p-0 text-[17px] font-bold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-0 leading-7">

                                            {{-- CONTENT --}}
                                            <textarea name="content" x-model="postContent" maxlength="10000" required spellcheck="true"
                                                placeholder="What do you want to talk about?"
                                                class="mt-1 w-full min-h-[150px] border-0 bg-transparent p-0 text-[13px] leading-6 text-slate-700 placeholder:text-slate-400 resize-none focus:outline-none focus:ring-0 whitespace-pre-wrap"></textarea>

                                            {{-- TAGS --}}
                                            <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <div>
                                                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Tags</div>
                                                        <div class="text-[10px] text-slate-400 mt-1">Select or unselect tags. Maximum 5.</div>
                                                    </div>
                                                    <span class="text-[10px] font-semibold text-slate-400" x-text="selectedTags.length + '/5'"></span>
                                                </div>

                                                <div x-show="availableTags.length" class="flex flex-wrap gap-2">
                                                    <template x-for="tag in availableTags" :key="tag.id">
                                                        <label class="cursor-pointer select-none">
                                                            <input type="checkbox" name="tags[]" :value="tag.id"
                                                                :checked="selectedTags.includes(Number(tag.id))"
                                                                @change="toggleEditTagFromInput($event, tag.id)"
                                                                class="sr-only">
                                                            <span
                                                                :class="selectedTags.includes(Number(tag.id)) ? 'bg-[#0b1329] text-white border-[#0b1329]' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'"
                                                                class="inline-flex items-center h-8 px-3 rounded-full border text-[11px] font-medium transition-all">
                                                                <span class="mr-1.5 text-[10px]" x-text="selectedTags.includes(Number(tag.id)) ? '✓' : '+'"></span>
                                                                <span x-text="tag.name"></span>
                                                            </span>
                                                        </label>
                                                    </template>
                                                </div>
                                                <div x-show="!availableTags.length" class="text-[11px] text-slate-400">No tags available.</div>
                                            </div>

                                            {{-- EXISTING MEDIA --}}
                                            @if ($post->media->isNotEmpty())
                                                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div>
                                                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Current media</div>
                                                            <div class="text-[10px] text-slate-400 mt-1">Use the arrows to switch media.</div>
                                                        </div>
                                                        @if ($post->media->count() > 1)
                                                            <span class="text-[10px] font-semibold text-slate-400">{{ $post->media->count() }} files</span>
                                                        @endif
                                                    </div>

                                                    <div class="relative overflow-hidden rounded-xl bg-slate-900 min-h-[220px] flex items-center justify-center">
                                                        @foreach ($post->media as $index => $mediaItem)
                                                            <div x-show="editExistingMediaIndex === {{ $index }}" x-cloak class="w-full flex items-center justify-center">
                                                                @if (($mediaItem->type ?? '') === 'video' || str_starts_with($mediaItem->mime_type ?? '', 'video'))
                                                                    <video src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                                        class="w-full max-h-[320px] object-contain" controls preload="metadata"></video>
                                                                @else
                                                                    <img src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                                        alt="{{ $post->title }}" class="w-full max-h-[320px] object-contain">
                                                                @endif
                                                            </div>
                                                        @endforeach

                                                        @if ($post->media->count() > 1)
                                                            <button type="button" @click="prevEditExistingMedia({{ $post->media->count() }})"
                                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10" aria-label="Previous media">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                                                            </button>
                                                            <button type="button" @click="nextEditExistingMedia({{ $post->media->count() }})"
                                                                class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10" aria-label="Next media">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                                            </button>
                                                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-slate-950/65 text-white text-[10px] font-semibold px-2.5 py-1"
                                                                x-text="(editExistingMediaIndex + 1) + ' / {{ $post->media->count() }}'"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- NEW MEDIA UPLOAD --}}
                                            <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-4">
                                                <div class="flex items-center justify-between gap-3">
                                                    <div>
                                                        <div class="text-[11px] font-semibold text-slate-700">Add photos or videos</div>
                                                        <div class="text-[10px] text-slate-400 mt-1">JPG, JPEG, PNG, WEBP or MP4 · max 10MB each · up to 10 files</div>
                                                    </div>
                                                    <label class="shrink-0 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-slate-200 text-[10px] font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-900 cursor-pointer transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" /></svg>
                                                        Add files
                                                        <input x-ref="editMediaInput" type="file" name="media[]" multiple
                                                            accept="image/*,video/*" @change="handleEditFiles($event)" class="hidden">
                                                    </label>
                                                </div>

                                                <div x-show="editFilePreviews.length" x-cloak class="mt-4">
                                                    <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-slate-900 min-h-[220px] flex items-center justify-center">
                                                        <template x-for="(file, index) in editFilePreviews" :key="file.name + index">
                                                            <div x-show="editPreviewIndex === index" class="w-full relative">
                                                                <div class="min-h-[220px] max-h-[340px] flex items-center justify-center overflow-hidden">
                                                                    <template x-if="file.isVideo">
                                                                        <video :src="file.url" class="w-full max-h-[340px] object-contain" muted playsinline controls></video>
                                                                    </template>
                                                                    <template x-if="!file.isVideo">
                                                                        <img :src="file.url" :alt="file.name" class="w-full max-h-[340px] object-contain">
                                                                    </template>
                                                                </div>

                                                                <button type="button" @click="removeEditFile(index)"
                                                                    class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/95 shadow flex items-center justify-center text-slate-600 hover:text-red-500 transition" aria-label="Remove file">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                                                                </button>
                                                            </div>
                                                        </template>

                                                        <template x-if="editFilePreviews.length > 1">
                                                            <div>
                                                                <button type="button" @click="editPreviewIndex = (editPreviewIndex - 1 + editFilePreviews.length) % editFilePreviews.length"
                                                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10" aria-label="Previous selected file">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                                                                </button>
                                                                <button type="button" @click="editPreviewIndex = (editPreviewIndex + 1) % editFilePreviews.length"
                                                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/95 shadow-lg flex items-center justify-center text-slate-700 hover:bg-white transition z-10" aria-label="Next selected file">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                                                </button>
                                                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 rounded-full bg-slate-950/65 text-white text-[10px] font-semibold px-2.5 py-1"
                                                                    x-text="(editPreviewIndex + 1) + ' / ' + editFilePreviews.length"></div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div class="mt-2 text-center text-[10px] text-slate-500 truncate px-8" x-text="editFilePreviews[editPreviewIndex]?.name || ''"></div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- FOOTER --}}
                                        <div
                                            class="flex items-center justify-between px-6 py-4 border-t border-slate-100 shrink-0 bg-white">
                                            <div class="text-[10px] text-slate-400">
                                                <span x-show="!editFiles.length">No new files selected</span>
                                                <span x-show="editFiles.length" x-text="editFiles.length + ' new file' + (editFiles.length === 1 ? '' : 's') + ' selected'"></span>
                                            </div>

                                            <button type="submit"
                                                class="bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-sm px-6 py-2.5 rounded-full transition shadow-sm cursor-pointer">
                                                Publish
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>

                        {{-- POST DELETE CONFIRMATION MODAL --}}
                        <template x-teleport="body">
                            <div x-show="postDeleteConfirm" x-cloak x-transition.opacity
                                class="fixed inset-0 z-[115] flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
                                    @click="cancelDeletePost()"></div>

                                <div x-show="postDeleteConfirm" x-transition @click.stop
                                    class="relative w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200">

                                    <form method="POST"
                                        action="{{ route('community.posts.destroy', ['post' => $post]) }}"
                                        @submit="postActionLoading = true">

                                        @csrf
                                        @method('DELETE')

                                        <div class="p-5 sm:p-6">
                                            <div class="flex items-start gap-3.5">
                                                <div
                                                    class="w-11 h-11 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 9v4m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 3h15.58a2 2 0 001.74-3l-7.82-14a2 2 0 01-3.48 0z" />
                                                    </svg>
                                                </div>

                                                <div class="min-w-0">
                                                    <h3 class="text-sm font-bold text-slate-900">Delete this post?</h3>
                                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                                        This discussion will be removed. This action cannot be undone.
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 mt-6">
                                                <button type="button" @click="cancelDeletePost()"
                                                    :disabled="postActionLoading"
                                                    class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition disabled:opacity-50">
                                                    Cancel
                                                </button>

                                                <button type="submit" :disabled="postActionLoading"
                                                    class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition disabled:opacity-50 min-w-20">
                                                    <span x-show="!postActionLoading">Delete</span>
                                                    <span x-show="postActionLoading">Deleting...</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </template>
                        {{-- DELETE CONFIRMATION MODAL --}}
                        <div x-show="deleteConfirmComment" x-cloak x-transition.opacity
                            class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                            <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
                                @click="cancelDeleteComment()"></div>
                            <div x-show="deleteConfirmComment" x-transition @click.stop
                                class="relative w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200">
                                <div class="p-5 sm:p-6">
                                    <div class="flex items-start gap-3.5">
                                        <div
                                            class="w-11 h-11 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v4m0 4h.01M10.29 3.86l-7.82 14a2 2 0 001.74 3h15.58a2 2 0 001.74-3l-7.82-14a2 2 0 00-3.48 0z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold text-slate-900">Are you sure?</h3>
                                            <p class="mt-1 text-xs leading-relaxed text-slate-500">This <span
                                                    x-text="deleteConfirmIsReply ? 'reply' : 'comment'"></span> will be
                                                permanently deleted. This action cannot be undone.</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-6">
                                        <button type="button" @click="cancelDeleteComment()"
                                            :disabled="commentActionLoading"
                                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition disabled:opacity-50">Cancel</button>
                                        <button type="button" @click="confirmDeleteComment()"
                                            :disabled="commentActionLoading"
                                            class="px-4 py-2.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 transition disabled:opacity-50">
                                            <span x-show="!commentActionLoading">Delete</span><span
                                                x-show="commentActionLoading">Deleting...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @empty

                <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-slate-200/70">
                    <p class="text-xs text-slate-500">
                        No discussions found matching your criteria.
                    </p>
                </div>

            @endforelse

            {{-- PAGINATION --}}
            @if (method_exists($posts, 'links'))
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar :user="$user" :trending-topics="$trendingTopics" />

    </main>




    {{-- INDEX-ONLY AJAX: create post without changing sidebar/controller --}}
    <script>
        (() => {
            if (window.__communityIndexAjaxInstalled) return;
            window.__communityIndexAjaxInstalled = true;

            const csrf = () => document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';

            async function replaceFeedFromHtml(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const freshFeed = doc.querySelector('#community-posts-feed');
                const currentFeed = document.querySelector('#community-posts-feed');

                if (!freshFeed || !currentFeed) {
                    throw new Error('Community feed could not be updated.');
                }

                currentFeed.replaceWith(freshFeed);
                window.dispatchEvent(new CustomEvent('community-feed-updated'));
            }

            // Capture phase runs before the sidebar's Alpine submit handler.
            // This keeps the implementation entirely inside index.blade.php.
            document.addEventListener('submit', async (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;

                const action = form.getAttribute('action') || '';
                const method = (form.getAttribute('method') || 'GET').toUpperCase();

                if (method !== 'POST' || !/\/community\/posts\/?$/.test(new URL(action, window.location
                        .origin).pathname)) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                if (form.dataset.ajaxBusy === '1') return;
                form.dataset.ajaxBusy = '1';

                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonHtml = submitButton?.innerHTML;

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Posting...';
                }

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html,application/xhtml+xml,application/json'
                        },
                        body: new FormData(form),
                        credentials: 'same-origin'
                    });

                    if (response.status === 401) {
                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                        return;
                    }

                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(text || 'Unable to create post.');
                    }

                    const html = await response.text();
                    await replaceFeedFromHtml(html);

                    // Close the existing Create Post modal without modifying sidebar.blade.php.
                    const modal = document.querySelector('[x-show="openModal"]');
                    if (modal && modal.__x) {
                        modal.__x.$data.openModal = false;
                    } else {
                        // Alpine v3 exposes data through the element; dispatch a click on its close button.
                        const closeButton = modal?.querySelector('button[type="button"]');
                        if (closeButton) closeButton.click();
                    }

                    form.reset();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } catch (error) {
                    console.error('Create post error:', error);
                    window.dispatchEvent(new CustomEvent('community-index-error', {
                        detail: {
                            message: error.message || 'Unable to create post.'
                        }
                    }));
                } finally {
                    form.dataset.ajaxBusy = '0';
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonHtml || 'Post';
                    }
                }
            }, true);

            // Keep all newly inserted Alpine menus closed.
            window.addEventListener('community-feed-updated', () => {
                document.querySelectorAll('[x-cloak]').forEach(el => {
                    // Alpine owns visibility; this is intentionally a no-op for initialized elements.
                });
            });
        })();
    </script>


</body>

</html>
