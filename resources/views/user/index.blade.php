@extends('layouts.app')

@section('title', 'User Management')
@section('page_title', 'User Management')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Master Data</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Users</span>
@endsection

@section('content')

@php
    $totalUsers = $rows->count();
    $adminUsers = $rows->where('role', 'admin')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-2" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Users</span>
            <span class="stat-icon"><i class="bi bi-people"></i></span>
        </div>
        <div class="stat-value">{{ $totalUsers }}</div>
        <div class="stat-footer">
            <span class="stat-description">Registered users</span>
        </div>
    </div>
    <div class="stat-widget danger">
        <div class="stat-header">
            <span class="stat-label">Admin Users</span>
            <span class="stat-icon"><i class="bi bi-shield-lock"></i></span>
        </div>
        <div class="stat-value">{{ $adminUsers }}</div>
        <div class="stat-footer">
            <span class="stat-description">Administrator</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Users</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalUser">
            <i class="bi bi-plus-lg"></i> Tambah User
        </button>
    </div>

    <div class="card-body no-padding">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-lock" style="font-size:3rem;"></i>
                <h6>Belum ada data user</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Tambahkan user pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalUser">
                    <i class="bi bi-plus-lg"></i> Tambah User
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, rgb(249 115 22) 0%, rgb(234 88 12) 100%); color:white; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.75rem;">
                                        {{ strtoupper(substr($row->nama, 0, 1)) }}
                                    </div>
                                    <strong>{{ $row->username }}</strong>
                                </div>
                            </td>
                            <td>{{ $row->nama }}</td>
                            <td>
                                @if($row->role === 'admin')
                                    <span class="badge danger">Admin</span>
                                @else
                                    <span class="badge info">User</span>
                                @endif
                            </td>
                            <td><span style="font-size:0.85rem; color:var(--gray-500);">{{ $row->created_at->format('d M Y, H:i') }}</span></td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-username="{{ $row->username }}"
                                            data-nama="{{ $row->nama }}"
                                            data-role="{{ $row->role }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if(auth()->id() !== $row->id)
                                        <form method="post" action="{{ route('user.destroy', $row) }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete btn-delete" title="Hapus">
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
