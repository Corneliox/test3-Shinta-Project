<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
        {
            // 1. Fetch logs (latest first)
            // We use get() instead of paginate() because grouping breaks pagination easily.
            // If you have thousands of logs, we might need a different approach later.
            $logs = ActivityLog::with('user')->latest()->get();

            // 2. Group them by Month and Year (e.g., "November 2025")
            $groupedLogs = $logs->groupBy(function($date) {
                return $date->created_at->format('F Y');
            });

            return view('admin.activity_logs.index', compact('groupedLogs'));
        }
}