<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with(['user', 'employee'])->latest()->paginate(20);
        return view('activity-logs.index', compact('logs'));
    }
}
