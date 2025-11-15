<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <-- 1. Import this

class Artwork extends Model
{
    use HasFactory;

    /**
     * Get the user (artist) that owns the artwork.
     */
    public function user(): BelongsTo // <-- 2. Add this entire method
    {
        return $this->belongsTo(User::class);
    }
}