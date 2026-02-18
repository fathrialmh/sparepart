<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Models\Customer;
use App\Models\NotaMenyusul;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotaMenyusulController extends Controller
{
    public function index(Request $request): View
    {
        $rows = NotaMenyusul::with('customer')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest('id')
            ->get();

        $customers = Customer::orderBy('nama')->get();

        return view('nota-menyusul.index', compact('rows', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'             => ['required', 'date'],
            'customer_id'         => ['required', 'exists:customers,id'],
            'tipe_pajak'          => ['required', 'in:ppn,non-ppn'],
            'judul'               => ['required', 'string', 'max:255'],
            'konten'              => ['nullable', 'string'],
            'referensi_dokumen'   => ['nullable', 'string', 'max:255'],
        ]);

        NotaMenyusul::create([
            'nomor'              => NumberGenerator::generateRunningNumber('nota_menyusuls', 'nomor', 'NM'),
            'tanggal'            => $data['tanggal'],
            'customer_id'        => (int) $data['customer_id'],
            'tipe_pajak'         => $data['tipe_pajak'],
            'judul'              => $data['judul'],
            'konten'             => $data['konten'] ?? null,
            'referensi_dokumen'  => $data['referensi_dokumen'] ?? null,
            'status'             => 'draft',
            'created_by'         => (int) auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Nota menyusul berhasil dibuat.');
    }

    public function update(Request $request, NotaMenyusul $notaMenyusul): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'             => ['required', 'date'],
            'customer_id'         => ['required', 'exists:customers,id'],
            'tipe_pajak'          => ['required', 'in:ppn,non-ppn'],
            'judul'               => ['required', 'string', 'max:255'],
            'konten'              => ['nullable', 'string'],
            'referensi_dokumen'   => ['nullable', 'string', 'max:255'],
        ]);

        $notaMenyusul->update([
            'tanggal'            => $data['tanggal'],
            'customer_id'        => (int) $data['customer_id'],
            'tipe_pajak'         => $data['tipe_pajak'],
            'judul'              => $data['judul'],
            'konten'             => $data['konten'] ?? null,
            'referensi_dokumen'  => $data['referensi_dokumen'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Nota menyusul berhasil diperbarui.');
    }

    public function destroy(NotaMenyusul $notaMenyusul): RedirectResponse
    {
        $notaMenyusul->delete();

        return redirect()->back()->with('success', 'Nota menyusul berhasil dihapus.');
    }
}
