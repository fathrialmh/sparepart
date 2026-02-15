<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'tanggal',
        'surat_jalan_id',
        'customer_id',
        'subtotal',
        'diskon_persen',
        'diskon_nilai',
        'ppn',
        'ongkos_kirim',
        'total',
        'dp',
        'sisa',
        'pembayaran',
        'keterangan',
        'created_by',
    ];

    public function suratJalan(): BelongsTo
    {
        return $this->belongsTo(SuratJalan::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
