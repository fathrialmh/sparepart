<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $eligibleSuratJalan = SuratJalan::query()
            ->with('customer')
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->latest('id')
            ->get();

        $rows = Invoice::with(['suratJalan', 'customer'])->latest('id')->get();

        return view('invoice.index', compact('eligibleSuratJalan', 'rows'));
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $sj = SuratJalan::query()
            ->with(['customer', 'details'])
            ->whereKey((int) $data['surat_jalan_id'])
            ->where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->first();

        if (!$sj) {
            return back()->with('error', 'Surat jalan tidak valid atau invoice sudah dibuat.');
        }

        $subtotal = (float) $sj->details->sum('subtotal');
        $diskonPersen = (float) ($data['diskon_persen'] ?? 0);
        $diskonNilai = $subtotal * ($diskonPersen / 100);
        $afterDiskon = $subtotal - $diskonNilai;
        $ppn = $sj->tipe_pajak === 'kena_pajak' ? $afterDiskon * ((float) $sj->ppn_persen / 100) : 0;
        $ongkosKirim = (float) ($data['ongkos_kirim'] ?? 0);
        $dp = (float) ($data['dp'] ?? 0);
        $total = $afterDiskon + $ppn + $ongkosKirim;
        $sisa = max($total - $dp, 0);

        Invoice::create([
            'nomor' => NumberGenerator::generateRunningNumber('invoices', 'nomor', 'INV'),
            'tanggal' => $data['tanggal'],
            'surat_jalan_id' => $sj->id,
            'customer_id' => $sj->customer_id,
            'subtotal' => $subtotal,
            'diskon_persen' => $diskonPersen,
            'diskon_nilai' => $diskonNilai,
            'ppn' => $ppn,
            'ongkos_kirim' => $ongkosKirim,
            'total' => $total,
            'dp' => $dp,
            'sisa' => $sisa,
            'pembayaran' => $data['pembayaran'] ?? 'Transfer',
            'keterangan' => $data['keterangan'] ?? null,
            'created_by' => (int) auth()->id(),
        ]);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'suratJalan.details.barang']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'header' => $invoice,
            'details' => $invoice->suratJalan->details,
        ])->setPaper('a4');

        return $pdf->stream('invoice-' . $invoice->nomor . '.pdf');
    }
}
