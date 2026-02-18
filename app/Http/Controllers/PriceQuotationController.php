<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\PriceQuotation;
use App\Models\PriceQuotationDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PriceQuotationController extends Controller
{
    public function index(Request $request): View
    {
        $rows = PriceQuotation::with('customer')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('id')
            ->get();

        $customers = Customer::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama')->get();

        return view('price-quotation.index', compact('rows', 'customers', 'barangs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'customer_id'  => ['required', 'exists:customers,id'],
            'tipe_pajak'   => ['required', 'in:ppn,non-ppn'],
            'catatan'      => ['nullable', 'string'],
            'barang_id'    => ['required', 'array', 'min:1'],
            'barang_id.*'  => ['required', 'exists:barang,id'],
            'qty'          => ['required', 'array', 'min:1'],
            'qty.*'        => ['required', 'integer', 'min:1'],
            'harga'        => ['required', 'array', 'min:1'],
            'harga.*'      => ['required', 'numeric', 'min:0'],
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

                $header = PriceQuotation::create([
                    'nomor'       => NumberGenerator::generateRunningNumber('price_quotations', 'nomor', 'QUO'),
                    'tanggal'     => $data['tanggal'],
                    'customer_id' => (int) $data['customer_id'],
                    'tipe_pajak'  => $data['tipe_pajak'],
                    'ppn_persen'  => $ppnPersen,
                    'catatan'     => $data['catatan'] ?? null,
                    'subtotal'    => $subtotal,
                    'ppn'         => $ppn,
                    'total'       => $total,
                    'status'      => 'draft',
                    'created_by'  => (int) auth()->id(),
                ]);

                foreach ($details as $detail) {
                    $detail['price_quotation_id'] = $header->id;
                    PriceQuotationDetail::create($detail);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat penawaran harga: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Penawaran harga berhasil dibuat.');
    }

    public function update(Request $request, PriceQuotation $priceQuotation): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'      => ['required', 'date'],
            'customer_id'  => ['required', 'exists:customers,id'],
            'tipe_pajak'   => ['required', 'in:ppn,non-ppn'],
            'catatan'      => ['nullable', 'string'],
            'barang_id'    => ['required', 'array', 'min:1'],
            'barang_id.*'  => ['required', 'exists:barang,id'],
            'qty'          => ['required', 'array', 'min:1'],
            'qty.*'        => ['required', 'integer', 'min:1'],
            'harga'        => ['required', 'array', 'min:1'],
            'harga.*'      => ['required', 'numeric', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($data, $priceQuotation) {
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

                $priceQuotation->update([
                    'tanggal'     => $data['tanggal'],
                    'customer_id' => (int) $data['customer_id'],
                    'tipe_pajak'  => $data['tipe_pajak'],
                    'ppn_persen'  => $ppnPersen,
                    'catatan'     => $data['catatan'] ?? null,
                    'subtotal'    => $subtotal,
                    'ppn'         => $ppn,
                    'total'       => $total,
                ]);

                $priceQuotation->details()->delete();

                foreach ($details as $detail) {
                    $detail['price_quotation_id'] = $priceQuotation->id;
                    PriceQuotationDetail::create($detail);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memperbarui penawaran harga: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Penawaran harga berhasil diperbarui.');
    }

    public function destroy(PriceQuotation $priceQuotation): RedirectResponse
    {
        $priceQuotation->details()->delete();
        $priceQuotation->delete();

        return redirect()->back()->with('success', 'Penawaran harga berhasil dihapus.');
    }
}
