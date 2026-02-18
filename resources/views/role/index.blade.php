@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page_title', 'Roles & Permissions')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Master Data</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Roles</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="stats-grid-2" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Roles</span>
            <span class="stat-icon"><i class="bi bi-shield-lock"></i></span>
        </div>
        <div class="stat-value">{{ $roles->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Role terdaftar</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Total Permissions</span>
            <span class="stat-icon"><i class="bi bi-key"></i></span>
        </div>
        <div class="stat-value">{{ $permissions->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Permission tersedia</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Roles</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalRole">
            <i class="bi bi-plus-lg"></i> Tambah Role
        </button>
    </div>

    <div class="card-body no-padding">
        @if($roles->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield-lock" style="font-size:3rem;"></i>
                <h6>Belum ada role</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Tambahkan role pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalRole">
                    <i class="bi bi-plus-lg"></i> Tambah Role
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Permissions</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td><span class="badge gray">{{ $role->guard_name ?? 'web' }}</span></td>
                            <td>
                                <span class="badge info">{{ $role->permissions_count ?? $role->permissions->count() }} permissions</span>
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $role->id }}"
                                            data-name="{{ $role->name }}"
                                            data-permissions="{{ $role->permissions->pluck('id')->toJson() }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('role.destroy', $role) }}" style="display:inline;">
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

{{-- ========== MODAL TAMBAH / EDIT ROLE ========== --}}
<div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Tambah Role</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRole" method="post" action="{{ route('role.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-shield-lock"></i> Informasi Role</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Role <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="inputName" class="form-control" placeholder="Contoh: Manager, Staff, Admin" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-key"></i> Permissions</div>
                        @if($permissions->isNotEmpty())
                            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:0.75rem; padding:1rem; background:var(--gray-50); border:1px solid var(--gray-200); border-radius:0.5rem;">
                                @foreach($permissions as $permission)
                                    <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer;">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="form-check-input perm-checkbox">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted" style="font-size:0.875rem;">Belum ada permission yang tersedia.</p>
                        @endif
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
    const modal      = document.getElementById('modalRole');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formRole');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("role.store") }}';
    const updateUrl  = '{{ route("role.update", ":id") }}';

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Tambah Role');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        $('#inputName').val('');
        $('.perm-checkbox').prop('checked', false);
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Role');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputName').val(d.name);
        $('.perm-checkbox').prop('checked', false);
        if (d.permissions && d.permissions.length) {
            d.permissions.forEach(function (pid) {
                $('.perm-checkbox[value="' + pid + '"]').prop('checked', true);
            });
        }
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'),
            name: b.data('name'),
            permissions: b.data('permissions') || [],
        });
        bsModal.show();
    });

    @if($errors->any()) bsModal.show(); @endif
});
</script>
@endpush
