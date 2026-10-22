<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerInstall extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'device_id',
        'platform',
        'app_version',
        'ip_address',
        'click_id',
        'is_registered',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'is_registered' => 'boolean',
        'created_at'    => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InfluencerCampaign::class, 'campaign_id');
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(InfluencerClick::class, 'click_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
