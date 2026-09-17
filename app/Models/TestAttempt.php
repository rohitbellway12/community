<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'test_id',
        'attempt_number',
        'started_at',
        'allowed_until',
        'submitted_at',
        'total_questions',
        'answered_count',
        'correct_count',
        'incorrect_count',
        'unanswered_count',
        'score_obtained',
        'percentage',
        'result',
        'submission_type',
        'tab_switch_count',
        'status',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'total_questions' => 'integer',
        'answered_count' => 'integer',
        'correct_count' => 'integer',
        'incorrect_count' => 'integer',
        'unanswered_count' => 'integer',
        'score_obtained' => 'decimal:2',
        'percentage' => 'decimal:2',
        'tab_switch_count' => 'integer',
        'started_at' => 'datetime',
        'allowed_until' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function isTimedOut(): bool
    {
        return $this->status === 'active'
            && $this->allowed_until
            && $this->allowed_until->lt(now());
    }

    public function gradeAndComplete(?array $answers = null, string $submissionType = 'manual', int $tabSwitchCount = 0): void
    {
        $this->load(['test.questions.options', 'answers.question.options', 'user']);

        $test = $this->test;
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $scoreObtained = 0;
        $totalMarks = 0;

        foreach ($this->answers as $answer) {
            $question = $answer->question;
            if (!$question) {
                continue;
            }

            $totalMarks += $question->marks;

            if ($answers !== null) {
                $selectedOptionId = $answers[$question->id] ?? null;
                $answer->selected_option_id = $selectedOptionId;
                $answer->answered_at = now();
            } else {
                $selectedOptionId = $answer->selected_option_id;
                $answer->answered_at = $answer->answered_at ?? now();
            }

            $correctOption = $question->options->firstWhere('is_correct', true);

            if ($selectedOptionId && $correctOption && $selectedOptionId == $correctOption->id) {
                $answer->is_correct = true;
                $answer->marks_obtained = $question->marks;
                $correctCount++;
                $scoreObtained += $question->marks;
            } elseif ($selectedOptionId) {
                $answer->is_correct = false;
                $answer->marks_obtained = 0;
                $incorrectCount++;
            } else {
                $answer->is_correct = false;
                $answer->marks_obtained = 0;
                $unansweredCount++;
            }

            $answer->save();
        }

        if ($submissionType === 'manual' && $test->tab_switch_limit > 0 && $tabSwitchCount >= $test->tab_switch_limit) {
            $submissionType = 'tab_switch_violation';
        }

        $answeredCount = $correctCount + $incorrectCount;
        $percentage = $totalMarks > 0 ? round(($scoreObtained / $totalMarks) * 100, 2) : 0;
        $result = $scoreObtained >= $test->passing_marks ? 'pass' : 'fail';

        $this->update([
            'answered_count' => $answeredCount,
            'correct_count' => $correctCount,
            'incorrect_count' => $incorrectCount,
            'unanswered_count' => $unansweredCount,
            'score_obtained' => $scoreObtained,
            'percentage' => $percentage,
            'result' => $result,
            'submission_type' => $submissionType,
            'submitted_at' => now(),
            'tab_switch_count' => $tabSwitchCount,
            'status' => 'completed',
        ]);
    }
}
