<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
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
        Route::resource('barang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('supplier', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('customer', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('barang-masuk', BarangMasukController::class)->only(['store']);
        Route::get('barang-masuk', fn () => redirect()->route('barang.index'))->name('barang-masuk.index');
        Route::resource('invoice', InvoiceController::class)->only(['index', 'store']);
        Route::resource('user', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    });

    Route::resource('surat-jalan', SuratJalanController::class)->only(['index', 'store']);
    Route::patch('surat-jalan/{suratJalan}/status', [SuratJalanController::class, 'updateStatus'])->name('surat-jalan.status');
    Route::get('surat-jalan/{suratJalan}/print', [SuratJalanController::class, 'print'])->name('surat-jalan.print');
    Route::get('invoice/{invoice}/print', [InvoiceController::class, 'print'])->name('invoice.print');
});
