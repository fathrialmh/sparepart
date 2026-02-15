@extends('layouts.app')

@section('title', 'Barang Masuk')
@section('page_title', 'Barang Masuk')
@section('use_toast', true)

@section('content')

{{-- ========== RIWAYAT BARANG MASUK ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-in-down text-primary"></i> Riwayat Barang Masuk
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                    data-bs-toggle="modal" data-bs-target="#modalBarangMasuk">
                <i class="bi bi-plus-lg"></i> Input Barang Masuk
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-box-arrow-in-down"></i>
                <h6>Belum ada transaksi barang masuk</h6>
                <p class="text-muted mb-2">Catat penerimaan barang pertama Anda.</p>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalBarangMasuk">
                    <i class="bi bi-plus-lg"></i> Input Barang Masuk
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:140px">Nomor</th>
                            <th class="text-center" style="width:120px">Tanggal</th>
                            <th class="text-start" style="min-width:160px">Supplier</th>
                            <th class="text-center" style="width:90px">Tipe</th>
                            <th class="text-end" style="min-width:140px">Total Nilai</th>
                            <th class="text-start" style="min-width:120px">Dibuat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td><span class="fw-semibold">{{ $row->nomor }}</span></td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                                <td>{{ $row->supplier->nama }}</td>
                                <td class="text-center">
                                    @if($row->tipe === 'lokal')
                                        <span class="badge badge-tipe badge-lokal">Lokal</span>
                                    @else
                                        <span class="badge badge-tipe badge-impor">Impor</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">@rupiah($row->total_nilai)</td>
                                <td><small class="text-muted">{{ $row->creator->nama }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========== MODAL INPUT BARANG MASUK ========== --}}
<div class="modal fade" id="modalBarangMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-down text-primary"></i>
                    <span>Input Barang Masuk</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('barang-masuk.store') }}" id="formBarangMasuk">
                @csrf
                <div class="modal-body">
                    {{-- Header --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Transaksi</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">— Pilih Supplier —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->nama }} ({{ $supplier->tipe }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipe</label>
                                <select name="tipe" class="form-select bm-tipe">
                                    <option value="lokal">Lokal</option>
                                    <option value="impor">Impor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan">
                            </div>
                        </div>
                    </div>

                    {{-- Impor fields --}}
                    <div class="form-section bm-impor-fields" style="display:none;">
                        <div class="form-section-title"><i class="bi bi-globe"></i> Data Impor</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nomor BC</label>
                                <input type="text" name="nomor_bc" class="form-control" placeholder="Nomor BC">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal BC</label>
                                <input type="date" name="tanggal_bc" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pelabuhan</label>
                                <input type="text" name="pelabuhan" class="form-control" placeholder="Pelabuhan masuk">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Negara Asal</label>
                                <input type="text" name="negara_asal" class="form-control" placeholder="Negara asal barang">
                            </div>
                        </div>
                    </div>

                    {{-- Detail barang --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> Detail Barang</span>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="bm-add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="bm-detail-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:250px">Barang</th>
                                        <th style="width:100px" class="text-center">Qty</th>
                                        <th style="width:160px" class="text-end">Harga Beli</th>
                                        <th style="width:60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="barang_id[]" class="form-select form-select-sm" required>
                                                <option value="">— Pilih Barang —</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}">{{ $barang->kode }} - {{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="qty[]" class="form-control form-control-sm text-center" min="1" value="1" required></td>
                                        <td><input type="number" name="harga_beli[]" class="form-control form-control-sm text-end" min="0" placeholder="0" required></td>
                                        <td class="text-center">
                                            <button class="btn btn-icon btn-outline-danger btn-sm remove-row" type="button" title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
    // Toast
    @if(session('success'))
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session("success") }}', showConfirmButton:false, timer:3000, timerProgressBar:true });
    @endif
    @if(session('error'))
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'{{ session("error") }}', showConfirmButton:false, timer:4000, timerProgressBar:true });
    @endif

    // Impor fields toggle
    const tipeSelect = document.querySelector('.bm-tipe');
    const imporFields = document.querySelector('.bm-impor-fields');
    const toggleImpor = () => { imporFields.style.display = tipeSelect.value === 'impor' ? 'block' : 'none'; };
    tipeSelect.addEventListener('change', toggleImpor);
    toggleImpor();

    // Detail rows
    const tbody = document.querySelector('#bm-detail-table tbody');
    const optionHtml = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;

    const bindRemove = () => {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function () { if (tbody.querySelectorAll('tr').length > 1) this.closest('tr').remove(); };
        });
    };

    document.getElementById('bm-add-row').addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><select name="barang_id[]" class="form-select form-select-sm" required><option value="">— Pilih Barang —</option>${optionHtml}</select></td><td><input type="number" name="qty[]" class="form-control form-control-sm text-center" min="1" value="1" required></td><td><input type="number" name="harga_beli[]" class="form-control form-control-sm text-end" min="0" placeholder="0" required></td><td class="text-center"><button class="btn btn-icon btn-outline-danger btn-sm remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
        tbody.appendChild(tr);
        bindRemove();
    });

    bindRemove();
});
</script>
@endpush
