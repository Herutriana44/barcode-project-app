<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'operator_mobil_id',
        'pengirim_id',
        'operator_forklift_id',
        'scanned_by_employee_id',
        'customer',
        'part_name',
        'part_number',
        'model',
        'berat',
        'qty',
        'static_qty',
        'dynamic_qty',
        'qty_sub_pack',
        'berat_packaging_gram',
        'berat_per_pcs_gram',
        'inspector_name',
        'checker_name',
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
        'static_qty' => 'integer',
        'dynamic_qty' => 'integer',
        'qty_sub_pack' => 'integer',
        'berat_packaging_gram' => 'integer',
        'berat_per_pcs_gram' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function operatorMobil()
    {
        return $this->belongsTo(Employee::class, 'operator_mobil_id');
    }

    public function pengirim()
    {
        return $this->belongsTo(Employee::class, 'pengirim_id');
    }

    public function operatorForklift()
    {
        return $this->belongsTo(Employee::class, 'operator_forklift_id');
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

    public function uniqueItems()
    {
        return $this->hasMany(UniqueItem::class);
    }

    public function scannedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'scanned_by_employee_id');
    }
}
