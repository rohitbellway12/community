@extends('layouts.admin')

@section('title', 'Question Bank')

@section('content')
<div class="p-6 space-y-6 max-w-[1400px] w-full mx-auto"
     x-data="{
         search: '{{ request('search') }}',
         levelId: '{{ request('level_id') }}',
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
            <h1 class="text-xl font-extrabold text-slate-900">Question Bank</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage questions by level. Questions are reusable across tests.</p>
        </div>
        <div>
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
                            <td colspan="7" class="py-8 text-center text-slate-400">
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

</div>
@endsection
