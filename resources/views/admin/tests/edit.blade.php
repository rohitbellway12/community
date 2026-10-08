@extends('layouts.admin')

@section('title', 'Edit Test')

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
            <h1 class="text-xl font-extrabold text-slate-900">Edit Test</h1>
            <p class="text-xs text-slate-500 mt-0.5">Update test settings</p>
        </div>
        <a href="{{ route('admin.tests.index') }}"
           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            ← Back
        </a>
    </div>

    <form action="{{ route('admin.tests.update', $test) }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- LEVEL --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Test Level *</label>
            <select name="test_level_id" required class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="">Select Level</option>
                @foreach($levels as $level)
                    <option value="{{ $level->id }}" {{ $test->test_level_id == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- TITLE --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Test Title *</label>
            <input type="text" name="title" value="{{ old('title', $test->title) }}" required
                   class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
        </div>

        {{-- DESCRIPTION --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y">{{ old('description', $test->description ?? '') }}</textarea>
        </div>

        {{-- INSTRUCTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Instructions</label>
            <textarea name="instructions" rows="2"
                      class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none resize-y">{{ old('instructions', $test->instructions ?? '') }}</textarea>
        </div>

        {{-- DURATION & MARKS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Duration (min) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $test->duration_minutes) }}" min="1" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Total Marks *</label>
                <input type="number" name="total_marks" value="{{ old('total_marks', $test->total_marks) }}" min="1" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Passing Marks *</label>
                <input type="number" name="passing_marks" value="{{ old('passing_marks', $test->passing_marks) }}" min="0" required
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- SCHEDULE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Open Date</label>
                <input type="date" name="open_date" value="{{ old('open_date', $test->open_date?->format('Y-m-d') ?? '') }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Open Time</label>
                <input type="time" name="open_time" value="{{ old('open_time', $test->open_time?->format('H:i') ?? '') }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Close Date</label>
                <input type="date" name="close_date" value="{{ old('close_date', $test->close_date?->format('Y-m-d') ?? '') }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Close Time</label>
                <input type="time" name="close_time" value="{{ old('close_time', $test->close_time?->format('H:i') ?? '') }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- SETTINGS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Auto Open</label>
                <select name="auto_open" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    <option value="1" {{ $test->auto_open ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$test->auto_open ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Max Attempts</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $test->max_attempts) }}" min="0"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tab Switch Limit</label>
                <input type="number" name="tab_switch_limit" value="{{ old('tab_switch_limit', $test->tab_switch_limit) }}" min="0"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
            </div>
        </div>

        {{-- STATUS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
            <select name="status" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                <option value="draft" {{ $test->status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ $test->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="published" {{ $test->status === 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ $test->status === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        {{-- TARGET AUDIENCE & ACCESS CONTROL --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-4" x-data="{ targetType: '{{ old('target_type', $test->target_type ?? 'all') }}' }">
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1">Target Audience (Who Can Take This Test?) *</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition"
                           :class="targetType === 'all' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200'">
                        <input type="radio" name="target_type" value="all" x-model="targetType" class="accent-amber-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">All Students</div>
                            <div class="text-[10px] text-slate-500">Open to all registered learners</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition"
                           :class="targetType === 'group' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200'">
                        <input type="radio" name="target_type" value="group" x-model="targetType" class="accent-amber-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Specific Group</div>
                            <div class="text-[10px] text-slate-500">Only members of a group</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition"
                           :class="targetType === 'user' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200'">
                        <input type="radio" name="target_type" value="user" x-model="targetType" class="accent-amber-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Specific Student</div>
                            <div class="text-[10px] text-slate-500">Only assigned candidate</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- SPECIFIC GROUP SELECTOR --}}
            <div x-show="targetType === 'group'" x-cloak>
                <label class="block text-xs font-bold text-slate-700 mb-1">Select Community Group *</label>
                <select name="target_group_id" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                    <option value="">Select a Group</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ old('target_group_id', $test->target_group_id) == $grp->id ? 'selected' : '' }}>
                            {{ $grp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SPECIFIC STUDENT SELECTOR --}}
            <div x-show="targetType === 'user'" x-cloak>
                <label class="block text-xs font-bold text-slate-700 mb-1">Select Student / Candidate *</label>
                <select name="target_user_id" class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                    <option value="">Select a Student</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" {{ old('target_user_id', $test->target_user_id) == $st->id ? 'selected' : '' }}>
                            {{ $st->name }} ({{ $st->email }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- PROCTORING / PHOTO CAPTURE REQUIREMENT --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="require_camera_photo" value="1" {{ old('require_camera_photo', $test->require_camera_photo) ? 'checked' : '' }}
                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-amber-500 focus:ring-amber-400 accent-amber-500">
                <div>
                    <div class="text-xs font-bold text-slate-900">Require Student Photo Before Test (Identity Verification)</div>
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        If checked, students must take a live camera snapshot before starting the exam. This photo will be printed in the top-left badge of their Certificate of Achievement.
                    </div>
                </div>
            </label>
        </div>

        {{-- CERTIFICATE BRANDING & SIGNATORIES --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Certificate Branding & Signatures</h3>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Consultancy / Agency Name (Header)</label>
                <input type="text" name="agency_name" value="{{ old('agency_name', $test->agency_name ?? 'REIAC Test Assessment Center') }}"
                       placeholder="e.g. Global Educational Consultancy"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                <p class="text-[10px] text-slate-400 mt-1">This name will appear on the top header of the student's Certificate of Achievement.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Controller of Examinations (Name)</label>
                    <input type="text" name="controller_name" value="{{ old('controller_name', $test->controller_name ?? 'Kang Min-Seok') }}"
                           placeholder="e.g. Kang Min-Seok"
                           class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Academic Director (Name)</label>
                    <input type="text" name="director_name" value="{{ old('director_name', $test->director_name ?? 'Dr. Rajesh Sharma') }}"
                           placeholder="e.g. Dr. Rajesh Sharma"
                           class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                </div>
            </div>
        </div>

        {{-- QUESTIONS --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Questions * (select at least 1)</label>
            <div class="flex flex-col sm:flex-row gap-3 mb-3">
                <select id="levelFilter" class="text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none">
                    <option value="all">All Levels</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ $test->test_level_id == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
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
                               {{ in_array($question->id, $attachedIds) ? 'checked' : '' }}
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
                    Total Marks: <span id="autoTotalMarks" class="text-reiac-navy">{{ $test->total_marks }}</span>
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
        if (autoTotalMarks) autoTotalMarks.textContent = total;
        if (totalMarksInput && total > 0) {
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
    filterQuestions();
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
                Update Test
            </button>
        </div>
    </form>
</div>
@endsection
