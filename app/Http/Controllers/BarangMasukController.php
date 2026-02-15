<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreBarangMasukRequest;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BarangMasukController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama')->get();
        $rows = BarangMasuk::query()
            ->with(['supplier', 'creator'])
            ->latest('id')
            ->get();

        return view('barang-masuk.index', compact('suppliers', 'barangs', 'rows'));
    }

    public function store(StoreBarangMasukRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $header = BarangMasuk::create([
                    'nomor' => NumberGenerator::generateRunningNumber('barang_masuk', 'nomor', 'BM'),
                    'tanggal' => $data['tanggal'],
                    'supplier_id' => (int) $data['supplier_id'],
                    'tipe' => $data['tipe'],
                    'nomor_bc' => $data['tipe'] === 'impor' ? ($data['nomor_bc'] ?? null) : null,
                    'tanggal_bc' => $data['tipe'] === 'impor' ? ($data['tanggal_bc'] ?? null) : null,
                    'pelabuhan' => $data['tipe'] === 'impor' ? ($data['pelabuhan'] ?? null) : null,
                    'negara_asal' => $data['tipe'] === 'impor' ? ($data['negara_asal'] ?? null) : null,
                    'total_nilai' => 0,
                    'keterangan' => $data['keterangan'] ?? null,
                    'created_by' => (int) auth()->id(),
                ]);

                $totalNilai = 0;
                foreach ($data['barang_id'] as $index => $barangId) {
                    $qty = (int) ($data['qty'][$index] ?? 0);
                    $hargaBeli = (float) ($data['harga_beli'][$index] ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }

                    $subtotal = $qty * $hargaBeli;
                    $totalNilai += $subtotal;

                    BarangMasukDetail::create([
                        'barang_masuk_id' => $header->id,
                        'barang_id' => (int) $barangId,
                        'qty' => $qty,
                        'harga_beli' => $hargaBeli,
                        'subtotal' => $subtotal,
                    ]);

                    Barang::whereKey((int) $barangId)->update([
                        'stok' => DB::raw('stok + ' . $qty),
                        'harga_beli' => $hargaBeli,
                    ]);
                }

                $header->update(['total_nilai' => $totalNilai]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal simpan barang masuk: ' . $e->getMessage());
        }

        return redirect()->route('barang.index')->with('success', 'Transaksi barang masuk berhasil disimpan.');
    }
}
