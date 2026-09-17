@extends('layouts.admin')

@section('title', 'Create Test')

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
            <h1 class="text-xl font-extrabold text-slate-900">Create New Test</h1>
            <p class="text-xs text-slate-500 mt-0.5">Configure test settings and add questions</p>
        </div>
        <a href="{{ route('admin.tests.index') }}"
           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            ← Back
        </a>
    </div>

    <form action="{{ route('admin.tests.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
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

        {{-- TITLE --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Test Title *</label>
            <input type="text" name="title" required placeholder="e.g. Korean Beginner Test 1"
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- DESCRIPTION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="2" placeholder="Brief description of this test"
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y"></textarea>
        </div>

        {{-- INSTRUCTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Instructions</label>
            <textarea name="instructions" rows="2" placeholder="Test instructions for students..."
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y"></textarea>
        </div>

        {{-- DURATION & MARKS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Duration (min) *</label>
                <input type="number" name="duration_minutes" value="30" min="1" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Total Marks *</label>
                <input type="number" name="total_marks" value="30" min="1" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Passing Marks *</label>
                <input type="number" name="passing_marks" value="18" min="0" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- SCHEDULE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Open Date</label>
                <input type="date" name="open_date"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Open Time</label>
                <input type="time" name="open_time"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Close Date</label>
                <input type="date" name="close_date"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Close Time</label>
                <input type="time" name="close_time"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- SETTINGS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Auto Open</label>
                <select name="auto_open" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    <option value="1" selected>Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Max Attempts</label>
                <input type="number" name="max_attempts" value="1" min="0"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tab Switch Limit</label>
                <input type="number" name="tab_switch_limit" value="0" min="0"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        {{-- QUESTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Questions * (select at least 1)</label>
            <div class="flex flex-col sm:flex-row gap-3 mb-3">
                <select id="levelFilter" class="text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    <option value="all">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
                <input type="text" id="questionSearch" placeholder="Search questions..."
                       class="text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none flex-1">
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 max-h-64 overflow-y-auto" id="questionsContainer">
                @foreach($questions as $question)
                    <label class="flex items-center gap-2 py-1.5 cursor-pointer question-item"
                           data-level="{{ $question->test_level_id }}"
                           data-marks="{{ $question->marks }}">
                        <input type="checkbox" name="questions[]" value="{{ $question->id }}"
                               class="accent-reiac-navy question-checkbox">
                        <span class="text-xs text-slate-700 font-medium">{{ Str::limit($question->question_text, 80) }}</span>
                        <span class="text-[10px] text-slate-400 ml-auto">{{ Str::ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                        <span class="text-[10px] text-slate-400">marks: {{ $question->marks }}</span>
                    </label>
                @endforeach
            </div>
            <div class="flex justify-between items-center mt-2">
                <p class="text-[10px] text-slate-400">Questions will appear in selection order.</p>
                <p class="text-xs font-bold text-slate-700">
                    Total Marks: <span id="autoTotalMarks" class="text-reiac-navy">0</span>
                </p>
            </div>
        </div>

        <script>
document.addEventListener('DOMContentLoaded', function () {
    const levelFilter = document.getElementById('levelFilter');
    const questionSearch = document.getElementById('questionSearch');
    const questionsContainer = document.getElementById('questionsContainer');
    const questionItems = questionsContainer ? questionsContainer.querySelectorAll('.question-item') : [];
    const checkboxes = questionsContainer ? questionsContainer.querySelectorAll('.question-checkbox') : [];
    const totalMarksInput = document.querySelector('input[name="total_marks"]');
    const autoTotalMarks = document.getElementById('autoTotalMarks');

    function updateTotalMarks() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                const item = cb.closest('.question-item');
                total += parseInt(item.dataset.marks) || 0;
            }
        });
        autoTotalMarks.textContent = total;
        if (totalMarksInput) {
            totalMarksInput.value = total;
        }
    }

    function filterQuestions() {
        const level = levelFilter ? levelFilter.value : 'all';
        const search = questionSearch ? questionSearch.value.toLowerCase() : '';
        questionItems.forEach(item => {
            const itemLevel = item.dataset.level;
            const text = item.textContent.toLowerCase();
            const showByLevel = (level === 'all' || itemLevel === level);
            const showBySearch = search === '' || text.includes(search);
            item.style.display = (showByLevel && showBySearch) ? 'flex' : 'none';
        });
    }

    if (levelFilter) levelFilter.addEventListener('change', filterQuestions);
    if (questionSearch) questionSearch.addEventListener('input', filterQuestions);
    checkboxes.forEach(cb => cb.addEventListener('change', updateTotalMarks));

    updateTotalMarks();
});
</script>

        {{-- SUBMIT --}}
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.tests.index') }}"
               class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2.5 text-xs font-bold text-white bg-reiac-navy hover:bg-slate-800 rounded-xl transition shadow-xs">
                Create Test
            </button>
        </div>
    </form>
</div>
@endsection
