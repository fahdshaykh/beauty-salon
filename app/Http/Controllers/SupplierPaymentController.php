<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function edit($id)
    {
        $supplier = Supplier::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();
        return view('suppliers.add_payment', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string',
        ]);

        $supplier = Supplier::where('id', $request->supplier_id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'company_id' => auth()->user()->company_id,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        return redirect()->route('suppliers.balance_summary', $request->supplier_id)->with('success', 'Payment recorded.');
    }
}
