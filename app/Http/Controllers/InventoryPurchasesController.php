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
use Illuminate\Support\Facades\Auth;

class InventoryPurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view inventory purchases'))) {
            abort(403);
        }

        // Rows per page logic
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            auth()->user()->setSettings(['inventories_purchases_rows_per_page' => $rows]);
        }
        $rows = auth()->user()->getSetting('inventories_purchases_rows_per_page', auth()->user()->default_rows ?? 15);

        // Start building the query
        $query = InventoryPurchases::where('company_id', auth()->user()->company_id);

        // Filter by Supplier
        if ($request->filled('supplier_id') && $request->supplier_id !== '') {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
            // or use 'created_at' if you store purchase date there
            // $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
            // or: $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute query with pagination
        $purchases = $query->orderBy('created_at', 'desc')->paginate($rows)->withQueryString();

        // Get suppliers for dropdown
        $suppliers = Supplier::where('company_id', auth()->user()->company_id)->get();

        return view('inventories.inventories_purchase.index', compact('purchases', 'rows', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! (auth()->user() && auth()->user()->can('create inventory purchases'))) {
            abort(403);
        }

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
        if (! (auth()->user() && auth()->user()->can('create inventory purchases'))) {
            abort(403);
        }

        $validate = $request->validate([
            'inventory_id'   => 'required|exists:inventories,id',
            'quantity'       => 'required|numeric|min:1',
            'price'          => 'required|numeric|min:0',
            'supplier_id'    => 'required|exists:suppliers,id',
            'paypemnt_type'  => 'required|in:cash,check',
            'amount_paid'    => 'required|numeric|min:0|lte:price',
            'purchase_date'  => 'required|date',
            'description'    => 'nullable|string|max:160',
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;  
        $data['amount_pending'] = $request->price - $request->amount_paid;
        // dd($data);
        $purchase = InventoryPurchases::create($data);

        if ($request->amount_paid > 0) {
            SupplierPayment::create([
                'supplier_id' => $request->supplier_id,
                'company_id' => auth()->user()->company_id,
                'inventory_purchase_id' => $purchase->id,
                'amount' => $request->amount_paid,
                'note' => 'Initial payment during purchase',
            ]);
        }
    

        return redirect(route('inventories_purchases.index'))->with('success', 'Inventory item purchased successfully');
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
        if (! (auth()->user() && auth()->user()->can('edit inventory purchases'))) {
            abort(403);
        }

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
        if (! (auth()->user() && auth()->user()->can('edit inventory purchases'))) {
            abort(403);
        }

        $validate = $request->validate([
            'inventory_id'   => 'required|exists:inventories,id',
            'quantity'       => 'required|numeric|min:1',
            'price'          => 'required|numeric|min:0',
            'supplier_id'    => 'required|exists:suppliers,id',
            'paypemnt_type'  => 'required|in:cash,check',
            'amount_paid'    => 'nullable|numeric|min:0|lte:price',
            'description'    => 'nullable|string|max:160',
        ]);

        $purchase = InventoryPurchases::findOrFail($id);
    
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
        // $totalPaid = $purchase->payments()->sum('amount'); // Total paid amount for this purchase
        // if ($request->amount_paid > $totalPaid) {
        //     // Add new payment if the amount paid is greater
        //     SupplierPayment::create([
        //         'supplier_id' => $request->supplier_id,
        //         'company_id' => auth()->user()->company_id,
        //         'inventory_purchase_id' => $purchase->id,
        //         'amount' => $request->amount_paid - $totalPaid,
        //         'note' => 'Updated payment during purchase',
        //     ]);
        // } elseif ($request->amount_paid < $totalPaid) {
        //     // If amount paid decreases, reverse some payments
        //     $amountToReverse = $totalPaid - $request->amount_paid;
        //     $paymentsToReverse = SupplierPayment::where('inventory_purchase_id', $purchase->id)
        //                                         ->where('amount', '>=', $amountToReverse)
        //                                         ->orderByDesc('created_at')
        //                                         ->limit(1)
        //                                         ->get();
    
        //     foreach ($paymentsToReverse as $payment) {
        //         $payment->delete();
        //         $amountToReverse -= $payment->amount;
        //         if ($amountToReverse <= 0) {
        //             break;
        //         }
        //     }
        // }

        if ($request->amount_paid) {
            $supplierPayment = SupplierPayment::where('inventory_purchase_id', $purchase->id)->first();
            $supplierPayment->amount = $request->amount_paid;
            $supplierPayment->save();
        }
    
        // Redirect to the list of purchases
        return redirect(route('inventories_purchases.index'))->with('success', 'Inventory item updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InventoryPurchases  $inventoryPurchases
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! (auth()->user() && auth()->user()->can('delete inventory purchases'))) {
            abort(403);
        }

        $purchase = InventoryPurchases::findOrFail($id);

        // Optional: Log reverse payments or notify
        $purchase->payments()->delete(); // Delete related payment records
    
        $purchase->delete();
    
        return redirect()->back()->with('destroy', 'Purchase and payments reversed.');
    }
}
