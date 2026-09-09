@props([
    'profileUser' => auth()->user(), // The user whose profile is being viewed
    'posts' => collect(), // The posts/activity for the selected tab
])

@php
    $activeTab = request('tab', 'posts');
@endphp

<x-community.shell title="{{ $profileUser->name }} | REIAC Community" :user="auth()->user()">
    
    

    <a href="{{ route('community.index') }}" class="inline-block mb-4 text-sm font-semibold text-[#0c1b33] hover:underline">
        &larr; Back to discussions
    </a>

    {{-- PROFILE HEADER CARD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/70 overflow-hidden">
        
        {{-- Cover Image --}}
        <div class="h-48 w-full relative bg-slate-900">
            <img src="{{ $profileUser->profile?->cover_image ? asset('storage/' . $profileUser->profile->cover_image) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1200&h=400&fit=crop' }}" 
                 alt="Cover" class="w-full h-full object-cover opacity-80 mix-blend-overlay">
        </div>

        {{-- Profile Info Section --}}
        <div class="px-6 sm:px-8 pb-6">
            
            {{-- Avatar, Name, and Action Buttons --}}
            <div class="flex justify-between items-start">
                <div class="flex items-end gap-4 -mt-12 relative z-10">
                    {{-- Avatar with Online Indicator --}}
                    <div class="relative shrink-0">
                        <img src="{{ $profileUser->profile?->avatar ? asset('storage/' . $profileUser->profile->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($profileUser->name).'&background=0c1b33&color=fff' }}" 
                             alt="{{ $profileUser->name }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white object-cover bg-white shadow-sm">
                        <div class="absolute bottom-1.5 right-1.5 w-5 h-5 bg-emerald-500 border-[3px] border-white rounded-full"></div>
                    </div>
                    
                    {{-- Name & Username --}}
                    <div class="mb-1 sm:mb-2">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $profileUser->name }}</h1>
                        <p class="text-sm font-medium text-slate-500">{{ '@' . ($profileUser->profile?->username ?? strtolower(str_replace(' ', '', $profileUser->name))) }}</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4 flex items-center gap-2">
                    @if(auth()->id() === $profileUser->id)
                        <a href="{{ route('community.profile.edit') }}" class="px-4 py-1.5 border border-slate-200 rounded-lg text-sm font-bold text-[#0c1b33] hover:bg-slate-50 transition shadow-sm">
                            Edit Profile
                        </a>
                    @else
                        <button class="px-4 py-1.5 bg-[#0c1b33] text-white rounded-lg text-sm font-bold hover:bg-slate-800 transition shadow-sm">
                            Follow
                        </button>
                    @endif
                    <button class="p-1.5 border border-slate-200 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Bio & Details --}}
            <div class="mt-5 space-y-3">
                @if($profileUser->profile?->headline)
                    <p class="text-sm font-semibold text-slate-500">
                        {{ $profileUser->profile->headline }}
                    </p>
                @endif
                <p class="text-sm text-slate-700 leading-relaxed max-w-2xl">
                    {{ $profileUser->profile?->bio ?? 'Exploring ideas, sharing knowledge, and connecting with the community.' }}
                </p>
                
                <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-xs font-medium text-slate-500 pt-1">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path></svg>
                        {{ $profileUser->profile?->location ?? 'Global' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Joined {{ $profileUser->created_at ? $profileUser->created_at->format('M Y') : 'Recently' }}
                    </span>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-4 text-center py-5 border-y border-slate-100 mt-6">
                <div class="flex flex-col items-center">
                    <span class="text-lg sm:text-xl font-extrabold text-[#0c1b33]">{{ $profileUser->posts_count ?? 0 }}</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5">Posts</span>
                </div>
                <div class="flex flex-col items-center border-l border-slate-100">
                    <span class="text-lg sm:text-xl font-extrabold text-[#0c1b33]">{{ $profileUser->comments_count ?? 0 }}</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5">Comments</span>
                </div>
                <div class="flex flex-col items-center border-l border-slate-100">
                    <span class="text-lg sm:text-xl font-extrabold text-[#0c1b33]">{{ $profileUser->likes_count ?? 0 }}</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5">Likes</span>
                </div>
                <div class="flex flex-col items-center border-l border-slate-100">
                    <span class="text-lg sm:text-xl font-extrabold text-[#0c1b33]">{{ $profileUser->shares_count ?? 0 }}</span>
                    <span class="text-[11px] sm:text-xs font-semibold text-slate-400 mt-0.5">Shares</span>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex items-center justify-between sm:justify-start sm:gap-10 pt-2 border-b border-slate-100 px-2 mt-2 text-sm font-bold overflow-x-auto custom-scrollbar">
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'posts']) }}" class="pb-3 whitespace-nowrap {{ $activeTab === 'posts' ? 'border-b-2 border-amber-400 text-[#0c1b33]' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition' }}">Posts</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'comments']) }}" class="pb-3 whitespace-nowrap {{ $activeTab === 'comments' ? 'border-b-2 border-amber-400 text-[#0c1b33]' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition' }}">Comments</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'saved']) }}" class="pb-3 whitespace-nowrap {{ $activeTab === 'saved' ? 'border-b-2 border-amber-400 text-[#0c1b33]' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition' }}">Saved</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'liked']) }}" class="pb-3 whitespace-nowrap {{ $activeTab === 'liked' ? 'border-b-2 border-amber-400 text-[#0c1b33]' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition' }}">Liked</a>
                <a href="{{ request()->fullUrlWithQuery(['tab' => 'activity']) }}" class="pb-3 whitespace-nowrap {{ $activeTab === 'activity' ? 'border-b-2 border-amber-400 text-[#0c1b33]' : 'border-b-2 border-transparent text-slate-500 hover:text-slate-700 transition' }}">Activity</a>
            </div>

            {{-- TAB CONTENT AREA (Post Feed Loop) --}}
            <div class="pt-6 space-y-6">
                @forelse($posts as $post)
                    <div class="pb-6 border-b border-slate-100 last:border-0 last:pb-0"
                         x-data="{ 
                            likesCount: {{ $post->likes_count ?? 0 }},
                            liked: {{ auth()->check() && collect($post->likes)->where('user_id', auth()->id())->isNotEmpty() ? 'true' : 'false' }},
                            saved: {{ auth()->check() && collect($post->savedBy)->where('user_id', auth()->id())->isNotEmpty() ? 'true' : 'false' }},
                            copied: false,
                            commentsCount: {{ $post->comments_count ?? 0 }},
                            toggleLike() {
                                fetch('{{ route('community.posts.like', $post) }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content') }
                                }).then(res => res.json()).then(data => { if(data.success) { this.liked = data.liked; this.likesCount = data.likes_count; } });
                            },
                            toggleSave() {
                                fetch('{{ route('community.posts.save', $post) }}', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content') }
                                }).then(res => res.json()).then(data => { if(data.success) { this.saved = data.saved; } });
                            },
                            sharePost() {
                                navigator.clipboard.writeText('{{ route('community.posts.show', $post) }}').then(() => {
                                    this.copied = true; setTimeout(() => this.copied = false, 2000);
                                });
                            }
                         }">
                        
                        <div class="flex items-start justify-between">
                            <div class="flex gap-3">
                                <img src="{{ $post->user->profile?->avatar ? asset('storage/' . $post->user->profile->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($post->user->name).'&background=0c1b33&color=fff' }}" class="w-10 h-10 rounded-full object-cover shadow-sm ring-1 ring-slate-100">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm hover:underline"><a href="{{ route('community.profile', $post->user->username ?? $post->user->id) }}">{{ $post->user->name }}</a></div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        {{ $post->created_at->diffForHumans() }} &bull; <span class="text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded tracking-wide text-[9px] uppercase">{{ $post->category->name ?? 'GENERAL' }}</span>
                                    </div>
                                </div>
                            </div>
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                            </button>
                        </div>

                        <div class="mt-3 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <h3 class="font-bold text-slate-900 text-[15px] leading-snug pr-2">
                                    <a href="{{ route('community.posts.show', $post) }}" class="hover:underline">{{ $post->title }}</a>
                                </h3>
                                
                                <p class="text-xs text-slate-600 mt-1.5 line-clamp-2 leading-relaxed">
                                    {{ $post->content }}
                                </p>
                                
                                {{-- Post Actions --}}
                                <div class="flex items-center gap-5 pt-4 text-xs font-semibold text-slate-500">
                                    <button @click="toggleLike()" class="flex items-center gap-1.5 transition" :class="liked ? 'text-red-500' : 'hover:text-red-500'">
                                        <svg class="w-4 h-4" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> 
                                        <span x-text="likesCount"></span>
                                    </button>
                                    <a href="{{ route('community.posts.show', $post) }}" class="flex items-center gap-1.5 hover:text-blue-500 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg> 
                                        <span x-text="commentsCount"></span>
                                    </a>
                                    <button @click="sharePost()" class="flex items-center gap-1.5 transition" :class="copied ? 'text-emerald-600' : 'hover:text-emerald-500'">
                                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg> 
                                        <svg x-show="copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="hidden sm:inline" x-text="copied ? 'Copied' : 'Share'"></span>
                                    </button>
                                    <button @click="toggleSave()" class="flex items-center gap-1.5 transition" :class="saved ? 'text-amber-500' : 'hover:text-amber-500'">
                                        <svg class="w-4 h-4" :fill="saved ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg> 
                                        <span class="hidden sm:inline" x-text="saved ? 'Saved' : 'Save'"></span>
                                    </button>
                                </div>
                            </div>
                            
                            {{-- Right Aligned Thumbnail (If post has media) --}}
                            @if($post->media && $post->media->isNotEmpty())
                                <div class="w-full sm:w-32 h-32 sm:h-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 mt-2 sm:mt-1 bg-slate-100">
                                    <img src="{{ asset('storage/' . $post->media->first()->file_path) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">No {{ $activeTab }} found</p>
                        <p class="text-xs text-slate-400 mt-1">This section is currently empty.</p>
                    </div>
                @endforelse

                {{-- Pagination Links --}}
                @if(method_exists($posts, 'links'))
                    <div class="mt-4 border-t border-slate-100 pt-4">
                        {{ $posts->appends(['tab' => $activeTab])->links() }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-community.shell>