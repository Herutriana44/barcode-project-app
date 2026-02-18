<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'item_id',
        'qty',
        'posisi_rak',
        'tingkat',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
