<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreSuratJalanRequest;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuratJalanController extends Controller
{
    public function index(): View
    {
        $customers = Customer::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama')->get();
        $rows = SuratJalan::with('customer')->latest('id')->get();

        return view('surat-jalan.index', compact('customers', 'barangs', 'rows'));
    }

    public function store(StoreSuratJalanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $header = SuratJalan::create([
                    'nomor' => NumberGenerator::generateRunningNumber('surat_jalan', 'nomor', 'SJ'),
                    'tanggal' => $data['tanggal'],
                    'customer_id' => (int) $data['customer_id'],
                    'alamat_kirim' => $data['alamat_kirim'] ?? null,
                    'no_po' => $data['no_po'] ?? null,
                    'no_polisi' => $data['no_polisi'] ?? null,
                    'sopir' => $data['sopir'] ?? null,
                    'tipe_pajak' => $data['tipe_pajak'],
                    'ppn_persen' => $data['tipe_pajak'] === 'kena_pajak' ? 11 : 0,
                    'status' => 'pending',
                    'pembayaran' => $data['pembayaran'] ?? 'C.O.D',
                    'keterangan' => $data['keterangan'] ?? null,
                    'created_by' => (int) auth()->id(),
                ]);

                foreach ($data['barang_id'] as $index => $barangId) {
                    $qty = (int) ($data['qty'][$index] ?? 0);
                    $hargaLine = (float) ($data['harga'][$index] ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }

                    $barang = Barang::whereKey((int) $barangId)->lockForUpdate()->first();
                    if (!$barang || $barang->stok < $qty) {
                        throw new \RuntimeException('Stok barang tidak cukup.');
                    }

                    $subtotal = $hargaLine;
                    $harga = $qty > 0 ? ($hargaLine / $qty) : 0;
                    SuratJalanDetail::create([
                        'surat_jalan_id' => $header->id,
                        'barang_id' => (int) $barangId,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ]);

                    $barang->decrement('stok', $qty);
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat surat jalan: ' . $e->getMessage());
        }

        return redirect()->route('surat-jalan.index')->with('success', 'Surat jalan berhasil dibuat dan stok barang berkurang.');
    }

    public function updateStatus(Request $request, SuratJalan $suratJalan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $suratJalan->update(['status' => $validated['status']]);

        return back()->with('success', 'Status surat jalan diperbarui.');
    }

    public function print(SuratJalan $suratJalan)
    {
        $suratJalan->load(['customer', 'details.barang']);

        $pdf = Pdf::loadView('pdf.surat-jalan', [
            'header' => $suratJalan,
            'details' => $suratJalan->details,
        ])->setPaper('a4');

        return $pdf->stream('surat-jalan-' . $suratJalan->nomor . '.pdf');
    }
}
