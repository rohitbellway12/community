<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $group->name }} | Community</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">
  
  <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div
        x-show="mobileMenuOpen"
        class="fixed inset-0 z-50 lg:hidden flex"
        style="display: none;"
    >
        <div
            @click="mobileMenuOpen = false"
            x-show="mobileMenuOpen"
            x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto"
        >
            @auth
                @php
                    $mobileProfile = $user?->profile;
                    $mobileAvatar = $mobileProfile?->avatar
                        ? asset('storage/' . $mobileProfile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff';
                @endphp

                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img
                        src="{{ $mobileAvatar }}"
                        alt="{{ $user?->name }}"
                        class="w-10 h-10 rounded-full object-cover"
                    >

                    <div>
                        <div class="font-bold text-slate-900 text-sm">
                            {{ $user?->name }}
                        </div>

                        <a
                            href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}"
                            class="text-xs text-amber-600 font-semibold hover:underline"
                        >
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a
                    href="{{ route('community.index') }}"
                    class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50"
                >
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Community Home
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        <x-community.sidebar
            :top-contributors="$topContributors ?? collect()"
            :categories="$categories"
            :tags="$tags"
        />

        {{-- CENTER FEED --}}
        <section class="space-y-5 min-w-0">

            {{-- GROUP HEADER CARD --}}
            <div class="bg-white p-5 lg:p-6 rounded-2xl shadow-sm border border-slate-200/70 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-md">Group</span>
                            <span class="text-xs text-slate-500">{{ $group->users_count }} Members</span>
                        </div>
                        <h1 class="text-xl lg:text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
                            {{ $group->name }}
                        </h1>
                    </div>

                    {{-- MEMBERSHIP STATUS BADGE --}}
                    <div>
                        @if($isMember)
                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-emerald-200/60">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Active Member
                            </span>
                        @elseif($membership)
                            <span class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-amber-200/60">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Membership Pending
                            </span>
                        @else
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 border border-slate-200">
                                Not a Member
                            </span>
                        @endif
                    </div>
                </div>

                <p class="text-xs lg:text-sm text-slate-600 leading-relaxed">
                    {{ $group->description ?? 'No description provided for this group yet.' }}
                </p>
            </div>

            {{-- POSTS FEED OR RESTRICTED NOTICE --}}
            @if($isMember)
                @forelse($posts as $post)
                    @php
                        $author = $post->user;
                        $profile = $author?->profile;

                        $authorAvatar = $profile?->avatar
                            ? asset('storage/' . $profile->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($author?->name ?? 'User') . '&background=0c1b33&color=fff';

                        $authorUsername = $profile?->username;

                        $authorUrl = $authorUsername
                            ? route('community.profile', $authorUsername)
                            : route('community.profile', $author?->id);
                    @endphp

                    <div
                        class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/70 space-y-3.5"
                        x-data="{
                            likesCount: {{ $post->likes_count ?? $post->likes->count() }},
                            liked: {{ auth()->check() && $post->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                            copied: false,
                            commentsCount: {{ $post->comments->count() }},
                            showAllComments: false,
                            expandedContent: false,
                            expandedTags: false,
                            newCommentText: '',
                            comments: {{ json_encode($post->comments->map(fn($c) => [
                                'id' => $c->id,
                                'content' => $c->content,
                                'created_at_human' => $c->created_at->diffForHumans(),
                                'user' => [
                                    'name' => $c->user?->name ?? 'User',
                                    'avatar' => $c->user?->profile?->avatar
                                        ? asset('storage/' . $c->user->profile->avatar)
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($c->user?->name ?? 'User') . '&background=0c1b33&color=fff'
                                ]
                            ])) }},
                            loadingMore: false,

                            toggleLike() {
                                @guest
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                @endguest

                                fetch('{{ route('community.posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(async res => {
                                    if (res.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return;
                                    }
                                    return res.json();
                                })
                                .then(data => {
                                    if (data && data.success) {
                                        this.liked = data.liked;
                                        this.likesCount = data.likes_count;
                                    }
                                });
                            },

                            sharePost() {
                                navigator.clipboard.writeText('{{ route('community.posts.show', $post) }}')
                                    .then(() => {
                                        this.copied = true;
                                        setTimeout(() => { this.copied = false; }, 2000);
                                    });
                            },

                            submitComment() {
                                @guest
                                    window.dispatchEvent(new CustomEvent('open-login-modal'));
                                    return;
                                @endguest

                                if (!this.newCommentText.trim()) return;

                                fetch('{{ route('community.posts.comment.store', $post) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ content: this.newCommentText })
                                })
                                .then(async res => {
                                    if (res.status === 401) {
                                        window.dispatchEvent(new CustomEvent('open-login-modal'));
                                        return;
                                    }
                                    return res.json();
                                })
                                .then(data => {
                                    if (data && data.success) {
                                        this.comments.unshift(data.comment);
                                        this.commentsCount++;
                                        this.newCommentText = '';
                                    }
                                });
                            },

                            loadMoreComments() {
                                this.loadingMore = true;
                                fetch(`{{ route('community.posts.comments', $post) }}?skip=` + this.comments.length)
                                    .then(res => res.json())
                                    .then(data => {
                                        this.comments = this.comments.concat(data.comments);
                                        this.loadingMore = false;
                                    });
                            }
                        }"
                    >
                        {{-- POST HEADER --}}
                        <div class="flex items-start justify-between gap-2.5">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <img
                                    src="{{ $authorAvatar }}"
                                    alt="{{ $author?->name ?? 'User' }}"
                                    class="w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-slate-100"
                                >

                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 text-xs truncate flex items-center gap-1.5">
                                        @if($author)
                                            <a href="{{ $authorUrl }}" class="hover:underline">
                                                {{ $author->name }}
                                            </a>

                                            @if($profile?->country && $profile->country->iso_code)
                                                <img 
                                                    src="https://flagcdn.com/20x15/{{ strtolower($profile->country->iso_code) }}.png" 
                                                    class="w-4 h-3 object-cover rounded-xs shadow-2xs inline-block shrink-0 align-middle" 
                                                    alt="{{ $profile->country->name }}"
                                                >
                                            @endif
                                        @else
                                            User
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-slate-400 flex flex-wrap items-center gap-x-1.5 gap-y-0.5 mt-0.5 leading-tight">
                                        @if($authorUsername)
                                            <span class="truncate max-w-[120px] sm:max-w-[200px] font-medium text-slate-500">
                                                {{ '@' . $authorUsername }}
                                            </span>
                                            <span class="text-slate-300">•</span>
                                        @endif

                                        <span class="whitespace-nowrap shrink-0 text-slate-400">
                                            {{ $post->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TITLE / CONTENT --}}
                        <h3 class="font-bold text-slate-900 text-sm leading-snug">
                            <a href="{{ route('community.posts.show', $post) }}" class="hover:underline">
                                {{ $post->title }}
                            </a>
                        </h3>

                        <div class="text-xs text-slate-600 leading-relaxed">
                            <p :class="expandedContent ? '' : 'line-clamp-3'">
                                {{ $post->content ?? $post->body ?? '' }}
                            </p>

                            @if(mb_strlen($post->content ?? $post->body ?? '') > 160)
                                <button
                                    type="button"
                                    @click="expandedContent = !expandedContent"
                                    class="text-xs font-bold text-amber-600 hover:text-amber-700 hover:underline mt-1.5 inline-block focus:outline-none cursor-pointer"
                                >
                                    <span x-text="expandedContent ? 'Show less' : '...See more'"></span>
                                </button>
                            @endif
                        </div>

                        {{-- MEDIA --}}
                        @if($post->media && $post->media->isNotEmpty())
                            <div class="space-y-3 pt-1">
                                @foreach($post->media as $mediaItem)
                                    <div class="w-full max-h-[550px] bg-slate-900 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                                        @if(str_starts_with($mediaItem->file_type ?? '', 'video'))
                                            <video src="{{ asset('storage/' . $mediaItem->file_path) }}" class="w-full max-h-[550px] object-contain" controls preload="metadata"></video>
                                        @else
                                            <img src="{{ asset('storage/' . $mediaItem->file_path) }}" alt="{{ $post->title }}" class="w-full h-auto max-h-[550px] object-contain">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- ACTION BAR --}}
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs font-semibold text-slate-500">
                            <div class="flex items-center gap-5">
                                <button
                                    type="button"
                                    @click="toggleLike()"
                                    class="flex items-center gap-1.5 transition"
                                    :class="liked ? 'text-red-500' : 'hover:text-red-500'"
                                >
                                    <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span x-text="likesCount"></span>
                                </button>

                                <button
                                    type="button"
                                    @click="showAllComments = !showAllComments"
                                    class="flex items-center gap-1.5 hover:text-blue-500 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span x-text="commentsCount"></span>
                                </button>

                                <button
                                    type="button"
                                    @click="sharePost()"
                                    class="flex items-center gap-1.5 transition"
                                    :class="copied ? 'text-emerald-600' : 'hover:text-emerald-500'"
                                >
                                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                    <span x-text="copied ? 'Copied!' : 'Share'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- COMMENTS SECTION --}}
                        <div class="pt-3 border-t border-slate-100 space-y-3" x-show="showAllComments" x-cloak>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    x-model="newCommentText"
                                    @keydown.enter="submitComment()"
                                    placeholder="Write a comment..."
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-amber-400"
                                >
                                <button
                                    type="button"
                                    @click="submitComment()"
                                    class="px-3 py-2 bg-[#0b1329] text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition"
                                >
                                    Post
                                </button>
                            </div>

                            <div class="space-y-2.5 pt-1">
                                <template x-for="comment in comments" :key="comment.id">
                                    <div class="flex items-start gap-2.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <img :src="comment.user.avatar" class="w-7 h-7 rounded-full object-cover shrink-0">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-slate-900 text-xs" x-text="comment.user.name"></span>
                                                <span class="text-[10px] text-slate-400" x-text="comment.created_at_human"></span>
                                            </div>
                                            <p class="text-xs text-slate-700 mt-0.5 leading-relaxed" x-text="comment.content"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="text-center pt-1" x-show="comments.length < commentsCount">
                                <button type="button" @click="loadMoreComments()" class="text-xs font-semibold text-amber-600 hover:underline cursor-pointer" x-text="loadingMore ? 'Loading...' : 'See more comments'"></button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-slate-200/70">
                        <p class="text-xs text-slate-500">No posts found in this group yet. Be the first to post!</p>
                    </div>
                @endforelse

                {{-- PAGINATION --}}
                @if(method_exists($posts, 'links'))
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                {{-- RESTRICTED ACCESS NOTICE --}}
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-xs lg:text-sm font-semibold text-amber-800">
                        You must be an active member of this group to view and interact with its posts.
                    </p>
                </div>
            @endif

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar
            :user="$user"
            :trending-topics="collect()"
        />

    </main>

</body>
</html>