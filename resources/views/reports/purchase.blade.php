@extends('layouts.app')

@section('title', 'Laporan Pembelian')
@section('page_title', 'Laporan Pembelian')

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Laporan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Pembelian</span>
@endsection

@section('content')

{{-- Header --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="margin: 0;">Laporan Pembelian</h1>
        <p class="page-description" style="margin-top: 0.25rem;">Laporan pembelian dan pengeluaran</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('reports.purchase-hutang') }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-credit-card"></i>
            <span>Hutang Usaha</span>
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total PO</span>
            <span class="stat-icon"><i class="bi bi-cart-check"></i></span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">Purchase Order tercatat</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Total Amount</span>
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalAmount)</div>
        <div class="stat-footer">
            <span class="stat-description">Total nilai PO</span>
        </div>
    </div>
    <div class="stat-widget info">
        <div class="stat-header">
            <span class="stat-label">Total Terbayar</span>
            <span class="stat-icon"><i class="bi bi-check-circle"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalPaid)</div>
        <div class="stat-footer">
            <span class="stat-description">Sudah dibayar</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Total Belum Dibayar</span>
            <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalUnpaid)</div>
        <div class="stat-footer">
            <span class="stat-description">Belum dibayar</span>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="widget-card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="bi bi-funnel"></i> Filter</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.purchase') }}">
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="from" class="form-input" value="{{ request('from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="to" class="form-input" value="{{ request('to') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe Supplier</label>
                    <select name="tipe" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="lokal" {{ request('tipe') == 'lokal' ? 'selected' : '' }}>Lokal</option>
                        <option value="impor" {{ request('tipe') == 'impor' ? 'selected' : '' }}>Impor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status PO</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Pembayaran</label>
                    <select name="payment_status" class="form-select">
                        <option value="">Semua</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <button type="submit" class="btn-primary btn-sm">
                    <i class="bi bi-search"></i> Terapkan Filter
                </button>
                <a href="{{ route('reports.purchase') }}" class="btn-secondary btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Data Pembelian</h3>
        <span style="color: var(--gray-500); font-size: 0.85rem;">{{ $rows->count() }} data</span>
    </div>
    <div class="card-body no-padding">
        <table class="filament-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No PO</th>
                    <th>Supplier</th>
                    <th>Tipe</th>
                    <th>Status PO</th>
                    <th>Pembayaran</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                @php
                    $poStatus = $row->status ?? 'pending';
                    $payStatus = $row->payment_status ?? 'unpaid';
                    $tipe = $row->tipe ?? 'lokal';
                    $statusColors = [
                        'completed' => 'success',
                        'confirmed' => 'info',
                        'received'  => 'warning',
                        'cancelled' => 'danger',
                        'pending'   => 'gray',
                    ];
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td><strong>{{ $row->nomor_po }}</strong></td>
                    <td>
                        <div style="font-weight: 500;">{{ $row->supplier->nama ?? '-' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $tipe === 'impor' ? 'warning' : 'info' }}">
                            {{ ucfirst($tipe) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $statusColors[$poStatus] ?? 'gray' }}">
                            {{ ucfirst($poStatus) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $payStatus === 'paid' ? 'success' : ($payStatus === 'partial' ? 'warning' : 'gray') }}">
                            {{ $payStatus === 'paid' ? 'Lunas' : ($payStatus === 'partial' ? 'Sebagian' : 'Belum Lunas') }}
                        </span>
                    </td>
                    <td style="text-align: right;"><strong>@rupiah($row->total)</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 2rem; color: var(--gray-500);">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Tidak ada data pembelian</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
            <tfoot style="background: var(--gray-50); font-weight: bold;">
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 1rem;">TOTAL:</td>
                    <td style="text-align: right;">@rupiah($totalAmount)</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
