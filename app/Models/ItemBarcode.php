<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_receiving_id',
        'barcode_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function itemReceiving()
    {
        return $this->belongsTo(ItemReceiving::class);
    }
}
