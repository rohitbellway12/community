@props([
    'trendingTopics' => collect(),
])

{{-- RIGHT SIDEBAR (Desktop) --}}
<aside class="space-y-6 hidden lg:block sticky top-6 self-start">
  
    @auth
        @php
            $user = auth()->user();
            $user->load('profile')->loadCount(['posts', 'comments', 'likes']);
        @endphp

        {{-- Premium Profile Card --}}
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-slate-100 overflow-hidden text-center transition-all duration-300 hover:shadow-[0_8px_30px_-4px_rgba(15,23,42,0.08)]">

            <div class="h-24 bg-gradient-to-br from-slate-800 via-[#0c1b33] to-slate-900 relative">
                @if($user->profile?->cover_image)
                    <img
                        src="{{ asset('storage/' . $user->profile->cover_image) }}"
                        class="w-full h-full object-cover opacity-70 mix-blend-overlay"
                        alt="Cover"
                    >
                @else
                    <img
                        src="https://images.unsplash.com/photo-1541963463532-d68292c34b19?w=600&h=240&fit=crop"
                        class="w-full h-full object-cover opacity-40 mix-blend-overlay"
                        alt="Cover"
                    >
                @endif
            </div>

            <div class="px-6 pb-6 relative">

                {{-- Avatar --}}
                <div class="relative inline-block mx-auto -mt-12 mb-3">
                    <img
                        src="{{ $user->profile?->avatar
                            ? asset('storage/' . $user->profile->avatar)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name ?? 'User') . '&background=0c1b33&color=fff' }}"
                        alt="{{ $user->name }}"
                        class="w-20 h-20 rounded-full object-cover shadow-md ring-4 ring-white bg-white"
                    >
                </div>

                <h3 class="font-bold text-slate-900 text-base tracking-tight">
                    {{ $user->name }}
                </h3>

                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    {{ '@' . ($user->profile?->username ?? strtolower(str_replace(' ', '', $user->name ?? 'user'))) }}
                </p>

                <p class="text-xs text-slate-600 mt-3 leading-relaxed px-2 line-clamp-2">
                    {{ $user->profile?->bio ?? 'Exploring ideas, sharing knowledge, and connecting with the community.' }}
                </p>

                <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-[11px] font-medium text-slate-400 mt-3">

                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                        </svg>

                        {{ $user->profile?->location ?? 'Global' }}
                    </span>

                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                        </svg>

                        Joined {{ $user->created_at ? $user->created_at->format('M Y') : 'Recently' }}
                    </span>

                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-1 mt-5 pt-4 border-t border-slate-100 divide-x divide-slate-100">

                    <div class="flex flex-col items-center">
                        <span class="font-black text-slate-800 text-sm">
                            {{ $user->posts_count ?? 0 }}
                        </span>

                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                            Posts
                        </span>
                    </div>

                    <div class="flex flex-col items-center">
                        <span class="font-black text-slate-800 text-sm">
                            {{ $user->comments_count ?? 0 }}
                        </span>

                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                            Replies
                        </span>
                    </div>

                    <div class="flex flex-col items-center">
                        <span class="font-black text-slate-800 text-sm">
                            {{ $user->likes_count ?? 0 }}
                        </span>

                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">
                            Likes
                        </span>
                    </div>

                </div>

                <a
                        href="{{ route('community.profile', $user?->profile?->username ?? $user?->id) }}"
                    class="mt-5 flex items-center justify-center w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition-colors shadow-sm ring-1 ring-inset ring-slate-800/10"
                >
                    View Full Profile
                </a>

            </div>
        </div>
    @endauth

    {{-- Trending Topics --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-slate-100">

        <div class="flex items-center justify-between mb-4">

            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg
                        class="w-4 h-4 text-amber-500"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        ></path>
                    </svg>
                </div>

                <h3 class="font-bold text-slate-900 text-sm tracking-tight">
                    Trending Topics
                </h3>
            </div>

            <a
                href="{{ route('community.index', ['sort' => 'trending']) }}"
                class="text-xs text-amber-500 hover:text-amber-600 font-bold transition"
            >
                View All
            </a>

        </div>

        @if(isset($trendingTopics) && $trendingTopics->isNotEmpty())

            <div class="space-y-1">

                @foreach($trendingTopics as $index => $topic)

                    <a
                        href="{{ route('community.index', ['search' => $topic->name]) }}"
                        class="group flex items-center justify-between p-2.5 -mx-2.5 rounded-xl hover:bg-slate-50 transition"
                    >

                        <div class="flex items-center gap-2.5 min-w-0">

                            <span class="w-5 text-center text-[11px] font-bold text-slate-300">
                                {{ $index + 1 }}
                            </span>

                            <div class="min-w-0">

                                <div class="flex items-center gap-1">

                                    <span class="text-slate-300 font-medium text-sm">
                                        #
                                    </span>

                                    <span class="font-semibold text-slate-700 text-sm group-hover:text-[#0c1b33] transition truncate">
                                        {{ $topic->name }}
                                    </span>

                                </div>

                            </div>

                        </div>

                        <div class="shrink-0 ml-2">

                            @if($index === 0)
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 border border-amber-100 px-2 py-1 rounded-md">
                                    <svg
                                        class="w-3 h-3"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>

                                    Hot
                                </span>
                            @else
                                <span class="text-slate-400 text-[10px] font-semibold">
                                    {{ $topic->total_likes }}
                                </span>
                            @endif

                        </div>

                    </a>

                @endforeach

            </div>

        @else

            <div class="flex flex-col items-center justify-center py-6 text-center">

                <div class="w-11 h-11 rounded-full bg-slate-50 flex items-center justify-center mb-2.5">

                    <svg
                        class="w-5 h-5 text-slate-300"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                        ></path>
                    </svg>

                </div>

                <p class="text-xs font-semibold text-slate-500">
                    No trending topics yet
                </p>

                <p class="text-[10px] text-slate-400 mt-1">
                    Topics will appear as the community gets active.
                </p>

            </div>

        @endif

    </div>

    {{-- Community Guidelines Card --}}
    <div class="bg-white p-5 rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-slate-100">
        <h3 class="font-bold text-slate-900 text-sm tracking-tight mb-3">
            Community Guidelines
        </h3>
        <ul class="space-y-2 text-xs text-slate-600 font-medium">
            <li class="flex items-start gap-1.5">
                <span class="text-slate-400 font-bold">&rsaquo;</span>
                <span>Be respectful and kind to others.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-slate-400 font-bold">&rsaquo;</span>
                <span>No spam or self-promotion.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-slate-400 font-bold">&rsaquo;</span>
                <span>Help others and share authentic info.</span>
            </li>
            <li class="flex items-start gap-1.5">
                <span class="text-slate-400 font-bold">&rsaquo;</span>
                <span>Use correct category for your post.</span>
            </li>
        </ul>
        <div class="mt-4 pt-3 border-t border-slate-100">
            <a href="#" class="text-xs font-bold text-[#0c1b33] hover:text-indigo-600 transition inline-flex items-center gap-1">
                Read Full Guidelines &rarr;
            </a>
        </div>
    </div>

    {{-- Need Expert Help Card --}}
    <div class="bg-gradient-to-br from-amber-50/60 via-white to-amber-50/30 p-5 rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-amber-100/60 flex items-center justify-between gap-4">
        <div class="space-y-2.5">
            <div>
                <h3 class="font-bold text-slate-900 text-sm tracking-tight">
                    Need Expert Help?
                </h3>
                <p class="text-xs text-slate-500 mt-0.5 leading-snug">
                    Our counselors are here to help you.
                </p>
            </div>
            <a href="#" class="inline-block text-xs font-bold text-amber-600 hover:text-amber-700 transition">
                Book a Free Consultation
            </a>
        </div>
        <div class="shrink-0">
            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=200&h=150&fit=crop" alt="Counselors" class="w-24 h-20 object-cover rounded-xl shadow-xs border border-white">
        </div>
    </div>

    @auth
        {{-- Recent Activity --}}
        <div class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-slate-100">

            <div class="flex items-center justify-between mb-4">

                <h3 class="font-bold text-slate-900 text-sm tracking-tight">
                    Recent Activity
                </h3>

              

            </div>

            <div class="space-y-4">

                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)

                    <div class="flex items-start gap-3 group cursor-pointer">

                        <div class="relative flex h-2 w-2 mt-1.5 shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </div>

                        <div class="min-w-0">

                            <p class="text-xs font-medium text-slate-700 group-hover:text-[#0c1b33] transition leading-snug">
                                {!! $notification->data['message'] ?? 'New interaction' !!}
                            </p>

                            <p class="text-[10px] text-slate-400 font-medium mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>

                        </div>

                    </div>

                @empty

                    <div class="flex flex-col items-center justify-center py-4 text-center">

                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center mb-2">

                            <svg
                                class="w-5 h-5 text-slate-300"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                ></path>
                            </svg>

                        </div>

                        <span class="text-slate-400 text-xs font-medium">
                            You're all caught up!
                        </span>

                    </div>

                @endforelse

            </div>

        </div>

    @endauth

    @guest
    <div class="bg-white p-6 rounded-2xl shadow-[0_4px_20px_-4px_rgba(15,23,42,0.05)] border border-slate-100 text-center">
        <h3 class="font-bold text-slate-900 text-sm tracking-tight mb-2">
            Join the Community
        </h3>
        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
            Log in to post, comment, and track your activity.
        </p>
        <div class="flex flex-col gap-2">
            <a href="{{ route('login') }}"
               class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl transition-colors">
              Community Login
            </a>
            <a href="{{ route('register') }}"
               class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-xl transition-colors">
                Create Account
            </a>
        </div>
    </div>
@endguest

</aside>