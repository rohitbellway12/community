<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeShareVerification extends Model
{
    protected $fillable = [
        'resume_id',
        'user_id',
        'platform',
        'verification_method',
        'proof_image',
        'proof_images',
        'post_url',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'proof_images' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get list of full proof URLs (supports both single and multiple uploads).
     */
    public function getProofUrlsAttribute(): array
    {
        $urls = [];
        if (!empty($this->proof_images) && is_array($this->proof_images)) {
            foreach ($this->proof_images as $path) {
                if ($path) {
                    $urls[] = asset('storage/' . $path);
                }
            }
        } elseif (!empty($this->proof_image)) {
            $urls[] = asset('storage/' . $this->proof_image);
        }
        return $urls;
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
