@props([
    'title' => 'Saved Posts | REIAC Community',
    'active' => 'saved',
    'user' => auth()->user(),
    'posts' => collect(),
    'categories' => collect(),
    'tags' => collect(),
    'trendingTopics' => collect(),
    'topContributors' => [],
    'notificationsCount' => 0,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-[#f3f4f6]" x-data="{ mobileMenuOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

<body class="min-h-screen bg-[#f3f4f6] font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-white pb-20 lg:pb-0">

    {{-- TOPBAR --}}
    <x-community.topbar :notifications-count="$notificationsCount ?? 0" />

    {{-- MOBILE SIDEBAR OVERLAY --}}
    <div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden flex" style="display: none;">
        <div @click="mobileMenuOpen = false" x-show="mobileMenuOpen" x-transition.opacity
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-80 max-w-[85%] bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">

            @auth
                <div class="p-4 border-b border-slate-100 flex items-center gap-3">
                    <img src="{{ $user?->profile?->avatar
                        ? asset('storage/' . $user->profile->avatar)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=0c1b33&color=fff' }}"
                        alt="{{ $user?->name ?? 'User' }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $user?->name }}</div>
                        <a href="{{ route('community.profile.me') }}" class="text-xs text-amber-600 font-semibold hover:underline">
                            View Profile
                        </a>
                    </div>
                </div>
            @endauth

            <nav class="p-3 space-y-1 text-sm font-medium text-slate-600">
                <a href="{{ route('community.index') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 011-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 011 1m-6 0h6"></path>
                    </svg>
                    Community Home
                </a>
                <a href="{{ route('community.saved') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl bg-slate-100 text-slate-900 font-semibold">
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Saved Posts
                </a>
                <a href="{{ route('community.guidelines') }}" class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Guidelines
                </a>
            </nav>
        </div>
    </div>

    {{-- MAIN 3-COLUMN LAYOUT --}}
    <main class="max-w-[1520px] mx-auto px-4 lg:px-6 py-6 grid grid-cols-1 lg:grid-cols-[280px_1fr_330px] xl:grid-cols-[300px_minmax(0,1fr)_360px] gap-6 items-start">

        {{-- LEFT SIDEBAR --}}
        <x-community.sidebar :top-contributors="$topContributors" :categories="$categories" :tags="$tags" />

        {{-- CENTER CONTENT --}}
        <section class="space-y-5 min-w-0">

            {{-- BREADCRUMB & BACK LINK --}}
            <div class="flex items-center justify-between text-xs">
                <nav class="flex items-center space-x-1.5 font-medium text-slate-500">
                    <a href="{{ route('community.index') }}" class="hover:text-slate-900 transition">Community</a>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">Saved Posts</span>
                </nav>

                <a href="{{ route('community.index') }}"
                   class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-900 font-semibold transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Feed</span>
                </a>
            </div>

            {{-- HEADER CARD --}}
            <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Saved Posts</h1>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            All discussions and helpful answers you've bookmarked for later.
                        </p>
                    </div>
                </div>

                <div class="shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold">
                        <span>{{ $posts->total() }}</span>
                        <span>Saved {{ \Illuminate\Support\Str::plural('Post', $posts->total()) }}</span>
                    </span>
                </div>
            </div>

            {{-- POSTS LIST --}}
            <div class="space-y-4">
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
                        $shareUrl = route('community.posts.show', $post);
                        $mediaCount = $post->media?->count() ?? 0;
                        $postContentLength = mb_strlen(strip_tags($post->content ?? ''));
                    @endphp

                    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200/80 space-y-4 transition"
                         x-data="{
                             saved: true,
                             showFullContent: false,
                             mediaIndex: 0,
                             prevMedia(count) {
                                 this.mediaIndex = (this.mediaIndex - 1 + count) % count;
                             },
                             nextMedia(count) {
                                 this.mediaIndex = (this.mediaIndex + 1) % count;
                             },
                             liked: {{ auth()->check() && $post->likes()->where('user_id', auth()->id())->exists() ? 'true' : 'false' }},
                             likesCount: {{ $post->likes_count ?? 0 }},
                             toggleLike() {
                                 fetch('{{ route('community.posts.like', $post) }}', {
                                     method: 'POST',
                                     headers: {
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json'
                                     }
                                 }).then(r => r.json()).then(data => {
                                     if (data.success) {
                                         this.liked = data.liked;
                                         this.likesCount = data.likes_count;
                                     }
                                 });
                             },
                             toggleSave() {
                                 fetch('{{ route('community.posts.save', $post) }}', {
                                     method: 'POST',
                                     headers: {
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json'
                                     }
                                 }).then(r => r.json()).then(data => {
                                     if (data.success) {
                                         this.saved = data.saved;
                                     }
                                 });
                             }
                         }">

                        {{-- Post Author Row --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <a href="{{ $authorUrl }}">
                                    <img src="{{ $authorAvatar }}" alt="{{ $author?->name }}"
                                         class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                </a>
                                <div>
                                    <a href="{{ $authorUrl }}" class="font-bold text-slate-900 text-xs sm:text-sm hover:underline">
                                        {{ $author?->name ?? 'Anonymous' }}
                                    </a>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5">
                                        @if($authorUsername)
                                            <span>&#64;{{ $authorUsername }}</span>
                                            <span>•</span>
                                        @endif
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($post->category)
                                <a href="{{ route('community.index', ['category' => $post->category->slug]) }}"
                                   class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                    {{ $post->category->name }}
                                </a>
                            @endif
                        </div>

                        {{-- Title & Content --}}
                        <div class="space-y-2">
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-snug">
                                <a href="{{ route('community.posts.show', $post) }}" class="hover:text-amber-600 transition">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-wrap break-words"
                               :class="showFullContent ? '' : 'line-clamp-4'">
                                {{ strip_tags($post->content) }}
                            </p>

                            @if($postContentLength > 240)
                                <button type="button" @click="showFullContent = !showFullContent"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 hover:text-amber-600 transition">
                                    <span x-text="showFullContent ? 'Show less' : 'Show more'"></span>
                                    <svg class="w-3 h-3 transition-transform" :class="showFullContent ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- MEDIA CAROUSEL (IMAGES / VIDEOS) --}}
                        @if ($mediaCount > 0)
                            <div class="pt-1">
                                <div class="relative w-full max-h-[550px] min-h-[220px] bg-slate-900 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                                    @foreach ($post->media as $index => $mediaItem)
                                        @php
                                            $isVid = ($mediaItem->type ?? '') === 'video'
                                                || str_starts_with($mediaItem->mime_type ?? '', 'video')
                                                || preg_match('/\.(mp4|mov|avi|webm|mkv|m4v|3gp|qt|ogg)$/i', $mediaItem->file_path ?? '');
                                        @endphp
                                        <div x-show="mediaIndex === {{ $index }}" x-cloak class="w-full h-full flex items-center justify-center">
                                            @if ($isVid)
                                                <video class="w-full max-h-[550px] object-contain" controls preload="metadata" playsinline>
                                                    <source src="{{ asset('storage/' . $mediaItem->file_path) }}" type="{{ $mediaItem->mime_type ?: 'video/mp4' }}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @else
                                                <a href="{{ asset('storage/' . $mediaItem->file_path) }}" target="_blank" class="w-full h-full flex items-center justify-center cursor-zoom-in">
                                                    <img src="{{ asset('storage/' . $mediaItem->file_path) }}"
                                                        alt="{{ $post->title }}" class="w-full max-h-[550px] object-contain" loading="lazy">
                                                </a>
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

                        {{-- Post Tags --}}
                        @if($post->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($post->tags as $tag)
                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Post Actions Bar --}}
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                            <div class="flex items-center gap-4">
                                {{-- Like button --}}
                                <button type="button" @click="toggleLike()"
                                        class="inline-flex items-center gap-1.5 transition font-semibold"
                                        :class="liked ? 'text-rose-500' : 'text-slate-500 hover:text-slate-800'">
                                    <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    <span x-text="likesCount"></span>
                                </button>

                                {{-- Comments link --}}
                                <a href="{{ route('community.posts.show', $post) }}"
                                   class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-800 transition font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span>{{ $post->comments_count ?? 0 }}</span>
                                </a>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Toggle Save button --}}
                                <button type="button" @click="toggleSave()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-bold transition"
                                        :class="saved ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                    </svg>
                                    <span x-text="saved ? 'Saved' : 'Save'"></span>
                                </button>

                                <a href="{{ route('community.posts.show', $post) }}"
                                   class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                                    View Post
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mx-auto">
                            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </div>
                        <div class="space-y-1 max-w-sm mx-auto">
                            <h3 class="text-base font-extrabold text-slate-900">No saved posts yet</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">
                                When you come across helpful discussions or answers in the community feed, click the bookmark icon on the post to save it here.
                            </p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('community.index') }}"
                               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                                <span>Browse Community Feed</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="pt-2">
                    {{ $posts->links() }}
                </div>
            @endif

        </section>

        {{-- RIGHT SIDEBAR --}}
        <x-community.rightbar :user="$user" :trending-topics="$trendingTopics" />

    </main>

</body>
</html>