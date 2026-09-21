<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AttemptAnswer;
use App\Models\QuestionOption;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentTestController extends Controller
{
    use ApiResponse;

    /**
     * List all published tests available for students.
     *
     * GET /api/v1/tests
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('sanctum');
        $now = now();

        $query = Test::with(['testLevel'])
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
            ->withCount('questions');

        if ($request->filled('test_level_id')) {
            $query->where('test_level_id', $request->integer('test_level_id'));
        }

        $tests = $query->orderBy('open_date', 'asc')
            ->orderByDesc('created_at')
            ->get();

        $userAttempts = collect();
        if ($user) {
            $userAttempts = TestAttempt::where('user_id', $user->id)
                ->whereIn('test_id', $tests->pluck('id'))
                ->latest('created_at')
                ->get()
                ->groupBy('test_id');
        }

        $data = $tests->map(function ($test) use ($user, $userAttempts) {
            $attempts = $user ? $userAttempts->get($test->id, collect()) : collect();
            $completedCount = $attempts->where('status', 'completed')->count();
            $maxAttempts = $test->max_attempts > 0 ? $test->max_attempts : 1;
            $activeAttempt = $attempts->firstWhere('status', 'active');

            return [
                'id'                     => $test->id,
                'title'                  => $test->title,
                'description'            => $test->description,
                'level'                  => $test->testLevel ? [
                    'id'   => $test->testLevel->id,
                    'name' => $test->testLevel->name,
                    'slug' => $test->testLevel->slug,
                ] : null,
                'duration_minutes'       => (int) $test->duration_minutes,
                'total_marks'            => (int) $test->total_marks,
                'passing_marks'          => (int) $test->passing_marks,
                'total_questions'        => (int) $test->questions_count,
                'max_attempts'           => (int) $test->max_attempts,
                'is_open'                => $test->isOpen(),
                'open_date'              => $test->open_date?->toDateString(),
                'close_date'             => $test->close_date?->toDateString(),
                'user_stats'             => $user ? [
                    'completed_attempts' => $completedCount,
                    'has_active_attempt' => !is_null($activeAttempt),
                    'active_attempt_id'  => $activeAttempt?->id,
                    'can_take'           => $test->isOpen() && ($completedCount < $maxAttempts),
                ] : null,
            ];
        });

        return $this->successResponse($data, 'Tests retrieved successfully.');
    }

    /**
     * Show details and instructions for a specific test.
     *
     * GET /api/v1/tests/{test}
     */
    public function show(Request $request, Test $test): JsonResponse
    {
        $this->validateTestAvailability($test, true);

        $test->load(['testLevel']);
        $test->loadCount('questions');

        $user = $request->user('sanctum');
        $userStats = null;

        if ($user) {
            $activeAttempt = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->where('status', 'active')
                ->whereNull('submitted_at')
                ->first();

            if ($activeAttempt && $activeAttempt->isTimedOut()) {
                $activeAttempt->gradeAndComplete(null, 'timeout');
                $activeAttempt = null;
            }

            $completedCount = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->where('status', 'completed')
                ->count();

            $maxAttempts = $test->max_attempts > 0 ? $test->max_attempts : 1;

            $latestAttempt = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->where('status', 'completed')
                ->latest('submitted_at')
                ->first();

            $userStats = [
                'completed_attempts' => $completedCount,
                'can_take'           => $test->isOpen() && ($completedCount < $maxAttempts),
                'active_attempt'     => $activeAttempt ? [
                    'id'            => $activeAttempt->id,
                    'started_at'    => $activeAttempt->started_at?->toIso8601String(),
                    'allowed_until' => $activeAttempt->allowed_until?->toIso8601String(),
                ] : null,
                'latest_result'      => $latestAttempt ? [
                    'attempt_id'     => $latestAttempt->id,
                    'score_obtained' => (float) $latestAttempt->score_obtained,
                    'percentage'     => (float) $latestAttempt->percentage,
                    'result'         => $latestAttempt->result,
                    'submitted_at'   => $latestAttempt->submitted_at?->toIso8601String(),
                ] : null,
            ];
        }

        $data = [
            'id'               => $test->id,
            'title'            => $test->title,
            'description'      => $test->description,
            'instructions'     => $test->instructions,
            'level'            => $test->testLevel ? [
                'id'   => $test->testLevel->id,
                'name' => $test->testLevel->name,
                'slug' => $test->testLevel->slug,
            ] : null,
            'duration_minutes' => (int) $test->duration_minutes,
            'total_marks'      => (int) $test->total_marks,
            'passing_marks'    => (int) $test->passing_marks,
            'total_questions'  => (int) $test->questions_count,
            'max_attempts'     => (int) $test->max_attempts,
            'tab_switch_limit' => (int) $test->tab_switch_limit,
            'is_open'          => $test->isOpen(),
            'open_date'        => $test->open_date?->toDateString(),
            'close_date'       => $test->close_date?->toDateString(),
            'user_stats'       => $userStats,
        ];

        return $this->successResponse($data, 'Test details retrieved successfully.');
    }

    /**
     * Start a new test attempt or resume an ongoing session.
     *
     * POST /api/v1/tests/{test}/start
     */
    public function start(Request $request, Test $test): JsonResponse
    {
        $this->validateTestAvailability($test);

        $test->load(['questions']);

        if ($test->questions->isEmpty()) {
            return $this->errorResponse('This examination currently has no questions assigned.', 422);
        }

        $user = $request->user();

        // Resume ongoing active attempt if available
        $activeAttempt = TestAttempt::where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', 'active')
            ->whereNull('submitted_at')
            ->first();

        if ($activeAttempt) {
            if ($activeAttempt->isTimedOut()) {
                $activeAttempt->gradeAndComplete(null, 'timeout');
                return $this->errorResponse('Examination duration ended. Responses were auto-submitted.', 400, [
                    'attempt_id' => $activeAttempt->id,
                    'is_completed' => true,
                ]);
            }

            $remainingSeconds = max(0, $activeAttempt->allowed_until->timestamp - now()->timestamp);

            return $this->successResponse([
                'attempt_id'        => $activeAttempt->id,
                'resumed'           => true,
                'started_at'        => $activeAttempt->started_at?->toIso8601String(),
                'allowed_until'     => $activeAttempt->allowed_until?->toIso8601String(),
                'remaining_seconds' => $remainingSeconds,
            ], 'Resumed ongoing examination session.');
        }

        // Check max attempts
        if ($test->max_attempts > 0) {
            $completedCount = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $test->id)
                ->where('status', 'completed')
                ->count();

            if ($completedCount >= $test->max_attempts) {
                return $this->errorResponse('You have already completed the maximum allowed attempts for this examination.', 422);
            }
        }

        $userAttemptsCount = TestAttempt::where('test_id', $test->id)->where('user_id', $user->id)->count();

        $attempt = TestAttempt::create([
            'user_id'          => $user->id,
            'test_id'          => $test->id,
            'attempt_number'   => $userAttemptsCount + 1,
            'started_at'       => now(),
            'allowed_until'    => now()->addMinutes($test->duration_minutes),
            'total_questions'  => $test->questions->count(),
            'answered_count'   => 0,
            'correct_count'    => 0,
            'incorrect_count'  => 0,
            'unanswered_count' => $test->questions->count(),
            'score_obtained'   => 0,
            'percentage'       => 0,
            'result'           => 'pending',
            'submission_type'  => 'manual',
            'tab_switch_count' => 0,
            'status'           => 'active',
        ]);

        foreach ($test->questions as $question) {
            AttemptAnswer::create([
                'test_attempt_id'      => $attempt->id,
                'question_id'          => $question->id,
                'selected_option_id'   => null,
                'is_marked_for_review' => false,
                'is_correct'           => false,
                'marks_obtained'       => 0,
                'answered_at'          => null,
            ]);
        }

        $remainingSeconds = (int) ($test->duration_minutes * 60);

        return $this->successResponse([
            'attempt_id'        => $attempt->id,
            'resumed'           => false,
            'started_at'        => $attempt->started_at?->toIso8601String(),
            'allowed_until'     => $attempt->allowed_until?->toIso8601String(),
            'remaining_seconds' => $remainingSeconds,
        ], 'Examination session started. All the best!', 201);
    }

    /**
     * Load test questions, options, and attempt state for taking the test.
     *
     * GET /api/v1/tests/{test}/attempts/{attempt}
     */
    public function take(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $test, $attempt);

        if ($attempt->status === 'completed') {
            return $this->errorResponse('This examination attempt has already been submitted.', 400, [
                'status' => 'completed',
            ]);
        }

        if ($attempt->isTimedOut()) {
            $attempt->gradeAndComplete(null, 'timeout');
            return $this->errorResponse('Allocated examination time has expired. Responses have been auto-submitted.', 400, [
                'status' => 'completed',
            ]);
        }

        $test->load(['questions.options', 'testLevel']);

        // Ensure attempt answers are populated
        foreach ($test->questions as $q) {
            AttemptAnswer::firstOrCreate([
                'test_attempt_id' => $attempt->id,
                'question_id'     => $q->id,
            ], [
                'selected_option_id'   => null,
                'is_marked_for_review' => false,
                'is_correct'           => false,
                'marks_obtained'       => 0,
                'answered_at'          => null,
            ]);
        }

        $attempt->load(['answers']);

        // Format questions WITHOUT is_correct
        $questionsData = $test->questions->map(function ($q) {
            return [
                'id'            => (int) $q->id,
                'question_text' => (string) $q->question_text,
                'question_type' => (string) $q->question_type,
                'image_url'     => $q->image_url,
                'audio_url'     => $q->audio_url,
                'marks'         => (int) $q->marks,
                'options'       => $q->options->map(function ($opt) {
                    return [
                        'id'           => (int) $opt->id,
                        'option_label' => (string) $opt->option_label,
                        'option_text'  => (string) $opt->option_text,
                    ];
                })->values(),
            ];
        })->values();

        $answersData = $attempt->answers->mapWithKeys(function ($a) {
            return [
                $a->question_id => [
                    'selected_option_id'   => $a->selected_option_id ? (int) $a->selected_option_id : null,
                    'is_marked_for_review' => (bool) $a->is_marked_for_review,
                    'answered_at'          => $a->answered_at?->toIso8601String(),
                ]
            ];
        });

        $remainingSeconds = $attempt->allowed_until
            ? max(0, $attempt->allowed_until->timestamp - now()->timestamp)
            : ($test->duration_minutes * 60);

        return $this->successResponse([
            'test' => [
                'id'               => $test->id,
                'title'            => $test->title,
                'duration_minutes' => (int) $test->duration_minutes,
                'total_marks'      => (int) $test->total_marks,
                'tab_switch_limit' => (int) $test->tab_switch_limit,
            ],
            'attempt' => [
                'id'                => $attempt->id,
                'status'            => $attempt->status,
                'started_at'        => $attempt->started_at?->toIso8601String(),
                'allowed_until'     => $attempt->allowed_until?->toIso8601String(),
                'remaining_seconds' => $remainingSeconds,
            ],
            'questions' => $questionsData,
            'answers'   => $answersData,
        ], 'Examination questions loaded.');
    }

    /**
     * Save an answer for a specific question during a test attempt.
     *
     * POST /api/v1/tests/{test}/attempts/{attempt}/answer
     */
    public function saveAnswer(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $test, $attempt);

        $validated = $request->validate([
            'question_id'        => ['required', 'integer', 'exists:questions,id'],
            'selected_option_id' => ['required', 'integer', 'exists:question_options,id'],
        ]);

        $optionExists = QuestionOption::where('question_id', $validated['question_id'])
            ->where('id', $validated['selected_option_id'])
            ->exists();

        if (!$optionExists) {
            return $this->errorResponse('Invalid option selected for this question.', 422);
        }

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $validated['question_id'])
            ->first();

        if (!$answer) {
            $answer = AttemptAnswer::create([
                'test_attempt_id'      => $attempt->id,
                'question_id'          => $validated['question_id'],
                'selected_option_id'   => $validated['selected_option_id'],
                'is_marked_for_review' => false,
                'is_correct'           => false,
                'marks_obtained'       => 0,
                'answered_at'          => now(),
            ]);
        } else {
            $answer->update([
                'selected_option_id' => $validated['selected_option_id'],
                'answered_at'        => now(),
            ]);
        }

        return $this->successResponse([
            'question_id'        => (int) $validated['question_id'],
            'selected_option_id' => (int) $validated['selected_option_id'],
        ], 'Answer saved successfully.');
    }

    /**
     * Clear an answer for a specific question.
     *
     * POST /api/v1/tests/{test}/attempts/{attempt}/clear-answer
     */
    public function clearAnswer(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $test, $attempt);

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $validated['question_id'])
            ->first();

        if ($answer) {
            $answer->update([
                'selected_option_id' => null,
                'answered_at'        => null,
            ]);
        }

        return $this->successResponse([
            'question_id' => (int) $validated['question_id'],
            'cleared'     => true,
        ], 'Answer cleared successfully.');
    }

    /**
     * Toggle mark for review on a question.
     *
     * POST /api/v1/tests/{test}/attempts/{attempt}/mark-review
     */
    public function toggleMarkReview(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $test, $attempt);

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        $answer = AttemptAnswer::where('test_attempt_id', $attempt->id)
            ->where('question_id', $validated['question_id'])
            ->first();

        if (!$answer) {
            $answer = AttemptAnswer::create([
                'test_attempt_id'      => $attempt->id,
                'question_id'          => $validated['question_id'],
                'selected_option_id'   => null,
                'is_marked_for_review' => true,
                'is_correct'           => false,
                'marks_obtained'       => 0,
            ]);
        } else {
            $answer->update([
                'is_marked_for_review' => !$answer->is_marked_for_review,
            ]);
        }

        return $this->successResponse([
            'question_id'          => (int) $validated['question_id'],
            'is_marked_for_review' => (bool) $answer->is_marked_for_review,
        ], 'Mark for review updated.');
    }

    /**
     * Submit test attempt and compute grade/results.
     *
     * POST /api/v1/tests/{test}/attempts/{attempt}/submit
     */
    public function submit(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $this->authorizeAttempt($request, $test, $attempt);

        $tabSwitchCount = (int) $request->input('tab_switch_count', 0);
        $isTimeout = $attempt->isTimedOut() || $request->boolean('is_timeout');

        $submissionType = 'manual';
        if ($isTimeout) {
            $submissionType = 'timeout';
        } elseif ($tabSwitchCount >= 1 && $test->tab_switch_limit > 0 && $tabSwitchCount >= $test->tab_switch_limit) {
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

        return $this->successResponse([
            'attempt_id'       => $attempt->id,
            'status'           => $attempt->status,
            'submission_type'  => $submissionType,
            'total_questions'  => (int) $attempt->total_questions,
            'answered_count'   => (int) $attempt->answered_count,
            'correct_count'    => (int) $attempt->correct_count,
            'incorrect_count'  => (int) $attempt->incorrect_count,
            'unanswered_count' => (int) $attempt->unanswered_count,
            'score_obtained'   => (float) $attempt->score_obtained,
            'percentage'       => (float) $attempt->percentage,
            'result'           => $attempt->result,
            'submitted_at'     => $attempt->submitted_at?->toIso8601String(),
        ], 'Examination submitted successfully.');
    }

    /**
     * View complete scorecard, solutions, and analytics for an attempt.
     *
     * GET /api/v1/tests/{test}/attempts/{attempt}/result
     */
    public function result(Request $request, Test $test, TestAttempt $attempt): JsonResponse
    {
        $user = $request->user();

        if ((int) $attempt->user_id !== (int) $user->id) {
            return $this->errorResponse('Unauthorized to view this attempt.', 403);
        }

        // Auto-complete if still active but timed out
        if ($attempt->status === 'active') {
            if ($attempt->isTimedOut()) {
                $attempt->gradeAndComplete(null, 'timeout');
            } else {
                return $this->errorResponse('This attempt is still ongoing. Please submit it first.', 400);
            }
        }

        $test->load(['questions.options', 'testLevel']);
        $attempt->load(['answers.question.options', 'answers.option']);

        $orderMap = $test->questions->pluck('pivot.question_order', 'id')->all();
        $sortedAnswers = $attempt->answers->sortBy(function ($ans) use ($orderMap) {
            return $orderMap[$ans->question_id] ?? $ans->id;
        })->values();

        $solutions = $sortedAnswers->map(function ($ans) {
            $question = $ans->question;
            $correctOption = $question?->options->firstWhere('is_correct', true);

            return [
                'question_id'        => (int) $ans->question_id,
                'question_text'      => $question?->question_text,
                'marks'              => (int) ($question?->marks ?? 1),
                'marks_obtained'     => (float) $ans->marks_obtained,
                'is_correct'         => (bool) $ans->is_correct,
                'explanation'        => $question?->explanation,
                'selected_option_id' => $ans->selected_option_id ? (int) $ans->selected_option_id : null,
                'correct_option_id'  => $correctOption ? (int) $correctOption->id : null,
                'options'            => $question?->options->map(function ($opt) {
                    return [
                        'id'           => (int) $opt->id,
                        'option_label' => (string) $opt->option_label,
                        'option_text'  => (string) $opt->option_text,
                        'is_correct'   => (bool) $opt->is_correct,
                    ];
                })->values(),
            ];
        });

        $matchedSlab = \App\Models\ResultSlab::getSlabForScore((float) $attempt->score_obtained, $attempt->test_id);
        $certNumber = 'REIAC-CRT-' . date('Y') . '-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT);
        $certificateUrl = url('/community/tests/student/' . $test->id . '/result/' . $attempt->id);

        $data = [
            'test' => [
                'id'               => $test->id,
                'title'            => $test->title,
                'level'            => $test->testLevel?->name,
                'total_marks'      => (int) $test->total_marks,
                'passing_marks'    => (int) $test->passing_marks,
                'duration_minutes' => (int) $test->duration_minutes,
            ],
            'certificate' => [
                'certificate_number' => $certNumber,
                'issue_date'         => $attempt->submitted_at ? $attempt->submitted_at->format('F d, Y') : date('F d, Y'),
                'candidate_name'     => $user->name,
                'candidate_username' => $user->username ?? ('ID-' . $user->id),
                'result_standing'    => $matchedSlab ? $matchedSlab->name : ($attempt->result === 'pass' ? 'QUALIFIED (PASS)' : 'COMPLETED'),
                'certificate_url'    => $certificateUrl,
            ],
            'scorecard' => [
                'attempt_id'       => $attempt->id,
                'attempt_number'   => (int) $attempt->attempt_number,
                'total_questions'  => (int) $attempt->total_questions,
                'answered_count'   => (int) $attempt->answered_count,
                'correct_count'    => (int) $attempt->correct_count,
                'incorrect_count'  => (int) $attempt->incorrect_count,
                'unanswered_count' => (int) $attempt->unanswered_count,
                'score_obtained'   => (float) $attempt->score_obtained,
                'percentage'       => (float) $attempt->percentage,
                'result'           => $attempt->result,
                'grade_slab'       => $matchedSlab ? $matchedSlab->name : null,
                'submission_type'  => $attempt->submission_type,
                'started_at'       => $attempt->started_at?->toIso8601String(),
                'submitted_at'     => $attempt->submitted_at?->toIso8601String(),
            ],
            'solutions' => $solutions,
        ];

        return $this->successResponse($data, 'Examination result retrieved successfully.');
    }

    /**
     * Get student's previous test attempt history.
     *
     * GET /api/v1/tests/my-attempts
     */
    public function myAttempts(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min($request->integer('per_page', 15), 50);

        $attempts = TestAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with(['test.testLevel'])
            ->latest('submitted_at')
            ->paginate($perPage);

        $data = $attempts->through(function ($att) {
            return [
                'id'              => $att->id,
                'test_id'         => $att->test_id,
                'test_title'      => $att->test?->title,
                'level_name'      => $att->test?->testLevel?->name,
                'attempt_number'  => (int) $att->attempt_number,
                'score_obtained'  => (float) $att->score_obtained,
                'total_marks'     => (int) ($att->test?->total_marks ?? 0),
                'percentage'      => (float) $att->percentage,
                'result'          => $att->result,
                'certificate_url' => url('/community/tests/student/' . $att->test_id . '/result/' . $att->id),
                'submitted_at'    => $att->submitted_at?->toIso8601String(),
            ];
        });

        return $this->successResponse($data, 'Test attempts history retrieved successfully.');
    }

    /**
     * Validate test availability.
     */
    protected function validateTestAvailability(Test $test, bool $allowClosed = false): void
    {
        if ($test->status !== 'published') {
            abort(404, 'Examination not found.');
        }

        if ($test->testLevel && $test->testLevel->status !== 'active') {
            abort(403, 'This examination level is currently inactive.');
        }

        if (!$allowClosed && !$test->isOpen()) {
            abort(403, 'This examination is currently not open or has expired.');
        }
    }

    /**
     * Authorize that the attempt belongs to current user and is active.
     */
    protected function authorizeAttempt(Request $request, Test $test, TestAttempt $attempt): void
    {
        $user = $request->user();

        if ((int) $attempt->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access to this examination attempt.');
        }

        if ((int) $attempt->test_id !== (int) $test->id) {
            abort(400, 'Attempt does not belong to this test.');
        }

        if ($test->isExpired()) {
            abort(403, 'This examination has expired.');
        }
    }
}
