<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KeteranganLainController;
use App\Http\Controllers\NotaMenyusulController;
use App\Http\Controllers\PriceQuotationController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\PurchaseReportHutangController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SalesReportPiutangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', function () {
        return auth()->user()->role === 'admin'
            ? redirect()->route('dashboard')
            : redirect()->route('surat-jalan.index');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('admin');

    Route::middleware('admin')->group(function (): void {
        // Existing CRUD
        Route::resource('barang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('supplier', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('customer', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('barang-masuk', BarangMasukController::class)->only(['store']);
        Route::get('barang-masuk', fn () => redirect()->route('barang.index'))->name('barang-masuk.index');
        Route::resource('invoice', InvoiceController::class)->only(['index', 'store']);
        Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);

        // Price Quotations
        Route::resource('price-quotation', PriceQuotationController::class)->only(['index', 'store', 'update', 'destroy']);

        // Purchase Orders
        Route::resource('purchase-order', PurchaseOrderController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('purchase-order/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-order.status');

        // Nota Menyusul
        Route::resource('nota-menyusul', NotaMenyusulController::class)->only(['index', 'store', 'update', 'destroy']);

        // Keterangan Lain
        Route::resource('keterangan-lain', KeteranganLainController::class)->only(['index', 'store', 'update', 'destroy']);

        // Roles & Permissions
        Route::resource('role', RoleController::class)->only(['index', 'store', 'update', 'destroy']);

        // Reports
        Route::get('reports/sales', [SalesReportController::class, 'index'])->name('reports.sales');
        Route::get('reports/sales-piutang', [SalesReportPiutangController::class, 'index'])->name('reports.sales-piutang');
        Route::post('reports/sales-piutang/payment', [SalesReportPiutangController::class, 'recordPayment'])->name('reports.sales-piutang.payment');
        Route::get('reports/purchase', [PurchaseReportController::class, 'index'])->name('reports.purchase');
        Route::get('reports/purchase-hutang', [PurchaseReportHutangController::class, 'index'])->name('reports.purchase-hutang');
        Route::post('reports/purchase-hutang/payment', [PurchaseReportHutangController::class, 'recordPayment'])->name('reports.purchase-hutang.payment');
        Route::get('reports/inventory', [InventoryReportController::class, 'index'])->name('reports.inventory');
    });

    Route::resource('surat-jalan', SuratJalanController::class)->only(['index', 'store']);
    Route::patch('surat-jalan/{suratJalan}/status', [SuratJalanController::class, 'updateStatus'])->name('surat-jalan.status');
    Route::get('surat-jalan/{suratJalan}/print', [SuratJalanController::class, 'print'])->name('surat-jalan.print');
    Route::get('invoice/{invoice}/print', [InvoiceController::class, 'print'])->name('invoice.print');
});
