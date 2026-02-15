<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(): View
    {
        $filterTipe = request('tipe');
        $editData = request('edit') ? Barang::find((int) request('edit')) : null;
        $supplierRows = Supplier::orderBy('nama')->get();
        $barangs = Barang::orderBy('nama')->get();
        $barangMasukRows = BarangMasuk::query()
            ->with(['supplier', 'creator'])
            ->latest('id')
            ->get();

        $rows = Barang::query()
            ->with('supplier')
            ->when(in_array($filterTipe, ['lokal', 'impor'], true), fn ($q) => $q->where('tipe', $filterTipe))
            ->latest('id')
            ->get();

        return view('barang.index', compact(
            'rows',
            'supplierRows',
            'editData',
            'filterTipe',
            'barangs',
            'barangMasukRows'
        ));
    }

    public function store(StoreBarangRequest $request): RedirectResponse
    {
        Barang::create($request->validated() + [
            'kode' => NumberGenerator::generateMasterCode('barang', 'kode', 'BRG'),
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $barang->update($request->validated());

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
    }
}
