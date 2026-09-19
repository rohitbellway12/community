<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultSlab extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_marks',
        'max_marks',
        'badge_color',
        'description',
        'test_id',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'min_marks'  => 'decimal:2',
        'max_marks'  => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Optional relationship to a specific test.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Find matching active slab for a given score.
     * Priority: Test-specific slab first, fallback to Global (where test_id is null).
     */
    public static function getSlabForScore(float $score, ?int $testId = null): ?self
    {
        if ($testId) {
            $slab = self::where('status', 'active')
                ->where('test_id', $testId)
                ->where('min_marks', '<=', $score)
                ->where('max_marks', '>=', $score)
                ->orderByDesc('min_marks')
                ->first();

            if ($slab) {
                return $slab;
            }
        }

        return self::where('status', 'active')
            ->whereNull('test_id')
            ->where('min_marks', '<=', $score)
            ->where('max_marks', '>=', $score)
            ->orderByDesc('min_marks')
            ->first();
    }

    /**
     * Helper to return Tailwind badge CSS classes based on badge_color.
     */
    public function getBadgeClassAttribute(): string
    {
        return match (strtolower($this->badge_color ?? 'emerald')) {
            'blue'   => 'bg-blue-50 text-blue-700 border-blue-200',
            'amber'  => 'bg-amber-50 text-amber-700 border-amber-200',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
            'rose'   => 'bg-rose-50 text-rose-700 border-rose-200',
            'slate'  => 'bg-slate-100 text-slate-700 border-slate-200',
            default  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
