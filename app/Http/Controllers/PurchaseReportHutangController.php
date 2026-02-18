<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseReportHutangController extends Controller
{
    public function index(Request $request): View
    {
        $rows = PurchaseOrder::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest('tanggal')
            ->get();

        $totalOutstanding = $rows->sum(fn ($po) => $po->total - $po->payment_amount);
        $totalOverdue = $rows->filter(fn ($po) => $po->due_date && $po->due_date < now())
            ->sum(fn ($po) => $po->total - $po->payment_amount);

        return view('reports.purchase-hutang', compact('rows', 'totalOutstanding', 'totalOverdue'));
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $po = PurchaseOrder::findOrFail($request->purchase_order_id);
        $newPayment = $po->payment_amount + $request->amount;
        $remaining = $po->total - $newPayment;

        $po->update([
            'payment_amount' => $newPayment,
            'payment_date' => $request->payment_date,
            'payment_status' => $remaining <= 0 ? 'paid' : 'partial',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
