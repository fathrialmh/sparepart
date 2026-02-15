<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\SuratJalan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBarang = Barang::count();
        $stokRendah = Barang::where('stok', '<=', 10)->count();
        $sjPending = SuratJalan::where('status', 'pending')->count();
        $totalCustomer = Customer::count();
        $totalSupplier = Supplier::count();
        $totalInvoice = Invoice::count();

        $months = collect(range(5, 0))
            ->map(fn ($i) => now()->subMonths($i)->format('Y-m'))
            ->push(now()->format('Y-m'))
            ->values();

        $sjMonthly = SuratJalan::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('tanggal', '>=', now()->subMonths(6)->toDateString())
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $bmMonthly = BarangMasuk::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('tanggal', '>=', now()->subMonths(6)->toDateString())
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $revenueMonthly = Invoice::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, SUM(total) as revenue")
            ->where('tanggal', '>=', now()->subMonths(6)->toDateString())
            ->groupBy('bulan')
            ->pluck('revenue', 'bulan');

        $monthLabels = $months
            ->map(fn ($m) => \DateTime::createFromFormat('Y-m', $m)?->format('M Y') ?? $m)
            ->values();

        $sjData = $months->map(fn ($m) => (int) ($sjMonthly[$m] ?? 0))->values();
        $bmData = $months->map(fn ($m) => (int) ($bmMonthly[$m] ?? 0))->values();
        $revData = $months->map(fn ($m) => (float) ($revenueMonthly[$m] ?? 0))->values();

        $sjStatus = SuratJalan::selectRaw('status, COUNT(*) as total')->groupBy('status')->get();
        $statusLabels = $sjStatus->pluck('status')->map(fn ($s) => ucfirst($s))->values();
        $statusData = $sjStatus->pluck('total')->map(fn ($v) => (int) $v)->values();

        $topBarang = \DB::table('surat_jalan_detail as sjd')
            ->join('barang as b', 'b.id', '=', 'sjd.barang_id')
            ->selectRaw('b.nama, SUM(sjd.qty) as total_qty')
            ->groupBy('sjd.barang_id', 'b.nama')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $stokRendahList = Barang::select('kode', 'nama', 'stok', 'satuan')
            ->where('stok', '<=', 10)
            ->orderBy('stok')
            ->limit(5)
            ->get();

        $latestTransactions = SuratJalan::query()
            ->join('customers as c', 'c.id', '=', 'surat_jalan.customer_id')
            ->select('surat_jalan.nomor', 'surat_jalan.tanggal', 'c.nama as customer_nama', 'surat_jalan.status', 'surat_jalan.tipe_pajak')
            ->latest('surat_jalan.id')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalBarang',
            'stokRendah',
            'sjPending',
            'totalCustomer',
            'totalSupplier',
            'totalInvoice',
            'monthLabels',
            'sjData',
            'bmData',
            'revData',
            'statusLabels',
            'statusData',
            'topBarang',
            'stokRendahList',
            'latestTransactions',
        ));
    }
}
