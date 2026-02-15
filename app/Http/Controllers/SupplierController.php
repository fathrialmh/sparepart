<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $editData = request('edit') ? Supplier::find((int) request('edit')) : null;
        $rows = Supplier::latest('id')->get();

        return view('supplier.index', compact('rows', 'editData'));
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (($data['tipe'] ?? 'lokal') === 'lokal') {
            $data['negara_asal'] = null;
        }

        Supplier::create($data + [
            'kode' => NumberGenerator::generateMasterCode('suppliers', 'kode', 'SUP'),
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = $request->validated();
        if (($data['tipe'] ?? 'lokal') === 'lokal') {
            $data['negara_asal'] = null;
        }

        $supplier->update($data);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
