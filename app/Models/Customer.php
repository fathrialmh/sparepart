<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'telepon',
        'email',
        'npwp',
    ];

    public function suratJalan(): HasMany
    {
        return $this->hasMany(SuratJalan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
