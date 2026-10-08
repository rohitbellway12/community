<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use App\Models\TestLevel;
use App\Models\User;
use App\Notifications\NewTestNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTestController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $levelId = $request->input('level_id');
        $status = $request->input('status');

        $query = Test::with(['testLevel', 'questions'])
            ->orderByDesc('created_at');

        if ($levelId) {
            $query->where('test_level_id', $levelId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $tests = $query->paginate(15)->withQueryString();
        $levels = TestLevel::where('status', 'active')->orderBy('name')->get();

        return view('admin.tests.index', compact('tests', 'levels', 'levelId', 'status', 'search'));
    }

    public function create(): View
    {
        $levels = TestLevel::where('status', 'active')->orderBy('name')->get();
        $questions = Question::where('status', 'active')->orderBy('id')->get();
        $groups = \App\Models\Group::orderBy('name')->get(['id', 'name']);
        $students = User::where('role', '!=', \App\Enums\UserRole::ADMIN->value)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.tests.create', compact('levels', 'questions', 'groups', 'students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_level_id' => 'required|exists:test_levels,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'open_date' => 'nullable|date',
            'open_time' => 'nullable|date_format:H:i',
            'close_date' => 'nullable|date',
            'close_time' => 'nullable|date_format:H:i',
            'auto_open' => 'required|boolean',
            'max_attempts' => 'required|integer|min:0',
            'tab_switch_limit' => 'required|integer|min:0',
            'status' => 'required|in:draft,scheduled,published,archived',
            'target_type' => 'required|in:all,group,user',
            'target_group_id' => 'nullable|required_if:target_type,group|exists:groups,id',
            'target_user_id' => 'nullable|required_if:target_type,user|exists:users,id',
            'require_camera_photo' => 'nullable|boolean',
            'agency_name' => 'nullable|string|max:255',
            'controller_name' => 'nullable|string|max:255',
            'director_name' => 'nullable|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*' => [
                'required',
                Rule::exists('questions', 'id')->where('status', 'active'),
            ],
        ]);

        $test = Test::create([
            'test_level_id' => $validated['test_level_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'passing_marks' => $validated['passing_marks'],
            'open_date' => $validated['open_date'] ?? null,
            'open_time' => $validated['open_time'] ?? null,
            'close_date' => $validated['close_date'] ?? null,
            'close_time' => $validated['close_time'] ?? null,
            'auto_open' => $validated['auto_open'],
            'max_attempts' => $validated['max_attempts'],
            'tab_switch_limit' => $validated['tab_switch_limit'],
            'status' => $validated['status'],
            'target_type' => $validated['target_type'],
            'target_group_id' => $validated['target_type'] === 'group' ? $validated['target_group_id'] : null,
            'target_user_id' => $validated['target_type'] === 'user' ? $validated['target_user_id'] : null,
            'require_camera_photo' => $request->boolean('require_camera_photo'),
            'agency_name' => !empty($validated['agency_name']) ? $validated['agency_name'] : 'REIAC Test Assessment Center',
            'controller_name' => !empty($validated['controller_name']) ? $validated['controller_name'] : 'Kang Min-Seok',
            'director_name' => !empty($validated['director_name']) ? $validated['director_name'] : 'Dr. Rajesh Sharma',
        ]);

        if (!empty($validated['questions'])) {
            $orderedQuestions = collect($validated['questions'])->mapWithKeys(function ($questionId, $index) {
                return [$questionId => ['question_order' => $index + 1]];
            });
            $test->questions()->sync($orderedQuestions);
        }

        // Notify students about the new/updated test
        if ($test->status === 'published') {
            if ($test->target_type === 'user' && $test->target_user_id) {
                $students = User::where('id', $test->target_user_id)->get();
            } elseif ($test->target_type === 'group' && $test->target_group_id) {
                $students = $test->targetGroup ? $test->targetGroup->users()->wherePivot('status', 'active')->get() : collect();
            } else {
                $students = User::where('role', \App\Enums\UserRole::USER->value)
                    ->orWhere('role', '!=', \App\Enums\UserRole::ADMIN->value)
                    ->get();
            }
            if ($students->isNotEmpty()) {
                Notification::send($students, new NewTestNotification($test));
            }
        }

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test created successfully.');
    }

    public function edit(Test $test): View
    {
        $levels = TestLevel::where('status', 'active')->orderBy('name')->get();
        $attachedIds = $test->questions->pluck('id')->toArray();
        $questions = Question::where('status', 'active')
            ->orWhereIn('id', $attachedIds)
            ->orderBy('id')
            ->get();
        $groups = \App\Models\Group::orderBy('name')->get(['id', 'name']);
        $students = User::where('role', '!=', \App\Enums\UserRole::ADMIN->value)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.tests.edit', compact('test', 'levels', 'questions', 'attachedIds', 'groups', 'students'));
    }

    public function update(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'test_level_id' => 'required|exists:test_levels,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'instructions' => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'open_date' => 'nullable|date',
            'open_time' => 'nullable|date_format:H:i',
            'close_date' => 'nullable|date',
            'close_time' => 'nullable|date_format:H:i',
            'auto_open' => 'required|boolean',
            'max_attempts' => 'required|integer|min:0',
            'tab_switch_limit' => 'required|integer|min:0',
            'status' => 'required|in:draft,scheduled,published,archived',
            'target_type' => 'required|in:all,group,user',
            'target_group_id' => 'nullable|required_if:target_type,group|exists:groups,id',
            'target_user_id' => 'nullable|required_if:target_type,user|exists:users,id',
            'require_camera_photo' => 'nullable|boolean',
            'agency_name' => 'nullable|string|max:255',
            'controller_name' => 'nullable|string|max:255',
            'director_name' => 'nullable|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*' => 'exists:questions,id',
        ]);

        $test->update([
            'test_level_id' => $validated['test_level_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'passing_marks' => $validated['passing_marks'],
            'open_date' => $validated['open_date'] ?? null,
            'open_time' => $validated['open_time'] ?? null,
            'close_date' => $validated['close_date'] ?? null,
            'close_time' => $validated['close_time'] ?? null,
            'auto_open' => $validated['auto_open'],
            'max_attempts' => $validated['max_attempts'],
            'tab_switch_limit' => $validated['tab_switch_limit'],
            'status' => $validated['status'],
            'target_type' => $validated['target_type'],
            'target_group_id' => $validated['target_type'] === 'group' ? $validated['target_group_id'] : null,
            'target_user_id' => $validated['target_type'] === 'user' ? $validated['target_user_id'] : null,
            'require_camera_photo' => $request->boolean('require_camera_photo'),
            'agency_name' => !empty($validated['agency_name']) ? $validated['agency_name'] : 'REIAC Test Assessment Center',
            'controller_name' => !empty($validated['controller_name']) ? $validated['controller_name'] : 'Kang Min-Seok',
            'director_name' => !empty($validated['director_name']) ? $validated['director_name'] : 'Dr. Rajesh Sharma',
        ]);

        if (!empty($validated['questions'])) {
            $orderedQuestions = collect($validated['questions'])->mapWithKeys(function ($questionId, $index) {
                return [$questionId => ['question_order' => $index + 1]];
            });
            $test->questions()->sync($orderedQuestions);
        }

        // Notify students if test is published
        if ($test->status === 'published') {
            if ($test->target_type === 'user' && $test->target_user_id) {
                $students = User::where('id', $test->target_user_id)->get();
            } elseif ($test->target_type === 'group' && $test->target_group_id) {
                $students = $test->targetGroup ? $test->targetGroup->users()->wherePivot('status', 'active')->get() : collect();
            } else {
                $students = User::where('role', '!=', \App\Enums\UserRole::ADMIN->value)->get();
            }
            if ($students->isNotEmpty()) {
                Notification::send($students, new NewTestNotification($test));
            }
        }

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test): RedirectResponse
    {
        $test->delete();

        return redirect()->route('admin.tests.index')
            ->with('success', 'Test deleted successfully.');
    }
}
