<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileActivity extends Model
{
    use HasFactory;

    protected $table = 'profile_activities';

    protected $fillable = [
        'key',
        'title',
        'description',
        'points',
        'action_url',
        'action_label',
        'icon',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status'     => 'boolean',
            'points'     => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Scope only active activities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true)->orderBy('sort_order')->orderBy('id');
    }
}
