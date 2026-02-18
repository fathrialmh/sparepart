@extends('layouts.app')

@section('title', 'Customer')
@section('page_title', 'Customer')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Customers</span>
@endsection

@section('content')

@php
    $tipe = request('tipe');
    $filteredRows = $rows;
    if ($tipe === 'ppn') {
        $filteredRows = $rows->filter(fn($r) => !empty($r->npwp));
    } elseif ($tipe === 'non-ppn') {
        $filteredRows = $rows->filter(fn($r) => empty($r->npwp));
    }
@endphp

{{-- Stats --}}
<div class="stats-grid-2" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Customers</span>
            <span class="stat-icon"><i class="bi bi-people"></i></span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Total customers terdaftar</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Active Customers</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Customers aktif</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Customer</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalCustomer">
            <i class="bi bi-plus-lg"></i> Tambah Customer
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('customer.index') }}" class="tab-button {{ !$tipe ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-people"></i> Semua Customer
            </a>
            <a href="{{ route('customer.index', ['tipe' => 'ppn']) }}" class="tab-button {{ $tipe === 'ppn' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-file-earmark-text"></i> PPN
            </a>
            <a href="{{ route('customer.index', ['tipe' => 'non-ppn']) }}" class="tab-button {{ $tipe === 'non-ppn' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-file-earmark"></i> Non-PPN
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-people" style="font-size:3rem;"></i>
                <h6>Belum ada data customer</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Tambahkan customer pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalCustomer">
                    <i class="bi bi-plus-lg"></i> Tambah Customer
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Tipe</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $row->nama }}</div>
                                <div style="font-size:0.8rem; color:var(--gray-500);">{{ $row->kode }}</div>
                            </td>
                            <td>{{ $row->alamat ?: '—' }}</td>
                            <td>{{ $row->telepon ?: '—' }}</td>
                            <td>{{ $row->email ?: '—' }}</td>
                            <td>
                                @if(!empty($row->npwp))
                                    <span class="badge info">PPN</span>
                                @else
                                    <span class="badge gray">Non-PPN</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-nama="{{ $row->nama }}"
                                            data-alamat="{{ $row->alamat }}"
                                            data-telepon="{{ $row->telepon }}"
                                            data-email="{{ $row->email }}"
                                            data-npwp="{{ $row->npwp }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('customer.destroy', $row) }}" style="display:inline;">
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

{{-- ========== MODAL TAMBAH / EDIT CUSTOMER ========== --}}
<div class="modal fade" id="modalCustomer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah Customer Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCustomer" method="post" action="{{ route('customer.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    {{-- Informasi Utama --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-person"></i> Informasi Utama</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" id="inputNama"
                                       placeholder="Masukkan nama customer" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NPWP</label>
                                <input type="text" class="form-control" name="npwp" id="inputNpwp"
                                       placeholder="Nomor NPWP" value="{{ old('npwp') }}">
                            </div>
                        </div>
                    </div>
                    {{-- Kontak --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-telephone"></i> Kontak</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" name="telepon" id="inputTelepon"
                                       placeholder="Nomor telepon" value="{{ old('telepon') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="inputEmail"
                                       placeholder="email@example.com" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>
                    {{-- Alamat --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-geo-alt"></i> Alamat</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <input type="text" class="form-control" name="alamat" id="inputAlamat"
                                       placeholder="Alamat lengkap customer" value="{{ old('alamat') }}">
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
    const modal      = document.getElementById('modalCustomer');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formCustomer');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("customer.store") }}';
    const updateUrl  = '{{ route("customer.update", ":id") }}';

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Tambah Customer Baru');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        form.querySelectorAll('input:not([type=hidden])').forEach(el => el.value = '');
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Customer');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputNama').val(d.nama);
        $('#inputAlamat').val(d.alamat);
        $('#inputTelepon').val(d.telepon);
        $('#inputEmail').val(d.email);
        $('#inputNpwp').val(d.npwp);
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'), nama: b.data('nama'), alamat: b.data('alamat'),
            telepon: b.data('telepon'), email: b.data('email'), npwp: b.data('npwp'),
        });
        bsModal.show();
    });

    @if($errors->any()) bsModal.show(); @endif
    @if($editData)
        setEditMode({
            id:{{ $editData->id }}, nama:'{{ addslashes($editData->nama) }}',
            alamat:'{{ addslashes($editData->alamat ?? "") }}', telepon:'{{ $editData->telepon ?? "" }}',
            email:'{{ $editData->email ?? "" }}', npwp:'{{ $editData->npwp ?? "" }}',
        });
        bsModal.show();
    @endif
});
</script>
@endpush
