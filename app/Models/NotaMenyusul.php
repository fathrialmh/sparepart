<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaMenyusul extends Model
{
    protected $fillable = [
        'nomor', 'tanggal', 'customer_id', 'tipe_pajak', 'judul',
        'konten', 'referensi_dokumen', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
