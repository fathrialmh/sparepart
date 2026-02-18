@extends('layouts.app')

@section('title', 'Surat Jalan')
@section('page_title', 'Surat Jalan')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Surat Jalan</span>
@endsection

@section('content')

@php
    $statusFilter = request('status');
    $filteredRows = $rows;
    if ($statusFilter) {
        $filteredRows = $rows->where('status', $statusFilter);
    }
    $totalSJ = $rows->count();
    $pendingCount = $rows->where('status', 'pending')->count();
    $progressCount = $rows->where('status', 'in_progress')->count();
    $completedCount = $rows->where('status', 'completed')->count();
@endphp

{{-- Stats --}}
<div class="stats-grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Surat Jalan</span>
            <span class="stat-icon"><i class="bi bi-truck"></i></span>
        </div>
        <div class="stat-value">{{ $totalSJ }}</div>
        <div class="stat-footer">
            <span class="stat-description">Surat jalan dibuat</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Pending</span>
            <span class="stat-icon"><i class="bi bi-clock"></i></span>
        </div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Menunggu proses</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">In Progress</span>
            <span class="stat-icon"><i class="bi bi-arrow-repeat"></i></span>
        </div>
        <div class="stat-value">{{ $progressCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Sedang diproses</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Completed</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value">{{ $completedCount }}</div>
        <div class="stat-footer">
            <span class="stat-description">Selesai dikirim</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Surat Jalan</h3>
        <button type="button" class="btn-primary" id="btnTambah"
                data-bs-toggle="modal" data-bs-target="#modalSuratJalan">
            <i class="bi bi-plus-lg"></i> Buat Surat Jalan
        </button>
    </div>

    {{-- Tabs --}}
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--gray-200); background: var(--gray-50);">
        <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--gray-200);">
            <a href="{{ route('surat-jalan.index') }}" class="tab-button {{ !$statusFilter ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-truck"></i> Semua
            </a>
            <a href="{{ route('surat-jalan.index', ['status' => 'pending']) }}" class="tab-button {{ $statusFilter === 'pending' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-clock"></i> Pending
                @if($pendingCount > 0) <span class="badge warning" style="font-size:0.7rem;">{{ $pendingCount }}</span> @endif
            </a>
            <a href="{{ route('surat-jalan.index', ['status' => 'in_progress']) }}" class="tab-button {{ $statusFilter === 'in_progress' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-arrow-repeat"></i> In Progress
                @if($progressCount > 0) <span class="badge info" style="font-size:0.7rem;">{{ $progressCount }}</span> @endif
            </a>
            <a href="{{ route('surat-jalan.index', ['status' => 'completed']) }}" class="tab-button {{ $statusFilter === 'completed' ? 'active' : '' }}" style="text-decoration:none;">
                <i class="bi bi-check-circle"></i> Completed
                @if($completedCount > 0) <span class="badge success" style="font-size:0.7rem;">{{ $completedCount }}</span> @endif
            </a>
        </div>
    </div>

    <div class="card-body no-padding">
        @if($filteredRows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-truck" style="font-size:3rem;"></i>
                <h6>Belum ada surat jalan</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Buat surat jalan pertama Anda.</p>
                <button type="button" class="btn-primary"
                        data-bs-toggle="modal" data-bs-target="#modalSuratJalan">
                    <i class="bi bi-plus-lg"></i> Buat Surat Jalan
                </button>
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Pajak</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredRows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                            <td>{{ $row->customer->nama }}</td>
                            <td>
                                @if($row->tipe_pajak === 'kena_pajak')
                                    <span class="badge info">PPN 11%</span>
                                @else
                                    <span class="badge gray">Non-PPN</span>
                                @endif
                            </td>
                            <td>
                                @if($row->status === 'pending')
                                    <span class="badge warning">Pending</span>
                                @elseif($row->status === 'in_progress')
                                    <span class="badge info">In Progress</span>
                                @else
                                    <span class="badge success">Completed</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end; flex-wrap:wrap;">
                                    @if($row->status !== 'pending')
                                    <form method="post" action="{{ route('surat-jalan.status', $row) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="pending">
                                        <button class="action-btn" title="Set Pending" style="font-size:.75rem; color:var(--warning-600);">
                                            <i class="bi bi-clock"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($row->status !== 'in_progress')
                                    <form method="post" action="{{ route('surat-jalan.status', $row) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button class="action-btn" title="Set In Progress" style="font-size:.75rem; color:var(--primary-600);">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($row->status !== 'completed')
                                    <form method="post" action="{{ route('surat-jalan.status', $row) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="action-btn" title="Set Completed" style="font-size:.75rem; color:var(--success-600);">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <a class="action-btn view" target="_blank"
                                       href="{{ route('surat-jalan.print', $row) }}" title="Cetak" style="text-decoration:none;">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ========== MODAL BUAT SURAT JALAN ========== --}}
