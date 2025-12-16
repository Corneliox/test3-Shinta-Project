<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Removed: use Laravel\Sanctum\HasApiTokens; 
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity; // Removed HasApiTokens from here

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_artist',      // Role
        'is_admin',       // Role
        'is_superadmin',  // Role (God Mode)
        'phone',          // General Phone
        'is_shop_contact', // The Gate Flag
        'slug',           // Added slug to fillable just in case
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_artist' => 'boolean',
            'is_admin' => 'boolean',
            'is_superadmin' => 'boolean',
        ];
    }

    /**
     * Relationship: User has one Artist Profile.
     */
    public function artistProfile()
    {
        return $this->hasOne(ArtistProfile::class);
    }

    /**
     * Relationship: User has many Artworks.
     */
    public function artworks()
    {
        return $this->hasMany(Artwork::class);
    }

    /**
     * Booted method to handle Slug Generation
     */
    protected static function booted(): void
    {
        // 1. On Create
        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = Str::slug($user->name) . '-' . Str::lower(Str::random(4));
            }
        });

        // 2. On Update (FIXED LOGIC)
        static::updating(function ($user) {
            // Generate slug if it is MISSING, even if the name didn't change
            if (empty($user->slug)) {
                $user->slug = Str::slug($user->name) . '-' . Str::lower(Str::random(4));
            }
            // Also regenerate if the name CHANGED
            elseif ($user->isDirty('name')) {
                $user->slug = Str::slug($user->name) . '-' . Str::lower(Str::random(4));
            }
        });
    }
}