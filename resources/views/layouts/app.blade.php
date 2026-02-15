<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Sparepart')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand">Sistem Sparepart</div>
        <nav class="nav flex-column">
            @if(auth()->user()->role === 'admin')
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a class="nav-link {{ request()->routeIs('barang.*') ? 'active' : '' }}" href="{{ route('barang.index') }}"><i class="bi bi-box-seam me-2"></i>Barang</a>
                <a class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}"><i class="bi bi-building me-2"></i>Supplier</a>
                <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" href="{{ route('customer.index') }}"><i class="bi bi-people me-2"></i>Customer</a>
                <a class="nav-link {{ request()->routeIs('surat-jalan.*') ? 'active' : '' }}" href="{{ route('surat-jalan.index') }}"><i class="bi bi-truck me-2"></i>Surat Jalan</a>
                <a class="nav-link {{ request()->routeIs('invoice.*') ? 'active' : '' }}" href="{{ route('invoice.index') }}"><i class="bi bi-receipt me-2"></i>Invoice</a>
                <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}"><i class="bi bi-shield-lock me-2"></i>User Management</a>
            @else
                <a class="nav-link {{ request()->routeIs('surat-jalan.*') ? 'active' : '' }}" href="{{ route('surat-jalan.index') }}"><i class="bi bi-truck me-2"></i>Surat Jalan</a>
            @endif
        </nav>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h5 class="mb-0">@yield('page_title', 'Dashboard')</h5>
                <small class="text-muted">Selamat datang, {{ auth()->user()->nama }}</small>
            </div>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>

        @unless(View::hasSection('use_toast'))
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endunless

        @yield('content')
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
