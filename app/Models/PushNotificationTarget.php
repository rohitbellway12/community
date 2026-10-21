<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotificationTarget extends Model
{
    protected $fillable = [
        'notification_id',
        'target_type',
        'target_id',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(PushNotification::class, 'notification_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'target_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'target_id');
    }
}
