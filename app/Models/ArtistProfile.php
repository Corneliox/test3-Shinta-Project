<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'about',
        'profile_picture',
        'phone',
    ];

    /**
     * An ArtistProfile belongs back to a single User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // AUTOMATIC ABOUT TRANSLATOR
    public function getAboutAttribute($value)
    {
        if (App::getLocale() == 'id' && !empty($this->attributes['about_id'])) {
            return $this->attributes['about_id'];
        }
        return $value;
    }
}