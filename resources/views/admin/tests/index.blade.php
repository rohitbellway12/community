@extends('layouts.admin')

@section('title', 'Tests Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         search: '{{ request('search') }}',
         levelId: '{{ request('level_id') }}',
         status: '{{ request('status') }}',
     }">

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
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-xs">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Tests</h1>
            <p class="text-xs text-slate-500 mt-0.5">Create and manage tests under each level.</p>
        </div>
        <div>
            <a href="{{ route('admin.tests.create') }}"
               class="bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Test
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.tests.index') }}" method="GET" class="w-full sm:w-72">
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search test title..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>

        <form action="{{ route('admin.tests.index') }}" method="GET" class="w-full sm:w-48">
            <select name="level_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </form>

        <form action="{{ route('admin.tests.index') }}" method="GET" class="w-full sm:w-44">
            <select name="status" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </form>
    </div>

    {{-- TESTS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Title</th>
                        <th class="py-3.5 px-4">Level</th>
                        <th class="py-3.5 px-4">Questions</th>
                        <th class="py-3.5 px-4">Duration</th>
                        <th class="py-3.5 px-4">Passing</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tests as $test)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $test->title }}</div>
                                @if($test->open_date)
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Opens: {{ $test->open_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $test->testLevel->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $test->questions->count() }}</span>
                                <span class="text-[10px] text-slate-400">Q</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $test->duration_minutes }}</span>
                                <span class="text-[10px] text-slate-400">min</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $test->passing_marks }}</span>
                                <span class="text-[10px] text-slate-400">/ {{ $test->total_marks }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($test->status === 'published')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Published</span>
                                @elseif($test->status === 'draft')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Draft</span>
                                @elseif($test->status === 'scheduled')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Scheduled</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Archived</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.tests.edit', $test) }}"
                                   class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1">
                                    Edit
                                </a>
                                <form action="{{ route('admin.tests.destroy', $test) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this test?');">
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
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No tests found. Click "Create Test" to create your first one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $tests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
