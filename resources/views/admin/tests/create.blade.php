@extends('layouts.admin')

@section('title', 'Create Test')

@section('content')
@php
    $groupsList = $groups->map(fn($g) => [
        'id' => (int) $g->id,
        'name' => (string) $g->name,
        'users_count' => (int) ($g->users_count ?? 0),
    ])->values();
    $oldTargetGroupIds = array_values(array_unique(array_filter(array_map('intval', (array) old('target_group_ids', old('target_group_id') ? [old('target_group_id')] : [])))));

    $studentsList = $students->map(fn($s) => [
        'id' => (int) $s->id,
        'name' => (string) $s->name,
        'email' => (string) $s->email,
    ])->values();
    $oldTargetUserIds = array_values(array_unique(array_filter(array_map('intval', (array) old('target_user_ids', old('target_user_id') ? [old('target_user_id')] : [])))));
@endphp
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
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700">Open Date *</label>
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        KST (Korea Standard Time)
                    </span>
                </div>
                <input type="date" name="open_date"
                       value="{{ old('open_date', now('Asia/Seoul')->format('Y-m-d')) }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none bg-white">
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-bold text-slate-700">Open Time *</label>
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        KST (Korea Standard Time)
                    </span>
                </div>
                <input type="time" name="open_time"
                       value="{{ old('open_time', now('Asia/Seoul')->format('H:i')) }}"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-reiac-gold focus:outline-none bg-white">
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

        {{-- TARGET AUDIENCE & ACCESS CONTROL --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-4"
             x-data="testTargetAudienceHandler({
                 initialType: '{{ old('target_type', 'all') }}',
                 groups: {{ Js::from($groupsList) }},
                 initialSelectedGroups: {{ Js::from($oldTargetGroupIds) }},
                 students: {{ Js::from($studentsList) }},
                 initialSelectedUsers: {{ Js::from($oldTargetUserIds) }}
             })">
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
                            <div class="text-xs font-bold text-slate-800">Specific Groups</div>
                            <div class="text-[10px] text-slate-500">Select one or multiple community groups</div>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-3 bg-white border rounded-xl cursor-pointer transition"
                           :class="targetType === 'user' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200'">
                        <input type="radio" name="target_type" value="user" x-model="targetType" class="accent-amber-500">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Specific Students</div>
                            <div class="text-[10px] text-slate-500">Select one or multiple candidates</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- SPECIFIC GROUPS SELECTOR (MULTI-SELECT) --}}
            <div x-show="targetType === 'group'" x-cloak class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Select Community Groups *</label>
                        <p class="text-[11px] text-slate-500">Search and select one or multiple groups whose members can take this test.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold px-2.5 py-1 bg-blue-100 text-blue-800 rounded-lg border border-blue-200">
                            <span x-text="selectedGroupIds.length"></span> Groups Selected
                        </span>
                    </div>
                </div>

                {{-- Search & Bulk Actions --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text"
                               x-model="groupSearchQuery"
                               placeholder="Search groups by name..."
                               class="w-full text-xs pl-9 pr-7 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white">
                        <button type="button"
                                x-show="groupSearchQuery.length > 0"
                                @click="groupSearchQuery = ''"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">✕</button>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button"
                                @click="selectAllFilteredGroups()"
                                class="px-2.5 py-2 text-[11px] font-semibold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition">
                            Select Filtered (<span x-text="filteredGroups.length"></span>)
                        </button>
                        <button type="button"
                                @click="clearAllGroups()"
                                x-show="selectedGroupIds.length > 0"
                                class="px-2.5 py-2 text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                            Clear All
                        </button>
                    </div>
                </div>

                {{-- Selected Badges (Chips) Preview --}}
                <div x-show="selectedGroupIds.length > 0" class="flex flex-wrap gap-1.5 p-2.5 bg-white border border-slate-200 rounded-xl max-h-28 overflow-y-auto">
                    <template x-for="grp in selectedGroupsList" :key="grp.id">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[11px] font-medium bg-blue-50 text-blue-900 border border-blue-200 rounded-lg">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span x-text="grp.name" class="font-bold"></span>
                            <span x-text="'(' + grp.users_count + ' members)'" class="text-[10px] text-blue-700/80"></span>
                            <button type="button" @click="toggleGroup(grp.id)" class="text-blue-600 hover:text-red-500 font-bold ml-0.5">✕</button>
                        </span>
                    </template>
                </div>

                {{-- Scrollable Checkbox List --}}
                <div class="bg-white border border-slate-200 rounded-xl max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="grp in filteredGroups" :key="grp.id">
                        <label class="flex items-center gap-2.5 px-3 py-2 hover:bg-blue-50/50 cursor-pointer transition"
                               :class="selectedGroupIds.includes(grp.id) ? 'bg-blue-50/30' : ''">
                            <input type="checkbox"
                                   :value="grp.id"
                                   :checked="selectedGroupIds.includes(grp.id)"
                                   @change="toggleGroup(grp.id)"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-400 accent-blue-600">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase"
                                 x-text="grp.name ? grp.name.charAt(0) : 'G'">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-slate-800 truncate" x-text="grp.name"></div>
                                <div class="text-[11px] text-slate-400 truncate" x-text="grp.users_count + ' active members'"></div>
                            </div>
                            <span x-show="selectedGroupIds.includes(grp.id)" class="text-[10px] font-bold text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded">
                                Selected
                            </span>
                        </label>
                    </template>
                    <div x-show="filteredGroups.length === 0" class="py-6 text-center text-xs text-slate-400">
                        No groups found matching your search.
                    </div>
                </div>

                {{-- Hidden Form Inputs for Submission --}}
                <template x-for="id in selectedGroupIds" :key="'target-grp-' + id">
                    <input type="hidden" name="target_group_ids[]" :value="id">
                </template>
                <input type="hidden" name="target_group_id" :value="selectedGroupIds[0] || ''">
            </div>

            {{-- SPECIFIC STUDENT SELECTOR (MULTI-SELECT) --}}
            <div x-show="targetType === 'user'" x-cloak class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">Select Candidates / Students *</label>
                        <p class="text-[11px] text-slate-500">Search and select one or multiple learners who can take this test.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold px-2.5 py-1 bg-amber-100 text-amber-800 rounded-lg border border-amber-200">
                            <span x-text="selectedUserIds.length"></span> Students Selected
                        </span>
                    </div>
                </div>

                {{-- Search & Bulk Actions --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Search students by name or email..."
                               class="w-full text-xs pl-9 pr-7 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                        <button type="button"
                                x-show="searchQuery.length > 0"
                                @click="searchQuery = ''"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">✕</button>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button"
                                @click="selectAllFiltered()"
                                class="px-2.5 py-2 text-[11px] font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition">
                            Select Filtered (<span x-text="filteredStudents.length"></span>)
                        </button>
                        <button type="button"
                                @click="clearAll()"
                                x-show="selectedUserIds.length > 0"
                                class="px-2.5 py-2 text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition">
                            Clear All
                        </button>
                    </div>
                </div>

                {{-- Selected Badges (Chips) Preview --}}
                <div x-show="selectedUserIds.length > 0" class="flex flex-wrap gap-1.5 p-2.5 bg-white border border-slate-200 rounded-xl max-h-28 overflow-y-auto">
                    <template x-for="st in selectedStudentsList" :key="st.id">
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200 rounded-lg">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span x-text="st.name" class="font-bold"></span>
                            <span x-text="'(' + st.email + ')'" class="text-[10px] text-amber-700/80 max-w-[150px] truncate"></span>
                            <button type="button" @click="toggleUser(st.id)" class="text-amber-600 hover:text-red-500 font-bold ml-0.5">✕</button>
                        </span>
                    </template>
                </div>

                {{-- Scrollable Checkbox List --}}
                <div class="bg-white border border-slate-200 rounded-xl max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="st in filteredStudents" :key="st.id">
                        <label class="flex items-center gap-2.5 px-3 py-2 hover:bg-amber-50/50 cursor-pointer transition"
                               :class="selectedUserIds.includes(st.id) ? 'bg-amber-50/30' : ''">
                            <input type="checkbox"
                                   :value="st.id"
                                   :checked="selectedUserIds.includes(st.id)"
                                   @change="toggleUser(st.id)"
                                   class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400 accent-amber-500">
                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 uppercase"
                                 x-text="st.name ? st.name.charAt(0) : 'U'">
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-slate-800 truncate" x-text="st.name"></div>
                                <div class="text-[11px] text-slate-400 truncate" x-text="st.email"></div>
                            </div>
                            <span x-show="selectedUserIds.includes(st.id)" class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">
                                Selected
                            </span>
                        </label>
                    </template>
                    <div x-show="filteredStudents.length === 0" class="py-6 text-center text-xs text-slate-400">
                        No students found matching your search.
                    </div>
                </div>

                {{-- Hidden Form Inputs for Submission --}}
                <template x-for="id in selectedUserIds" :key="'target-user-' + id">
                    <input type="hidden" name="target_user_ids[]" :value="id">
                </template>
                <input type="hidden" name="target_user_id" :value="selectedUserIds[0] || ''">
            </div>
        </div>

        {{-- PROCTORING / PHOTO CAPTURE REQUIREMENT --}}
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="require_camera_photo" value="1" {{ old('require_camera_photo') ? 'checked' : '' }}
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
                <input type="text" name="agency_name" value="{{ old('agency_name', 'REIAC Test Assessment Center') }}"
                       placeholder="e.g. Global Educational Consultancy"
                       class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                <p class="text-[10px] text-slate-400 mt-1">This name will appear on the top header of the student's Certificate of Achievement.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Controller of Examinations (Name)</label>
                    <input type="text" name="controller_name" value="{{ old('controller_name', 'Kang Min-Seok') }}"
                           placeholder="e.g. Kang Min-Seok"
                           class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Academic Director (Name)</label>
                    <input type="text" name="director_name" value="{{ old('director_name', 'Dr. Rajesh Sharma') }}"
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
function testTargetAudienceHandler(config) {
    return {
        targetType: config.initialType || 'all',

        // Groups Multi-Select
        allGroups: Array.isArray(config.groups) ? config.groups : [],
        selectedGroupIds: Array.isArray(config.initialSelectedGroups) ? config.initialSelectedGroups.map(Number) : [],
        groupSearchQuery: '',

        get filteredGroups() {
            if (!this.groupSearchQuery.trim()) {
                return this.allGroups;
            }
            const q = this.groupSearchQuery.toLowerCase();
            return this.allGroups.filter(g => (g.name || '').toLowerCase().includes(q));
        },

        get selectedGroupsList() {
            const set = new Set(this.selectedGroupIds);
            return this.allGroups.filter(g => set.has(g.id));
        },

        toggleGroup(id) {
            id = Number(id);
            const idx = this.selectedGroupIds.indexOf(id);
            if (idx > -1) {
                this.selectedGroupIds.splice(idx, 1);
            } else {
                this.selectedGroupIds.push(id);
            }
        },

        selectAllFilteredGroups() {
            const filteredIds = this.filteredGroups.map(g => Number(g.id));
            const currentSet = new Set(this.selectedGroupIds);
            filteredIds.forEach(id => currentSet.add(id));
            this.selectedGroupIds = Array.from(currentSet);
        },

        clearAllGroups() {
            this.selectedGroupIds = [];
        },

        // Students Multi-Select
        allStudents: Array.isArray(config.students) ? config.students : [],
        selectedUserIds: Array.isArray(config.initialSelectedUsers) ? config.initialSelectedUsers.map(Number) : [],
        searchQuery: '',

        get filteredStudents() {
            if (!this.searchQuery.trim()) {
                return this.allStudents;
            }
            const q = this.searchQuery.toLowerCase();
            return this.allStudents.filter(s => {
                const name = (s.name || '').toLowerCase();
                const email = (s.email || '').toLowerCase();
                return name.includes(q) || email.includes(q);
            });
        },

        get selectedStudentsList() {
            const set = new Set(this.selectedUserIds);
            return this.allStudents.filter(s => set.has(s.id));
        },

        toggleUser(id) {
            id = Number(id);
            const idx = this.selectedUserIds.indexOf(id);
            if (idx > -1) {
                this.selectedUserIds.splice(idx, 1);
            } else {
                this.selectedUserIds.push(id);
            }
        },

        selectAllFiltered() {
            const filteredIds = this.filteredStudents.map(s => Number(s.id));
            const currentSet = new Set(this.selectedUserIds);
            filteredIds.forEach(id => currentSet.add(id));
            this.selectedUserIds = Array.from(currentSet);
        },

        clearAll() {
            this.selectedUserIds = [];
        }
    };
}

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
