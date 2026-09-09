<?php

// app/Models/Report.php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'status' => ReportStatus::class,
        'reviewed_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
