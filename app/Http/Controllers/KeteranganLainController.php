<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Models\KeteranganLain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KeteranganLainController extends Controller
{
    public function index(Request $request): View
    {
        $rows = KeteranganLain::query()
            ->when($request->kategori, fn ($q, $kategori) => $q->where('kategori', $kategori))
            ->latest('id')
            ->get();

        return view('keterangan-lain.index', compact('rows'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'         => ['required', 'date'],
            'judul'           => ['required', 'string', 'max:255'],
            'kategori'        => ['required', 'string', 'max:255'],
            'tipe_pajak'      => ['required', 'in:ppn,non-ppn'],
            'konten'          => ['nullable', 'string'],
            'berlaku_sampai'  => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        KeteranganLain::create([
            'nomor'          => NumberGenerator::generateRunningNumber('keterangan_lains', 'nomor', 'KL'),
            'tanggal'        => $data['tanggal'],
            'judul'          => $data['judul'],
            'kategori'       => $data['kategori'],
            'tipe_pajak'     => $data['tipe_pajak'],
            'konten'         => $data['konten'] ?? null,
            'berlaku_sampai' => $data['berlaku_sampai'] ?? null,
            'created_by'     => (int) auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Keterangan lain berhasil dibuat.');
    }

    public function update(Request $request, KeteranganLain $keteranganLain): RedirectResponse
    {
        $data = $request->validate([
            'tanggal'         => ['required', 'date'],
            'judul'           => ['required', 'string', 'max:255'],
            'kategori'        => ['required', 'string', 'max:255'],
            'tipe_pajak'      => ['required', 'in:ppn,non-ppn'],
            'konten'          => ['nullable', 'string'],
            'berlaku_sampai'  => ['nullable', 'date', 'after_or_equal:tanggal'],
        ]);

        $keteranganLain->update([
            'tanggal'        => $data['tanggal'],
            'judul'          => $data['judul'],
            'kategori'       => $data['kategori'],
            'tipe_pajak'     => $data['tipe_pajak'],
            'konten'         => $data['konten'] ?? null,
            'berlaku_sampai' => $data['berlaku_sampai'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Keterangan lain berhasil diperbarui.');
    }

    public function destroy(KeteranganLain $keteranganLain): RedirectResponse
    {
        $keteranganLain->delete();

        return redirect()->back()->with('success', 'Keterangan lain berhasil dihapus.');
    }
}
