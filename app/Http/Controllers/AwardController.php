<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Employee;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\UserSetting;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
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
        

        $awards = Award::where('company_id', auth()->user()->company_id)
        ->paginate($perPage)->appends(['per_page' => $perPage]);

        return view('award.index', compact('awards', 'paginationOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users =DB::table('users')->where('company_id', auth()->user()->company_id)
        ->get(); // Fetch all employees
        return view('award.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'award' => 'required|string|max:255',
            'gift' => 'required|string|max:255',
            'date' => 'required|string|max:255'
            
        ]);
        Award::create([
            'name' => $request->name,
            'award' => $request->award,
            'gift' => $request->gift,
            'date' => $request->date,
            'company_id' => auth()->user()->company_id
            
        ]);
        
    
        return redirect('/award');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
}
