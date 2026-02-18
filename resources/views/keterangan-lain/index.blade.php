@extends('layouts.app')

@section('title', 'Keterangan Lain')
@section('page_title', 'Keterangan Lain')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Keterangan Lain</span>
@endsection

@section('content')

@php
    $kategoriFilter = request('kategori');
    $filteredRows = $rows;
    if ($kategoriFilter) {
        $filteredRows = $rows->where('kategori', $kategoriFilter);
    }
    $totalDocs       = $rows->count();
    $kebijakanCount  = $rows->where('kategori', 'kebijakan')->count();
    $prosedurCount   = $rows->where('kategori', 'prosedur')->count();
    $peraturanCount  = $rows->where('kategori', 'peraturan')->count();
    $informasiCount  = $rows->where('kategori', 'informasi')->count();
    $komunikasiCount = $rows->where('kategori', 'komunikasi')->count();
    $lainnyaCount    = $rows->where('kategori', 'lainnya')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Dokumen</span>
            <span class="stat-icon"><i class="bi bi-file-earmark-text"></i></span>
        </div>
        <div class="stat-value">{{ $totalDocs }}</div>
        <div class="stat-footer">
            <span class="stat-description">Dokumen dibuat</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Kebijakan</span>
            <span class="stat-icon"><i class="bi bi-clipboard-check"></i></span>
        </div>
        <div class="stat-value">{{ $kebijakanCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Dokumen kebijakan</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Prosedur</span>
            <span class="stat-icon"><i class="bi bi-gear"></i></span>
        </div>
        <div class="stat-value">{{ $prosedurCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Dokumen prosedur</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Informasi</span>
            <span class="stat-icon"><i class="bi bi-info-circle"></i></span>
        </div>
        <div class="stat-value">{{ $informasiCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Dokumen informasi</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Keterangan Lain</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalKeteranganLain">
            <i class="bi bi-plus-lg"></i> Buat Keterangan
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; flex-wrap:wrap; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('keterangan-lain.index') }}" class="tab-button {{ !$kategoriFilter ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-file-earmark-text"></i> Semua
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'kebijakan']) }}" class="tab-button {{ $kategoriFilter === 'kebijakan' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clipboard-check"></i> Kebijakan
                @if($kebijakanCount > 0) <span class="badge info" style="font-size:0.7rem;">{{ $kebijakanCount }}</span> @endif
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'prosedur']) }}" class="tab-button {{ $kategoriFilter === 'prosedur' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-gear"></i> Prosedur
                @if($prosedurCount > 0) <span class="badge warning" style="font-size:0.7rem;">{{ $prosedurCount }}</span> @endif
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'peraturan']) }}" class="tab-button {{ $kategoriFilter === 'peraturan' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-shield-check"></i> Peraturan
                @if($peraturanCount > 0) <span class="badge danger" style="font-size:0.7rem;">{{ $peraturanCount }}</span> @endif
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'informasi']) }}" class="tab-button {{ $kategoriFilter === 'informasi' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-info-circle"></i> Informasi
                @if($informasiCount > 0) <span class="badge success" style="font-size:0.7rem;">{{ $informasiCount }}</span> @endif
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'komunikasi']) }}" class="tab-button {{ $kategoriFilter === 'komunikasi' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-chat-dots"></i> Komunikasi
                @if($komunikasiCount > 0) <span class="badge primary" style="font-size:0.7rem;">{{ $komunikasiCount }}</span> @endif
            </a>
            <a href="{{ route('keterangan-lain.index', ['kategori' => 'lainnya']) }}" class="tab-button {{ $kategoriFilter === 'lainnya' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-three-dots"></i> Lainnya
                @if($lainnyaCount > 0) <span class="badge gray" style="font-size:0.7rem;">{{ $lainnyaCount }}</span> @endif
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-file-earmark-text" style="font-size:3rem;"></i>
                <h6>Belum ada keterangan lain</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Buat keterangan lain pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalKeteranganLain">
                    <i class="bi bi-plus-lg"></i> Buat Keterangan
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tipe Pajak</th>
                        <th>Berlaku Sampai</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                            <td style="max-width:250px;">
                                <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $row->judul }}">{{ $row->judul }}</div>
                            </td>
                            <td>
                                @switch($row->kategori)
                                    @case('kebijakan')
                                        <span class="badge info">Kebijakan</span>
                                        @break
                                    @case('prosedur')
                                        <span class="badge warning">Prosedur</span>
                                        @break
                                    @case('peraturan')
                                        <span class="badge danger">Peraturan</span>
                                        @break
                                    @case('informasi')
                                        <span class="badge success">Informasi</span>
                                        @break
                                    @case('komunikasi')
                                        <span class="badge info">Komunikasi</span>
                                        @break
                                    @default
                                        <span class="badge gray">Lainnya</span>
                                @endswitch
                            </td>
                            <td>
                                @if($row->tipe_pajak === 'ppn')
                                    <span class="badge info">PPN</span>
                                @else
                                    <span class="badge gray">Non-PPN</span>
                                @endif
                            </td>
                            <td>
                                @if($row->berlaku_sampai)
                                    {{ \Carbon\Carbon::parse($row->berlaku_sampai)->format('d M Y') }}
                                @else
                                    <span style="color:var(--gray-400);">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button type="button"
                                            class="action-btn edit btn-edit"
                                            title="Edit"
                                            data-id="{{ $row->id }}"
                                            data-tanggal="{{ $row->tanggal->format('Y-m-d') }}"
                                            data-judul="{{ $row->judul }}"
                                            data-kategori="{{ $row->kategori }}"
                                            data-tipe_pajak="{{ $row->tipe_pajak }}"
                                            data-konten="{{ $row->konten }}"
                                            data-berlaku_sampai="{{ $row->berlaku_sampai ? $row->berlaku_sampai->format('Y-m-d') : '' }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="post" action="{{ route('keterangan-lain.destroy', $row) }}" style="display:inline;">
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

