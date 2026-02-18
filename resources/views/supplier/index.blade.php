@extends('layouts.app')

@section('title', 'Supplier')
@section('page_title', 'Supplier')
@section('use_toast', true)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-item">Pembelian</span>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-item">Suppliers</span>
@endsection

@section('content')

{{-- ========== STATS ========== --}}
<div class="stats-grid-2" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Suppliers</span>
            <span class="stat-icon">🏭</span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Registered suppliers</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Active Suppliers</span>
            <span class="stat-icon">✅</span>
        </div>
        <div class="stat-value text-success">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Currently active</span>
        </div>
    </div>
</div>

{{-- ========== DAFTAR SUPPLIER ========== --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Suppliers</h3>
        <button type="button" class="btn-primary btn-sm" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalSupplier">
            <span>+</span> Tambah Supplier
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('supplier.index') }}" class="tab-button {{ !request('tipe') ? 'active' : '' }}" style="text-decoration: none;">
                <span>📋</span>
                <span>Semua Supplier</span>
            </a>
            <a href="{{ route('supplier.index', ['tipe' => 'lokal']) }}" class="tab-button {{ request('tipe') === 'lokal' ? 'active' : '' }}" style="text-decoration: none;">
                <span>🏠</span>
                <span>Supplier Lokal</span>
            </a>
            <a href="{{ route('supplier.index', ['tipe' => 'impor']) }}" class="tab-button {{ request('tipe') === 'impor' ? 'active' : '' }}" style="text-decoration: none;">
                <span>🌏</span>
                <span>Supplier Import</span>
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($rows->isEmpty())
            <div class="empty-state">
                <div class="icon">🏭</div>
                <h6>Belum ada data supplier</h6>
                <p class="text-muted" style="margin-bottom: 0.5rem;">Tambahkan supplier pertama Anda.</p>
                <button type="button" class="btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalSupplier">
                    <span>+</span> Tambah Supplier
                </button>
            </div>
        @else
            <table class="filament-table datatable">
                <thead>
                    <tr>
                        <th style="min-width:200px">Supplier</th>
                        <th>Tipe</th>
                        <th>Negara</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>
                                <strong>{{ $row->nama }}</strong>
                                <br><small style="color: var(--gray-500);">{{ $row->kode }}</small>
                            </td>
                            <td>
                                @if($row->tipe === 'lokal')
                                    <span class="badge gray">🏠 Lokal</span>
                                @else
                                    <span class="badge info">🌏 Impor</span>
                                @endif
                            </td>
                            <td>{{ $row->negara_asal ?: '—' }}</td>
                            <td>{{ $row->telepon ?: '—' }}</td>
                            <td>{{ $row->email ?: '—' }}</td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content: flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-nama="{{ $row->nama }}"
                                            data-tipe="{{ $row->tipe }}"
                                            data-negara_asal="{{ $row->negara_asal }}"
                                            data-telepon="{{ $row->telepon }}"
                                            data-email="{{ $row->email }}"
                                            data-npwp="{{ $row->npwp }}"
                                            data-alamat="{{ $row->alamat }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('supplier.destroy', $row) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete btn-delete" title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ========== MODAL TAMBAH / EDIT SUPPLIER ========== --}}
<div class="modal fade" id="modalSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah Supplier Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSupplier" method="post" action="{{ route('supplier.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    {{-- Informasi Utama --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-building"></i> Informasi Utama</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" id="inputNama"
                                       placeholder="Masukkan nama supplier" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                                <select class="form-select" name="tipe" id="inputTipe">
                                    <option value="lokal">Lokal</option>
                                    <option value="impor">Impor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Negara Asal</label>
                                <input type="text" class="form-control" name="negara_asal" id="inputNegaraAsal"
                                       placeholder="cth: Jepang, China" value="{{ old('negara_asal') }}">
                            </div>
                        </div>
                    </div>
                    {{-- Kontak --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-telephone"></i> Kontak</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" name="telepon" id="inputTelepon"
                                       placeholder="Nomor telepon" value="{{ old('telepon') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="inputEmail"
                                       placeholder="email@example.com" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">NPWP</label>
                                <input type="text" class="form-control" name="npwp" id="inputNpwp"
                                       placeholder="Nomor NPWP" value="{{ old('npwp') }}">
                            </div>
                        </div>
                    </div>
                    {{-- Alamat --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-geo-alt"></i> Alamat</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <input type="text" class="form-control" name="alamat" id="inputAlamat"
                                       placeholder="Alamat lengkap supplier" value="{{ old('alamat') }}">
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

@endsection

@push('scripts')
<script>
$(function () {
    const modal      = document.getElementById('modalSupplier');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formSupplier');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("supplier.store") }}';
    const updateUrl  = '{{ route("supplier.update", ":id") }}';

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Tambah Supplier Baru');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        form.querySelectorAll('input:not([type=hidden])').forEach(el => el.value = '');
        $('#inputTipe').val('lokal');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Supplier');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputNama').val(d.nama);
        $('#inputTipe').val(d.tipe);
        $('#inputNegaraAsal').val(d.negara_asal);
        $('#inputTelepon').val(d.telepon);
        $('#inputEmail').val(d.email);
        $('#inputNpwp').val(d.npwp);
        $('#inputAlamat').val(d.alamat);
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'), nama: b.data('nama'), tipe: b.data('tipe'),
            negara_asal: b.data('negara_asal'), telepon: b.data('telepon'),
            email: b.data('email'), npwp: b.data('npwp'), alamat: b.data('alamat'),
        });
        bsModal.show();
    });

    @if($errors->any()) bsModal.show(); @endif
    @if($editData)
        setEditMode({
            id:{{ $editData->id }}, nama:'{{ addslashes($editData->nama) }}', tipe:'{{ $editData->tipe }}',
            negara_asal:'{{ addslashes($editData->negara_asal ?? "") }}', telepon:'{{ $editData->telepon ?? "" }}',
            email:'{{ $editData->email ?? "" }}', npwp:'{{ $editData->npwp ?? "" }}', alamat:'{{ addslashes($editData->alamat ?? "") }}',
        });
        bsModal.show();
    @endif
});
</script>
@endpush
