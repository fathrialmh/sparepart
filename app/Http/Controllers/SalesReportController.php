<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['suratJalan', 'customer']);

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->whereHas('suratJalan', fn ($q) => $q->where('tipe_pajak', $request->tipe));
        }

        if ($request->filled('from')) {
            $query->where('tanggal', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('tanggal', '<=', $request->to);
        }

        $rows = $query->latest('tanggal')->get();

        $totalAmount = $rows->sum('total');
        $totalPaid = $rows->where('payment_status', 'paid')->sum('total');
        $totalUnpaid = $rows->where('payment_status', 'unpaid')->sum('total');

        return view('reports.sales', compact('rows', 'totalAmount', 'totalPaid', 'totalUnpaid'));
    }
}
