<?php

// app/Models/VisaService.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaService extends Model
{
    use HasFactory;

    protected $table = 'visa_services';

    protected $fillable = [
        'country_id',
        'title',
        'slug',
        'description',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function items()
    {
        return $this->hasMany(VisaServiceItem::class);
    }
}
