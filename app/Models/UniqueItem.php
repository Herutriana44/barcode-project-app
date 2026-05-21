<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniqueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'jenis',
        'qty',
        'status_keluar',
        'production_date',
        'expired_date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'status_keluar' => 'boolean',
        'production_date' => 'date',
        'expired_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
