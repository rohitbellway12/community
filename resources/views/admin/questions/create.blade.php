@extends('layouts.admin')

@section('title', 'Add Question')

@section('content')
<div class="p-6 max-w-[1000px] w-full mx-auto">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-semibold flex items-center justify-between shadow-xs mb-4">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm font-semibold shadow-xs mb-4">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Add New Question</h1>
            <p class="text-xs text-slate-500 mt-0.5">Create a question under a test level</p>
        </div>
        <a href="{{ route('admin.questions.index') }}"
           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            ← Back
        </a>
    </div>

    <form action="{{ route('admin.questions.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf

        {{-- LEVEL --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Test Level *</label>
            <select name="test_level_id" required class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="">Select Level</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- QUESTION TEXT --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Question Text *</label>
            <textarea name="question_text" rows="3" required placeholder="Enter the question..."
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y"></textarea>
        </div>

        {{-- QUESTION TYPE --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Question Type *</label>
            <select name="question_type" id="questionType" required
                    class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    onchange="handleTypeChange()">
                <option value="mcq">MCQ (Multiple Choice)</option>
               {{-- <option value="true_false">True / False</option>
                <option value="image_based">Image Based</option>
                <option value="audio_based">Audio Based</option>  --}}
            </select>
        </div>

        {{-- IMAGE URL (conditional) --}}
        <div id="imageUrlGroup" style="display:none">
            <label class="block text-xs font-bold text-slate-700 mb-1">Image URL</label>
            <input type="url" name="image_url" placeholder="https://..."
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- AUDIO URL (conditional) --}}
        <div id="audioUrlGroup" style="display:none">
            <label class="block text-xs font-bold text-slate-700 mb-1">Audio URL</label>
            <input type="url" name="audio_url" placeholder="https://..."
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- MARKS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Marks *</label>
            <input type="number" name="marks" value="1" min="1" required
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- OPTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Options *</label>
            <div id="optionsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="relative">
                    <input type="hidden" name="options[A][label]" value="A">
                    <span class="absolute left-3 top-2.5 text-xs font-bold text-reiac-gold bg-slate-100 px-1.5 py-0.5 rounded">A</span>
                    <input type="text" name="options[A][text]" value="{{ old('options.A.text') }}" required placeholder="Option A text (e.g. True)"
                           class="w-full text-xs pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>
                <div class="relative">
                    <input type="hidden" name="options[B][label]" value="B">
                    <span class="absolute left-3 top-2.5 text-xs font-bold text-reiac-gold bg-slate-100 px-1.5 py-0.5 rounded">B</span>
                    <input type="text" name="options[B][text]" value="{{ old('options.B.text') }}" required placeholder="Option B text (e.g. False)"
                           class="w-full text-xs pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>
                <div id="optionC" class="relative">
                    <input type="hidden" name="options[C][label]" value="C">
                    <span class="absolute left-3 top-2.5 text-xs font-bold text-reiac-gold bg-slate-100 px-1.5 py-0.5 rounded">C</span>
                    <input type="text" name="options[C][text]" value="{{ old('options.C.text') }}" placeholder="Option C text (optional for True/False)"
                           class="w-full text-xs pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>
                <div id="optionD" class="relative">
                    <input type="hidden" name="options[D][label]" value="D">
                    <span class="absolute left-3 top-2.5 text-xs font-bold text-reiac-gold bg-slate-100 px-1.5 py-0.5 rounded">D</span>
                    <input type="text" name="options[D][text]" value="{{ old('options.D.text') }}" placeholder="Option D text (optional for True/False)"
                           class="w-full text-xs pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                </div>
            </div>
            <small class="text-[10px] text-slate-400 mt-1 block">True/False type: C and D are optional</small>
        </div>

        {{-- CORRECT OPTION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Correct Answer *</label>
            <div class="flex gap-3">
                @foreach(['A', 'B', 'C', 'D'] as $letter)
                    <label id="radio_correct_{{ $letter }}" class="flex items-center gap-1.5 cursor-pointer px-3 py-2 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        <input type="radio" name="correct_option" value="{{ $letter }}" {{ old('correct_option', 'A') === $letter ? 'checked' : '' }} required
                               class="accent-reiac-navy">
                        <span class="text-xs font-bold text-slate-700">{{ $letter }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- EXPLANATION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Explanation (shown after test review)</label>
            <textarea name="explanation" rows="3" placeholder="Explain the answer, grammar rule, translation..."
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y"></textarea>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        {{-- SUBMIT --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.questions.index') }}"
               class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                Save Question
            </button>
        </div>
    </form>
</div>

<script>
function handleTypeChange() {
    const type = document.getElementById('questionType').value;
    document.getElementById('imageUrlGroup').style.display = (type === 'image_based') ? 'block' : 'none';
    document.getElementById('audioUrlGroup').style.display = (type === 'audio_based') ? 'block' : 'none';
    // For True/False, hide C and D options and their correct answer radios
    const isTf = (type === 'true_false');
    document.getElementById('optionC').style.display = isTf ? 'none' : 'block';
    document.getElementById('optionD').style.display = isTf ? 'none' : 'block';
    const optCRadio = document.getElementById('radio_correct_C');
    const optDRadio = document.getElementById('radio_correct_D');
    if (optCRadio) optCRadio.style.display = isTf ? 'none' : 'inline-flex';
    if (optDRadio) optDRadio.style.display = isTf ? 'none' : 'inline-flex';
}
window.addEventListener('DOMContentLoaded', handleTypeChange);
</script>
@endsection
