<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $targetType, string $activity, string $details = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'employee_id' => session('active_employee_id'),
            'target_type' => $targetType,
            'activity' => $activity,
            'details' => $details,
        ]);
    }
}