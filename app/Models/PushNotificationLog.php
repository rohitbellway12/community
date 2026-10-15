<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotificationLog extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'device_token',
        'status',
        'error_message',
        'delivered_at',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(PushNotification::class, 'notification_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
