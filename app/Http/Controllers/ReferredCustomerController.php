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

        $perPage = $request->get('per_page', 10);
        $referreds = ReferredCustomer::forCompany()->orderBy('id', 'desc')->paginate($perPage);
        return view('referred_customers.index', compact('referreds', 'paginationOptions'));
    }

    public function create()
    {
        return view('referred_customers.create');
    }

    public function store(Request $request)
    {
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
        return view('referred_customers.show', compact('referred'));
    }

    public function edit($id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);
        return view('referred_customers.edit', compact('referred'));
    }

    public function update(Request $request, $id)
    {
        $referred = ReferredCustomer::forCompany()->findOrFail($id);

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
        $referred->delete();
        return redirect()->route('referred_customers.index')->with('destroy', 'Referred source deleted.');
    }
}
