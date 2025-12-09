<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // <-- 1. MAKE SURE THIS IS AT THE TOP

class Artwork extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'title',
    //     'slug', // <-- Make sure 'slug' is in fillable
    //     'description',
    //     'category',
    //     'image_path',
    //     'user_id',
    // ];
    protected $fillable = [
        'user_id', 
        'title', 
        'slug',
        'description',
        'category',
        'image_path',
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
    ];

    // Helper: Check if item is effectively sold out
    public function isSoldOut()
    {
        // If no price, stock doesn't matter
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

    // AUTOMATIC TITLE TRANSLATOR
    public function getTitleAttribute($value)
    {
        // If current language is ID AND title_id is not empty, return title_id
        if (App::getLocale() == 'id' && !empty($this->attributes['title_id'])) {
            return $this->attributes['title_id'];
        }
        // Otherwise return default (English)
        return $value;
    }

    // AUTOMATIC DESCRIPTION TRANSLATOR
    public function getDescriptionAttribute($value)
    {
        if (App::getLocale() == 'id' && !empty($this->attributes['description_id'])) {
            return $this->attributes['description_id'];
        }
        return $value;
    }
    
    /**
     * The "booted" method of the model.
     * This automatically creates the slug when you save.
     */
    protected static function booted(): void // <-- 2. MAKE SURE THIS METHOD EXISTS
    {
        static::saving(function ($artwork) {
            $artwork->slug = Str::slug($artwork->title);
        });
    }

    /**
     * Get the user (artist) that owns the artwork.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}