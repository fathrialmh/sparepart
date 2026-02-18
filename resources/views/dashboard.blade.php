@extends('layouts.app')

@section('title', 'Dashboard - Adam Jaya')
@section('page_title', 'Dashboard')

@section('breadcrumb')
    <span class="breadcrumb-item">Dashboard</span>
@endsection

@push('styles')
<style>
    .dashboard-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .dashboard-page-header {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 0.75rem;
        padding: 1.25rem;
    }

    .dashboard-table-wrap {
        overflow-x: auto;
    }

    .dashboard-content .widget-card,
    .dashboard-content .stat-widget {
        min-width: 0;
    }

    .dashboard-content .stats-grid-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .dashboard-content canvas {
        width: 100% !important;
        max-height: 320px;
    }

    @media (max-width: 768px) {
        .dashboard-content .stats-grid-3 {
            grid-template-columns: 1fr;
        }

        .dashboard-page-header {
            padding: 1rem;
        }

        .dashboard-content {
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <div class="dashboard-page-header">
        <h1 class="page-title" style="margin-bottom: 0.25rem;">📊 Dashboard - Monitoring Bisnis</h1>
        <p class="page-description" style="margin-bottom: 0;">Overview performa bisnis dan KPI utama</p>
    </div>

    <div class="stats-grid-4">
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-icon">💰</span>
            </div>
            <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalRevenue)</div>
            <div class="stat-footer"><span class="stat-description">Akumulasi nilai invoice</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Total Orders</span>
                <span class="stat-icon">📦</span>
            </div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-footer"><span class="stat-description">Total surat jalan</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Active Customers</span>
                <span class="stat-icon">👥</span>
            </div>
            <div class="stat-value text-info">{{ $activeCustomers }}</div>
            <div class="stat-footer"><span class="stat-description">Customer dengan transaksi</span></div>
        </div>
        <div class="stat-widget {{ $pendingInvoices > 0 ? 'alert' : '' }}">
            <div class="stat-header">
                <span class="stat-label">Pending Invoices</span>
                <span class="stat-icon">⚠️</span>
            </div>
            <div class="stat-value {{ $pendingInvoices > 0 ? 'text-danger' : '' }}">{{ $pendingInvoices }}</div>
            <div class="stat-footer"><span class="stat-description">Perlu tindak lanjut</span></div>
        </div>
    </div>

    <div class="widgets-section">
        <h2 class="section-title">💰 Finance Metrics</h2>
        <div class="stats-grid-3">
            <div class="stat-widget clickable">
                <div class="stat-header">
                    <span class="stat-label">📈 Total Piutang</span>
                </div>
                <div class="stat-value text-warning" style="font-size: 1.25rem;">@rupiah($totalPiutang)</div>
                <div class="stat-footer"><span class="stat-description">Belum dibayar oleh customer</span></div>
                <div class="stat-chart">
                    <canvas id="piutangChart" height="40"></canvas>
                </div>
            </div>
            <div class="stat-widget {{ $overduePiutang > 0 ? 'alert' : '' }} clickable">
                <div class="stat-header">
                    <span class="stat-label">🔴 Piutang Jatuh Tempo</span>
                </div>
                <div class="stat-value {{ $overduePiutang > 0 ? 'text-danger' : '' }}" style="font-size: 1.25rem;">@rupiah($overduePiutang)</div>
                <div class="stat-footer"><span class="stat-description">Invoice overdue</span></div>
            </div>
            <div class="stat-widget success">
                <div class="stat-header">
                    <span class="stat-label">💵 Pembayaran Diterima</span>
                </div>
                <div class="stat-value text-success" style="font-size: 1.25rem;">@rupiah($totalPaymentReceived)</div>
                <div class="stat-footer"><span class="stat-description">Total cash in</span></div>
                <div class="stat-chart">
                    <canvas id="paymentChart" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="stats-grid-2">
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Total Barang</span>
                <span class="stat-icon">🧱</span>
            </div>
            <div class="stat-value">{{ $totalBarang }}</div>
            <div class="stat-footer"><span class="stat-description">Produk terdaftar</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">SJ Pending</span>
                <span class="stat-icon">🚚</span>
            </div>
            <div class="stat-value text-warning">{{ $sjPending }}</div>
            <div class="stat-footer"><span class="stat-description">Surat jalan menunggu</span></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">📊 Sales Revenue Trend</h3>
            </div>
            <div class="card-body">
                <canvas id="salesRevenueChart" height="320"></canvas>
            </div>
        </div>
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">⏰ Aging Analysis - Piutang</h3>
            </div>
            <div class="card-body">
                <canvas id="agingAnalysisChart" height="240"></canvas>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">👥 Top 5 Customers</h3>
            </div>
            <div class="card-body no-padding">
                <div class="dashboard-table-wrap">
                    <table class="filament-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                                <tr>
                                    <td><strong>{{ $customer->nama }}</strong></td>
                                    <td class="text-right">@rupiah($customer->total_revenue)</td>
                                    <td class="text-right">{{ $customer->total_orders }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center; color:var(--gray-500); padding:2rem;">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">🚚 Surat Jalan Terbaru</h3>
            </div>
            <div class="card-body no-padding">
                <div class="dashboard-table-wrap">
                    <table class="filament-table">
                        <thead>
                            <tr>
                                <th>No. SJ</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $tx)
                                <tr>
                                    <td><strong>{{ $tx->nomor }}</strong></td>
                                    <td>{{ $tx->customer_nama }}</td>
                                    <td>{{ \Carbon\Carbon::parse($tx->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        @if($tx->status === 'completed')
                                            <span class="badge success">Completed</span>
                                        @elseif($tx->status === 'in_progress')
                                            <span class="badge info">In Progress</span>
                                        @else
                                            <span class="badge warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" style="text-align:center; color:var(--gray-500); padding:2rem;">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-card {{ $stokRendah > 0 ? 'alert' : '' }}">
        <div class="card-header" style="{{ $stokRendah > 0 ? 'background: rgb(254 242 242);' : '' }}">
            <h3 class="card-title" style="{{ $stokRendah > 0 ? 'color: rgb(153 27 27);' : '' }}">⚠️ Inventory Alerts</h3>
        </div>
        <div class="card-body">
            @if($stokRendah > 0)
                <div class="alert-list">
                    @foreach($stokRendahList as $item)
                        <div class="alert-item {{ $item->stok <= 0 ? 'danger' : '' }}">
                            <span class="alert-icon">{{ $item->stok <= 0 ? '🔴' : '🟡' }}</span>
                            <div class="alert-content">
                                <strong>{{ $item->nama }}</strong> ({{ $item->kode }}) stok tersisa {{ $item->stok }} {{ $item->satuan }}
                            </div>
                        </div>
                    @endforeach
                    <a href="{{ route('barang.index') }}" class="btn-link" style="align-self: flex-start;">Lihat Detail →</a>
                </div>
            @else
                <div style="text-align:center; padding:1rem;">
                    <div style="font-size:2rem; margin-bottom:0.5rem;">✅</div>
                    <p style="color:var(--gray-600); margin:0;">Tidak ada alert stok, inventory dalam kondisi baik.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Top 5 Barang Terjual</h3>
            </div>
            <div class="card-body no-padding">
                <div class="dashboard-table-wrap">
                    <table class="filament-table">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th class="text-right">Total Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBarang as $item)
                                <tr>
                                    <td><strong>{{ $item->nama }}</strong></td>
                                    <td class="text-right">{{ $item->total_qty }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" style="text-align:center; color:var(--gray-500); padding:2rem;">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Master Data</h3>
            </div>
            <div class="card-body">
                <div class="stats-grid-2" style="gap: 0.75rem;">
                    <div class="stat-widget" style="padding: 1rem;">
                        <div class="stat-label">Total Customer</div>
                        <div class="stat-value text-info" style="font-size: 1.5rem;">{{ $totalCustomer }}</div>
                    </div>
                    <div class="stat-widget" style="padding: 1rem;">
                        <div class="stat-label">Total Supplier</div>
                        <div class="stat-value text-success" style="font-size: 1.5rem;">{{ $totalSupplier }}</div>
                    </div>
                    <div class="stat-widget" style="padding: 1rem;">
                        <div class="stat-label">Total Invoice</div>
                        <div class="stat-value" style="font-size: 1.5rem;">{{ $totalInvoice }}</div>
                    </div>
                    <div class="stat-widget" style="padding: 1rem;">
                        <div class="stat-label">Stok Rendah</div>
                        <div class="stat-value {{ $stokRendah > 0 ? 'text-danger' : '' }}" style="font-size: 1.5rem;">{{ $stokRendah }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const monthLabels = @json($monthLabels);
const revData = @json($revData);
const sjData = @json($sjData);
const statusLabels = @json($statusLabels);
const statusData = @json($statusData);

new Chart(document.getElementById('salesRevenueChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [
            {
                label: 'Revenue',
                data: revData,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                fill: true,
                tension: 0.35
            },
            {
                label: 'Surat Jalan',
                data: sjData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                fill: true,
                tension: 0.35
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

const overduePiutang = {{ $overduePiutang }};
const totalPiutang = {{ $totalPiutang }};
const currentPiutang = Math.max(totalPiutang - overduePiutang, 0);

new Chart(document.getElementById('agingAnalysisChart'), {
    type: 'doughnut',
    data: {
        labels: ['Current', 'Overdue'],
        datasets: [{
            data: [currentPiutang, overduePiutang],
            backgroundColor: ['#3b82f6', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

new Chart(document.getElementById('piutangChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Surat Jalan',
            data: sjData,
            borderColor: '#f59e0b',
            tension: 0.35,
            pointRadius: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: { x: { display: false }, y: { display: false } }
    }
});

new Chart(document.getElementById('paymentChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Pembayaran',
            data: revData,
            borderColor: '#10b981',
            tension: 0.35,
            pointRadius: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        scales: { x: { display: false }, y: { display: false } }
    }
});

if (statusLabels.length && statusData.length) {
    // Optional mini chart data hook for future usage.
    window.dashboardStatusSummary = { labels: statusLabels, data: statusData };
}
</script>
@endpush
