@extends('layouts.app')

@section('title', 'Hutang Usaha')
@section('page_title', 'Hutang Usaha')

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Laporan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Hutang Usaha</span>
@endsection

@section('content')

{{-- Header --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="margin: 0;">Hutang Usaha</h1>
        <p class="page-description" style="margin-top: 0.25rem;">Monitor dan kelola hutang ke supplier</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('reports.purchase') }}" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-arrow-left"></i>
            <span>Laporan Pembelian</span>
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Total Outstanding</span>
            <span class="stat-icon"><i class="bi bi-exclamation-circle"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalOutstanding)</div>
        <div class="stat-footer">
            <span class="stat-description">Hutang belum lunas</span>
        </div>
    </div>
    <div class="stat-widget danger">
        <div class="stat-header">
            <span class="stat-label">Total Overdue</span>
            <span class="stat-icon"><i class="bi bi-alarm"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalOverdue)</div>
        <div class="stat-footer">
            <span class="stat-description">Sudah jatuh tempo</span>
        </div>
    </div>
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Jumlah Hutang</span>
            <span class="stat-icon"><i class="bi bi-file-text"></i></span>
        </div>
        <div class="stat-value">{{ $rows->count() }}</div>
        <div class="stat-footer">
            <span class="stat-description">PO belum lunas</span>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Hutang</h3>
        <span style="color: var(--gray-500); font-size: 0.85rem;">{{ $rows->count() }} data</span>
    </div>
    <div class="card-body no-padding">
        <table class="filament-table">
            <thead>
                <tr>
                    <th>No PO</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Due Date</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: right;">Terbayar</th>
                    <th style="text-align: right;">Sisa</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                @php
                    $sisa = $row->total - ($row->payment_amount ?? 0);
                    $payStatus = $row->payment_status ?? 'unpaid';
                @endphp
                <tr>
                    <td><strong>{{ $row->nomor_po }}</strong></td>
                    <td>
                        <div style="font-weight: 500;">{{ $row->supplier->nama ?? '-' }}</div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        @if($row->due_date)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($row->due_date)->isPast();
                            @endphp
                            <span style="{{ $isOverdue ? 'color: var(--danger-600); font-weight: bold;' : '' }}">
                                {{ \Carbon\Carbon::parse($row->due_date)->format('d/m/Y') }}
                            </span>
                        @else
                            <span style="color: var(--gray-400);">-</span>
                        @endif
                    </td>
                    <td style="text-align: right;">@rupiah($row->total)</td>
                    <td style="text-align: right;">@rupiah($row->payment_amount ?? 0)</td>
                    <td style="text-align: right; font-weight: bold; color: var(--danger-600);">
                        @rupiah($sisa)
                    </td>
                    <td>
                        <span class="badge {{ $payStatus === 'partial' ? 'info' : 'warning' }}">
                            {{ $payStatus === 'partial' ? 'Sebagian' : 'Belum Lunas' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="table-actions" style="justify-content: flex-end;">
                            <button type="button" class="btn-icon success" title="Catat Pembayaran"
                                    data-bs-toggle="modal" data-bs-target="#paymentModal"
                                    onclick="openPaymentModal({{ $row->id }}, '{{ $row->nomor_po }}', {{ $sisa }})">
                                <i class="bi bi-cash-coin"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 2rem; color: var(--gray-500);">
                        <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--success-600);"></i>
                        <p style="margin-top: 0.5rem;">Semua hutang sudah lunas!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Payment Modal --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('reports.purchase-hutang.payment') }}">
                @csrf
                <input type="hidden" name="purchase_order_id" id="paymentPoId">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Catat Pembayaran Hutang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 1rem; color: var(--gray-600);">
                        PO: <strong id="paymentPoNo"></strong>
                    </p>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <input type="number" name="amount" id="paymentAmount" class="form-input"
                               min="1" step="1" required placeholder="Masukkan jumlah pembayaran">
                        <small style="color: var(--gray-500);">Sisa: <span id="paymentRemaining"></span></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="form-input"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openPaymentModal(poId, poNo, remaining) {
    document.getElementById('paymentPoId').value = poId;
    document.getElementById('paymentPoNo').textContent = poNo;
    document.getElementById('paymentAmount').max = remaining;
    document.getElementById('paymentRemaining').textContent =
        new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(remaining);
}
</script>
@endpush
