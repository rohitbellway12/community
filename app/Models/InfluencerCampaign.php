<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InfluencerCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'influencer_name',
        'campaign_name',
        'code',
        'channel',
        'destination_url',
        'play_store_url',
        'app_store_url',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    protected $appends = [
        'tracking_url',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(InfluencerClick::class, 'campaign_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(InfluencerRegistration::class, 'campaign_id');
    }

    public function installs(): HasMany
    {
        return $this->hasMany(InfluencerInstall::class, 'campaign_id');
    }

    public function getTrackingUrlAttribute(): string
    {
        return url('/c/' . $this->code);
    }

    /**
     * Calculate conversion rate percentage.
     */
    public function getConversionRateAttribute(): float
    {
        $clicksCount = $this->clicks()->count();
        if ($clicksCount === 0) {
            return 0.0;
        }

        $regCount = $this->registrations()->count();
        return round(($regCount / $clicksCount) * 100, 2);
    }
}
