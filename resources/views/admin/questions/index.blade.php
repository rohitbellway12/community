@extends('layouts.admin')

@section('title', 'Question Bank')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         search: '{{ request('search') }}',
         levelId: '{{ request('level_id') }}',
         importModalOpen: false,
         isUploading: false
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
    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-50 border border-amber-300 text-amber-900 px-4 py-3.5 rounded-xl text-xs shadow-xs space-y-2">
            <div class="flex items-center justify-between font-bold text-amber-800">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Import Notice / Skipped Rows ({{ count(session('import_errors')) }}):
                </span>
                <button type="button" @click="$el.parentElement.parentElement.remove()" class="text-amber-600 hover:text-amber-800 font-bold">✕</button>
            </div>
            <ul class="list-disc pl-5 space-y-1 text-amber-800 max-h-48 overflow-y-auto font-mono text-[11px]">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
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
            <h1 class="text-xl font-extrabold text-slate-900">Question Bank</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage questions by level. Reusable across tests. Bulk import via Excel/CSV.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- DOWNLOAD SAMPLE EXCEL --}}
            <a href="{{ route('admin.questions.sampleTemplate') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-200 shadow-xs transition"
               title="Download sample Excel / CSV dummy file to fill in questions">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Dummy Excel
            </a>

            {{-- IMPORT QUESTIONS BUTTON --}}
            <button type="button"
                    @click="importModalOpen = true"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Questions
            </button>

            {{-- ADD QUESTION --}}
            <a href="{{ route('admin.questions.create') }}"
               class="bg-reiac-navy hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-reiac-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Question
            </a>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.questions.index') }}" method="GET" class="w-full sm:w-72">
            <div class="relative w-full">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search question..."
                       class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>

        <form action="{{ route('admin.questions.index') }}" method="GET" class="w-full sm:w-56">
            <select name="level_id" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-reiac-gold focus:bg-white transition" onchange="this.form.submit()">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- QUESTIONS TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-16">S.No.</th>
                        <th class="py-3.5 px-4">Level</th>
                        <th class="py-3.5 px-4">Question</th>
                        <th class="py-3.5 px-4">Type</th>
                        <th class="py-3.5 px-4">Options</th>
                        <th class="py-3.5 px-4">Marks</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($questions as $question)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-500 font-mono">
                                    #{{ $question->id }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $question->testLevel->name ?? '—' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs">
                                <div class="line-clamp-2">{{ Str::limit($question->question_text, 100) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $question->options->count() }}</span>
                                <span class="text-[10px] text-slate-400">options</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $question->marks }}</span>
                                <span class="text-[10px] text-slate-400">pts</span>
                            </td>
                            <td class="py-3 px-4" x-data="{
                                active: {{ $question->status === 'active' ? 'true' : 'false' }},
                                loading: false,
                                async toggle() {
                                    if (this.loading) return;
                                    this.loading = true;
                                    const prev = this.active;
                                    this.active = !this.active;
                                    try {
                                        const res = await fetch('{{ route('admin.questions.updateStatus', $question) }}', {
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
                            }">
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            @click="toggle()"
                                            :disabled="loading"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-1"
                                            :class="active ? 'bg-emerald-500' : 'bg-slate-300'"
                                            :title="active ? 'Click to Deactivate' : 'Click to Activate'">
                                        <span class="sr-only">Toggle Status</span>
                                        <span aria-hidden="true"
                                              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                              :class="active ? 'translate-x-5' : 'translate-x-0'">
                                        </span>
                                    </button>
                                    <span class="text-[11px] font-bold select-none min-w-[48px]"
                                          :class="active ? 'text-emerald-700' : 'text-slate-400'"
                                          x-text="loading ? '...' : (active ? 'Active' : 'Inactive')">
                                        {{ $question->status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.questions.edit', $question) }}"
                                   class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition mr-1">
                                    Edit
                                </a>
                                <form action="{{ route('admin.questions.destroy', $question) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            data-item-name="{{ $question->question_text }}"
                                            onclick="confirmDelete(this.form, this.dataset.itemName, 'question')"
                                            class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                No questions found. Click "Add Question" to create your first one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $questions->links() }}
            </div>
        @endif
    </div>

    {{-- =========================================================
        BULK IMPORT QUESTIONS MODAL
    ========================================================== --}}
    <div x-show="importModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5"
             @click.outside="if (!isUploading) importModalOpen = false">

            {{-- MODAL HEADER --}}
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Bulk Import Questions</h3>
                        <p class="text-[11px] text-slate-500">Upload questions via Excel (.xlsx) or CSV file</p>
                    </div>
                </div>
                <button type="button"
                        :disabled="isUploading"
                        @click="importModalOpen = false"
                        class="text-slate-400 hover:text-slate-600 text-lg font-bold disabled:opacity-50">✕</button>
            </div>

            {{-- STEP 1: SAMPLE TEMPLATE BANNER --}}
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start justify-between gap-3">
                <div class="space-y-0.5">
                    <div class="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Need Dummy Excel Template?
                    </div>
                    <p class="text-[11px] text-emerald-700 leading-relaxed">
                        Download our pre-formatted sample file. It has sample Korean questions and correct column headers ready for editing.
                    </p>
                </div>
                <a href="{{ route('admin.questions.sampleTemplate') }}"
                   class="shrink-0 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg shadow-xs transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            </div>

            {{-- STEP 2: IMPORT FORM --}}
            <form action="{{ route('admin.questions.import') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  @submit="isUploading = true"
                  class="space-y-4">
                @csrf

                {{-- DEFAULT TEST LEVEL --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Default Test Level <span class="text-slate-400 font-normal">(Optional fallback)</span>
                    </label>
                    <select name="test_level_id"
                            class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:outline-none transition">
                        <option value="">-- Match level name from Excel/CSV (or choose fallback) --</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">
                        If the file specifies the level name (e.g. "EPS-TOPIK 1"), it will match automatically.
                    </p>
                </div>

                {{-- FILE UPLOAD --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Upload Excel or CSV File <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-blue-400 rounded-xl p-4 text-center bg-slate-50/50 hover:bg-blue-50/20 transition cursor-pointer relative">
                        <input type="file"
                               name="file"
                               accept=".xlsx,.xls,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                               required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="space-y-1 pointer-events-none">
                            <svg class="w-8 h-8 text-blue-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-xs font-bold text-slate-700">Click or Drag & Drop File</p>
                            <p class="text-[10px] text-slate-400">Supports .xlsx, .xls, .csv (Max 10MB)</p>
                        </div>
                    </div>
                </div>

                {{-- INSTRUCTIONS SUMMARY --}}
                <div class="text-[11px] text-slate-500 bg-slate-50 rounded-xl p-3 space-y-1 border border-slate-100">
                    <div class="font-bold text-slate-700">Columns recognized:</div>
                    <p class="font-mono text-[10px] text-slate-600">
                        Test Level, Question Text, Question Type, Marks, Option A, Option B, Option C, Option D, Correct Option, Explanation, Status
                    </p>
                    <p class="text-[10px] text-slate-400">
                        • Correct Option can be A, B, C, D (or 1, 2, 3, 4).<br>
                        • Korean and all foreign languages are fully supported.
                    </p>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button"
                            :disabled="isUploading"
                            @click="importModalOpen = false"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="submit"
                            :disabled="isUploading"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 disabled:opacity-60 cursor-pointer">
                        <span x-show="!isUploading" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Start Import
                        </span>
                        <span x-show="isUploading" class="flex items-center gap-1.5">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Importing Questions...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

