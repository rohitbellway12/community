@extends('layouts.admin')

@section('title', 'Test Levels Management')

@section('content')

<div
    class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
    x-data="{
        addModalOpen: false,
        editModalOpen: false,
        isSaving: false,

        editLevel: {
            id: null,
            name: '',
            description: '',
            status: 'active'
        },

        editActionUrl: '',

        openEdit(btn) {
            const level = JSON.parse(btn.dataset.level);
            this.editLevel = {
                id:          level.id,
                name:        level.name        || '',
                description: level.description || '',
                status:      level.status      || 'active'
            };
            this.editActionUrl = btn.dataset.updateUrl;
            this.isSaving = false;
            this.editModalOpen = true;
        },

        async submitEdit() {
            const url = this.editActionUrl;
            if (!url) {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Update URL not set.' });
                } else {
                    alert('Error: update URL not set.');
                }
                return;
            }

            this.isSaving = true;

            const tokenMeta = document.querySelector('meta[name=csrf-token]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '{{ csrf_token() }}';
            const fd = new FormData();
            fd.append('_token',      token);
            fd.append('_method',     'PUT');
            fd.append('name',        this.editLevel.name);
            fd.append('description', this.editLevel.description || '');
            fd.append('status',      this.editLevel.status);

            try {
                const res = await fetch(url, {
                    method:  'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body:    fd
                });

                if (res.status === 422) {
                    const data = await res.json();
                    const msgs = Object.values(data.errors || {}).flat().join('\n');
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validation Failed',
                            text: msgs || 'Please check your inputs.'
                        });
                    } else {
                        alert(msgs || 'Validation failed.');
                    }
                    this.isSaving = false;
                } else if (res.ok) {
                    window.location.reload();
                } else {
                    const data = await res.json().catch(() => ({}));
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: data.message || 'Something went wrong.'
                        });
                    } else {
                        alert(data.message || 'Something went wrong.');
                    }
                    this.isSaving = false;
                }
            } catch(e) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: e.message
                    });
                } else {
                    alert('Network error: ' + e.message);
                }
                this.isSaving = false;
            }
        }
    }"
