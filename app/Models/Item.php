<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer',
        'part_name',
        'part_number',
        'model',
        'berat',
        'qty',
        'inspector_name',
        'tgl_produksi',
        'tgl_expired',
        'code',
        'posisi_rak',
        'tingkat',
        'ukuran_material',
        'jenis_bahan',
        'quantity_material',
        'no_surat_jalan_material',
        'tanggal_terima_material',
    ];

    protected $casts = [
        'tgl_produksi' => 'date',
        'tgl_expired' => 'date',
        'tanggal_terima_material' => 'date',
        'berat' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function itemReceivings()
    {
        return $this->hasMany(ItemReceiving::class);
    }

    public function itemBarcodes()
    {
        return $this->hasMany(ItemBarcode::class);
    }

    public function companyItems()
    {
        return $this->hasMany(CompanyItem::class);
    }
}
