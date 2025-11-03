<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\InventoryPurchases;
use Illuminate\Http\Request;
use App\http\requests\StoreInventoryRequest;
use App\InventoryRemain;
use App\Models\Setting;
use App\Models\UserSetting;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view inventories'))) {
            abort(403);
        }

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

        $inventories = Inventory::where('company_id', auth()->user()->company_id)
        ->paginate($perPage)->appends(['per_page' => $perPage]);
        
        return view('inventories.index', compact('inventories', 'paginationOptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! (auth()->user() && auth()->user()->can('create inventories'))) {
            abort(403);
        }

        $inventories_purchases = InventoryPurchases::where('company_id', auth()->user()->company_id)->get();
        return view('inventories.create', compact('inventories_purchases'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('create inventories'))) {
            abort(403);
        }

        $validate = $request->validate([
            'name'         => 'required|string|max:255|unique:inventories,name,NULL,id,company_id,' . auth()->user()->company_id,
            'low_alert'    => 'required|numeric|min:0',
            'max_alert'    => 'required|numeric|gt:low_alert',
            'description'  => 'nullable|string|max:160',
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;  

        Inventory::create($data);
    

        return redirect('/inventories');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! (auth()->user() && auth()->user()->can('edit inventories'))) {
            abort(403);
        }

        $inventory = Inventory::findOrFail($id);

        $inventories_purchases = InventoryPurchases::where('company_id', auth()->user()->company_id)->get();

        return view('inventories.edit', compact('inventory', 'inventories_purchases'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! (auth()->user() && auth()->user()->can('edit inventories'))) {
            abort(403);
        }

        $validate = $request->validate([
            'name'         => 'required|string|max:255|unique:inventories,name,NULL,id,company_id,' . auth()->user()->company_id,
            'low_alert'    => 'required|numeric|min:0',
            'max_alert'    => 'required|numeric|gt:low_alert',
            'description'  => 'nullable|string|max:160',
        ]);

        $inventory = Inventory::findOrFail($id);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;  

        $inventory->update($data);
    

        return redirect('/inventories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! (auth()->user() && auth()->user()->can('delete inventories'))) {
            abort(403);
        }

        // Find the inventory by ID
        $inventory = Inventory::findOrFail($id);

        // Delete the inventory
        $inventory->delete();

        // Redirect back with a success message
        return redirect()->route('inventories.index')->with('success', 'Inventory deleted successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function available(Request $request)
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

        $inventories = InventoryRemain::where('company_id', auth()->user()->company_id)
        ->paginate($perPage)->appends(['per_page' => $perPage]);
        
        return view('inventories.show', compact('inventories', 'paginationOptions'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkQuantity(Request $request)
    {
        $inventory_id = $request->inventory_id;

        $remain = InventoryRemain::where('inventory_id', $inventory_id)->first();
        $inventory = Inventory::find($inventory_id);

        $quantity = $remain ? $remain->total_quantity : 0;
        $low_alert = $inventory ? $inventory->low_alert : 10;

        return response()->json([
            'status' => 'success',
            'quantity' => $quantity,
            'low_alert' => $low_alert
        ]);
    }

}
