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
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
