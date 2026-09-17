@extends('layouts.admin')

@section('title', 'Edit Question')

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
            <h1 class="text-xl font-extrabold text-slate-900">Edit Question</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update question details</p>
        </div>
        <a href="{{ route('admin.questions.index') }}"
           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            ← Back
        </a>
    </div>

    <form action="{{ route('admin.questions.update', $question) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- LEVEL --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Test Level *</label>
            <select name="test_level_id" required class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="">Select Level</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ $question->test_level_id == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- QUESTION TEXT --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Question Text *</label>
            <textarea name="question_text" rows="3" required
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y">{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        {{-- QUESTION TYPE --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Question Type *</label>
            <select name="question_type" id="questionType" required
                    class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none"
                    onchange="handleTypeChange()">
                <option value="mcq" {{ $question->question_type === 'mcq' ? 'selected' : '' }}>MCQ (Multiple Choice)</option>
             {{--   <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                <option value="image_based" {{ $question->question_type === 'image_based' ? 'selected' : '' }}>Image Based</option>
                <option value="audio_based" {{ $question->question_type === 'audio_based' ? 'selected' : '' }}>Audio Based</option>  --}}
            </select>
        </div>

        {{-- IMAGE URL (conditional) --}}
        <div id="imageUrlGroup" style="display:{{ $question->question_type === 'image_based' ? 'block' : 'none' }}">
            <label class="block text-xs font-bold text-slate-700 mb-1">Image URL</label>
            <input type="url" name="image_url" value="{{ old('image_url', $question->image_url ?? '') }}"
                   placeholder="https://..."
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- AUDIO URL (conditional) --}}
        <div id="audioUrlGroup" style="display:{{ $question->question_type === 'audio_based' ? 'block' : 'none' }}">
            <label class="block text-xs font-bold text-slate-700 mb-1">Audio URL</label>
            <input type="url" name="audio_url" value="{{ old('audio_url', $question->audio_url ?? '') }}"
                   placeholder="https://..."
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- MARKS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Marks *</label>
            <input type="number" name="marks" value="{{ old('marks', $question->marks) }}" min="1" required
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- OPTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2">Options *</label>
            <div id="optionsContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(['A', 'B', 'C', 'D'] as $letter)
                    @php
                        $option = $options->firstWhere('option_label', $letter);
                        $isOptional = in_array($letter, ['C', 'D']);
                    @endphp
                    <div id="option{{ $letter }}" class="relative">
                        <input type="hidden" name="options[{{ $letter }}][label]" value="{{ $letter }}">
                        <span class="absolute left-3 top-2.5 text-xs font-bold text-reiac-gold bg-slate-100 px-1.5 py-0.5 rounded">{{ $letter }}</span>
                        <input type="text" name="options[{{ $letter }}][text]"
                               value="{{ old("options.{$letter}.text", $option?->option_text ?? '') }}"
                               {{ $isOptional ? '' : 'required' }}
                               placeholder="Option {{ $letter }} text{{ $isOptional ? ' (optional for True/False)' : '' }}"
                               class="w-full text-xs pl-10 pr-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    </div>
                @endforeach
            </div>
            <small class="text-[10px] text-slate-400 mt-1 block">True/False type: C and D are optional</small>
        </div>

        {{-- CORRECT OPTION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Correct Answer *</label>
            <div class="flex gap-3">
                @foreach(['A', 'B', 'C', 'D'] as $letter)
                    <label id="radio_correct_{{ $letter }}" class="flex items-center gap-1.5 cursor-pointer px-3 py-2 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        <input type="radio" name="correct_option" value="{{ $letter }}" {{ old('correct_option', $correctOption) === $letter ? 'checked' : '' }} required
                               class="accent-reiac-navy">
                        <span class="text-xs font-bold text-slate-700">{{ $letter }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- EXPLANATION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Explanation (shown after test review)</label>
            <textarea name="explanation" rows="3"
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y">{{ old('explanation', $question->explanation ?? '') }}</textarea>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="active" {{ $question->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $question->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                Update Question
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
    const optC = document.getElementById('optionC');
    const optD = document.getElementById('optionD');
    if (optC) optC.style.display = isTf ? 'none' : 'block';
    if (optD) optD.style.display = isTf ? 'none' : 'block';
    const optCRadio = document.getElementById('radio_correct_C');
    const optDRadio = document.getElementById('radio_correct_D');
    if (optCRadio) optCRadio.style.display = isTf ? 'none' : 'inline-flex';
    if (optDRadio) optDRadio.style.display = isTf ? 'none' : 'inline-flex';
}
window.addEventListener('DOMContentLoaded', handleTypeChange);
</script>
@endsection
