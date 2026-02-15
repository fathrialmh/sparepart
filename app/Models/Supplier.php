<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'telepon',
        'email',
        'npwp',
        'tipe',
        'negara_asal',
    ];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class);
    }

    public function barangMasuk(): HasMany
    {
        return $this->hasMany(BarangMasuk::class);
    }
}
