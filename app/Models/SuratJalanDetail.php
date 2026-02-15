<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan_detail';

    public $timestamps = false;

    protected $fillable = [
        'surat_jalan_id',
        'barang_id',
        'qty',
        'harga',
        'subtotal',
    ];

    public function suratJalan(): BelongsTo
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