<div class="modal fade" id="modalSuratJalan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-truck text-primary"></i>
                    <span>Buat Surat Jalan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('surat-jalan.store') }}" id="formSuratJalan">
                @csrf
                <div class="modal-body">
                    {{-- Info Pengiriman --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> Informasi Pengiriman</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-select" id="sj-customer" required>
                                    <option value="">— Pilih Customer —</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-alamat="{{ $customer->alamat }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Alamat Kirim</label>
                                <input type="text" name="alamat_kirim" id="sj-alamat-kirim" class="form-control" placeholder="Alamat tujuan pengiriman">
                            </div>
                        </div>
                    </div>

                    {{-- Detail Kendaraan & Pembayaran --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-credit-card"></i> Kendaraan & Pembayaran</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">No PO</label>
                                <input type="text" name="no_po" class="form-control" placeholder="Nomor Purchase Order">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">No Polisi</label>
                                <input type="text" name="no_polisi" class="form-control" placeholder="B 1234 XX">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sopir</label>
                                <input type="text" name="sopir" class="form-control" placeholder="Nama sopir">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pembayaran</label>
                                <input type="text" name="pembayaran" class="form-control" value="C.O.D">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipe Pajak</label>
                                <select name="tipe_pajak" class="form-select">
                                    <option value="tidak_kena_pajak">Tidak Kena Pajak</option>
                                    <option value="kena_pajak">Kena Pajak (PPN 11%)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan">
                            </div>
                        </div>
                    </div>

                    {{-- Detail barang --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-list-check"></i> Detail Barang</span>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="sj-add-row">
                                <i class="bi bi-plus-lg"></i> Tambah Baris
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="sj-detail-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:280px">Barang</th>
                                        <th style="width:90px" class="text-center">Stok</th>
                                        <th style="width:100px" class="text-center">Qty</th>
                                        <th style="width:160px" class="text-end">Harga</th>
                                        <th style="width:60px" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="barang_id[]" class="form-select form-select-sm sj-barang" required>
                                                <option value="">— Pilih Barang —</option>
                                                @foreach($barangs as $barang)
                                                    <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}" data-harga="{{ $barang->harga_jual }}">{{ $barang->kode }} - {{ $barang->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm text-center sj-stok" readonly></td>
                                        <td><input type="number" name="qty[]" class="form-control form-control-sm text-center sj-qty" min="1" value="1" required></td>
                                        <td><input type="number" name="harga[]" class="form-control form-control-sm text-end sj-harga" min="0" value="0" readonly required></td>
                                        <td class="text-center">
                                            <button class="btn btn-icon btn-outline-danger btn-sm sj-remove-row" type="button" title="Hapus">
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
                        <i class="bi bi-save"></i> Simpan Surat Jalan
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
    // Auto-fill alamat from customer
    const customerSelect = document.getElementById('sj-customer');
    const alamatInput = document.getElementById('sj-alamat-kirim');
    customerSelect.addEventListener('change', () => {
        const opt = customerSelect.options[customerSelect.selectedIndex];
        alamatInput.value = opt ? (opt.dataset.alamat || '') : '';
    });

    // Detail rows
    const tbody = document.querySelector('#sj-detail-table tbody');
    const optionHtml = `{!! collect($barangs)->map(fn($b) => '<option value="'.$b->id.'" data-stok="'.$b->stok.'" data-harga="'.$b->harga_jual.'">'.$b->kode.' - '.$b->nama.'</option>')->implode('') !!}`;

    const recalc = (tr) => {
        const sel = tr.querySelector('.sj-barang');
        const qty = Number(tr.querySelector('.sj-qty').value || 0);
        const opt = sel.options[sel.selectedIndex];
        const stok = Number(opt?.dataset.stok || 0);
        const harga = Number(opt?.dataset.harga || 0);
        tr.querySelector('.sj-stok').value = stok || '';
        tr.querySelector('.sj-harga').value = qty > 0 ? harga * qty : 0;
    };

    const bindRows = () => {
        document.querySelectorAll('.sj-barang').forEach(el => el.onchange = () => recalc(el.closest('tr')));
        document.querySelectorAll('.sj-qty').forEach(el => el.oninput = () => recalc(el.closest('tr')));
        document.querySelectorAll('.sj-remove-row').forEach(btn => {
            btn.onclick = function () { if (tbody.querySelectorAll('tr').length > 1) this.closest('tr').remove(); };
        });
        tbody.querySelectorAll('tr').forEach(recalc);
    };

    document.getElementById('sj-add-row').addEventListener('click', () => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><select name="barang_id[]" class="form-select form-select-sm sj-barang" required><option value="">— Pilih Barang —</option>${optionHtml}</select></td><td><input type="text" class="form-control form-control-sm text-center sj-stok" readonly></td><td><input type="number" name="qty[]" class="form-control form-control-sm text-center sj-qty" min="1" value="1" required></td><td><input type="number" name="harga[]" class="form-control form-control-sm text-end sj-harga" min="0" value="0" readonly required></td><td class="text-center"><button class="btn btn-icon btn-outline-danger btn-sm sj-remove-row" type="button" title="Hapus"><i class="bi bi-trash3"></i></button></td>`;
        tbody.appendChild(tr);
        bindRows();
    });

    bindRows();
});
</script>
@endpush
