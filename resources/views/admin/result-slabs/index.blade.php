@extends('layouts.admin')

@section('title', 'Result Slabs Management')

@section('content')

<div
    class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
    x-data="{
        addModalOpen: false,
        editModalOpen: false,
        isSaving: false,

        editSlab: {
            id: null,
            name: '',
            min_marks: 0,
            max_marks: 0,
            badge_color: 'emerald',
            description: '',
            test_id: '',
            status: 'active'
        },

        editActionUrl: '',

        openEdit(btn) {
            const slab = JSON.parse(btn.dataset.slab);
            this.editSlab = {
                id:          slab.id,
                name:        slab.name        || '',
                min_marks:   slab.min_marks   !== undefined ? slab.min_marks : 0,
                max_marks:   slab.max_marks   !== undefined ? slab.max_marks : 0,
                badge_color: slab.badge_color || 'emerald',
                description: slab.description || '',
                test_id:     slab.test_id     ? slab.test_id : '',
                status:      slab.status      || 'active'
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
            fd.append('name',        this.editSlab.name);
            fd.append('min_marks',   this.editSlab.min_marks);
            fd.append('max_marks',   this.editSlab.max_marks);
            fd.append('badge_color', this.editSlab.badge_color);
            fd.append('description', this.editSlab.description || '');
            if (this.editSlab.test_id) {
                fd.append('test_id', this.editSlab.test_id);
            }
            fd.append('status',      this.editSlab.status);

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
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-extrabold text-slate-900">Result Slabs (Grading)</h1>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-reiac-gold/20 text-slate-800">
                    Step 3
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Set marks-based grade slabs (e.g. 5 to 10 marks = A++) shown on student results & admin submissions.
            </p>
        </div>

        <div>
            <button
                type="button"
                @click="addModalOpen = true"
                class="bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2"
            >
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Result Slab
            </button>
        </div>
    </div>

    {{-- KPI STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Slabs</div>
            <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ $slabs->total() }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Slabs</div>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1.5">{{ \App\Models\ResultSlab::where('status', 'active')->count() }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Global Slabs</div>
            <div class="text-2xl font-extrabold text-blue-600 mt-1.5">{{ \App\Models\ResultSlab::whereNull('test_id')->count() }}</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Test-Specific</div>
            <div class="text-2xl font-extrabold text-purple-600 mt-1.5">{{ \App\Models\ResultSlab::whereNotNull('test_id')->count() }}</div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.result-slabs.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by slab name, description..."
                    class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                >
            </div>

            <select
                name="test_id"
                onchange="this.form.submit()"
                class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-reiac-gold focus:outline-none"
            >
                <option value="">All Scopes (Global & Test Specific)</option>
                <option value="global" {{ request('test_id') === 'global' ? 'selected' : '' }}>Global Slabs Only (All Tests)</option>
                @foreach($tests as $t)
                    <option value="{{ $t->id }}" {{ (string)request('test_id') === (string)$t->id ? 'selected' : '' }}>
                        Test: {{ Str::limit($t->title, 35) }}
                    </option>
                @endforeach
            </select>

            <select
                name="status"
                onchange="this.form.submit()"
                class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-reiac-gold focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            @if(request('search') || request('test_id') || request('status'))
                <a
                    href="{{ route('admin.result-slabs.index') }}"
                    class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl transition flex items-center justify-center"
                >
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- SLABS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16">S.No.</th>
                        <th class="py-3.5 px-4">Slab / Grade</th>
                        <th class="py-3.5 px-4">Marks Range</th>
                        <th class="py-3.5 px-4">Applicable Test</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($slabs as $slab)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- S.No (Database ID) --}}
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-500 font-mono">
                                    #{{ $slab->id }}
                                </span>
                            </td>

                            {{-- SLAB NAME WITH BADGE --}}
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold border {{ $slab->badge_class }}">
                                    {{ $slab->name }}
                                </span>
                            </td>

                            {{-- MARKS RANGE --}}
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 font-mono text-sm">
                                    {{ (float)$slab->min_marks }} - {{ (float)$slab->max_marks }}
                                </span>
                                <span class="text-[10px] text-slate-400 ml-1">marks</span>
                            </td>

                            {{-- APPLICABLE TEST --}}
                            <td class="py-3 px-4">
                                @if($slab->test)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        {{ Str::limit($slab->test->title, 25) }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        All Tests (Global)
                                    </span>
                                @endif
                            </td>

                            {{-- DESCRIPTION --}}
                            <td class="py-3 px-4 text-slate-600 max-w-xs">
                                {{ $slab->description ?: '—' }}
                            </td>

                            {{-- STATUS TOGGLE --}}
                            <td
                                class="py-3 px-4"
                                x-data="{
                                    active: {{ $slab->status === 'active' ? 'true' : 'false' }},
                                    loading: false,

                                    async toggle() {
                                        if (this.loading) return;
                                        this.loading = true;
                                        const prev = this.active;
                                        this.active = !this.active;

                                        try {
                                            const res = await fetch('{{ route('admin.result-slabs.updateStatus', $slab) }}', {
                                                method: 'PATCH',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            });
                                            const data = await res.json();
                                            if (!res.ok || !data.success) {
                                                this.active = prev;
                                            } else {
                                                this.active = (data.status === 'active');
                                            }
                                        } catch (e) {
                                            this.active = prev;
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
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-1"
                                        :class="active ? 'bg-emerald-500' : 'bg-slate-300'"
                                        :title="active ? 'Click to Deactivate' : 'Click to Activate'"
                                    >
                                        <span class="sr-only">Toggle Status</span>
                                        <span
                                            aria-hidden="true"
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                            :class="active ? 'translate-x-5' : 'translate-x-0'"
                                        ></span>
                                    </button>
                                    <span
                                        class="text-[11px] font-bold select-none min-w-[48px]"
                                        :class="active ? 'text-emerald-700' : 'text-slate-400'"
                                        x-text="loading ? '...' : (active ? 'Active' : 'Inactive')"
                                    >
                                        {{ $slab->status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>

                            {{-- ACTIONS --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button
                                    type="button"
                                    @click="openEdit($el)"
                                    data-slab="{{ json_encode([
                                        'id' => $slab->id,
                                        'name' => $slab->name,
                                        'min_marks' => (float)$slab->min_marks,
                                        'max_marks' => (float)$slab->max_marks,
                                        'badge_color' => $slab->badge_color,
                                        'description' => $slab->description,
                                        'test_id' => $slab->test_id,
                                        'status' => $slab->status
                                    ]) }}"
                                    data-update-url="{{ route('admin.result-slabs.update', $slab) }}"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1"
                                >
                                    Edit
                                </button>

                                <form
                                    action="{{ route('admin.result-slabs.destroy', $slab) }}"
                                    method="POST"
                                    class="inline"
                                    id="delete-slab-{{ $slab->id }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        @click="
                                            const name = '{{ addslashes($slab->name) }}';
                                            const formEl = document.getElementById('delete-slab-{{ $slab->id }}');
                                            Swal.fire({
                                                title: 'Delete result slab?',
                                                html: '<p>Are you sure you want to delete slab<br><strong>' + name + '</strong>?<br>This cannot be undone.</p>',
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
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                No result slabs found. Click "Add Result Slab" to define your first grading range!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($slabs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $slabs->links() }}
            </div>
        @endif
    </div>

    {{-- =========================================================
        ADD RESULT SLAB MODAL
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
                <h3 class="text-sm font-bold text-slate-900">Add Result Slab (Grade)</h3>
                <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form action="{{ route('admin.result-slabs.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- SLAB NAME --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Slab / Grade Name *</label>
                    <input
                        type="text"
                        name="name"
                        required
                        maxlength="100"
                        placeholder="e.g. A++, Grade A, Excellent, Pass"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                </div>

                {{-- MARKS RANGE (MIN & MAX) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Min Marks *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="min_marks"
                            required
                            placeholder="e.g. 5"
                            class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none font-mono"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Max Marks *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="max_marks"
                            required
                            placeholder="e.g. 10"
                            class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none font-mono"
                        >
                    </div>
                </div>

                {{-- BADGE COLOR --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Badge Color *</label>
                    <select
                        name="badge_color"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="emerald">🟢 Emerald Green (e.g. A++, Top Grade)</option>
                        <option value="blue">🔵 Blue (e.g. Grade A, Very Good)</option>
                        <option value="amber">🟡 Amber Yellow (e.g. Grade B, Average)</option>
                        <option value="purple">🟣 Purple (e.g. Distinction)</option>
                        <option value="rose">🔴 Rose Red (e.g. Needs Improvement / Fail)</option>
                        <option value="slate">⚪ Slate Grey</option>
                    </select>
                </div>

                {{-- APPLICABLE TEST --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Applicable Test</label>
                    <select
                        name="test_id"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="">All Tests (Global Slab)</option>
                        @foreach($tests as $t)
                            <option value="{{ $t->id }}">Test: {{ $t->title }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-400 mt-1 block">Leave as "All Tests" to apply across all online tests.</span>
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description (Optional)</label>
                    <textarea
                        name="description"
                        rows="2"
                        maxlength="500"
                        placeholder="e.g. Outstanding performance, top ranker."
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    ></textarea>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select
                        name="status"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
                        Save Slab
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
        EDIT RESULT SLAB MODAL
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
                <h3 class="text-sm font-bold text-slate-900">Edit Result Slab</h3>
                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form
                id="edit-slab-form"
                method="POST"
                :action="editActionUrl"
                @submit.prevent="submitEdit()"
                class="space-y-4"
            >
                @csrf
                @method('PUT')

                {{-- SLAB NAME --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Slab / Grade Name *</label>
                    <input
                        type="text"
                        name="name"
                        x-model="editSlab.name"
                        required
                        maxlength="100"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                </div>

                {{-- MARKS RANGE (MIN & MAX) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Min Marks *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="min_marks"
                            x-model="editSlab.min_marks"
                            required
                            class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none font-mono"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Max Marks *</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="max_marks"
                            x-model="editSlab.max_marks"
                            required
                            class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none font-mono"
                        >
                    </div>
                </div>

                {{-- BADGE COLOR --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Badge Color *</label>
                    <select
                        name="badge_color"
                        x-model="editSlab.badge_color"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="emerald">🟢 Emerald Green</option>
                        <option value="blue">🔵 Blue</option>
                        <option value="amber">🟡 Amber Yellow</option>
                        <option value="purple">🟣 Purple</option>
                        <option value="rose">🔴 Rose Red</option>
                        <option value="slate">⚪ Slate Grey</option>
                    </select>
                </div>

                {{-- APPLICABLE TEST --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Applicable Test</label>
                    <select
                        name="test_id"
                        x-model="editSlab.test_id"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="">All Tests (Global Slab)</option>
                        @foreach($tests as $t)
                            <option value="{{ $t->id }}">Test: {{ $t->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea
                        name="description"
                        x-model="editSlab.description"
                        rows="2"
                        maxlength="500"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    ></textarea>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                    <select
                        name="status"
                        x-model="editSlab.status"
                        class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
                        <span x-text="isSaving ? 'Updating...' : 'Update Slab'">Update Slab</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
