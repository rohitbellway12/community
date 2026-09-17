<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Models\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class StudentTestController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $now = now();

        // Query published tests whose close date has not passed (not expired) and whose test level is active
        $tests = Test::with(['testLevel', 'questions'])
            ->where('status', 'published')
            ->whereHas('testLevel', function ($q) {
                $q->where('status', 'active');
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('close_date')
                  ->orWhere(function ($sub) use ($now) {
                      $sub->whereDate('close_date', '>', $now->toDateString())
                          ->orWhere(function ($s) use ($now) {
                              $s->whereDate('close_date', '=', $now->toDateString())
                                ->where(function ($t) use ($now) {
                                    $t->whereNull('close_time')
                                      ->orWhere('close_time', '>=', $now->toTimeString());
                                });
                          });
                  });
            })
            ->orderBy('open_date', 'asc')
            ->orderByDesc('created_at')
            ->get();

        $userAttempts = collect();
        if ($user) {
            $userAttempts = TestAttempt::where('user_id', $user->id)
                ->whereIn('test_id', $tests->pluck('id'))
                ->latest('created_at')
                ->get()
                ->groupBy('test_id');

            // Exclude tests that the student has already completed (exhausted attempts)
            $tests = $tests->reject(function ($test) use ($userAttempts) {
                $attempts = $userAttempts->get($test->id, collect());
                $completedCount = $attempts->where('status', 'completed')->count();
                $maxAttempts = $test->max_attempts > 0 ? $test->max_attempts : 1;
                return $completedCount >= $maxAttempts;
            })->values();
        }

        foreach ($tests as $test) {
            $attempts = $userAttempts->get($test->id, collect());
            $test->user_attempts_count = $attempts->count();
            $test->latest_attempt = $attempts->first();
            $test->active_attempt = $attempts->firstWhere('status', 'active');
            $test->is_open = $test->isOpen();
            $test->can_take = $user ? $test->isAvailableFor($user) : false;
        }

        $completedCountTotal = $user
            ? TestAttempt::where('user_id', $user->id)->where('status', 'completed')->count()
            : 0;

        return view('student.tests.index', compact('tests', 'user', 'completedCountTotal'));
    }

    public function show(Test $test): View
    {
        $this->authorizeStudent($test, true);
        $test->load(['testLevel', 'questions.options']);

        $user = auth()->user();
        $activeAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'active')
            ->whereNull('submitted_at')
            ->first();

        // Check if active attempt is actually timed out
        if ($activeAttempt && $activeAttempt->isTimedOut()) {
            $activeAttempt->gradeAndComplete(null, 'timeout');
            $activeAttempt = null;
        }

        $completedAttemptsCount = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'completed')
            ->count();

        $latestAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'completed')
            ->latest('submitted_at')
            ->first();

        return view('student.tests.show', compact('test', 'activeAttempt', 'completedAttemptsCount', 'latestAttempt'));
    }

    public function start(Test $test): RedirectResponse
    {
        $this->authorizeStudent($test);

        $test->load(['questions']);

        if ($test->questions->isEmpty()) {
            return back()->with('error', 'This examination currently has no questions assigned.');
        }

        $userId = auth()->id();

        // Resume ongoing active attempt if available
        $activeAttempt = TestAttempt::where('user_id', $userId)
            ->where('test_id', $test->id)
            ->where('status', 'active')
            ->whereNull('submitted_at')
            ->first();

        if ($activeAttempt) {
            if ($activeAttempt->isTimedOut()) {
                $activeAttempt->gradeAndComplete(null, 'timeout');
                return redirect()->route('tests.student.result', ['test' => $test, 'attempt' => $activeAttempt])
                    ->with('info', 'Examination duration ended. Responses were auto-submitted.');
            }
            return redirect()->route('tests.student.take', ['test' => $test, 'attempt' => $activeAttempt])
                ->with('info', 'Resumed ongoing examination session.');
        }

        // Check max attempts
        if ($test->max_attempts > 0) {
            $completedCount = TestAttempt::where('user_id', $userId)
                ->where('test_id', $test->id)
                ->where('status', 'completed')
                ->count();

            if ($completedCount >= $test->max_attempts) {
                $latestAttempt = TestAttempt::where('user_id', $userId)
                    ->where('test_id', $test->id)
                    ->where('status', 'completed')
                    ->latest('submitted_at')
                    ->first();

                if ($latestAttempt) {
                    return redirect()->route('tests.student.result', ['test' => $test, 'attempt' => $latestAttempt])
                        ->with('info', 'You have already completed the maximum allowed attempt (' . $test->max_attempts . ') for this examination.');
                }

                return redirect()->route('tests.student.index')
                    ->with('error', 'You have already completed the maximum allowed attempts for this examination.');
            }
        }

        $userAttemptsCount = TestAttempt::where('test_id', $test->id)->where('user_id', $userId)->count();

        $attempt = TestAttempt::create([
            'user_id' => $userId,
            'test_id' => $test->id,
            'attempt_number' => $userAttemptsCount + 1,
            'started_at' => now(),
            'allowed_until' => now()->addMinutes($test->duration_minutes),
            'total_questions' => $test->questions->count(),
            'answered_count' => 0,
            'correct_count' => 0,
            'incorrect_count' => 0,
            'unanswered_count' => $test->questions->count(),
            'score_obtained' => 0,
            'percentage' => 0,
            'result' => 'pending',
            'submission_type' => 'manual',
            'tab_switch_count' => 0,
            'status' => 'active',
        ]);

        foreach ($test->questions as $question) {
            AttemptAnswer::create([
                'test_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => null,
                'is_marked_for_review' => false,
                'is_correct' => false,
                'marks_obtained' => 0,
                'answered_at' => null,
            ]);
        }

        return redirect()->route('tests.student.take', ['test' => $test, 'attempt' => $attempt])
            ->with('success', 'Examination session started. All best for your performance!');
    }

    public function take(Test $test, TestAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeStudent($test);

        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('tests.student.result', ['test' => $test, 'attempt' => $attempt])
                ->with('info', 'This examination attempt has already been submitted.');
        }

        $this->authorizeAttempt($attempt);

        if ($attempt->isTimedOut()) {
            $attempt->gradeAndComplete(null, 'timeout');
            return redirect()->route('tests.student.result', ['test' => $test, 'attempt' => $attempt])
                ->with('info', 'Allocated examination time has expired. Your responses have been automatically submitted.');
        }

        $test->load(['questions.options', 'testLevel']);

        // Ensure all questions have attempt answer rows
        foreach ($test->questions as $q) {
            AttemptAnswer::firstOrCreate([
                'test_attempt_id' => $attempt->id,
                'question_id' => $q->id,
            ], [
                'selected_option_id' => null,
                'is_marked_for_review' => false,
                'is_correct' => false,
                'marks_obtained' => 0,
                'answered_at' => null,
            ]);
        }

        $attempt->load(['answers']);

        $questionsData = $test->questions->map(function ($q) {
            return [
                'id' => (int) $q->id,
                'question_text' => (string) $q->question_text,
                'question_type' => (string) $q->question_type,
                'marks' => (int) $q->marks,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => (int) $opt->id,
                        'option_label' => (string) $opt->option_label,
                        'option_text' => (string) $opt->option_text,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $answersData = $attempt->answers->map(function ($a) {
            return [
                'id' => (int) $a->id,
                'question_id' => (int) $a->question_id,
                'selected_option_id' => $a->selected_option_id ? (int) $a->selected_option_id : null,
                'is_marked_for_review' => (bool) $a->is_marked_for_review,
            ];
        })->values()->toArray();

        $remainingSeconds = $attempt->allowed_until
            ? max(0, $attempt->allowed_until->timestamp - now()->timestamp)
            : ($test->duration_minutes * 60);

        return view('student.tests.take', compact('test', 'attempt', 'remainingSeconds', 'questionsData', 'answersData'));
    }

    public function submit(Request $request, Test $test, TestAttempt $attempt): RedirectResponse
    {
        $this->authorizeStudent($test);
        $this->authorizeAttempt($attempt);

        $tabSwitchCount = (int) $request->input('tab_switch_count', 0);

        $isTimeout = $attempt->isTimedOut() || $request->input('is_timeout') == '1';
        $submissionType = 'manual';

        if ($isTimeout) {
            $submissionType = 'timeout';
        } elseif ($tabSwitchCount >= 1 || ($test->tab_switch_limit > 0 && $tabSwitchCount >= $test->tab_switch_limit)) {
            $submissionType = 'tab_switch_violation';
        }

        $answers = null;
        if ($request->filled('answers')) {
            $rawAnswers = $request->input('answers');
            if (is_string($rawAnswers)) {
                $decoded = json_decode($rawAnswers, true);
                if (is_array($decoded)) {
                    $answers = $decoded;
                }
            } elseif (is_array($rawAnswers)) {
                $answers = $rawAnswers;
            }
        }

        $attempt->gradeAndComplete($answers, $submissionType, $tabSwitchCount);

        $message = match ($submissionType) {
            'timeout' => 'Examination time expired. Your paper has been auto-submitted.',
            'tab_switch_violation' => 'Screen change detected. Your examination was auto-submitted.',
            default => 'Examination submitted successfully.'
        };

        return redirect()->route('tests.student.result', ['test' => $test, 'attempt' => $attempt])
            ->with('success', $message);
    }

    public function result(Test $test, TestAttempt $attempt): View
    {
        $this->authorizeStudent($test, true);
        $this->authorizeAttempt($attempt, false);

        $test->load(['questions.options', 'testLevel']);
        $attempt->load(['answers.question.options', 'answers.option', 'user']);

        // Maintain consistent question ordering according to test definition
        $orderMap = $test->questions->pluck('pivot.question_order', 'id')->all();
        $attempt->setRelation(
            'answers',
            $attempt->answers->sortBy(function ($ans) use ($orderMap) {
                return $orderMap[$ans->question_id] ?? $ans->id;
            })->values()
        );

        return view('student.tests.result', compact('test', 'attempt'));
    }

    public function saveAnswer(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeStudent($test);
        $this->authorizeAttempt($attempt);

        $questionId = (int) $request->input('question_id');
        $selectedOptionId = $request->input('selected_option_id') ?? $request->input('option_id');

        if (!$selectedOptionId) {
            return response()->json(['success' => false, 'message' => 'No option selected'], 422);
        }

        $selectedOptionId = (int) $selectedOptionId;

        // Verify that the option belongs to the question
        $optionExists = \App\Models\QuestionOption::where('question_id', $questionId)
            ->where('id', $selectedOptionId)
            ->exists();

        if (!$optionExists) {
            return response()->json(['success' => false, 'message' => 'Invalid option selected for this question'], 422);
        }

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->first();

        if (!$answer) {
            $answer = AttemptAnswer::create([
                'test_attempt_id' => $attempt->id,
                'question_id' => $questionId,
                'selected_option_id' => $selectedOptionId,
                'is_marked_for_review' => false,
                'is_correct' => false,
                'marks_obtained' => 0,
                'answered_at' => now(),
            ]);
        } else {
            $answer->update([
                'selected_option_id' => $selectedOptionId,
                'answered_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function clearAnswer(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeStudent($test);
        $this->authorizeAttempt($attempt);

        $questionId = (int) $request->input('question_id');

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->first();

        if ($answer) {
            $answer->update([
                'selected_option_id' => null,
                'answered_at' => null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function toggleMarkForReview(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeStudent($test);
        $this->authorizeAttempt($attempt);

        $questionId = $request->input('question_id');

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $questionId)
            ->firstOrFail();

        $answer->update([
            'is_marked_for_review' => !$answer->is_marked_for_review,
        ]);

        return response()->json([
            'success' => true,
            'is_marked_for_review' => $answer->is_marked_for_review,
        ]);
    }

    private function authorizeStudent(Test $test, bool $allowClosedView = false): void
    {
        if (!auth()->check()) {
            abort(403, 'Please login to take a test.');
        }

        if ($test->status !== 'published') {
            abort(404, 'Test not found.');
        }

        if ($test->testLevel && $test->testLevel->status !== 'active') {
            abort(403, 'This test level is currently inactive.');
        }

        if (!$allowClosedView && !$test->isOpen()) {
            abort(403, 'This test is not currently available.');
        }
    }

    private function authorizeAttempt(TestAttempt $attempt, bool $requireActive = true): void
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($requireActive && $attempt->status !== 'active') {
            abort(403, 'This attempt is no longer active.');
        }

        $test = $attempt->test;
        if ($test->close_date) {
            $closesAt = $test->close_time
                ? $test->close_date->copy()->setTimeFrom($test->close_time)
                : $test->close_date->endOfDay();

            if (now()->gt($closesAt)) {
                abort(403, 'This test has expired.');
            }
        }
    }
}