<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($news) {
            if ($news->isDirty('title')) {
                // Unique slug with random suffix to prevent crashes
                $news->slug = Str::slug($news->title) . '-' . Str::lower(Str::random(6));
            }
        });
    }
}