@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">
    
    {{-- PENDING MODERATION ALERT BANNER --}}
    @if($pendingReportsCount > 0)
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 font-bold shrink-0">
                    ⚠️
                </span>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Moderation Attention Required</h3>
                    <p class="text-xs text-amber-700">There are <strong class="font-black">{{ $pendingReportsCount }} pending user reports</strong> waiting for review.</p>
                </div>
            </div>
            <a href="{{ route('admin.reports', ['status' => 'pending']) }}"
               class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition shrink-0">
                Review Reports →
            </a>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Overview</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time statistics, moderation alerts & key engagement metrics for REIAC Community</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            System Healthy
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Members</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalMembers) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Registered Users</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Posts</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalPosts) }}</div>
            <div class="text-[11px] text-blue-600 font-semibold mt-0.5">Discussions</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Comments</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalComments) }}</div>
            <div class="text-[11px] text-purple-600 font-semibold mt-0.5">Replies given</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Categories</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalCategories) }}</div>
            <a href="{{ route('admin.categories.index') }}" class="text-[11px] text-amber-600 font-semibold mt-0.5 hover:underline block">Manage topics →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Groups</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($totalGroups) }}</div>
            <a href="{{ route('admin.groups.index') }}" class="text-[11px] text-indigo-600 font-semibold mt-0.5 hover:underline block">Inspect groups →</a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pending Reports</div>
            <div class="text-2xl font-extrabold {{ $pendingReportsCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1.5">
                {{ number_format($pendingReportsCount) }}
            </div>
            <a href="{{ route('admin.reports') }}" class="text-[11px] text-rose-600 font-semibold mt-0.5 hover:underline block">View reports →</a>
        </div>
    </div>

    {{-- GRID FOR DISCUSSIONS, TOP CONTRIBUTORS & NEW MEMBERS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- RECENT POSTS TABLE (2 cols on large screen) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-900 text-sm">Recent Community Discussions</h2>
                    <a href="{{ route('admin.posts') }}" class="text-xs font-bold text-amber-600 hover:underline">View All →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-3 rounded-l-xl">Title / Post</th>
                                <th class="p-3">Category</th>
                                <th class="p-3">Author</th>
                                <th class="p-3 rounded-r-xl">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($recentPosts as $post)
                                <tr>
                                    <td class="p-3 text-slate-900 font-bold max-w-[220px] truncate">
                                        {{ $post->title }}
                                    </td>
                                    <td class="p-3">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[10px] font-bold">
                                            {{ $post->category->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600">
                                        {{ $post->user->name ?? 'Anonymous' }}
                                    </td>
                                    <td class="p-3 text-slate-400 whitespace-nowrap">
                                        {{ $post->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-slate-400">No recent discussions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.posts') }}" class="w-full block text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition">
                    Manage All {{ number_format($totalPosts) }} Discussions
                </a>
            </div>
        </div>

        {{-- SIDE COLUMN: TOP CONTRIBUTORS & NEW MEMBERS --}}
        <div class="space-y-6">
            
            {{-- TOP CONTRIBUTORS WIDGET --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-1.5">
                        <span class="text-amber-500 text-sm">⭐</span>
                        <h2 class="font-bold text-slate-900 text-sm">Top Contributors</h2>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Points</span>
                </div>

                <div class="space-y-3">
                    @forelse($topContributors as $idx => $contributor)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-4 font-bold text-slate-400 text-[11px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-reiac-navy text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                    {{ strtoupper(substr($contributor->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate">{{ $contributor->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $contributor->posts_count }} posts • {{ $contributor->comments_count }} comments</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 font-bold text-[11px] shrink-0">
                                {{ $contributor->contributor_points }} pts
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 text-xs py-2">No contributors yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- NEW MEMBERS PANEL --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-900 text-sm">New Members</h2>
                    <a href="{{ route('admin.users') }}" class="text-xs text-slate-400 hover:text-amber-600 font-semibold transition">Manage →</a>
                </div>

                <div class="space-y-3">
                    @forelse($newMembers as $member)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 text-xs shrink-0">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 text-xs truncate">{{ $member->name }}</div>
                                    <div class="text-[10px] text-slate-400 truncate">{{ $member->email }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium shrink-0">
                                {{ $member->created_at->diffForHumans(null, true) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-slate-400 text-xs py-2">No new members.</div>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.users') }}" class="w-full block text-center py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition">
                        View All {{ number_format($totalMembers) }} Users
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection