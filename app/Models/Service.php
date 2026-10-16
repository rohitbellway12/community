<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'service_category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'image',
        'whatsapp_number',
        'whatsapp_message',
        'phone',
        'email',
        'sort_order',
        'is_published',
        'click_count',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
        'click_count' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'whatsapp_url',
    ];

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    /**
     * Category Relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Scope for published services
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('title', 'asc');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get cleaned WhatsApp number for wa.me link
     */
    public function getCleanWhatsappNumberAttribute(): string
    {
        // Remove spaces, dashes, plus signs, brackets
        $clean = preg_replace('/[^0-9]/', '', (string)$this->whatsapp_number);
        return $clean;
    }

    /**
     * Generate direct WhatsApp link with prefilled inquiry message
     */
    public function getWhatsappUrlAttribute(): string
    {
        $number = $this->clean_whatsapp_number;
        $message = $this->whatsapp_message;

        if (empty($message)) {
            $message = "Hello REIAC Consultancy, I am interested in {$this->title}. Please guide me with more details.";
        }

        return "https://wa.me/{$number}?text=" . rawurlencode($message);
    }
}
