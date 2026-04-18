<?php

namespace App\Support;

use App\Models\Employee;

final class EmployeeUrl
{
    /**
     * URL absolut ke halaman profil karyawan (untuk QR / barcode di ID card).
     */
    public static function forProfile(Employee $employee): string
    {
        return route('employees.show', ['employee' => $employee->getRouteKey()], absolute: true);
    }
}
