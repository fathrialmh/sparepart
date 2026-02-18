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

    .dashboard-table-wrap {
        overflow-x: auto;
    }

    .dashboard-content .widget-card,
    .dashboard-content .stat-widget {
        min-width: 0;
    }

    .dashboard-content canvas {
        width: 100% !important;
        max-height: 320px;
    }

    @media (max-width: 768px) {
        .dashboard-content {
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-content">
    <div class="stats-grid-4">
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Total Barang</span>
                <span class="stat-icon">📦</span>
            </div>
            <div class="stat-value">{{ $totalBarang }}</div>
            <div class="stat-footer"><span class="stat-description">Produk terdaftar</span></div>
        </div>
        <div class="stat-widget {{ $stokRendah > 0 ? 'alert' : '' }}">
            <div class="stat-header">
                <span class="stat-label">Stok Rendah</span>
                <span class="stat-icon">⚠️</span>
            </div>
            <div class="stat-value {{ $stokRendah > 0 ? 'text-danger' : '' }}">{{ $stokRendah }}</div>
            <div class="stat-footer"><span class="stat-description">Perlu restok</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">SJ Pending</span>
                <span class="stat-icon">🚚</span>
            </div>
            <div class="stat-value text-warning">{{ $sjPending }}</div>
            <div class="stat-footer"><span class="stat-description">Surat jalan menunggu</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Total Invoice</span>
                <span class="stat-icon">💰</span>
            </div>
            <div class="stat-value">{{ $totalInvoice }}</div>
            <div class="stat-footer"><span class="stat-description">Invoice dibuat</span></div>
        </div>
    </div>

    <div class="stats-grid-2">
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Customer</span>
                <span class="stat-icon">👥</span>
            </div>
            <div class="stat-value text-info">{{ $totalCustomer }}</div>
            <div class="stat-footer"><span class="stat-description">Total customers</span></div>
        </div>
        <div class="stat-widget">
            <div class="stat-header">
                <span class="stat-label">Supplier</span>
                <span class="stat-icon">🏭</span>
            </div>
            <div class="stat-value text-success">{{ $totalSupplier }}</div>
            <div class="stat-footer"><span class="stat-description">Total suppliers</span></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Tren Transaksi</h3>
            </div>
            <div class="card-body">
                <canvas id="chartTrend" height="200"></canvas>
            </div>
        </div>
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Status Surat Jalan</h3>
            </div>
            <div class="card-body">
                <canvas id="chartStatus" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Revenue</h3>
            </div>
            <div class="card-body">
                <canvas id="chartRevenue" height="200"></canvas>
            </div>
        </div>
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Top 5 Barang</h3>
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
    </div>

    <div class="grid-2">
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Recent Surat Jalan</h3>
            </div>
            <div class="card-body no-padding">
                <div class="dashboard-table-wrap">
                    <table class="filament-table">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $tx)
                                <tr>
                                    <td><strong>{{ $tx->nomor }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($tx->tanggal)->format('d M Y') }}</td>
                                    <td>{{ $tx->customer_nama }}</td>
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
        @if($stokRendah > 0)
        <div class="widget-card alert">
            <div class="card-header" style="background: rgb(254 242 242);">
                <h3 class="card-title" style="color: rgb(153 27 27);">⚠️ Peringatan Stok Rendah</h3>
            </div>
            <div class="card-body">
                <div class="alert-list">
                    @foreach($stokRendahList as $item)
                        <div class="alert-item {{ $item->stok <= 0 ? 'danger' : '' }}">
                            <span class="alert-icon">{{ $item->stok <= 0 ? '🔴' : '🟡' }}</span>
                            <div class="alert-content">
                                <strong>{{ $item->nama }}</strong> ({{ $item->kode }}) — Stok: {{ $item->stok }} {{ $item->satuan }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="widget-card">
            <div class="card-header">
                <h3 class="card-title">Stok Status</h3>
            </div>
            <div class="card-body" style="text-align:center; padding:2rem;">
                <div style="font-size:3rem; margin-bottom:0.5rem;">✅</div>
                <p style="color:var(--gray-600);">Semua stok dalam kondisi baik</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: {
        labels: @json($monthLabels),
        datasets: [
            { label: 'Surat Jalan', data: @json($sjData), borderColor: '#3b82f6', tension: 0.3 },
            { label: 'Barang Masuk', data: @json($bmData), borderColor: '#10b981', tension: 0.3 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: @json($statusLabels),
        datasets: [{ data: @json($statusData), backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
new Chart(document.getElementById('chartRevenue'), {
    type: 'bar',
    data: {
        labels: @json($monthLabels),
        datasets: [{ label: 'Revenue', data: @json($revData), backgroundColor: '#f59e0b' }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
