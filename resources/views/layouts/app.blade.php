<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Adam Jaya Management System')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/filament-style.css') }}" rel="stylesheet">
    <style>
        .tab-button {
            padding: 0.75rem 1.25rem;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
            transition: all 0.2s;
            margin-bottom: -2px;
        }
        .tab-button:hover { color: rgb(249 115 22); background: var(--gray-100); }
        .tab-button.active { color: rgb(249 115 22); border-bottom-color: rgb(249 115 22); background: white; }

        .type-selection-card {
            padding: 2rem 1.5rem;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .type-selection-card:hover {
            border-color: rgb(249 115 22);
            background: rgb(255 247 237);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .btn-success {
            padding: 0.625rem 1rem;
            background: rgb(22 163 74);
            border: 1px solid transparent;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: background-color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-success:hover { background: rgb(21 128 61); }

        .btn-warning {
            padding: 0.625rem 1rem;
            background: rgb(234 179 8);
            border: 1px solid transparent;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: background-color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-warning:hover { background: rgb(202 138 4); }

        .btn-info {
            padding: 0.625rem 1rem;
            background: rgb(37 99 235);
            border: 1px solid transparent;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: background-color 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-info:hover { background: rgb(29 78 216); }

        .text-muted { color: var(--gray-500); }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .d-flex { display: flex; }
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .mb-0 { margin-bottom: 0; }
        .mt-2 { margin-top: 0.5rem; }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--gray-500);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h6 { font-size: 1rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.5rem; }

        .badge-success { background: rgb(220 252 231); color: rgb(22 101 52); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="filament-app">
        <aside class="filament-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="logo-icon">📦</span>
                    <span class="logo-text">Adam Jaya</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                @if(auth()->user()->role === 'admin')
                <div class="nav-group">
                    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </div>
                <div class="nav-group">
                    <div class="nav-group-label">💼 Penjualan</div>
                    <a href="{{ route('customer.index') }}" class="nav-item {{ request()->routeIs('customer.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label">Customers</span>
                    </a>
                    <a href="{{ route('price-quotation.index') }}" class="nav-item {{ request()->routeIs('price-quotation.*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span class="nav-label">Surat Penawaran</span>
                    </a>
                    <a href="{{ route('surat-jalan.index') }}" class="nav-item {{ request()->routeIs('surat-jalan.*') ? 'active' : '' }}">
                        <span class="nav-icon">🚚</span>
                        <span class="nav-label">Surat Jalan</span>
                    </a>
                    <a href="{{ route('invoice.index') }}" class="nav-item {{ request()->routeIs('invoice.*') ? 'active' : '' }}">
                        <span class="nav-icon">💰</span>
                        <span class="nav-label">Invoice</span>
                    </a>
                    <a href="{{ route('nota-menyusul.index') }}" class="nav-item {{ request()->routeIs('nota-menyusul.*') ? 'active' : '' }}">
                        <span class="nav-icon">📝</span>
                        <span class="nav-label">Nota Menyusul</span>
                    </a>
                    <a href="{{ route('keterangan-lain.index') }}" class="nav-item {{ request()->routeIs('keterangan-lain.*') ? 'active' : '' }}">
                        <span class="nav-icon">📄</span>
                        <span class="nav-label">Keterangan Lain</span>
                    </a>
                </div>
                <div class="nav-group">
                    <div class="nav-group-label">🛒 Pembelian</div>
                    <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
                        <span class="nav-icon">📦</span>
                        <span class="nav-label">Master Barang/Stock</span>
                    </a>
                    <a href="{{ route('supplier.index') }}" class="nav-item {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏭</span>
                        <span class="nav-label">Suppliers</span>
                    </a>
                    <a href="{{ route('purchase-order.index') }}" class="nav-item {{ request()->routeIs('purchase-order.*') ? 'active' : '' }}">
                        <span class="nav-icon">🛍️</span>
                        <span class="nav-label">Pembelian Barang (PO)</span>
                    </a>
                </div>
                <div class="nav-group">
                    <div class="nav-group-label">📈 Laporan</div>
                    <a href="{{ route('reports.sales') }}" class="nav-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('reports.purchase') }}" class="nav-item {{ request()->routeIs('reports.purchase') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">Laporan Pembelian</span>
                    </a>
                    <a href="{{ route('reports.inventory') }}" class="nav-item {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                        <span class="nav-icon">📦</span>
                        <span class="nav-label">Laporan Inventory</span>
                    </a>
                </div>
                <div class="nav-group">
                    <div class="nav-group-label">⚙️ Master Data</div>
                    <a href="{{ route('user.index') }}" class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <span class="nav-icon">👤</span>
                        <span class="nav-label">Users</span>
                    </a>
                    <a href="{{ route('role.index') }}" class="nav-item {{ request()->routeIs('role.*') ? 'active' : '' }}">
                        <span class="nav-icon">🔐</span>
                        <span class="nav-label">Roles & Permissions</span>
                    </a>
                </div>
                @else
                <div class="nav-group">
                    <a href="{{ route('surat-jalan.index') }}" class="nav-item {{ request()->routeIs('surat-jalan.*') ? 'active' : '' }}">
                        <span class="nav-icon">🚚</span>
                        <span class="nav-label">Surat Jalan</span>
                    </a>
                </div>
                @endif
            </nav>
            <div class="sidebar-footer">
                <div class="user-menu">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</div>
                    <div class="user-info" style="border:none; margin:0; padding:0;">
                        <div class="user-name">{{ auth()->user()->nama }}</div>
                        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="filament-main">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle" type="button" title="Toggle Sidebar" aria-label="Toggle Sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <div class="breadcrumb">
                        @hasSection('breadcrumb')
                            @yield('breadcrumb')
                        @else
                            <span class="breadcrumb-item">@yield('page_title', 'Dashboard')</span>
                        @endif
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="user-menu">
                        <button class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</button>
                        <div class="user-dropdown">
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->nama }}</div>
                                <span class="badge warning">{{ ucfirst(auth()->user()->role) }}</span>
                            </div>
                            <div class="user-menu-items">
                                <form method="post" action="{{ route('logout') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="user-menu-item" style="width:100%; text-align:left; background:none; border:none; cursor:pointer; font-size:0.875rem;">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/filament-dashboard.js') }}"></script>
    <script>
        @if(session('success'))
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:{!! json_encode(session('success')) !!}, showConfirmButton:false, timer:3000, timerProgressBar:true });
        @endif
        @if(session('error'))
            Swal.fire({ toast:true, position:'top-end', icon:'error', title:{!! json_encode(session('error')) !!}, showConfirmButton:false, timer:4000, timerProgressBar:true });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
