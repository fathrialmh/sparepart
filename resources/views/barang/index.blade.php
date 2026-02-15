@extends('layouts.app')

@section('title', 'Master Barang')
@section('page_title', 'Master Barang')
@section('use_toast', true)

@section('content')

{{-- ========== DAFTAR BARANG ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-list-ul text-primary"></i> Daftar Barang
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                {{-- Filter Tipe --}}
                <form method="get" class="filter-bar">
                    <select name="tipe" class="form-select form-select-sm filter-select" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="lokal" @selected($filterTipe === 'lokal')>Lokal</option>
                        <option value="impor" @selected($filterTipe === 'impor')>Impor</option>
                    </select>
                </form>
                {{-- Tombol Tambah --}}
                <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                        data-bs-toggle="modal" data-bs-target="#modalBarang">
                    <i class="bi bi-plus-lg"></i> Tambah Barang
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            {{-- Empty State --}}
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h6>Belum ada data barang</h6>
                <p class="text-muted mb-2">Tambahkan barang pertama Anda.</p>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalBarang">
                    <i class="bi bi-plus-lg"></i> Tambah Barang
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable" id="tableBarang" data-search-placeholder="Cari nama / kode barang...">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:200px">Barang</th>
                            <th class="text-center" style="width:90px">Tipe</th>
                            <th class="text-center" style="width:80px">Satuan</th>
                            <th class="text-center" style="width:80px">Stok</th>
                            <th class="text-end" style="min-width:130px">Harga Beli</th>
                            <th class="text-end" style="min-width:130px">Harga Jual</th>
                            <th class="text-start" style="min-width:140px">Supplier</th>
                            <th class="text-center" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr id="row-{{ $row->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $row->nama }}</div>
                                    <small class="text-muted">{{ $row->kode }}</small>
                                </td>
                                <td class="text-center">
                                    @if($row->tipe === 'lokal')
                                        <span class="badge badge-tipe badge-lokal">Lokal</span>
                                    @else
                                        <span class="badge badge-tipe badge-impor">Impor</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $row->satuan }}</td>
                                <td class="text-center">
                                    @if($row->stok <= 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger">{{ $row->stok }}</span>
                                    @elseif($row->stok <= 5)
                                        <span class="badge bg-warning bg-opacity-10 text-warning">{{ $row->stok }}</span>
                                    @else
                                        <span class="fw-medium">{{ $row->stok }}</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">@rupiah($row->harga_beli)</td>
                                <td class="text-end font-monospace">@rupiah($row->harga_jual)</td>
                                <td>{{ $row->supplier->nama ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                                class="btn btn-icon btn-outline-warning btn-sm btn-edit"
                                                title="Edit"
                                                data-id="{{ $row->id }}"
                                                data-nama="{{ $row->nama }}"
                                                data-tipe="{{ $row->tipe }}"
                                                data-satuan="{{ $row->satuan }}"
                                                data-harga_beli="{{ $row->harga_beli }}"
                                                data-harga_jual="{{ $row->harga_jual }}"
                                                data-stok="{{ $row->stok }}"
                                                data-supplier_id="{{ $row->supplier_id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="post" action="{{ route('barang.destroy', $row) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-icon btn-outline-danger btn-sm btn-delete"
                                                    title="Hapus">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========== RIWAYAT BARANG MASUK ========== --}}
<div class="card shadow-sm mt-3">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-in-down text-primary"></i> Riwayat Barang Masuk
                <span class="badge bg-secondary rounded-pill">{{ $barangMasukRows->count() }}</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambahBarangMasuk"
                    data-bs-toggle="modal" data-bs-target="#modalBarangMasuk">
                <i class="bi bi-plus-lg"></i> Input Barang Masuk
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($barangMasukRows->isEmpty())
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
                <table class="table table-hover align-middle mb-0 datatable" data-search-placeholder="Cari nomor / supplier / user...">
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
                        @foreach($barangMasukRows as $bmRow)
                            <tr>
                                <td><span class="fw-semibold">{{ $bmRow->nomor }}</span></td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($bmRow->tanggal)->format('d M Y') }}</td>
                                <td>{{ $bmRow->supplier->nama }}</td>
                                <td class="text-center">
                                    @if($bmRow->tipe === 'lokal')
                                        <span class="badge badge-tipe badge-lokal">Lokal</span>
                                    @else
                                        <span class="badge badge-tipe badge-impor">Impor</span>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">@rupiah($bmRow->total_nilai)</td>
                                <td><small class="text-muted">{{ $bmRow->creator->nama }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ========== MODAL TAMBAH / EDIT BARANG ========== --}}
<div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="modalBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalBarangLabel">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah Barang Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formBarang" method="post" action="{{ route('barang.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">

                    {{-- Section: Informasi Utama --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-box-seam"></i> Informasi Utama
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                       name="nama" id="inputNama" placeholder="Masukkan nama barang"
                                       value="{{ old('nama') }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select @error('tipe') is-invalid @enderror" name="tipe" id="inputTipe">
                                    <option value="lokal" @selected(old('tipe', 'lokal') === 'lokal')>Lokal</option>
                                    <option value="impor" @selected(old('tipe') === 'impor')>Impor</option>
                                </select>
                                @error('tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror"
                                       name="satuan" id="inputSatuan" placeholder="cth: PCS, KG, SET"
                                       value="{{ old('satuan', 'PCS') }}" required>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Section: Harga --}}
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-tag"></i> Harga
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_beli') is-invalid @enderror"
                                           name="harga_beli" id="inputHargaBeli" placeholder="Masukkan harga beli" min="0"
                                           value="{{ old('harga_beli') }}" required>
                                    @error('harga_beli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('harga_jual') is-invalid @enderror"
                                           name="harga_jual" id="inputHargaJual" placeholder="Masukkan harga jual" min="0"
                                           value="{{ old('harga_jual') }}" required>
                                    @error('harga_jual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Stok & Supplier --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title">
                            <i class="bi bi-truck"></i> Stok & Supplier
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" id="labelStok">Stok Awal</label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror"
                                       name="stok" id="inputStok" placeholder="Masukkan jumlah stok" min="0"
                                       value="{{ old('stok') }}">
                                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror"
                                        name="supplier_id" id="inputSupplierId">
                                    <option value="">— Pilih Supplier —</option>
                                    @foreach($supplierRows as $sup)
                                        <option value="{{ $sup->id }}"
                                            @selected((int) old('supplier_id') === $sup->id)>
                                            {{ $sup->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="bi bi-save" id="btnSimpanIcon"></i>
                        <span id="btnSimpanText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
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
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Transaksi</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">— Pilih Supplier —</option>
                                    @foreach($supplierRows as $supplier)
                                        <option value="{{ $supplier->id }}" @selected((int) old('supplier_id') === $supplier->id)>
                                            {{ $supplier->nama }} ({{ $supplier->tipe }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipe</label>
                                <select name="tipe" class="form-select bm-tipe">
                                    <option value="lokal" @selected(old('tipe', 'lokal') === 'lokal')>Lokal</option>
                                    <option value="impor" @selected(old('tipe') === 'impor')>Impor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan" value="{{ old('keterangan') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-section bm-impor-fields" style="display:none;">
                        <div class="form-section-title"><i class="bi bi-globe"></i> Data Impor</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nomor BC</label>
                                <input type="text" name="nomor_bc" class="form-control" placeholder="Nomor BC" value="{{ old('nomor_bc') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal BC</label>
                                <input type="date" name="tanggal_bc" class="form-control" value="{{ old('tanggal_bc') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pelabuhan</label>
                                <input type="text" name="pelabuhan" class="form-control" placeholder="Pelabuhan masuk" value="{{ old('pelabuhan') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Negara Asal</label>
                                <input type="text" name="negara_asal" class="form-control" placeholder="Negara asal barang" value="{{ old('negara_asal') }}">
                            </div>
                        </div>
                    </div>

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
    const modal        = document.getElementById('modalBarang');
    const bsModal      = new bootstrap.Modal(modal);
    const form         = document.getElementById('formBarang');
    const formMethod   = document.getElementById('formMethod');
    const modalIcon    = document.getElementById('modalIcon');
    const modalTitle   = document.getElementById('modalTitleText');
    const btnIcon      = document.getElementById('btnSimpanIcon');
    const btnText      = document.getElementById('btnSimpanText');
    const btnSimpan    = document.getElementById('btnSimpan');
    const labelStok    = document.getElementById('labelStok');
    const storeUrl     = '{{ route("barang.store") }}';
    const updateUrlTpl = '{{ route("barang.update", ":id") }}';
    const bmModalEl    = document.getElementById('modalBarangMasuk');
    const bmModal      = bmModalEl ? new bootstrap.Modal(bmModalEl) : null;

    // ---- Reset modal to "Tambah" mode ----
    function resetModal() {
        form.action     = storeUrl;
        formMethod.value = 'POST';
        modalIcon.className  = 'bi bi-plus-circle text-primary';
        modalTitle.textContent = 'Tambah Barang Baru';
        btnIcon.className = 'bi bi-save';
        btnText.textContent = 'Simpan';
        labelStok.textContent = 'Stok Awal';

        // Clear all inputs
        form.querySelectorAll('input:not([type=hidden]), select').forEach(el => {
            if (el.name === 'satuan') { el.value = 'PCS'; }
            else if (el.name === 'tipe') { el.value = 'lokal'; }
            else if (el.name === 'supplier_id') { el.value = ''; }
            else if (el.tagName === 'SELECT') { el.selectedIndex = 0; }
            else { el.value = ''; }
        });

        // Clear validation states
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        checkFormValidity();
    }

    // ---- Set modal to "Edit" mode ----
    function setEditMode(data) {
        form.action      = updateUrlTpl.replace(':id', data.id);
        formMethod.value = 'PUT';
        modalIcon.className  = 'bi bi-pencil-square text-warning';
        modalTitle.textContent = 'Edit Barang';
        btnIcon.className = 'bi bi-check-lg';
        btnText.textContent = 'Update';
        labelStok.textContent = 'Stok';

        document.getElementById('inputNama').value       = data.nama;
        document.getElementById('inputTipe').value       = data.tipe;
        document.getElementById('inputSatuan').value     = data.satuan;
        document.getElementById('inputHargaBeli').value  = data.harga_beli;
        document.getElementById('inputHargaJual').value  = data.harga_jual;
        document.getElementById('inputStok').value       = data.stok;
        document.getElementById('inputSupplierId').value = data.supplier_id || '';

        checkFormValidity();
    }

    // ---- "Tambah" button resets the modal ----
    document.getElementById('btnTambah')?.addEventListener('click', resetModal);

    // Also reset when empty-state button is clicked
    document.querySelectorAll('[data-bs-target="#modalBarang"]:not(#btnTambah):not(.btn-edit)').forEach(btn => {
        btn.addEventListener('click', resetModal);
    });

    // ---- "Edit" button populates modal via data attributes ----
    $(document).on('click', '.btn-edit', function () {
        const btn = $(this);
        setEditMode({
            id:          btn.data('id'),
            nama:        btn.data('nama'),
            tipe:        btn.data('tipe'),
            satuan:      btn.data('satuan'),
            harga_beli:  btn.data('harga_beli'),
            harga_jual:  btn.data('harga_jual'),
            stok:        btn.data('stok'),
            supplier_id: btn.data('supplier_id'),
        });
        bsModal.show();
    });

    // ---- When modal closes, reset to "Tambah" mode ----
    modal.addEventListener('hidden.bs.modal', resetModal);

    // ---- Form validation: disable submit until valid ----
    function checkFormValidity() {
        if (form && btnSimpan) {
            btnSimpan.disabled = !form.checkValidity();
        }
    }

    form.addEventListener('input', checkFormValidity);
    checkFormValidity();

    // ---- Toast notifications ----
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    @endif

    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    @endif

    // ---- Highlight newly added/updated row ----
    @if(session('success'))
        const table = document.getElementById('tableBarang');
        if (table) {
            const firstRow = table.querySelector('tbody tr:first-child');
            if (firstRow) {
                firstRow.classList.add('row-highlight');
                firstRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => firstRow.classList.remove('row-highlight'), 3000);
            }
        }
    @endif

    // ---- Auto-open modal if there are validation errors ----
    @if($errors->any())
        const shouldOpenBarangMasukModal = @json(
            (bool) old('barang_id.0') ||
            (bool) old('qty.0') ||
            (bool) old('harga_beli.0') ||
            (bool) old('tanggal') ||
            (bool) old('keterangan') ||
            (bool) old('nomor_bc') ||
            (bool) old('tanggal_bc') ||
            (bool) old('pelabuhan') ||
            (bool) old('negara_asal')
        );

        if (shouldOpenBarangMasukModal && bmModal) {
            bmModal.show();
        } else {
            bsModal.show();
        }
    @endif

    // ---- Auto-open modal for old edit param (fallback for ?edit=id links) ----
    @if($editData)
        setEditMode({
            id:          {{ $editData->id }},
            nama:        '{{ addslashes($editData->nama) }}',
            tipe:        '{{ $editData->tipe }}',
            satuan:      '{{ addslashes($editData->satuan) }}',
            harga_beli:  {{ $editData->harga_beli }},
            harga_jual:  {{ $editData->harga_jual }},
            stok:        {{ $editData->stok }},
            supplier_id: {{ $editData->supplier_id ?? 'null' }},
        });
        bsModal.show();
    @endif

    // ---- Barang Masuk: impor fields toggle ----
    const tipeSelect = document.querySelector('#modalBarangMasuk .bm-tipe');
    const imporFields = document.querySelector('#modalBarangMasuk .bm-impor-fields');
    const toggleImpor = () => {
        if (!tipeSelect || !imporFields) {
            return;
        }
        imporFields.style.display = tipeSelect.value === 'impor' ? 'block' : 'none';
    };

    if (tipeSelect && imporFields) {
        tipeSelect.addEventListener('change', toggleImpor);
        toggleImpor();
    }

    // ---- Barang Masuk: dynamic detail rows ----
    const tbody = document.querySelector('#bm-detail-table tbody');
    const optionHtml = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;
    const bindRemove = () => {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function () {
                if (tbody && tbody.querySelectorAll('tr').length > 1) {
                    this.closest('tr').remove();
                }
            };
        });
    };

    const addRowBtn = document.getElementById('bm-add-row');
    if (addRowBtn && tbody) {
        addRowBtn.addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.innerHTML =
                `<td><select name="barang_id[]" class="form-select form-select-sm" required><option value="">— Pilih Barang —</option>${optionHtml}</select></td>` +
                `<td><input type="number" name="qty[]" class="form-control form-control-sm text-center" min="1" value="1" required></td>` +
                `<td><input type="number" name="harga_beli[]" class="form-control form-control-sm text-end" min="0" placeholder="0" required></td>` +
                `<td class="text-center"><button class="btn btn-icon btn-outline-danger btn-sm remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
            tbody.appendChild(tr);
            bindRemove();
        });
    }

    bindRemove();
});
</script>
@endpush
