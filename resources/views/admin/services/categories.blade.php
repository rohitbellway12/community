@extends('layouts.admin')

@section('title', 'Service Categories Management')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto" x-data="categoryManagement()">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl flex items-center justify-between text-emerald-800 text-xs font-bold shadow-xs">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold">✕</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-800 text-xs shadow-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <span>⚠️</span>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside pl-4 text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.services.index') }}" class="text-xs font-bold text-slate-400 hover:text-amber-600 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Back to Services</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-reiac-gold uppercase tracking-wider">Classification</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-0.5">Service Categories</h1>
            <p class="text-xs text-slate-500 mt-0.5">Organize consultancy services into intuitive groupings for users on mobile app and web</p>
        </div>

        <button
            type="button"
            @click="openCreateModal()"
            class="px-4 py-2.5 rounded-xl bg-reiac-gold hover:bg-amber-500 text-reiac-navy font-black text-xs shadow-xs transition flex items-center gap-2 shrink-0 active:scale-95"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add New Category</span>
        </button>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Categories</span>
                <span class="text-2xl font-black text-slate-800 mt-1 block">{{ $totalCategories }}</span>
            </div>
            <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                🏷️
            </span>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Active Categories</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $activeCategories }}</span>
            </div>
            <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                ✓
            </span>
        </div>
    </div>

    {{-- CATEGORIES TABLE --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-16 text-center">Order</th>
                        <th class="py-3.5 px-4">Category Name</th>
                        <th class="py-3.5 px-4">Slug &amp; Description</th>
                        <th class="py-3.5 px-4 text-center">Services Count</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition group">
                            {{-- Order --}}
                            <td class="py-4 px-4 text-center">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 font-black text-xs inline-flex items-center justify-center">
                                    {{ $category->sort_order }}
                                </span>
                            </td>

                            {{-- Name --}}
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-sm">
                                        🏷️
                                    </span>
                                    <span class="font-extrabold text-slate-800 text-sm">
                                        {{ $category->name }}
                                    </span>
                                </div>
                            </td>

                            {{-- Slug & Description --}}
                            <td class="py-4 px-4">
                                <div>
                                    <span class="font-mono text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                        {{ $category->slug }}
                                    </span>
                                    @if($category->description)
                                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-1 max-w-sm">
                                            {{ $category->description }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- Services Count --}}
                            <td class="py-4 px-4 text-center">
                                <a
                                    href="{{ route('admin.services.index', ['category_id' => $category->id]) }}"
                                    class="inline-flex items-center gap-1 font-extrabold text-amber-700 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg text-xs transition"
                                    title="View services in this category"
                                >
                                    <span>{{ $category->services_count }} services</span>
                                </a>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-4 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button
                                        type="button"
                                        @click="openEditModal({{ json_encode($category) }})"
                                        class="p-2 rounded-xl text-slate-600 hover:text-amber-600 hover:bg-amber-50 transition"
                                        title="Edit Category"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button
                                        type="button"
                                        @click="confirmDelete('{{ $category->id }}', '{{ addslashes($category->name) }}', {{ $category->services_count }})"
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                        title="Delete Category"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                    <form
                                        id="delete-category-{{ $category->id }}"
                                        action="{{ route('admin.service-categories.destroy', $category) }}"
                                        method="POST"
                                        class="hidden"
                                    >
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-center space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center font-bold text-xl">
                                        🏷️
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Categories Found</h4>
                                    <p class="text-xs text-slate-500">
                                        Create categories like Visa Guidance, University Admissions, or Language Prep.
                                    </p>
                                    <div class="pt-2">
                                        <button
                                            type="button"
                                            @click="openCreateModal()"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-reiac-gold text-reiac-navy font-bold text-xs shadow-xs"
                                        >
                                            <span>Add First Category</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- CATEGORY MODAL (Create & Edit) --}}
    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div
            @click.away="modalOpen = false"
            class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-5 border border-slate-100"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-black text-slate-900" x-text="isEdit ? 'Edit Category' : 'Create New Category'"></h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Name --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Category Name <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        name="name"
                        x-model="formData.name"
                        @input="generateSlug()"
                        required
                        placeholder="e.g. Visa & Immigration"
                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden"
                    >
                </div>

                {{-- Slug --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Slug (URL identifier)</label>
                    <input
                        type="text"
                        name="slug"
                        x-model="formData.slug"
                        placeholder="visa-immigration"
                        class="w-full px-3 py-2 text-xs font-mono border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden bg-slate-50"
                    >
                </div>

                {{-- Description --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Description</label>
                    <textarea
                        name="description"
                        x-model="formData.description"
                        rows="3"
                        placeholder="Brief summary of services contained in this category..."
                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden leading-relaxed"
                    ></textarea>
                </div>

                {{-- Sort Order & Active Grid --}}
                <div class="grid grid-cols-2 gap-4 pt-1">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">Sort Order</label>
                        <input
                            type="number"
                            name="sort_order"
                            x-model="formData.sort_order"
                            min="0"
                            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden"
                        >
                    </div>

                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                x-model="formData.is_active"
                                class="w-4 h-4 text-emerald-600 rounded-md border-slate-300 focus:ring-emerald-500"
                            >
                            <span class="text-xs font-bold text-slate-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        @click="modalOpen = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-reiac-gold hover:bg-amber-500 text-reiac-navy font-black text-xs shadow-xs transition"
                        x-text="isEdit ? 'Update Category' : 'Save Category'"
                    >
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function categoryManagement() {
    return {
        modalOpen: false,
        isEdit: false,
        formAction: '{{ route('admin.service-categories.store') }}',
        formData: {
            name: '',
            slug: '',
            description: '',
            sort_order: 0,
            is_active: true
        },

        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route('admin.service-categories.store') }}';
            this.formData = {
                name: '',
                slug: '',
                description: '',
                sort_order: 0,
                is_active: true
            };
            this.modalOpen = true;
        },

        openEditModal(category) {
            this.isEdit = true;
            this.formAction = `/admin/service-categories/${category.id}`;
            this.formData = {
                name: category.name || '',
                slug: category.slug || '',
                description: category.description || '',
                sort_order: category.sort_order || 0,
                is_active: Boolean(category.is_active)
            };
            this.modalOpen = true;
        },

        generateSlug() {
            if (!this.isEdit || !this.formData.slug) {
                this.formData.slug = this.formData.name
                    .toString()
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-');
            }
        },

        confirmDelete(categoryId, categoryName, servicesCount) {
            let warningText = `Are you sure you want to delete category <b>"${categoryName}"</b>?`;
            if (servicesCount > 0) {
                warningText += `<br><br><span class="text-rose-600 font-bold">⚠️ Warning: ${servicesCount} services are assigned to this category. Their category will be set to Uncategorized.</span>`;
            }

            Swal.fire({
                title: 'Delete Category?',
                html: warningText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-xs px-5 py-2.5',
                    cancelButton: 'rounded-xl font-bold text-xs px-5 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-category-${categoryId}`).submit();
                }
            });
        }
    }
}
</script>
@endpush
@endsection
