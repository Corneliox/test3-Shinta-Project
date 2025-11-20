<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'feedback',
        'is_seen',
    ];

    /**
     * Get the user that submitted the feedback.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}