<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReceiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'transfer_slip_no',
        'tanggal_terima_fg',
        'jumlah_box',
    ];

    protected $casts = [
        'tanggal_terima_fg' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function itemBarcodes()
    {
        return $this->hasMany(ItemBarcode::class);
    }
}
