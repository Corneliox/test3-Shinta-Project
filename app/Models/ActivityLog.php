<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'description'];

    /**
     * Helper to record an action easily.
     * Usage: \App\Models\ActivityLog::record('Action Name', 'Details...');
     */
    public static function record($action, $description)
    {
        self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}