<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    // Make all fields fillable
    protected $guarded = [];

    // Cast dates to Carbon objects
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     * This automatically creates the slug.
     */
    protected static function booted(): void
    {
        static::saving(function ($event) {
            $event->slug = Str::slug($event->title);
        });
    }
}