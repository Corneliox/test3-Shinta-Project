<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // <-- Required for Str::slug and Str::random
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage; // <-- Required for Storage check

class Artwork extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id', 
        'title', 
        'slug',
        'description',
        'category',
        'image_path',
        'additional_images',
        'price',
        'stock',
        'reserved_stock',
        'reserved_until',
        'is_promo',
        'promo_price'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'is_promo' => 'boolean',
        'additional_images' => 'array',
    ];

    // --- HELPER METHODS ---

    // Helper: Check if item is effectively sold out
    public function isSoldOut()
    {
        // If no price, stock doesn't matter (Gallery Item)
        if (!$this->price) return false; 
        return $this->stock <= 0;
    }

    // Helper: Calculate Discount %
    public function getDiscountPercentAttribute()
    {
        if ($this->price > 0 && $this->promo_price > 0) {
            return round((($this->price - $this->promo_price) / $this->price) * 100);
        }
        return 0;
    }

    // --- TRANSLATION ACCESSORS ---

    // Automatic Title Translator
    public function getTitleAttribute($value)
    {
        // If current language is ID AND title_id is not empty, return title_id
        if (App::getLocale() == 'id' && !empty($this->attributes['title_id'])) {
            return $this->attributes['title_id'];
        }
        // Otherwise return default (English)
        return $value;
    }

    // Automatic Description Translator
    public function getDescriptionAttribute($value)
    {
        if (App::getLocale() == 'id' && !empty($this->attributes['description_id'])) {
            return $this->attributes['description_id'];
        }
        return $value;
    }
    
    // --- BOOTED METHOD (SLUG GENERATION) ---

    /**
     * The "booted" method of the model.
     * Automatically creates a UNIQUE slug when saving.
     */
    protected static function booted(): void
    {
        static::creating(function ($artwork) {
            // Append random string to ensure uniqueness (prevents 1062 Duplicate Entry error)
            $artwork->slug = Str::slug($artwork->title) . '-' . Str::lower(Str::random(6));
        });

        static::updating(function ($artwork) {
            // Only update slug if the title actually changed
            if ($artwork->isDirty('title')) {
                $artwork->slug = Str::slug($artwork->title) . '-' . Str::lower(Str::random(6));
            }
        });
    }

    // --- RELATIONSHIPS ---

    /**
     * Get the user (artist) that owns the artwork.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // --- IMAGE HELPERS ---

    /**
     * Get the path to the original (High-Res) image.
     * Logic: Replaces ".jpg" with "_original.jpg"
     */
    public function getOriginalImagePath()
    {
        $path = $this->image_path;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $dirname = pathinfo($path, PATHINFO_DIRNAME);

        // Reconstruct path with _original suffix
        return $dirname . '/' . $filename . '_original.' . $extension;
    }

    /**
     * Check if original exists, otherwise return normal path.
     * Use this in the Details View (show.blade.php).
     */
    public function getHighResUrlAttribute()
    {
        $originalPath = $this->getOriginalImagePath();

        // If the original version exists on disk, return its URL
        if (Storage::disk('public')->exists($originalPath)) {
            return Storage::url($originalPath);
        }

        // Fallback to the standard (optimized) one if original is missing
        return Storage::url($this->image_path);
    }
}