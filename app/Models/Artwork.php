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
    protected $fillable = [
        'title',
        'slug', // <-- Make sure 'slug' is in fillable
        'description',
        'category',
        'image_path',
        'user_id',
    ];

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