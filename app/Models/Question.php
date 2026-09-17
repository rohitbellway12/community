<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_level_id',
        'question_text',
        'question_type',
        'image_url',
        'audio_url',
        'marks',
        'explanation',
        'status',
    ];

    protected $casts = [
        'marks' => 'integer',
        'status' => 'string',
        'question_type' => 'string',
    ];

    public function testLevel(): BelongsTo
    {
        return $this->belongsTo(TestLevel::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }
}
