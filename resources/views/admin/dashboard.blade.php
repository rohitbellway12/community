@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">
    
    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Overview</h1>
            <p class="text-xs text-slate-500 mt-0.5">Real-time statistics & key engagement metrics for REIAC Community</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs px-3 py-1.5 rounded-xl font-bold flex items-center gap-1.5 shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            System Healthy
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Members</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ number_format($totalMembers) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">Registered Users</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Posts</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ number_format($totalPosts) }}</div>
            <div class="text-[11px] text-blue-600 font-semibold mt-1">Discussions created</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Comments</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ number_format($totalComments) }}</div>
            <div class="text-[11px] text-purple-600 font-semibold mt-1">Community replies</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Today</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-2">{{ number_format($activeToday) }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">Active users</div>
        </div>
    </div>

    {{-- GRID FOR RECENT CONTENT & NEW MEMBERS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- RECENT POSTS TABLE --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-5">
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

        {{-- NEW MEMBERS PANEL --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-900 text-sm">New Members</h2>
                    <a href="{{ route('admin.users') }}" class="text-xs text-slate-400 hover:text-amber-600 font-semibold transition">Manage →</a>
                </div>

                <div class="space-y-4">
                    @forelse($newMembers as $member)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600 text-xs shrink-0">
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
                        <div class="text-center text-slate-400 text-xs py-4">No new members.</div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-slate-100">
                <a href="{{ route('admin.users') }}" class="w-full block text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl transition">
                    View All {{ number_format($totalMembers) }} Users
                </a>
            </div>
        </div>

    </div>

</div>
@endsection