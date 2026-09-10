@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         addModalOpen: false,
         editModalOpen: false,
         editCategory: { id: null, name: '', description: '', icon: '', status: 1, sort_order: 0 },
         openEdit(cat) {
             this.editCategory = {
                 id: cat.id,
                 name: cat.name,
                 description: cat.description || '',
                 icon: cat.icon || '',
                 status: cat.status ? 1 : 0,
                 sort_order: cat.sort_order || 0
             };
             this.editModalOpen = true;
         }
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
            <h1 class="text-xl font-extrabold text-slate-900">Discussion Categories</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage community topic categories, icons, ordering, and visibility.</p>
        </div>
        <div>
            <button type="button"
                    @click="addModalOpen = true"
                    class="bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </button>
        </div>
    </div>

    {{-- SEARCH & STATS --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-xs text-slate-500 font-semibold">
            Total Categories: <span class="font-bold text-slate-900">{{ $categories->total() }}</span>
        </div>

        <form action="{{ route('admin.categories.index') }}" method="GET" class="w-full sm:w-72">
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search category name..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>
    </div>

    {{-- CATEGORIES TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16">Order</th>
                        <th class="py-3.5 px-4">Name / Slug</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4">Posts Count</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 text-slate-400 font-bold">
                                {{ $category->sort_order }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                    @if($category->icon)
                                        <span class="text-base">{{ $category->icon }}</span>
                                    @endif
                                    {{ $category->name }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">/{{ $category->slug }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-sm">
                                {{ $category->description ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                    {{ $category->posts_count }} posts
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                @if($category->status)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button type="button"
                                        @click="openEdit({{ $category->toJson() }})"
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1">
                                    Edit
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete category {{ $category->name }}?');">
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
                                No categories found. Click "Add Category" to create your first topic!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- ADD CATEGORY MODAL --}}
    <div x-show="addModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4"
             @click.outside="addModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Add New Category</h3>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Visa Guidelines"
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description of this discussion topic"
                              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Icon / Emoji</label>
                        <input type="text" name="icon" placeholder="e.g. 🛂"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="0" min="0"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT CATEGORY MODAL --}}
    <div x-show="editModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4"
             @click.outside="editModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900">Edit Category</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form :action="'{{ route('admin.categories.index') }}/' + editCategory.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category Name *</label>
                    <input type="text" name="name" x-model="editCategory.name" required
                           class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" x-model="editCategory.description" rows="2"
                              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Icon / Emoji</label>
                        <input type="text" name="icon" x-model="editCategory.icon"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editCategory.sort_order" min="0"
                               class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" x-model="editCategory.status" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
