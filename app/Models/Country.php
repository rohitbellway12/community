<?php

// app/Models/Country.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'iso_code',
        'flag',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function visaServices()
    {
        return $this->hasMany(VisaService::class);
    }
}
