<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenSpout\Writer\CSV\Writer;
use OpenSpout\Common\Entity\Row;

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

            $header = ['NIP', 'Nama', 'Departemen', 'Jabatan', 'Di', 'Activity', 'Deskripsi', 'Tanggal'];
            $writer->addRow(Row::fromValues($header));

            ActivityLog::with(['user', 'employee'])->latest()->chunk(100, function ($logs) use ($writer) {
                foreach ($logs as $log) {
                    $writer->addRow(Row::fromValues([
                        (string) ($log->employee->nip ?? '-'),
                        (string) ($log->employee->name ?? '-'),
                        (string) ($log->employee->departemen ?? '-'),
                        (string) ($log->employee->jabatan ?? '-'),
                        (string) $log->target_type,
                        (string) $log->activity,
                        (string) $log->details,
                        (string) $log->created_at->format('d/m/Y H:i:s'),
                    ]));
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
