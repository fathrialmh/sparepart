<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceQuotationDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'price_quotation_id', 'barang_id', 'qty', 'harga', 'subtotal',
    ];

    public function priceQuotation(): BelongsTo
    {
        return $this->belongsTo(PriceQuotation::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
