<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artwork extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'category',
        'image_path',
        'user_id', // Make sure user_id is also here
    ];

    /**
     * Get the user (artist) that owns the artwork.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}