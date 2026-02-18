@extends('layouts.app')

@section('title', 'Laporan Inventory')
@section('page_title', 'Laporan Inventory')

@section('breadcrumb')
<a href="{{ route('dashboard') }}" class="breadcrumb-item">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Laporan</span>
<span class="breadcrumb-separator">/</span>
<span class="breadcrumb-item">Inventory</span>
@endsection

@section('content')

{{-- Header --}}
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="margin: 0;">Laporan Inventory</h1>
        <p class="page-description" style="margin-top: 0.25rem;">Monitor stock barang secara real-time</p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom: 1.5rem;">
    <div class="stat-widget">
        <div class="stat-header">
            <span class="stat-label">Total Items</span>
            <span class="stat-icon"><i class="bi bi-box-seam"></i></span>
        </div>
        <div class="stat-value">{{ $totalItems }}</div>
        <div class="stat-footer">
            <span class="stat-description">Jenis barang</span>
        </div>
    </div>
    <div class="stat-widget success">
        <div class="stat-header">
            <span class="stat-label">Total Value</span>
            <span class="stat-icon"><i class="bi bi-cash-stack"></i></span>
        </div>
        <div class="stat-value" style="font-size: 1.25rem;">@rupiah($totalValue)</div>
        <div class="stat-footer">
            <span class="stat-description">Nilai inventory</span>
        </div>
    </div>
    <div class="stat-widget warning">
        <div class="stat-header">
            <span class="stat-label">Low Stock</span>
            <span class="stat-icon"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <div class="stat-value">{{ $lowStock }}</div>
        <div class="stat-footer">
            <span class="stat-description">Stok menipis</span>
        </div>
    </div>
    <div class="stat-widget danger">
        <div class="stat-header">
            <span class="stat-label">Out of Stock</span>
            <span class="stat-icon"><i class="bi bi-x-circle"></i></span>
        </div>
        <div class="stat-value">{{ $outOfStock }}</div>
        <div class="stat-footer">
            <span class="stat-description">Stok habis</span>
        </div>
    </div>
</div>

{{-- Tabs & Filter --}}
<div class="widget-card" style="margin-bottom: 1.5rem;">
    {{-- Tabs --}}
    <div style="padding: 0 1rem; border-bottom: 2px solid var(--gray-200); background: var(--gray-50); display: flex; gap: 0;">
        @php
            $currentStatus = request('stock_status', 'all');
            $tabs = [
                'all'    => ['label' => 'All', 'icon' => 'bi-box-seam', 'count' => $rows->count()],
                'normal' => ['label' => 'Normal', 'icon' => 'bi-check-circle', 'count' => $rows->where('stok', '>', 10)->count()],
                'low'    => ['label' => 'Low Stock', 'icon' => 'bi-exclamation-triangle', 'count' => $lowStock],
                'out'    => ['label' => 'Out of Stock', 'icon' => 'bi-x-circle', 'count' => $outOfStock],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
        <a href="{{ route('reports.inventory', array_merge(request()->except('stock_status'), $key !== 'all' ? ['stock_status' => $key] : [])) }}"
           class="tab-button {{ $currentStatus === $key ? 'active' : '' }}"
           style="text-decoration: none;">
            <i class="bi {{ $tab['icon'] }}"></i>
            <span>{{ $tab['label'] }}</span>
            <span class="badge {{ $key === 'out' ? 'danger' : ($key === 'low' ? 'warning' : ($key === 'normal' ? 'success' : 'gray')) }}" style="margin-left: 0.25rem;">
                {{ $tab['count'] }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="card-body">
        <form method="GET" action="{{ route('reports.inventory') }}" style="display: flex; gap: 1rem; align-items: flex-end;">
            @if(request('stock_status'))
                <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif
            <div class="form-group" style="margin-bottom: 0; min-width: 200px;">
                <label class="form-label">Tipe Barang</label>
                <select name="tipe" class="form-select">
                    <option value="">Semua Tipe</option>
                    <option value="lokal" {{ request('tipe') == 'lokal' ? 'selected' : '' }}>Lokal</option>
                    <option value="impor" {{ request('tipe') == 'impor' ? 'selected' : '' }}>Impor</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn-primary btn-sm">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('reports.inventory') }}" class="btn-secondary btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="widget-card">
    <div class="card-header">
        <h3 class="card-title">Data Inventory</h3>
        <span style="color: var(--gray-500); font-size: 0.85rem;">{{ $rows->count() }} data</span>
    </div>
    <div class="card-body no-padding">
        <table class="filament-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Tipe</th>
                    <th>Satuan</th>
                    <th style="text-align: right;">Stok</th>
                    <th style="text-align: right;">Harga Beli</th>
                    <th style="text-align: right;">Total Value</th>
                    <th>Supplier</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                @php
                    $stok = $row->stok ?? 0;
                    $hargaBeli = $row->harga_beli ?? 0;
                    $totalVal = $stok * $hargaBeli;
                    $tipe = $row->tipe ?? 'lokal';

                    if ($stok <= 0) {
                        $stockStatus = 'out';
                        $stockBadgeClass = 'danger';
                        $stockLabel = 'Out of Stock';
                    } elseif ($stok <= 10) {
                        $stockStatus = 'low';
                        $stockBadgeClass = 'warning';
                        $stockLabel = 'Low Stock';
                    } else {
                        $stockStatus = 'normal';
                        $stockBadgeClass = 'success';
                        $stockLabel = 'Normal';
                    }
                @endphp
                <tr>
                    <td><strong>{{ $row->kode }}</strong></td>
                    <td>
                        <div style="font-weight: 500;">{{ $row->nama }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $tipe === 'impor' ? 'warning' : 'info' }}">
                            {{ ucfirst($tipe) }}
                        </span>
                    </td>
                    <td>{{ $row->satuan ?? '-' }}</td>
                    <td style="text-align: right;">
                        <span class="badge {{ $stockBadgeClass }}" style="font-weight: bold; font-size: 0.9rem;">
                            {{ number_format($stok, 0, ',', '.') }}
                        </span>
                    </td>
                    <td style="text-align: right;">@rupiah($hargaBeli)</td>
                    <td style="text-align: right;"><strong>@rupiah($totalVal)</strong></td>
                    <td>{{ $row->supplier->nama ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $stockBadgeClass }}">
                            {{ $stockLabel }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 2rem; color: var(--gray-500);">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">Tidak ada data inventory</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($rows->count() > 0)
            <tfoot style="background: var(--gray-50); font-weight: bold;">
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 1rem;">TOTAL NILAI INVENTORY:</td>
                    <td style="text-align: right; color: var(--success-600); font-size: 1.05rem;">@rupiah($totalValue)</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection
