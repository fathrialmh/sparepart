@extends('layouts.app')

@section('title', 'Customer')
@section('page_title', 'Customer')
@section('use_toast', true)

@section('content')

{{-- ========== DAFTAR CUSTOMER ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i> Daftar Customer
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                    data-bs-toggle="modal" data-bs-target="#modalCustomer">
                <i class="bi bi-plus-lg"></i> Tambah Customer
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <h6>Belum ada data customer</h6>
                <p class="text-muted mb-2">Tambahkan customer pertama Anda.</p>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalCustomer">
                    <i class="bi bi-plus-lg"></i> Tambah Customer
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:200px">Customer</th>
                            <th class="text-start">Alamat</th>
                            <th class="text-start" style="width:130px">Telepon</th>
                            <th class="text-start" style="width:180px">Email</th>
                            <th class="text-center" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $row->nama }}</div>
                                    <small class="text-muted">{{ $row->kode }}</small>
                                </td>
                                <td>{{ $row->alamat ?: '—' }}</td>
                                <td>{{ $row->telepon ?: '—' }}</td>
                                <td>{{ $row->email ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                                class="btn btn-icon btn-outline-warning btn-sm btn-edit"
                                                title="Edit"
                                                data-id="{{ $row->id }}"
                                                data-nama="{{ $row->nama }}"
                                                data-alamat="{{ $row->alamat }}"
                                                data-telepon="{{ $row->telepon }}"
                                                data-email="{{ $row->email }}"
                                                data-npwp="{{ $row->npwp }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="post" action="{{ route('customer.destroy', $row) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger btn-sm btn-delete" title="Hapus">
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

    @if(session('success'))
        Swal.fire({ toast:true, position:'top-end', icon:'success', title:'{{ session("success") }}', showConfirmButton:false, timer:3000, timerProgressBar:true });
    @endif
    @if(session('error'))
        Swal.fire({ toast:true, position:'top-end', icon:'error', title:'{{ session("error") }}', showConfirmButton:false, timer:4000, timerProgressBar:true });
    @endif
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
