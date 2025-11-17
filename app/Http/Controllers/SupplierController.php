<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view suppliers'))) {
            abort(403);
        }

        // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['suppliers_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('suppliers_rows_per_page', auth()->user()->default_rows);

        $suppliers = Supplier::where('company_id', auth()->user()->company_id)
        ->paginate($rows);

        return view('suppliers.index', compact('suppliers', 'rows'));
    }

    public function create()
    {
        if (! (auth()->user() && auth()->user()->can('create suppliers'))) {
            abort(403);
        }

        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('create suppliers'))) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('edit suppliers'))) {
            abort(403);
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('edit suppliers'))) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if (! (auth()->user() && auth()->user()->can('delete suppliers'))) {
            abort(403);
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('destroy', 'Supplier deleted successfully.');
    }

    public function balanceSummary($supplierId)
    {
        if (! (auth()->user() && auth()->user()->can('supplier summary'))) {
            abort(403);
        }

        $supplier = Supplier::with(['purchases.inventory', 'payments'])->findOrFail($supplierId);
        // dd($supplier);
        $totalPurchases = $supplier->purchases->sum('price');
        $totalPaid = $supplier->payments->sum('amount');
        $totalPending = $totalPurchases - $totalPaid;
    
        return view('suppliers.balance_summary', compact(
            'supplier', 'totalPurchases', 'totalPaid', 'totalPending'
        ));
    }
}
