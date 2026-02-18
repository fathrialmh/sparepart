@extends('layouts.app')

@section('title', 'Nota Menyusul')
@section('page_title', 'Nota Menyusul')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Nota Menyusul</span>
@endsection

@section('content')

@php
    $statusFilter = request('status');
    $filteredRows = $rows;
    if ($statusFilter) {
        $filteredRows = $rows->where('status', $statusFilter);
    }
    $totalNota     = $rows->count();
    $draftCount    = $rows->where('status', 'draft')->count();
    $pendingCount  = $rows->where('status', 'pending')->count();
    $completedCount = $rows->where('status', 'completed')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Nota</span>
            <span class="stat-icon"><i class="bi bi-journal-text"></i></span>
        </div>
        <div class="stat-value">{{ $totalNota }}</div>
        <div class="stat-footer">
            <span class="stat-description">Nota yang dibuat</span>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Draft</span>
            <span class="stat-icon"><i class="bi bi-pencil"></i></span>
        </div>
        <div class="stat-value">{{ $draftCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Masih draft</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Pending</span>
            <span class="stat-icon"><i class="bi bi-clock"></i></span>
        </div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Belum diselesaikan</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Completed</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $completedCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Selesai</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Nota Menyusul</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalNotaMenyusul">
            <i class="bi bi-plus-lg"></i> Buat Nota Menyusul
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('nota-menyusul.index') }}" class="tab-button {{ !$statusFilter ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-journal-text"></i> Semua
            </a>
            <a href="{{ route('nota-menyusul.index', ['status' => 'draft']) }}" class="tab-button {{ $statusFilter === 'draft' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-pencil"></i> Draft
                @if($draftCount > 0) <span class="badge gray" style="font-size:0.7rem;">{{ $draftCount }}</span> @endif
            </a>
            <a href="{{ route('nota-menyusul.index', ['status' => 'pending']) }}" class="tab-button {{ $statusFilter === 'pending' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clock"></i> Pending
                @if($pendingCount > 0) <span class="badge warning" style="font-size:0.7rem;">{{ $pendingCount }}</span> @endif
            </a>
            <a href="{{ route('nota-menyusul.index', ['status' => 'completed']) }}" class="tab-button {{ $statusFilter === 'completed' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-check-circle"></i> Completed
                @if($completedCount > 0) <span class="badge success" style="font-size:0.7rem;">{{ $completedCount }}</span> @endif
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-journal-text" style="font-size:3rem;"></i>
                <h6>Belum ada nota menyusul</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Buat nota menyusul pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalNotaMenyusul">
                    <i class="bi bi-plus-lg"></i> Buat Nota Menyusul
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Judul</th>
                        <th>Tipe Pajak</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                            <td>{{ $row->customer->nama ?? '—' }}</td>
                            <td style="max-width:200px;">
                                <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $row->judul }}">{{ $row->judul }}</div>
                                @if($row->referensi_dokumen)
                                    <div style="font-size:0.75rem; color:var(--gray-500);">Ref: {{ $row->referensi_dokumen }}</div>
                                @endif
                            </td>
                            <td>
                                @if($row->tipe_pajak === 'ppn')
                                    <span class="badge info">PPN</span>
                                @else
                                    <span class="badge gray">Non-PPN</span>
                                @endif
                            </td>
                            <td>
                                @if($row->status === 'draft')
                                    <span class="badge gray">Draft</span>
                                @elseif($row->status === 'pending')
                                    <span class="badge warning">Pending</span>
                                @else
                                    <span class="badge success">Completed</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                            data-customer_id="{{ $row->customer_id }}"
                                            data-tipe_pajak="{{ $row->tipe_pajak }}"
                                            data-judul="{{ $row->judul }}"
                                            data-konten="{{ $row->konten }}"
                                            data-referensi_dokumen="{{ $row->referensi_dokumen }}"
                                            data-status="{{ $row->status }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('nota-menyusul.destroy', $row) }}" style="display:inline;">
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

{{-- ========== MODAL TAMBAH / EDIT NOTA MENYUSUL ========== --}}
<div class="modal fade" id="modalNotaMenyusul" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Buat Nota Menyusul</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNotaMenyusul" method="post" action="{{ route('nota-menyusul.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="nota_id" id="notaId" value="">
                <div class="modal-body">
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Nota</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="inputCustomerId" class="form-select" required>
                                    <option value="">— Pilih Customer —</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe Pajak <span class="text-danger">*</span></label>
                                <select name="tipe_pajak" id="inputTipePajak" class="form-select" required>
                                    <option value="ppn">PPN</option>
                                    <option value="non-ppn">Non-PPN</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-journal-text"></i> Isi Nota</div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="inputJudul" class="form-control" placeholder="Judul nota menyusul" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Referensi Dokumen</label>
                                <input type="text" name="referensi_dokumen" id="inputReferensi" class="form-control" placeholder="INV-001, SJ-002, dll">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Konten <span class="text-danger">*</span></label>
                                <textarea name="konten" id="inputKonten" class="form-control" rows="4" placeholder="Isi lengkap nota menyusul..." required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-gear"></i> Status</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select name="status" id="inputStatus" class="form-select">
                                    <option value="draft">Draft</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                </select>
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
    const modal      = document.getElementById('modalNotaMenyusul');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formNotaMenyusul');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("nota-menyusul.store") }}';
    const updateUrl  = '{{ route("nota-menyusul.update", ":id") }}';

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#notaId').val('');
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Buat Nota Menyusul');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        $('#inputTanggal').val('{{ date("Y-m-d") }}');
        $('#inputCustomerId').val('');
        $('#inputTipePajak').val('ppn');
        $('#inputJudul').val('');
        $('#inputKonten').val('');
        $('#inputReferensi').val('');
        $('#inputStatus').val('draft');
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Nota Menyusul');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#notaId').val(d.id);
        $('#inputTanggal').val(d.tanggal);
        $('#inputCustomerId').val(d.customer_id);
        $('#inputTipePajak').val(d.tipe_pajak);
        $('#inputJudul').val(d.judul);
        $('#inputKonten').val(d.konten);
        $('#inputReferensi').val(d.referensi_dokumen);
        $('#inputStatus').val(d.status);
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'),
            tanggal: b.data('tanggal'),
            customer_id: b.data('customer_id'),
            tipe_pajak: b.data('tipe_pajak'),
            judul: b.data('judul'),
            konten: b.data('konten'),
            referensi_dokumen: b.data('referensi_dokumen'),
            status: b.data('status'),
        });
        bsModal.show();
    });

    @if($errors->any()) bsModal.show(); @endif
});
</script>
@endpush
