@extends('layouts.app')

@section('title', 'User Management')
@section('page_title', 'User Management')
@section('use_toast', true)

@section('content')

{{-- ========== DAFTAR USER ========== --}}
<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-primary"></i> Daftar User
                <span class="badge bg-secondary rounded-pill">{{ $rows->count() }}</span>
            </span>
            <button type="button" class="btn btn-primary btn-sm" id="btnTambah"
                    data-bs-toggle="modal" data-bs-target="#modalUser">
                <i class="bi bi-plus-lg"></i> Tambah User
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-lock"></i>
                <h6>Belum ada data user</h6>
                <p class="text-muted mb-2">Tambahkan user pertama Anda.</p>
                <button type="button" class="btn btn-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalUser">
                    <i class="bi bi-plus-lg"></i> Tambah User
                </button>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 datatable">
                    <thead>
                        <tr>
                            <th class="text-start" style="min-width:160px">User</th>
                            <th class="text-start" style="min-width:140px">Nama Lengkap</th>
                            <th class="text-center" style="width:100px">Role</th>
                            <th class="text-start" style="min-width:150px">Dibuat</th>
                            <th class="text-center" style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle {{ $row->role === 'admin' ? 'avatar-admin' : 'avatar-user' }}">
                                            {{ strtoupper(substr($row->nama, 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $row->username }}</span>
                                    </div>
                                </td>
                                <td>{{ $row->nama }}</td>
                                <td class="text-center">
                                    @if($row->role === 'admin')
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Admin</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info">User</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $row->created_at->format('d M Y, H:i') }}</small></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                                class="btn btn-icon btn-outline-warning btn-sm btn-edit"
                                                title="Edit"
                                                data-id="{{ $row->id }}"
                                                data-username="{{ $row->username }}"
                                                data-nama="{{ $row->nama }}"
                                                data-role="{{ $row->role }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if(auth()->id() !== $row->id)
                                            <form method="post" action="{{ route('user.destroy', $row) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-outline-danger btn-sm btn-delete" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        @endif
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

{{-- ========== MODAL TAMBAH / EDIT USER ========== --}}
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah User Baru</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUser" method="post" action="{{ route('user.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-person-badge"></i> Informasi Akun</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" id="inputUsername"
                                       placeholder="Masukkan username" value="{{ old('username') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" id="inputNama"
                                       placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" id="inputRole">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" id="labelPassword">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" id="inputPassword"
                                       placeholder="Masukkan password" required>
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
    const modal      = document.getElementById('modalUser');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formUser');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("user.store") }}';
    const updateUrl  = '{{ route("user.update", ":id") }}';
    const pwInput    = document.getElementById('inputPassword');
    const pwLabel    = document.getElementById('labelPassword');

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Tambah User Baru');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        form.querySelectorAll('input:not([type=hidden])').forEach(el => el.value = '');
        $('#inputRole').val('user');
        pwInput.required = true;
        pwInput.placeholder = 'Masukkan password';
        pwLabel.innerHTML = 'Password <span class="text-danger">*</span>';
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit User');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputUsername').val(d.username);
        $('#inputNama').val(d.nama);
        $('#inputRole').val(d.role);
        pwInput.value = '';
        pwInput.required = false;
        pwInput.placeholder = 'Kosongkan jika tidak diubah';
        pwLabel.innerHTML = 'Password <small class="text-muted">(opsional)</small>';
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'), username: b.data('username'),
            nama: b.data('nama'), role: b.data('role'),
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
            id:{{ $editData->id }}, username:'{{ addslashes($editData->username) }}',
            nama:'{{ addslashes($editData->nama) }}', role:'{{ $editData->role }}',
        });
        bsModal.show();
    @endif
});
</script>
@endpush
