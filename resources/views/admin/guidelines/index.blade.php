@extends('layouts.admin')

@section('title', 'Community Guidelines Management')

@section('content')
<div x-data="{
    addModalOpen: false,
    editModalOpen: false,
    editGuideline: {
        id: '',
        title: '',
        description: '',
        sort_order: 0,
        is_active: 1
    },
    openEdit(item) {
        this.editGuideline = {
            id: item.id,
            title: item.title,
            description: item.description || '',
            sort_order: item.sort_order,
            is_active: item.is_active ? 1 : 0
        };
        this.editModalOpen = true;
    }
}" class="space-y-6">

    {{-- TOP BANNER & STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Guidelines</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                📜
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active on Frontend</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['active'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                ✅
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Inactive / Hidden</p>
                <h3 class="text-2xl font-extrabold text-slate-400 mt-1">{{ $stats['inactive'] }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-lg font-bold">
                ⏸️
            </div>
        </div>
    </div>

    {{-- HEADER & ACTIONS --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Community Guidelines</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage community rules, sidebar bullet points, and full guidelines page.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('community.guidelines') }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                <span>View Frontend Page</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <button type="button" @click="addModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-950 bg-reiac-gold hover:bg-amber-400 rounded-xl transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Guideline</span>
            </button>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form action="{{ route('admin.guidelines.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search guidelines title or description..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>

            <select name="status" class="text-xs border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="">All Statuses</option>
                <option value="1" {{ $status === '1' ? 'selected' : '' }}>Active Only</option>
                <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inactive Only</option>
            </select>

            <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 rounded-xl transition">
                Filter
            </button>

            @if($search || $status !== null && $status !== '')
                <a href="{{ route('admin.guidelines.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/75 text-slate-500 uppercase tracking-wider font-bold">
                        <th class="py-3.5 px-4 w-16 text-center">Order</th>
                        <th class="py-3.5 px-4">Guideline Title & Rule Details</th>
                        <th class="py-3.5 px-4 w-28 text-center">Status</th>
                        <th class="py-3.5 px-4 w-32 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($guidelines as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                #{{ $item->sort_order }}
                            </td>
                            <td class="py-3.5 px-4 max-w-md">
                                <div class="font-bold text-slate-900 text-sm mb-1">
                                    {{ $item->title }}
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $item->description ?: 'No detailed description provided.' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('admin.guidelines.updateStatus', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Click to toggle status"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition
                                            {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" @click="openEdit({{ json_encode($item) }})"
                                            class="px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-slate-950 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                                        Edit
                                    </button>

                                    <form action="{{ route('admin.guidelines.destroy', $item) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this guideline?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📜</div>
                                <p class="text-sm font-semibold">No community guidelines found.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Add Guideline" above to create one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($guidelines->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $guidelines->links() }}
            </div>
        @endif
    </div>

    {{-- ADD GUIDELINE MODAL --}}
    <div x-show="addModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4"
             @click.outside="addModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Add New Guideline</h3>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('admin.guidelines.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Guideline Title / Rule *</label>
                    <input type="text" name="title" required placeholder="e.g. Be respectful and kind to others."
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Detailed Description (Shown on Full Page)</label>
                    <textarea name="description" rows="3" placeholder="Explain the context, reasons, and expectations for this guideline..."
                              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0" min="0"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="is_active" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                            <option value="1">Active (Show on Frontend & Sidebar)</option>
                            <option value="0">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-slate-950 bg-reiac-gold hover:bg-amber-400 rounded-xl transition shadow-xs">
                        Save Guideline
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT GUIDELINE MODAL --}}
    <div x-show="editModalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4"
             @click.outside="editModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Edit Guideline</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form :action="'{{ route('admin.guidelines.index') }}/' + editGuideline.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Guideline Title / Rule *</label>
                    <input type="text" name="title" x-model="editGuideline.title" required
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Detailed Description (Shown on Full Page)</label>
                    <textarea name="description" x-model="editGuideline.description" rows="3"
                              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editGuideline.sort_order" min="0"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="is_active" x-model="editGuideline.is_active" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                            <option value="1">Active (Show on Frontend & Sidebar)</option>
                            <option value="0">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-slate-950 bg-reiac-gold hover:bg-amber-400 rounded-xl transition shadow-xs">
                        Update Guideline
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
