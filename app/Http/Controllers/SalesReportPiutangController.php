<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesReportPiutangController extends Controller
{
    public function index(Request $request): View
    {
        $rows = Invoice::with(['suratJalan', 'customer'])
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest('tanggal')
            ->get();

        $totalOutstanding = $rows->sum('sisa');
        $totalOverdue = $rows->filter(fn ($inv) => $inv->due_date && $inv->due_date < now())->sum('sisa');

        return view('reports.sales-piutang', compact('rows', 'totalOutstanding', 'totalOverdue'));
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $newPayment = $invoice->payment_amount + $request->amount;
        $sisa = $invoice->total - $newPayment;

        $invoice->update([
            'payment_amount' => $newPayment,
            'payment_date' => $request->payment_date,
            'sisa' => max(0, $sisa),
            'payment_status' => $sisa <= 0 ? 'paid' : 'partial',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
