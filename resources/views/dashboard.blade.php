@extends('layouts.app')

@section('title', 'Dashboard - Sistem Sparepart')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-2"><div class="card"><div class="card-body"><small>Total Barang</small><h4>{{ $totalBarang }}</h4></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><small>Stok Rendah</small><h4>{{ $stokRendah }}</h4></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><small>SJ Pending</small><h4>{{ $sjPending }}</h4></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><small>Customer</small><h4>{{ $totalCustomer }}</h4></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><small>Supplier</small><h4>{{ $totalSupplier }}</h4></div></div></div>
    <div class="col-md-2"><div class="card"><div class="card-body"><small>Invoice</small><h4>{{ $totalInvoice }}</h4></div></div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-header">Tren Transaksi</div><div class="card-body"><canvas id="chartTrend"></canvas></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-header">Status Surat Jalan</div><div class="card-body"><canvas id="chartStatus"></canvas></div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card"><div class="card-header">Revenue</div><div class="card-body"><canvas id="chartRevenue"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Top Barang</div>
            <div class="card-body">
                <ul class="mb-0">
                    @forelse($topBarang as $item)
                        <li>{{ $item->nama }} ({{ $item->total_qty }})</li>
                    @empty
                        <li>Belum ada data.</li>
                    @endforelse
                </ul>
            </div>
        </div>
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
            { label: 'Surat Jalan', data: @json($sjData), borderColor: '#3b82f6' },
            { label: 'Barang Masuk', data: @json($bmData), borderColor: '#10b981' }
        ]
    }
});
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: { labels: @json($statusLabels), datasets: [{ data: @json($statusData) }] }
});
new Chart(document.getElementById('chartRevenue'), {
    type: 'bar',
    data: { labels: @json($monthLabels), datasets: [{ label: 'Revenue', data: @json($revData) }] }
});
</script>
@endpush
