<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Employee;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class AwardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Award::class);

        // Rows per page
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            auth()->user()->setSettings(['awards_rows_per_page' => $rows]);
        }
        $rows = auth()->user()->getSetting('awards_rows_per_page', auth()->user()->default_rows ?? 15);

        // Build query
        $query = Award::where('company_id', auth()->user()->company_id);

        // Filter by Employee (user_id)
        if ($request->filled('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Date Range (assuming award_date or created_at — adjust if needed)
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
            // Or use: 'created_at' if you don't have a separate award_date column
            // $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
            // Or: $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute with latest first + preserve all filters in pagination
        $awards = $query->latest('date')
                        ->paginate($rows)
                        ->withQueryString();

        // Load users (employees) for the Select2 dropdown
        $users = User::where('company_id', auth()->user()->company_id)
                                ->where('is_deleted', 0)
                                ->latest()
                                ->get();

        return view('awards.index', compact('awards', 'rows', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorize create
        $this->authorize('create', Award::class);
        $users =DB::table('users')->where('is_deleted', 0)->where('company_id', auth()->user()->company_id)
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
        
    
        return back()->with('success', 'Award created successfully.');
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

        return back()->with('success', 'Award updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Award $award)
    {
        // Authorize delete (policy will enforce company scope)
        $this->authorize('delete', $award);
        $award->delete();
        return back()->with('destroy', 'Award deleted successfully.');
    }
    
}
