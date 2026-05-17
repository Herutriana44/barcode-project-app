<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniqueItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'qty',
        'status_keluar',
    ];

    protected $casts = [
        'qty' => 'integer',
        'status_keluar' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
