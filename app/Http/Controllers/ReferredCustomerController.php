<?php

namespace App\Http\Controllers;

use App\ReferredCustomer;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReferredCustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ReferredCustomer::class);

        // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['referred_customers_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('referred_customers_rows_per_page', auth()->user()->default_rows);

        $referreds = ReferredCustomer::forCompany()->orderBy('id', 'desc')->paginate($rows);

        return view('referred_customers.index', compact('referreds', 'rows'));
    }

    public function create()
    {
        $this->authorize('create', ReferredCustomer::class);

        return view('referred_customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', ReferredCustomer::class);

        $data = $request->validate([
            'referred_by' => 'required|string|max:120',
            'is_active' => 'nullable|boolean',
        ]);

        $data['company_id'] = Auth::user()->company_id ?? null;
        $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;

        ReferredCustomer::create($data);

        return redirect()->route('referred_customers.index')->with('success', 'Referred source created.');
    }

    public function show($id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);
        $this->authorize('view', $referred);

        return view('referred_customers.show', compact('referred'));
    }

    public function edit($id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);
        $this->authorize('update', $referred);

        return view('referred_customers.edit', compact('referred'));
    }

    public function update(Request $request, $id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);
        $this->authorize('update', $referred);

        $data = $request->validate([
            'referred_by' => 'required|string|max:120',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? (bool)$request->is_active : false;

        $referred->update($data);

        return redirect()->route('referred_customers.index')->with('update', 'Referred source updated.');
    }

    public function destroy($id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);
        $this->authorize('delete', $referred);

        $referred->delete();
        return redirect()->route('referred_customers.index')->with('destroy', 'Referred source deleted.');
    }

    public function referredStatus(Request $request)
    {
        $category = ReferredCustomer::find($request->id);



        $category->is_active = $request->status;



        $category->save();



        return response()->json(['success'=>'Status changed successfully.']);

    }
}
