<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerRegistration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'click_id',
        'ip_address',
        'device_id',
        'is_suspicious',
        'suspicion_reasons',
        'created_at',
    ];

    protected $casts = [
        'is_suspicious'     => 'boolean',
        'suspicion_reasons' => 'array',
        'created_at'        => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InfluencerCampaign::class, 'campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(InfluencerClick::class, 'click_id');
    }
}
