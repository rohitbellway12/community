<x-community.shell :user="auth()->user()">
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Followers of &#64;{{ $profile->username }}</h1>
                <p class="text-xs text-slate-500">People who are following this user.</p>
            </div>
            <a href="{{ route('community.profile', $profile->username) }}" class="text-sm font-semibold text-[#0d3c81] hover:underline">&larr; Back to Profile</a>
        </div>

        <div class="space-y-3">
            @forelse($followers as $follower)
                @php
                    $followerAvatar = $follower->profile?->avatar ? asset('storage/' . $follower->profile->avatar) : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=160&q=80';
                @endphp
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $followerAvatar }}" class="h-10 w-10 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $follower->name }}</p>
                            <p class="text-xs text-slate-500">&#64;{{ $follower->profile?->username ?? 'user' }}</p>
                        </div>
                    </div>
                    @if($follower->profile)
                        <a href="{{ route('community.profile', $follower->profile->username) }}" class="text-xs font-semibold text-[#0d3c81] hover:underline">View Profile</a>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white p-12 text-center text-slate-500">
                    No followers found.
                </div>
            @endforelse
            {{ $followers->links() }}
        </div>
    </div>
</x-community.shell>