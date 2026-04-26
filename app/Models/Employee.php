<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'departemen',
        'jabatan',
        'photo_path',
    ];

    public function getRouteKeyName(): string
    {
        return 'nip';
    }

    public function photoPublicUrl(): ?string
    {
        if ($this->photo_path === null || $this->photo_path === '') {
            return null;
        }

        // Avoid relying on `public/storage` symlink (storage:link).
        // Always serve photo via an authenticated route.
        return route('employees.photo', $this);
    }

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
