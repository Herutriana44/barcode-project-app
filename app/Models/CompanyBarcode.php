<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'barcode_id',
        'scanned_by_employee_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scannedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'scanned_by_employee_id');
    }
}
