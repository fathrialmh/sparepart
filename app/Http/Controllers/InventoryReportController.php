<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Barang::with('supplier');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'low' => $query->where('stok', '<=', 10)->where('stok', '>', 0),
                'out' => $query->where('stok', '<=', 0),
                'normal' => $query->where('stok', '>', 10),
                default => null,
            };
        }

        $rows = $query->orderBy('nama')->get();

        $totalItems = Barang::count();
        $totalValue = Barang::selectRaw('SUM(stok * harga_beli) as total')->value('total') ?? 0;
        $lowStock = Barang::where('stok', '<=', 10)->where('stok', '>', 0)->count();
        $outOfStock = Barang::where('stok', '<=', 0)->count();

        return view('reports.inventory', compact('rows', 'totalItems', 'totalValue', 'lowStock', 'outOfStock'));
    }
}
