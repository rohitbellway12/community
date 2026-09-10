@extends('layouts.admin')

@section('title', 'Groups Moderation')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold">✕</button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Community Groups</h1>
            <p class="text-xs text-slate-500 mt-0.5">Inspect user-created interest groups, monitor discussions, and moderate groups.</p>
        </div>
        <div class="text-xs text-slate-500 font-semibold bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs">
            Total Groups: <span class="font-bold text-slate-900">{{ $groups->total() }}</span>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.groups.index') }}" method="GET" class="w-full sm:w-80">
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search group or owner..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    {{-- GROUPS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Group Name</th>
                        <th class="py-3.5 px-4">Owner / Creator</th>
                        <th class="py-3.5 px-4">Members</th>
                        <th class="py-3.5 px-4">Posts</th>
                        <th class="py-3.5 px-4">Created Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($groups as $group)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- Group Info --}}
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $group->name }}
                                </div>
                                @if($group->description)
                                    <p class="text-[11px] text-slate-500 line-clamp-1 max-w-sm mt-0.5">{{ $group->description }}</p>
                                @endif
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">/groups/{{ $group->slug }}</div>
                            </td>

                            {{-- Owner --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $group->owner->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $group->owner->email ?? '' }}</div>
                            </td>

                            {{-- Members --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ number_format($group->users_count) }} members
                                </span>
                            </td>

                            {{-- Posts --}}
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ number_format($group->posts_count) }} posts
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                {{ $group->created_at->format('M d, Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('community.groups.show', $group->slug) }}"
                                   target="_blank"
                                   class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1 inline-flex items-center gap-1">
                                    View ↗
                                </a>
                                <form action="{{ route('admin.groups.destroy', $group) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this group and all its discussions?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No community groups found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($groups->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $groups->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
