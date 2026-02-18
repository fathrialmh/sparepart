<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeteranganLain extends Model
{
    protected $fillable = [
        'nomor', 'tanggal', 'judul', 'kategori', 'tipe_pajak',
        'konten', 'berlaku_sampai', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
