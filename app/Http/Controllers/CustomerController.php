<?php

namespace App\Http\Controllers;

use App\Helpers\NumberGenerator;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $editData = request('edit') ? Customer::find((int) request('edit')) : null;
        $rows = Customer::latest('id')->get();

        return view('customer.index', compact('rows', 'editData'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated() + [
            'kode' => NumberGenerator::generateMasterCode('customers', 'kode', 'CUS'),
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'Customer berhasil dihapus.');
    }
}
