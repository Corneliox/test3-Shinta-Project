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
     * Relationship: An Event has many Gallery Images
     */
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    /**
     * AUTOMATIC TRANSLATION: Title
     * If site is in 'id' (Indonesian) mode, show title_id instead.
     */
    public function getTitleAttribute($value)
    {
        if (App::getLocale() == 'id' && !empty($this->attributes['title_id'])) {
            return $this->attributes['title_id'];
        }
        return $value;
    }

    /**
     * AUTOMATIC TRANSLATION: Description
     * If site is in 'id' (Indonesian) mode, show description_id instead.
     */
    public function getDescriptionAttribute($value)
    {
        if (App::getLocale() == 'id' && !empty($this->attributes['description_id'])) {
            return $this->attributes['description_id'];
        }
        return $value;
    }

    /**
     * The "booted" method of the model.
     * This automatically creates the slug.
     */
    protected static function booted(): void
    {
        static::saving(function ($event) {
            $event->slug = Str::slug($event->title); // Note: Slug uses the English title usually
        });
    }

    /**
     * Get the path to the original (High-Res) image.
     */
    public function getOriginalImagePath()
    {
        $path = $this->image_path;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $dirname = pathinfo($path, PATHINFO_DIRNAME);

        return $dirname . '/' . $filename . '_original.' . $extension;
    }

    /**
     * Check if original exists, otherwise return normal path.
     * Use this in the Details View.
     */
    public function getHighResUrlAttribute()
    {
        $originalPath = $this->getOriginalImagePath();

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($originalPath)) {
            return \Illuminate\Support\Facades\Storage::url($originalPath);
        }

        return \Illuminate\Support\Facades\Storage::url($this->image_path);
    }
}