<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'nomor', 'tanggal', 'supplier_id', 'tipe', 'tipe_pajak', 'ppn_persen',
        'status', 'expected_date', 'catatan', 'subtotal', 'ppn', 'total',
        'payment_status', 'payment_amount', 'payment_date', 'due_date', 'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'expected_date' => 'date',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
}
