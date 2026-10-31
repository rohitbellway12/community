@extends('layouts.admin')

@section('title', 'Services & WhatsApp Management')

@section('content')
<div class="w-full space-y-6" x-data="serviceManagement()">

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
                <span class="text-xs font-bold text-reiac-gold uppercase tracking-wider">Services Catalog</span>
                <span class="text-slate-300">•</span>
                <span class="text-xs text-slate-500">WhatsApp Lead Generation</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-0.5">Services & WhatsApp Integration</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage consultancy services, assigned WhatsApp numbers, prefilled inquiry templates, and listings ordering</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a
                href="{{ route('admin.service-categories.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition flex items-center gap-2 shrink-0"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Manage Categories</span>
            </a>

            <a
                href="{{ route('admin.services.create') }}"
                class="px-4 py-2.5 rounded-xl bg-reiac-gold hover:bg-amber-500 text-reiac-navy font-black text-xs shadow-xs transition flex items-center gap-2 shrink-0 active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Service</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total Services --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Services</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-800 mt-2">{{ $totalServices }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">{{ $totalCategories }} categories catalog</div>
        </div>

        {{-- Active / Published --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Active &amp; Live</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $activeCount }}</div>
            <div class="text-[11px] text-emerald-700/80 mt-0.5">Live on app &amp; web</div>
        </div>

        {{-- Inactive / Drafts --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactive</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-600 mt-2">{{ $inactiveCount }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Draft or deactivated</div>
        </div>

        {{-- WhatsApp Inquiries --}}
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">WhatsApp Clicks</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88 3.182 0 5.768-2.587 5.768-5.766.001-3.187-2.575-5.766-5.768-5.766zm9.969 5.828c0 5.523-4.477 10-10 10-1.854 0-3.593-.506-5.089-1.385l-6.911 1.385 1.85-6.621c-1.155-1.583-1.85-3.528-1.85-5.379 0-5.523 4.477-10 10-10s10 4.477 10 10z"/>
                    </svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-800 mt-2">{{ $totalClicks }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">User inquiries initiated</div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex flex-1 flex-col sm:flex-row items-center gap-3 w-full">
                {{-- Search Input --}}
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search service title, WhatsApp number, description..."
                        class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden"
                    >
                </div>

                {{-- Category Filter --}}
                <div class="w-full sm:w-48">
                    <select
                        name="category_id"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden text-slate-700 bg-white"
                    >
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="w-full sm:w-44">
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold/40 focus:border-reiac-gold outline-hidden text-slate-700 bg-white font-semibold"
                    >
                        <option value="all">All Statuses</option>
                        <option value="active" {{ (request('status') === 'active' || request('status') === 'published') ? 'selected' : '' }}>🟢 Active Only</option>
                        <option value="inactive" {{ (request('status') === 'inactive' || request('status') === 'draft') ? 'selected' : '' }}>⚫ Inactive Only</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="submit"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-xs transition"
                >
                    Apply Filter
                </button>

                @if(request()->hasAny(['search', 'category_id', 'status']))
                    <a
                        href="{{ route('admin.services.index') }}"
                        class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- SERVICES TABLE --}}
    <div class="bg-white border border-slate-100 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-16 text-center">Order</th>
                        <th class="py-3.5 px-4">Service &amp; Category</th>
                        <th class="py-3.5 px-4">Assigned WhatsApp &amp; Inquiries</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Clicks</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($services as $service)
                        <tr class="hover:bg-slate-50/60 transition group">
                            {{-- Sort Order --}}
                            <td class="py-4 px-4 text-center">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 font-black text-xs inline-flex items-center justify-center">
                                    {{ $service->sort_order }}
                                </span>
                            </td>

                            {{-- Service Info & Thumbnail --}}
                            <td class="py-4 px-4">
                                <div class="flex items-start gap-3.5">
                                    {{-- Thumbnail --}}
                                    <div class="w-14 h-14 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($service->image)
                                            <img
                                                src="{{ $service->image_url }}"
                                                alt="{{ $service->title }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <div class="w-full h-full bg-linear-to-br from-amber-50 to-orange-100 flex items-center justify-center text-amber-600">
                                                <svg class="w-6 h-6 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Text info --}}
                                    <div class="min-w-0 max-w-md">
                                        <div class="flex items-center gap-2">
                                            @if($service->category)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/60">
                                                    {{ $service->category->name }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500">
                                                    Uncategorized
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-slate-400 font-mono">/{{ $service->slug }}</span>
                                        </div>

                                        <h3 class="text-sm font-bold text-slate-800 mt-1 hover:text-amber-600 transition">
                                            <a href="{{ route('admin.services.edit', $service) }}">
                                                {{ $service->title }}
                                            </a>
                                        </h3>

                                        @if($service->short_description)
                                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">
                                                {{ $service->short_description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Assigned WhatsApp & Message --}}
                            <td class="py-4 px-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88 3.182 0 5.768-2.587 5.768-5.766.001-3.187-2.575-5.766-5.768-5.766zm9.969 5.828c0 5.523-4.477 10-10 10-1.854 0-3.593-.506-5.089-1.385l-6.911 1.385 1.85-6.621c-1.155-1.583-1.85-3.528-1.85-5.379 0-5.523 4.477-10 10-10s10 4.477 10 10z"/>
                                            </svg>
                                            <span>{{ $service->whatsapp_number }}</span>
                                        </span>

                                        {{-- WhatsApp Test button --}}
                                        <a
                                            href="{{ $service->whatsapp_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="Test WhatsApp Link with prefilled inquiry message"
                                            class="p-1 rounded-md text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100 transition"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>

                                    @if($service->whatsapp_message)
                                        <p class="text-[10px] text-slate-400 italic line-clamp-1 max-w-xs" title="{{ $service->whatsapp_message }}">
                                            "{{ $service->whatsapp_message }}"
                                        </p>
                                    @endif

                                    @if($service->phone || $service->email)
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                            @if($service->phone)
                                                <span>📞 {{ $service->phone }}</span>
                                            @endif
                                            @if($service->email)
                                                <span>✉️ {{ $service->email }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Status Toggle Switch (Active / Inactive) --}}
                            <td class="py-4 px-4 text-center">
                                @php
                                    $isActive = ($service->status === 'active' || (!$service->status && $service->is_published));
                                @endphp
                                <form method="POST" action="{{ route('admin.services.toggle-publish', $service) }}" class="inline-flex flex-col items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        role="switch"
                                        aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 {{ $isActive ? 'bg-emerald-500' : 'bg-slate-300' }}"
                                        title="{{ $isActive ? 'Currently Active - Click to Deactivate' : 'Currently Inactive - Click to Activate' }}"
                                    >
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-[10px] font-black tracking-tight {{ $isActive ? 'text-emerald-700' : 'text-slate-400' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </form>
                            </td>

                            {{-- Click Count --}}
                            <td class="py-4 px-4 text-center">
                                <span class="font-extrabold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg text-xs">
                                    {{ $service->click_count }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('admin.services.edit', $service) }}"
                                        class="p-2 rounded-xl text-slate-600 hover:text-amber-600 hover:bg-amber-50 transition"
                                        title="Edit Service"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <button
                                        type="button"
                                        @click="confirmDelete('{{ $service->id }}', '{{ addslashes($service->title) }}')"
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                        title="Delete Service"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                    <form
                                        id="delete-form-{{ $service->id }}"
                                        action="{{ route('admin.services.destroy', $service) }}"
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
                                        💼
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Services Found</h4>
                                    <p class="text-xs text-slate-500">
                                        Get started by adding your first consultancy service listing with a dedicated WhatsApp number.
                                    </p>
                                    <div class="pt-2">
                                        <a
                                            href="{{ route('admin.services.create') }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-reiac-gold text-reiac-navy font-bold text-xs shadow-xs"
                                        >
                                            <span>Add First Service</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($services->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function serviceManagement() {
    return {
        confirmDelete(serviceId, serviceTitle) {
            Swal.fire({
                title: 'Delete Service?',
                html: `Are you sure you want to delete <b>"${serviceTitle}"</b>? This action cannot be undone.`,
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
                    document.getElementById(`delete-form-${serviceId}`).submit();
                }
            });
        }
    }
}
</script>
@endpush
@endsection
