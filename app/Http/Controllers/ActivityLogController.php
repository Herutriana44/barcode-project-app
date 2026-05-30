<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with(['user', 'employee'])->latest()->paginate(20);
        return view('activity-logs.index', compact('logs'));
    }

    public function edit(ActivityLog $activityLog): View
    {
        return view('activity-logs.edit', compact('activityLog'));
    }

    public function update(Request $request, ActivityLog $activityLog)
    {
        $validated = $request->validate([
            'details' => 'nullable|string',
        ]);

        $activityLog->update($validated);

        return redirect()->route('activity-logs.index')->with('success', 'Log aktivitas berhasil diperbarui.');
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return redirect()->route('activity-logs.index')->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activity_logs,id',
        ]);

        ActivityLog::whereIn('id', $request->ids)->delete();

        return redirect()->route('activity-logs.index')->with('success', 'Log aktivitas yang dipilih berhasil dihapus.');
    }

    public function export(): StreamedResponse
    {
        $fileName = 'activity_logs_' . date('Y-m-d_His') . '.xlsx';

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
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);

        return $response;
    }
}
