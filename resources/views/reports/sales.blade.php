@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan')

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Laporan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Penjualan</span>
@endsection

@section('content')

{{-- Header --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="margin: 0;">Laporan Penjualan</h1>
        <p class="page-description" style="margin-top: 0.25rem;">Laporan penjualan dan revenue</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('reports.sales-piutang') }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-cash-coin"></i>
            <span>Piutang Usaha</span>
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Invoices</span>
            <span class="stat-icon"><i class="bi bi-receipt"></i></span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Invoice tercatat</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Total Amount</span>
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalAmount)</div>
        <div class="stat-footer">
            <span class="stat-description">Total nilai invoice</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Total Terbayar</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalPaid)</div>
        <div class="stat-footer">
            <span class="stat-description">Sudah diterima</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Total Belum Dibayar</span>
            <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalUnpaid)</div>
        <div class="stat-footer">
            <span class="stat-description">Belum diterima</span>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="widget-card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="bi bi-funnel"></i> Filter</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.sales') }}">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" class="form-input" value="{{ request('from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-input" value="{{ request('to') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Pajak</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="ppn" {{ request('tipe') == 'ppn' ? 'selected' : '' }}>PPN</option>
                        <option value="non-ppn" {{ request('tipe') == 'non-ppn' ? 'selected' : '' }}>Non-PPN</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <button type="submit" class="btn-primary btn-sm">
                    <i class="bi bi-search"></i> Terapkan Filter
                </button>
                <a href="{{ route('reports.sales') }}" class="btn-secondary btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Data Penjualan</h3>
        <span style="color: var(--gray-500); font-size: 0.85rem;">{{ $rows->count() }} data</span>
    </div>
    <div class="card-body no-padding">
        <table class="filament-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No Invoice</th>
                    <th>Customer</th>
                    <th>Tipe Pajak</th>
                    <th style="text-align: right;">Subtotal</th>
                    <th style="text-align: right;">PPN</th>
                    <th style="text-align: right;">Total</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td><strong>{{ $row->nomor_invoice }}</strong></td>
                    <td>
                        <div style="font-weight: 500;">{{ $row->suratJalan->customer->nama ?? '-' }}</div>
                    </td>
                    <td>
                        @php $tipePajak = $row->suratJalan->tipe_pajak ?? 'non-ppn'; @endphp
                        <span class="badge {{ $tipePajak === 'ppn' ? 'success' : 'gray' }}">
                            {{ strtoupper($tipePajak) }}
                        </span>
                    </td>
                    <td style="text-align: right;">@rupiah($row->subtotal)</td>
                    <td style="text-align: right;">@rupiah($row->ppn)</td>
                    <td style="text-align: right;"><strong>@rupiah($row->total)</strong></td>
                    <td>
                        @php $payStatus = $row->payment_status ?? 'unpaid'; @endphp
                        <span class="badge {{ $payStatus === 'paid' ? 'success' : ($payStatus === 'partial' ? 'info' : 'warning') }}">
                            {{ $payStatus === 'paid' ? 'Lunas' : ($payStatus === 'partial' ? 'Sebagian' : 'Belum Lunas') }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content: flex-end;">
                            <a href="{{ route('reports.sales-piutang') }}" class="btn-icon" title="Lihat Piutang">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 2rem; color: var(--gray-500);">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Tidak ada data penjualan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
            <tfoot style="background: var(--gray-50); font-weight: bold;">
                <tr>
                    <td colspan="4" style="text-align: right; padding-right: 1rem;">TOTAL:</td>
                    <td style="text-align: right;">@rupiah($rows->sum('subtotal'))</td>
                    <td style="text-align: right;">@rupiah($rows->sum('ppn'))</td>
                    <td style="text-align: right;">@rupiah($totalAmount)</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
