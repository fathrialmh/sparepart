@extends('layouts.app')

@section('title', 'Invoice')
@section('page_title', 'Invoice')
@section('use_toast', true)

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Invoice</span>
@endsection

@section('content')

@php
    $totalInvoice = $rows->count();
    $totalAmount = $rows->sum('total');
@endphp

{{-- Stats --}}
<div class="stats-grid-2" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Invoice</span>
            <span class="stat-icon"><i class="bi bi-receipt"></i></span>
        </div>
        <div class="stat-value">{{ $totalInvoice }}</div>
        <div class="stat-footer">
            <span class="stat-description">Invoice dibuat</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Total Amount</span>
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
        </div>
        <div class="stat-value" style="font-size:1.25rem;">@rupiah($totalAmount)</div>
        <div class="stat-footer">
            <span class="stat-description">Total nilai invoice</span>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Invoice</h3>
        @if($eligibleSuratJalan->isNotEmpty())
            <button type="button" class="btn-primary" id="btnTambah"
                    data-bs-toggle="modal" data-bs-target="#modalInvoice">
                <i class="bi bi-plus-lg"></i> Generate Invoice
            </button>
        @else
            <span style="color:var(--gray-500); font-size:0.85rem;"><i class="bi bi-info-circle"></i> Tidak ada surat jalan yang siap di-invoice</span>
        @endif
    </div>

    <div class="card-body no-padding">
        @if($rows->isEmpty())
            <div class="empty-state">
                <i class="bi bi-receipt" style="font-size:3rem;"></i>
                <h6>Belum ada invoice</h6>
                <p class="text-muted" style="margin-bottom:0.5rem;">Invoice dibuat dari surat jalan yang sudah berstatus Completed.</p>
                @if($eligibleSuratJalan->isNotEmpty())
                    <button type="button" class="btn-primary"
                            data-bs-toggle="modal" data-bs-target="#modalInvoice">
                        <i class="bi bi-plus-lg"></i> Generate Invoice
                    </button>
                @endif
            </div>
        @else
            <table class="filament-table">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>No SJ</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Sisa</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            <td><strong>{{ $row->nomor }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</td>
                            <td><span style="font-size:0.85rem; color:var(--gray-500);">{{ $row->suratJalan->nomor }}</span></td>
                            <td>{{ $row->customer->nama }}</td>
                            <td style="font-family:monospace;">@rupiah($row->total)</td>
                            <td>
                                @if($row->sisa <= 0)
                                    <span class="badge success">Lunas</span>
                                @else
                                    <span class="badge danger" style="font-family:monospace;">@rupiah($row->sisa)</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <a class="action-btn view" target="_blank"
                                       href="{{ route('invoice.print', $row) }}" title="Cetak" style="text-decoration:none;">
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

{{-- ========== MODAL GENERATE INVOICE ========== --}}
@if($eligibleSuratJalan->isNotEmpty())
<div class="modal fade" id="modalInvoice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i>
                    <span>Generate Invoice</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('invoice.store') }}" id="formInvoice">
                @csrf
                <div class="modal-body">
                    {{-- Surat Jalan & Tanggal --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-file-earmark-text"></i> Sumber Data</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Surat Jalan (Completed) <span class="text-danger">*</span></label>
                                <select name="surat_jalan_id" class="form-select" required>
                                    <option value="">— Pilih Surat Jalan —</option>
                                    @foreach($eligibleSuratJalan as $sj)
                                        <option value="{{ $sj->id }}">{{ $sj->nomor }} | {{ $sj->tanggal }} | {{ $sj->customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- Pengaturan Harga --}}
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-calculator"></i> Pengaturan Harga</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Diskon (%)</label>
                                <div class="input-group">
                                    <input type="number" name="diskon_persen" class="form-control" placeholder="0" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ongkos Kirim</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="ongkos_kirim" class="form-control" placeholder="0" value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">DP (Uang Muka)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="dp" class="form-control" placeholder="0" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Pembayaran --}}
                    <div class="form-section mb-0">
                        <div class="form-section-title"><i class="bi bi-credit-card"></i> Pembayaran</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Metode Pembayaran</label>
                                <input type="text" name="pembayaran" class="form-control" value="Transfer">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" value="BCA / Rekening perusahaan">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(function () {
});
</script>
@endpush
