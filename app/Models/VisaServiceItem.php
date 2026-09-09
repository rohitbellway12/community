<?php

// app/Models/VisaServiceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaServiceItem extends Model
{
    use HasFactory;

    protected $table = 'visa_service_items';

    protected $fillable = [
        'visa_service_id',
        'visa_code',
        'title',
        'description',
        'requirements',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'requirements' => 'array',
        'status' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(VisaService::class, 'visa_service_id');
    }
}
