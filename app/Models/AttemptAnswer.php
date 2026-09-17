<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_attempt_id',
        'question_id',
        'selected_option_id',
        'is_marked_for_review',
        'is_correct',
        'marks_obtained',
        'answered_at',
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'is_correct' => 'boolean',
        'marks_obtained' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    public function testAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(\App\Models\QuestionOption::class, 'selected_option_id');
    }
}
