<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceQuotation extends Model
{
    protected $fillable = [
        'nomor', 'tanggal', 'customer_id', 'tipe_pajak', 'ppn_persen',
        'status', 'catatan', 'subtotal', 'ppn', 'total', 'created_by',
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

    public function details(): HasMany
    {
        return $this->hasMany(PriceQuotationDetail::class);
    }
}
