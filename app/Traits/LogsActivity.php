<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        // 1. Listen for CREATED events
        static::created(function ($model) {
            self::logChange('Created', $model);
        });

        // 2. Listen for UPDATED events
        static::updated(function ($model) {
            self::logChange('Updated', $model);
        });

        // 3. Listen for DELETED events
        static::deleted(function ($model) {
            self::logChange('Deleted', $model);
        });
    }

    protected static function logChange($action, $model)
    {
        // Get the class name (e.g., "Event", "User")
        $modelName = class_basename($model);

        // Try to find a descriptive name (title, name, or just ID)
        $descriptionName = $model->title ?? $model->name ?? $model->subject ?? ('ID ' . $model->id);

        // Create the log
        ActivityLog::create([
            'user_id'     => Auth::id() ?? null, // Who did it?
            'action'      => "$modelName $action", // e.g., "Event Updated"
            'description' => "$action $modelName: $descriptionName", // e.g., "Updated Event: Art Exhibition"
        ]);
    }
}