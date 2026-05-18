<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenSpout\Writer\CSV\Writer;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with(['user', 'employee'])->latest()->paginate(20);
        return view('activity-logs.index', compact('logs'));
    }

    public function export(): StreamedResponse
    {
        $fileName = 'activity_logs_' . date('Y-m-d_His') . '.csv';

        $response = new StreamedResponse(function () {
            $writer = new Writer();
            $writer->openToFile('php://output');

            $writer->addRow(['NIP', 'Nama', 'Departemen', 'Jabatan', 'Di', 'Activity', 'Deskripsi', 'Tanggal']);

            ActivityLog::with(['user', 'employee'])->latest()->chunk(100, function ($logs) use ($writer) {
                foreach ($logs as $log) {
                    $writer->addRow([
                        $log->employee->nip ?? '-',
                        $log->employee->name ?? '-',
                        $log->employee->departemen ?? '-',
                        $log->employee->jabatan ?? '-',
                        $log->target_type,
                        $log->activity,
                        $log->details,
                        $log->created_at->format('d/m/Y H:i:s'),
                    ]);
                }
            });

            $writer->close();
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

        return $response;
    }
}
