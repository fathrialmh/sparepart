<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
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

        return view('reports.purchase', compact('rows', 'totalAmount', 'totalPaid', 'totalUnpaid'));
    }
}
