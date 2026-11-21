<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function edit($id)
    {
        $supplier = Supplier::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $payment_methods = PaymentMethod::where('is_active', 1)->get();

        return view('suppliers.add_payment', compact('supplier', 'payment_methods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'     => 'required|exists:suppliers,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_method'  => 'nullable|string',
            'payment_details' => 'nullable|array',
            'note'            => 'nullable|string',
        ]);

        $supplier = Supplier::where('id', $request->supplier_id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $paymentMethod = $request->input('payment_method', 'Cash');
        if (is_numeric($paymentMethod)) {
            $methodObj = PaymentMethod::find($paymentMethod);
            if ($methodObj) {
                $paymentMethod = $methodObj->method;
            }
        }

        SupplierPayment::create([
            'supplier_id'     => $supplier->id,
            'company_id'      => auth()->user()->company_id,
            'amount'          => $request->amount,
            'payment_method'  => $paymentMethod,
            'payment_details' => $request->payment_details ?? null,
            'note'            => $request->note,
        ]);

        return redirect()->route('suppliers.balance_summary', $request->supplier_id)->with('success', 'Payment recorded.');
    }
}