>

    {{-- =========================================================
        FLASH SUCCESS MESSAGE
    ========================================================== --}}
    @if(session('success'))
        <div
            class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs"
        >
            <span>{{ session('success') }}</span>

            <button
                type="button"
                @click="$el.parentElement.remove()"
                class="text-emerald-500 hover:text-emerald-700 font-bold"
            >
                ✕
            </button>
        </div>
    @endif


    {{-- =========================================================
        FLASH ERROR MESSAGE
    ========================================================== --}}
    @if(session('error'))
        <div
            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs"
        >
            <span>{{ session('error') }}</span>

            <button
                type="button"
                @click="$el.parentElement.remove()"
                class="text-red-500 hover:text-red-700 font-bold"
            >
                ✕
            </button>
        </div>
    @endif


    {{-- =========================================================
        VALIDATION ERRORS
    ========================================================== --}}
    @if($errors->any())
        <div
            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-xs"
        >
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-xl font-extrabold text-slate-900">
                Test Levels
            </h1>

            <p class="text-xs text-slate-500 mt-0.5">
                Manage Korean language test levels (Beginner, Intermediate, TOPIK etc.).
            </p>
        </div>

        <div>
            <button
                type="button"
                @click="addModalOpen = true"
                class="bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition flex items-center gap-1.5"
            >
                <svg
                    class="w-4 h-4 text-reiac-gold"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    />
                </svg>

                Add Test Level
            </button>
        </div>

    </div>


    {{-- =========================================================
        STATS
    ========================================================== --}}
    <div
        class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3"
    >

        <div class="text-xs text-slate-500 font-semibold">
            Total Levels:
            <span class="font-bold text-slate-900">
                {{ $levels->total() }}
            </span>
        </div>

        <div class="flex items-center gap-3">

            <div class="text-xs text-slate-500 font-semibold">
                Active:
                <span class="font-bold text-emerald-600">
                    {{ $levels->where('status', 'active')->count() }}
                </span>
            </div>

            <div class="text-xs text-slate-500 font-semibold">
                Inactive:
                <span class="font-bold text-slate-600">
                    {{ $levels->where('status', 'inactive')->count() }}
                </span>
            </div>

        </div>

    </div>


    {{-- =========================================================
        SEARCH
    ========================================================== --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">

        <form
            action="{{ route('admin.test-levels.index') }}"
            method="GET"
            class="flex flex-col sm:flex-row gap-2"
        >

            <div class="flex-1">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by level name or description..."
                    class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                >

            </div>

            <button
                type="submit"
                class="px-5 py-2.5 rounded-xl bg-reiac-navy hover:bg-slate-800 text-white text-xs font-bold transition"
            >
                Search
            </button>

            @if(request('search'))
                <a
                    href="{{ route('admin.test-levels.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition text-center"
                >
                    Clear
                </a>
            @endif

        </form>

    </div>


    {{-- =========================================================
        TEST LEVELS TABLE
    ========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left text-xs">

                <thead
                    class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider"
                >

                    <tr>

                        {{-- SERIAL NUMBER --}}
                        <th class="py-3.5 px-4 w-16">
                            S.No.
                        </th>

                        <th class="py-3.5 px-4">
                            Name
                        </th>

                        <th class="py-3.5 px-4">
                            Slug
                        </th>

                        <th class="py-3.5 px-4">
                            Description
                        </th>

                        <th class="py-3.5 px-4">
                            Questions
                        </th>

                        <th class="py-3.5 px-4">
                            Tests
                        </th>

                        <th class="py-3.5 px-4">
                            Status
                        </th>

                        <th class="py-3.5 px-4 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100 font-medium">

                    @forelse($levels as $index => $level)

                        <tr class="hover:bg-slate-50/60 transition">

                            {{-- SERIAL NUMBER (Database ID) --}}
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-500 font-mono">
                                    #{{ $level->id }}
                                </span>
                            </td>


                            {{-- NAME --}}
                            <td class="py-3 px-4">

                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $level->name }}
                                </div>

                            </td>


                            {{-- SLUG --}}
                            <td class="py-3 px-4">

                                <div class="text-[11px] text-slate-400 font-mono">
                                    /{{ $level->slug }}
                                </div>

                            </td>


                            {{-- DESCRIPTION --}}
                            <td class="py-3 px-4 text-slate-600 max-w-xs">

                                {{ $level->description ?? '—' }}

                            </td>


                            {{-- QUESTIONS --}}
                            <td class="py-3 px-4">

                                <span
                                    class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700"
                                >
                                    {{ $level->questions_count ?? 0 }}
                                </span>

                            </td>


                            {{-- TESTS --}}
                            <td class="py-3 px-4">

                                <span
                                    class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700"
                                >
                                    {{ $level->tests_count ?? 0 }}
                                </span>

                            </td>


                            {{-- =================================================
                                STATUS TOGGLE
                            ================================================== --}}
                            <td
                                class="py-3 px-4"
                                x-data="{
                                    active: {{ $level->status === 'active' ? 'true' : 'false' }},
                                    loading: false,

                                    async toggle() {

                                        if (this.loading) return;

                                        this.loading = true;

                                        const previousStatus = this.active;

                                        // Optimistic UI
                                        this.active = !this.active;

                                        try {

                                            const response = await fetch(
                                                '{{ route('admin.test-levels.updateStatus', $level) }}',
                                                {
                                                    method: 'PATCH',

                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json',
                                                        'Content-Type': 'application/json'
                                                    }
                                                }
                                            );

                                            const data = await response.json();

                                            if (!response.ok || !data.success) {

                                                this.active = previousStatus;

                                                alert(
                                                    data.message || 'Unable to update status.'
                                                );

                                            } else {

                                                this.active = data.status === 'active';

                                            }

                                        } catch (error) {

                                            this.active = previousStatus;

                                            alert(
                                                'Something went wrong. Please try again.'
                                            );

                                        } finally {

                                            this.loading = false;

                                        }
                                    }
                                }"
                            >

                                <div class="flex items-center gap-2">

                                    <button
                                        type="button"
                                        @click="toggle()"
                                        :disabled="loading"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-1 disabled:opacity-60 disabled:cursor-not-allowed"
                                        :class="active ? 'bg-emerald-500' : 'bg-slate-300'"
                                        :title="active ? 'Click to Deactivate' : 'Click to Activate'"
                                    >

                                        <span class="sr-only">
                                            Toggle Status
                                        </span>

                                        <span
                                            aria-hidden="true"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                            :class="active ? 'translate-x-5' : 'translate-x-0'"
                                        >
                                        </span>

                                    </button>


                                    <span
                                        class="text-[11px] font-bold select-none min-w-[48px]"
                                        :class="active ? 'text-emerald-700' : 'text-slate-400'"
                                        x-text="loading ? '...' : (active ? 'Active' : 'Inactive')"
                                    >
                                        {{ $level->status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>

                                </div>

                            </td>


                            {{-- =================================================
                                ACTIONS
                            ================================================== --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">

                                {{-- EDIT --}}
                                <button
                                    type="button"
                                    @click="openEdit($el)"
                                    data-level="{{ json_encode($level->only(['id', 'name', 'description', 'status'])) }}"
                                    data-update-url="{{ route('admin.test-levels.update', $level) }}"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1"
                                >
                                    Edit
                                </button>


                                {{-- DELETE --}}
                                <form
                                    action="{{ route('admin.test-levels.destroy', $level) }}"
                                    method="POST"
                                    class="inline"
                                    id="delete-level-{{ $level->id }}"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="button"
                                        @click="
                                            const name   = '{{ addslashes($level->name) }}';
                                            const formEl = document.getElementById('delete-level-{{ $level->id }}');
                                            Swal.fire({
                                                title: 'Delete test level?',
                                                html: '<p>Are you sure you want to delete<br><strong>' + name + '</strong>?<br>This cannot be undone.</p>',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Yes, Delete',
                                                cancelButtonText: 'Cancel',
                                                confirmButtonColor: '#E11D48',
                                                cancelButtonColor:  '#64748B',
                                                reverseButtons: true
                                            }).then(result => {
                                                if (result.isConfirmed) formEl.submit();
                                            });
                                        "
                                        class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="py-8 text-center text-slate-400"
                            >
                                No test levels found.
                                Click "Add Test Level" to create your first level!
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================================================
            PAGINATION
        ========================================================== --}}
        @if($levels->hasPages())

            <div class="p-4 border-t border-slate-100">

                {{ $levels->links() }}

            </div>

        @endif

    </div>


    {{-- =========================================================
        ADD TEST LEVEL MODAL
    ========================================================== --}}
    <div
        x-show="addModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
    >

        <div
            class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4"
            @click.outside="addModalOpen = false"
        >

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">

                <h3 class="text-sm font-bold text-slate-900">
                    Add New Test Level
                </h3>

                <button
                    type="button"
                    @click="addModalOpen = false"
                    class="text-slate-400 hover:text-slate-600 text-lg"
                >
                    ✕
                </button>

            </div>


            <form
                action="{{ route('admin.test-levels.store') }}"
                method="POST"
                class="space-y-4"
            >

                @csrf


                {{-- NAME --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Level Name *
                    </label>

                    <input
                        type="text"
                        name="name"
                        required
                        maxlength="100"
                        placeholder="e.g. Beginner Level 1"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >

                </div>


                {{-- DESCRIPTION --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="2"
                        maxlength="500"
                        placeholder="Brief description of this level"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    ></textarea>

                </div>


                {{-- STATUS --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- BUTTONS --}}
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">

                    <button
                        type="button"
                        @click="addModalOpen = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs"
                    >
                        Save Level
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        EDIT TEST LEVEL MODAL
    ========================================================== --}}
    <div
        x-show="editModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
    >

        <div
            class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4"
            @click.outside="editModalOpen = false"
        >

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">

                <h3 class="text-sm font-bold text-slate-900">
                    Edit Test Level
                </h3>

                <button
                    type="button"
                    @click="editModalOpen = false"
                    class="text-slate-400 hover:text-slate-600 text-lg"
                >
                    ✕
                </button>

            </div>


            <form
                id="edit-level-form"
                method="POST"
                action=""
                @submit.prevent="submitEdit()"
            >

                @csrf
                @method('PUT')


                {{-- NAME --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Level Name *
                    </label>

                    <input
                        type="text"
                        name="name"
                        x-model="editLevel.name"
                        required
                        maxlength="100"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >

                </div>


                {{-- DESCRIPTION --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="2"
                        maxlength="500"
                        x-model="editLevel.description"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    ></textarea>

                </div>


                {{-- STATUS --}}
                <div>

                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Status
                    </label>

                    <select
                        name="status"
                        x-model="editLevel.status"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- BUTTONS --}}
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">

                    <button
                        type="button"
                        @click="editModalOpen = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        :disabled="isSaving"
                        :class="isSaving ? 'opacity-60 cursor-not-allowed' : ''"
                        class="px-4 py-2 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs"
                    >
                        <span x-text="isSaving ? 'Updating...' : 'Update Level'">Update Level</span>
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection