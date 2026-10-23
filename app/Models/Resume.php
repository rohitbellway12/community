<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'template',
        'language',
        'personal_info',
        'summary',
        'education',
        'experience',
        'skills',
        'projects',
        'certifications',
        'languages',
        'military_service',
        'self_introduction',
        'language_tests',
        'computer_skills',
        'awards',
        'custom_sections',
        'is_watermark_removed',
        'watermark_removed_at',
        'download_count',
    ];

    protected $casts = [
        'personal_info'        => 'array',
        'education'            => 'array',
        'experience'           => 'array',
        'skills'               => 'array',
        'projects'             => 'array',
        'certifications'       => 'array',
        'languages'            => 'array',
        'military_service'     => 'array',
        'self_introduction'    => 'array',
        'language_tests'       => 'array',
        'computer_skills'      => 'array',
        'awards'               => 'array',
        'custom_sections'      => 'array',
        'is_watermark_removed' => 'boolean',
        'watermark_removed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(ResumeShareVerification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(ResumeShareVerification::class)->latestOfMany();
    }
}
