<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_level_id',
        'title',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'passing_marks',
        'open_date',
        'open_time',
        'close_date',
        'close_time',
        'auto_open',
        'max_attempts',
        'tab_switch_limit',
        'status',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
        'auto_open' => 'boolean',
        'max_attempts' => 'integer',
        'tab_switch_limit' => 'integer',
        'status' => 'string',
        'open_date' => 'date',
        'close_date' => 'date',
    ];

    public function getOpenTimeAttribute($value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getCloseTimeAttribute($value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function testLevel(): BelongsTo
    {
        return $this->belongsTo(TestLevel::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions', 'test_id', 'question_id')
            ->withPivot('question_order')
            ->orderByRaw('test_questions.question_order ASC');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function isExpired(): bool
    {
        if (!$this->close_date) {
            return false;
        }

        $closesAt = $this->close_time
            ? $this->close_date->copy()->setTimeFrom($this->close_time)
            : $this->close_date->endOfDay();

        return now()->gt($closesAt);
    }

    public function isUpcoming(): bool
    {
        if (!$this->open_date) {
            return false;
        }

        $opensAt = $this->open_time
            ? $this->open_date->copy()->setTimeFrom($this->open_time)
            : $this->open_date->startOfDay();

        return now()->lt($opensAt);
    }

    public function isOpen(): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->auto_open) {
            return true;
        }

        if ($this->isUpcoming()) {
            return false;
        }

        return true;
    }

    public function isClosed(): bool
    {
        return !$this->isOpen();
    }

    public function isAvailableFor($user): bool
    {
        if (!$this->isOpen()) {
            return false;
        }

        if (!$user) {
            return false;
        }

        if ($this->max_attempts > 0) {
            $completedCount = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $this->id)
                ->where('status', 'completed')
                ->count();

            if ($completedCount >= $this->max_attempts) {
                return false;
            }
        }

        return true;
    }

    public function remainingMinutes($startedAt): int
    {
        if (!$startedAt) {
            return $this->duration_minutes;
        }
        $end = (clone $startedAt)->addMinutes($this->duration_minutes);
        return max(0, now()->diffInMinutes($end, false));
    }

    public function remainingSeconds($startedAt): int
    {
        if (!$startedAt) {
            return $this->duration_minutes * 60;
        }
        $end = (clone $startedAt)->addMinutes($this->duration_minutes);
        return max(0, now()->diffInSeconds($end, false));
    }
}
