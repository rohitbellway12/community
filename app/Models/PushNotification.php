<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PushNotification extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'banner_url',
        'action_link',
        'audience_type',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'success_count',
        'failed_count',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
        'total_recipients' => 'integer',
        'success_count'    => 'integer',
        'failed_count'     => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(PushNotificationTarget::class, 'notification_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PushNotificationLog::class, 'notification_id');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now());
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'sent' => [
                'bg'    => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
                'label' => 'Sent',
                'dot'   => 'bg-emerald-400',
            ],
            'partially_failed' => [
                'bg'    => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
                'label' => 'Partially Failed',
                'dot'   => 'bg-amber-400',
            ],
            'failed' => [
                'bg'    => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
                'label' => 'Failed',
                'dot'   => 'bg-rose-400',
            ],
            'scheduled' => [
                'bg'    => 'bg-sky-500/15 text-sky-400 border border-sky-500/30',
                'label' => 'Scheduled',
                'dot'   => 'bg-sky-400',
            ],
            default => [
                'bg'    => 'bg-slate-500/15 text-slate-300 border border-slate-500/30',
                'label' => 'Draft',
                'dot'   => 'bg-slate-400',
            ],
        };
    }
}
