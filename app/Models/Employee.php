<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'phone',
    ];

    public function itemsAsOperatorMobil()
    {
        return $this->hasMany(Item::class, 'operator_mobil_id');
    }

    public function itemsAsPengirim()
    {
        return $this->hasMany(Item::class, 'pengirim_id');
    }

    public function itemsAsOperatorForklift()
    {
        return $this->hasMany(Item::class, 'operator_forklift_id');
    }
}
