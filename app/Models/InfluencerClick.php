<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InfluencerClick extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'ip_address',
        'user_agent',
        'referer',
        'device_type',
        'platform',
        'browser',
        'is_bot',
        'is_suspicious',
        'suspicion_reasons',
        'created_at',
    ];

    protected $casts = [
        'is_bot'            => 'boolean',
        'is_suspicious'     => 'boolean',
        'suspicion_reasons' => 'array',
        'created_at'        => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InfluencerCampaign::class, 'campaign_id');
    }

    public function registration(): HasOne
    {
        return $this->hasOne(InfluencerRegistration::class, 'click_id');
    }
}