{{-- ========== MODAL TAMBAH / EDIT KETERANGAN LAIN ========== --}}
<div class="modal fade" id="modalKeteranganLain" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle text-primary" id="modalIcon"></i>
                    <span id="modalTitleText">Buat Keterangan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formKeteranganLain" method="post" action="{{ route('keterangan-lain.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-body">
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Dokumen</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="inputTanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="kategori" id="inputKategori" class="form-select" required>
                                    <option value="">— Pilih Kategori —</option>
                                    <option value="kebijakan">Kebijakan</option>
                                    <option value="prosedur">Prosedur</option>
                                    <option value="peraturan">Peraturan</option>
                                    <option value="informasi">Informasi</option>
                                    <option value="komunikasi">Komunikasi</option>
                                    <option value="lainnya">Lainnya</option>
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
                        <div class="form-section-title"><i class="bi bi-file-earmark-text"></i> Konten Keterangan</div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Judul <span class="text-danger">*</span></label>
                                <input type="text" name="judul" id="inputJudul" class="form-control" placeholder="Judul keterangan" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" id="inputBerlakuSampai" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Konten <span class="text-danger">*</span></label>
                                <textarea name="konten" id="inputKonten" class="form-control" rows="5" placeholder="Isi lengkap keterangan..." required></textarea>
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
    const modal      = document.getElementById('modalKeteranganLain');
    const bsModal    = new bootstrap.Modal(modal);
    const form       = document.getElementById('formKeteranganLain');
    const formMethod = document.getElementById('formMethod');
    const storeUrl   = '{{ route("keterangan-lain.store") }}';
    const updateUrl  = '{{ route("keterangan-lain.update", ":id") }}';

    function resetModal() {
        form.action = storeUrl;
        formMethod.value = 'POST';
        $('#modalIcon').attr('class', 'bi bi-plus-circle text-primary');
        $('#modalTitleText').text('Buat Keterangan');
        $('#btnSimpanIcon').attr('class', 'bi bi-save');
        $('#btnSimpanText').text('Simpan');
        $('#inputTanggal').val('{{ date("Y-m-d") }}');
        $('#inputJudul').val('');
        $('#inputKategori').val('');
        $('#inputTipePajak').val('ppn');
        $('#inputKonten').val('');
        $('#inputBerlakuSampai').val('');
    }

    function setEditMode(d) {
        form.action = updateUrl.replace(':id', d.id);
        formMethod.value = 'PUT';
        $('#modalIcon').attr('class', 'bi bi-pencil-square text-warning');
        $('#modalTitleText').text('Edit Keterangan');
        $('#btnSimpanIcon').attr('class', 'bi bi-check-lg');
        $('#btnSimpanText').text('Update');
        $('#inputTanggal').val(d.tanggal);
        $('#inputJudul').val(d.judul);
        $('#inputKategori').val(d.kategori);
        $('#inputTipePajak').val(d.tipe_pajak);
        $('#inputKonten').val(d.konten);
        $('#inputBerlakuSampai').val(d.berlaku_sampai);
    }

    $('#btnTambah').on('click', resetModal);
    modal.addEventListener('hidden.bs.modal', resetModal);

    $(document).on('click', '.btn-edit', function () {
        const b = $(this);
        setEditMode({
            id: b.data('id'),
            tanggal: b.data('tanggal'),
            judul: b.data('judul'),
            kategori: b.data('kategori'),
            tipe_pajak: b.data('tipe_pajak'),
            konten: b.data('konten'),
            berlaku_sampai: b.data('berlaku_sampai') || '',
        });
        bsModal.show();
    });

    @if($errors->any()) bsModal.show(); @endif
});
</script>
@endpush
