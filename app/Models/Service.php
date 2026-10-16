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
        'status',
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
        'status_label',
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
            if (empty($service->status)) {
                $service->status = 'active';
            }
        });
    }

    /**
     * Status Helpers
     */
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isHold(): bool
    {
        return $this->status === 'hold';
    }

    /**
     * Status Label Accessor
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'hold' => 'On Hold',
            'inactive' => 'Inactive',
            default => 'Active',
        };
    }

    /**
     * Category Relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Scope for published / active services
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'active')
              ->orWhere(function ($sub) {
                  $sub->whereNull('status')->where('is_published', true);
              });
        });
    }

    /**
     * Scope active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope inactive
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope hold
     */
    public function scopeHold($query)
    {
        return $query->where('status', 'hold');
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
