<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class Event extends Model
{
    use HasFactory, LogsActivity;

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