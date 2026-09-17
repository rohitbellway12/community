<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $table = 'test_questions';

    protected $fillable = [
        'test_id',
        'question_id',
        'question_order',
    ];

    protected $casts = [
        'question_order' => 'integer',
    ];
}
