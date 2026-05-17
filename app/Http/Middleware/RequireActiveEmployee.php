<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan ada karyawan aktif di session sebelum mengakses fitur utama.
 * Karyawan aktif di-set saat scan badge EMP-{nip}.
 */
class RequireActiveEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('active_employee_id')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Scan karyawan terlebih dahulu.'], 403);
            }

            return redirect()->route('scan-employee.index')
                ->with('warning', 'Silakan scan badge karyawan terlebih dahulu untuk melanjutkan.');
        }

        return $next($request);
    }
}
