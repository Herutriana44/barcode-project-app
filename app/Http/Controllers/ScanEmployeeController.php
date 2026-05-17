<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeScanSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScanEmployeeController extends Controller
{
    /**
     * Halaman scan karyawan — input manual atau kamera.
     */
    public function index()
    {
        $activeEmployee = null;
        if (session()->has('active_employee_id')) {
            $activeEmployee = Employee::find(session('active_employee_id'));
        }

        return view('scan-employee.index', compact('activeEmployee'));
    }

    /**
     * Proses scan badge karyawan (EMP-{nip} atau NIP langsung).
     * Set session aktif dan catat log.
     */
    public function store(Request $request)
    {
        $request->validate([
            'badge_code' => 'required|string|max:128',
        ]);

        $raw = trim($request->input('badge_code'));

        // Terima format EMP-{nip} atau NIP langsung
        $nip = str_starts_with($raw, 'EMP-') ? substr($raw, 4) : $raw;

        $employee = Employee::where('nip', $nip)->first();

        if (! $employee) {
            return back()->withInput()->with('error', 'Karyawan dengan NIP "' . $nip . '" tidak ditemukan.');
        }

        $scannedAt = Carbon::now();

        // Catat log sesi scan
        EmployeeScanSession::create([
            'employee_id' => $employee->id,
            'scanned_at'  => $scannedAt,
        ]);

        // Set session aktif
        session([
            'active_employee_id'   => $employee->id,
            'active_employee_name' => $employee->name,
            'active_employee_nip'  => $employee->nip,
            'active_employee_scanned_at' => $scannedAt->toDateTimeString(),
        ]);

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang, ' . $employee->name . '! Sesi aktif sejak ' . $scannedAt->format('d/m/Y H:i') . '.');
    }

    /**
     * Hapus sesi karyawan aktif (logout karyawan, bukan logout user).
     */
    public function destroy()
    {
        session()->forget([
            'active_employee_id',
            'active_employee_name',
            'active_employee_nip',
            'active_employee_scanned_at',
        ]);

        return redirect()->route('scan-employee.index')
            ->with('success', 'Sesi karyawan telah diakhiri.');
    }
}
