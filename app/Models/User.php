<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Removed: use Laravel\Sanctum\HasApiTokens; 
use App\Traits\LogsActivity;

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
}