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
        // Authorize listing
        $this->authorize('viewAny', Award::class);
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

        return view('awards.index', compact('awards', 'paginationOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorize create
        $this->authorize('create', Award::class);
        $users =DB::table('users')->where('company_id', auth()->user()->company_id)
        ->get(); // Fetch all employees
        return view('awards.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Authorize create
        $this->authorize('create', Award::class);
        $request->validate([
            'user_id' => 'required',
            'award' => 'required|string|max:255',
            'gift' => 'required|string|max:255',
            'date' => 'required|string|max:255'
            
        ]);
        Award::create([
            'user_id' => $request->user_id,
            'award' => $request->award,
            'gift' => $request->gift,
            'date' => $request->date,
            'company_id' => auth()->user()->company_id
            
        ]);
        
    
        return redirect('/awards')->with('success', 'Award created successfully.');
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
    public function edit(Award $award)
    {
        // Authorize update (policy will enforce company scope)
        $this->authorize('update', $award);
        $users =DB::table('users')->where('company_id', auth()->user()->company_id)->get();

        return view('awards.edit', compact('award', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Award $award)
    {
        // Authorize update (policy will enforce company scope)
        $this->authorize('update', $award);
        $request->validate([
            'user_id' => 'required',
            'award' => 'required|string|max:255',
            'gift' => 'required|string|max:255',
            'date' => 'required|string|max:255'
            
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $award->update($data);

        return redirect('/awards')->with('success', 'Award updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        // Authorize delete (policy will enforce company scope)
        $this->authorize('delete', $award);
        $award->delete();
        return redirect('/awards')->with('success', 'Award deleted successfully.');
    }
    
}
