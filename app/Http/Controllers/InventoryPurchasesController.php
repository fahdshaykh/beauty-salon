<?php

namespace App\Http\Controllers;


use App\InventoryPurchases;
use Illuminate\Http\Request;
use App\http\requests\StoreInventoryPurchaseRequest;
use App\Inventory;
use App\Models\Supplier;
use App\Models\SupplierBalance;
use App\Models\SupplierPayment;
use App\Models\Setting;
use App\Models\UserSetting;

class InventoryPurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginationOptions = Setting::getPaginationOptions();
        $perPage = $request->input('per_page', auth()->user()->getPerPagePreference());
        if (!in_array($perPage, $paginationOptions)) {
            $perPage = auth()->user()->getPerPagePreference();
        }
        if ($request->has('per_page')) {
            if (auth()->user()->setting) {
                auth()->user()->setting->update(['per_page' => $perPage]);
            } else {
                UserSetting::create(['user_id' => auth()->user()->id, 'per_page' => $perPage]);
            }
        }

        $purchases = InventoryPurchases::where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')
        ->paginate($perPage)->appends(['per_page' => $perPage]);

        return view('inventories.inventories_purchase.index', compact('purchases', 'paginationOptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $inventories = Inventory::where('company_id', auth()->user()->company_id)->get();
        $suppliers = Supplier::where('is_active', 1)->where('company_id', auth()->user()->company_id)->get();

        return view('inventories.inventories_purchase.create', compact('inventories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;  
        $data['amount_pending'] = $request->price - $request->amount_paid;
        // dd($data);
        $purchase = InventoryPurchases::create($data);

        if ($request->amount_paid > 0) {
            SupplierPayment::create([
                'supplier_id' => $request->supplier_id,
                'inventory_purchase_id' => $purchase->id,
                'amount' => $request->amount_paid,
                'note' => 'Initial payment during purchase',
            ]);
        }
    

        return redirect(route('inventories_purchases.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InventoryPurchases  $inventoryPurchases
     * @return \Illuminate\Http\Response
     */
    public function show(InventoryPurchases $inventoryPurchases)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InventoryPurchases  $inventoryPurchases
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $inventories = Inventory::where('company_id', auth()->user()->company_id)->get();
        $suppliers = Supplier::where('is_active', 1)->where('company_id', auth()->user()->company_id)->get();
        $purchase = InventoryPurchases::findOrFail($id);
        return view('inventories.inventories_purchase.edit', compact('purchase', 'inventories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InventoryPurchases  $inventoryPurchases
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $purchase = InventoryPurchases::findOrFail($id);
    
        // Validation for price and amount_paid (optional, can add if needed)
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'amount_paid' => 'required|numeric|min:0',
        ]);
    
        // Calculate new pending amount
        $newAmountPending = $request->price - $request->amount_paid;
    
        // Update the inventory purchase
        $purchase->update([
            'supplier_id' => $request->supplier_id,
            'inventory_id' => $request->inventory_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'amount_paid' => $request->amount_paid,
            'amount_pending' => $newAmountPending,
            'payment_type' => $request->payment_type,
            'description' => $request->description,
        ]);
    
        // If amount paid has changed, update or add new SupplierPayment
        $totalPaid = $purchase->payments()->sum('amount'); // Total paid amount for this purchase
        if ($request->amount_paid > $totalPaid) {
            // Add new payment if the amount paid is greater
            SupplierPayment::create([
                'supplier_id' => $request->supplier_id,
                'inventory_purchase_id' => $purchase->id,
                'amount' => $request->amount_paid - $totalPaid,
                'note' => 'Updated payment during purchase',
            ]);
        } elseif ($request->amount_paid < $totalPaid) {
            // If amount paid decreases, reverse some payments
            $amountToReverse = $totalPaid - $request->amount_paid;
            $paymentsToReverse = SupplierPayment::where('inventory_purchase_id', $purchase->id)
                                                ->where('amount', '>=', $amountToReverse)
                                                ->orderByDesc('created_at')
                                                ->limit(1)
                                                ->get();
    
            foreach ($paymentsToReverse as $payment) {
                $payment->delete();
                $amountToReverse -= $payment->amount;
                if ($amountToReverse <= 0) {
                    break;
                }
            }
        }
    
        // Redirect to the list of purchases
        return redirect(route('inventories_purchases.index'))->with('success', 'Purchase updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InventoryPurchases  $inventoryPurchases
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = InventoryPurchases::findOrFail($id);

        // Optional: Log reverse payments or notify
        $purchase->payments()->delete(); // Delete related payment records
    
        $purchase->delete();
    
        return redirect()->back()->with('success', 'Purchase and payments reversed.');
    }
}
