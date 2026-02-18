<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $rows = PurchaseOrder::with('supplier')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->tipe, fn ($q, $tipe) => $q->where('tipe', $tipe))
            ->latest('id')
            ->get();

        $suppliers = Supplier::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama')->get();

        return view('purchase-order.index', compact('rows', 'suppliers', 'barangs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'       => ['required', 'date'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'tipe'          => ['required', 'in:lokal,impor'],
            'tipe_pajak'    => ['required', 'in:ppn,non-ppn'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'catatan'       => ['nullable', 'string'],
            'barang_id'     => ['required', 'array', 'min:1'],
            'barang_id.*'   => ['required', 'exists:barang,id'],
            'qty'           => ['required', 'array', 'min:1'],
            'qty.*'         => ['required', 'integer', 'min:1'],
            'harga'         => ['required', 'array', 'min:1'],
            'harga.*'       => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $subtotal = 0;
                $details = [];

                foreach ($data['barang_id'] as $i => $barangId) {
                    $qty = (int) $data['qty'][$i];
                    $harga = (float) $data['harga'][$i];
                    $lineTotal = $qty * $harga;
                    $subtotal += $lineTotal;

                    $details[] = [
                        'barang_id' => (int) $barangId,
                        'qty'       => $qty,
                        'harga'     => $harga,
                        'subtotal'  => $lineTotal,
                    ];
                }

                $ppnPersen = $data['tipe_pajak'] === 'ppn' ? 11 : 0;
                $ppn = $subtotal * ($ppnPersen / 100);
                $total = $subtotal + $ppn;

                $header = PurchaseOrder::create([
                    'nomor'         => NumberGenerator::generateRunningNumber('purchase_orders', 'nomor', 'PO'),
                    'tanggal'       => $data['tanggal'],
                    'supplier_id'   => (int) $data['supplier_id'],
                    'tipe'          => $data['tipe'],
                    'tipe_pajak'    => $data['tipe_pajak'],
                    'ppn_persen'    => $ppnPersen,
                    'expected_date' => $data['expected_date'] ?? null,
                    'catatan'       => $data['catatan'] ?? null,
                    'subtotal'      => $subtotal,
                    'ppn'           => $ppn,
                    'total'         => $total,
                    'status'        => 'pending',
                    'created_by'    => (int) auth()->id(),
                ]);

                foreach ($details as $detail) {
                    $detail['purchase_order_id'] = $header->id;
                    PurchaseOrderDetail::create($detail);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat purchase order: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Purchase order berhasil dibuat.');
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'       => ['required', 'date'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'tipe'          => ['required', 'in:lokal,impor'],
            'tipe_pajak'    => ['required', 'in:ppn,non-ppn'],
            'expected_date' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'catatan'       => ['nullable', 'string'],
            'barang_id'     => ['required', 'array', 'min:1'],
            'barang_id.*'   => ['required', 'exists:barang,id'],
            'qty'           => ['required', 'array', 'min:1'],
            'qty.*'         => ['required', 'integer', 'min:1'],
            'harga'         => ['required', 'array', 'min:1'],
            'harga.*'       => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data, $purchaseOrder) {
                $subtotal = 0;
                $details = [];

                foreach ($data['barang_id'] as $i => $barangId) {
                    $qty = (int) $data['qty'][$i];
                    $harga = (float) $data['harga'][$i];
                    $lineTotal = $qty * $harga;
                    $subtotal += $lineTotal;

                    $details[] = [
                        'barang_id' => (int) $barangId,
                        'qty'       => $qty,
                        'harga'     => $harga,
                        'subtotal'  => $lineTotal,
                    ];
                }

                $ppnPersen = $data['tipe_pajak'] === 'ppn' ? 11 : 0;
                $ppn = $subtotal * ($ppnPersen / 100);
                $total = $subtotal + $ppn;

                $purchaseOrder->update([
                    'tanggal'       => $data['tanggal'],
                    'supplier_id'   => (int) $data['supplier_id'],
                    'tipe'          => $data['tipe'],
                    'tipe_pajak'    => $data['tipe_pajak'],
                    'ppn_persen'    => $ppnPersen,
                    'expected_date' => $data['expected_date'] ?? null,
                    'catatan'       => $data['catatan'] ?? null,
                    'subtotal'      => $subtotal,
                    'ppn'           => $ppn,
                    'total'         => $total,
                ]);

                $purchaseOrder->details()->delete();

                foreach ($details as $detail) {
                    $detail['purchase_order_id'] = $purchaseOrder->id;
                    PurchaseOrderDetail::create($detail);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui purchase order: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Purchase order berhasil diperbarui.');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,partial,completed,cancelled'],
        ]);

        $purchaseOrder->update(['status' => $data['status']]);

        return redirect()->back()->with('success', 'Status purchase order berhasil diperbarui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $purchaseOrder->details()->delete();
        $purchaseOrder->delete();

        return redirect()->back()->with('success', 'Purchase order berhasil dihapus.');
    }
}
