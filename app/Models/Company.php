<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function companyItems()
    {
        return $this->hasMany(CompanyItem::class);
    }

    public function companyBarcodes()
    {
        return $this->hasMany(CompanyBarcode::class);
    }
}
