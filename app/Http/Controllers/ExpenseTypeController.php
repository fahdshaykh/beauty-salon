<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\ExpenseType;

use DB;



class ExpenseTypeController extends Controller

{

    /**
     * Use policy-based authorization for resource actions.
     * This replaces route/middleware checks and enforces policies on direct URL access.
     */
    public function __construct()
    {
        // We authorize in each method instead of using authorizeResource
        // because our routes use {id} instead of {expense_type}
    }
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()
    {
        $user = auth()->user();
        
        // Check viewAny permission via policy
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('viewAny', ExpenseType::class);
        }

        // Always enforce company isolation for multi-tenant security
        $expense_types = ExpenseType::where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('expense_types.index', compact('expense_types'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()
    {
        $user = auth()->user();
        
        // Check create permission via policy
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('create', ExpenseType::class);
        }
        
        return view('expense_types.create');
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, 

            [

                'type' => 'required',

            ],

            [

                'type.required' => 'Please enter Expense Type',

                'type.unique' => 'This Expense type already Exists'

            ],

        );

       

        $data['type'] = $request->type;

        $data['description'] = $request->description;

        $data['company_id'] = auth()->user()->company_id;

        ExpenseType::create($data);

        return redirect('expense-types')->with('success', 'Expense Type created successfully');

    }



    

    public function show($id)

    {

        //

    }



    

    public function edit($id)

    {
        $user = auth()->user();
        
        // For non-admins, only load expense types from their company
        $query = ExpenseType::where('id', $id);
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $query->where('company_id', $user->company_id);
        }
        
        $expense_type = $query->firstOrFail();
        
        // Still check the policy for permission-based access
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('update', $expense_type);
        }

        return view('expense_types.edit', compact('expense_type'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $this->validate($request, 

            [

                'type' => 'required',

            ],

            [

                'type.required' => 'Please enter Expense Type',

            ],

        );



        $user = auth()->user();
        
        // Enforce company isolation and find expense type
        $expense_type = ExpenseType::where('id', $id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        // Check permission via policy
        $this->authorize('update', $expense_type);

        // Update with company isolation maintained
        $data = $request->except('_token','_method');
        $data['company_id'] = $user->company_id; // Ensure company_id stays correct
        $expense_type->update($data);

        return redirect('expense-types')->with('success', 'Expense Type updated successfully');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */



    public function expense_type_status(Request $request)
    {
        $user = auth()->user();

        // Enforce company isolation when finding expense type
        $expensetype = ExpenseType::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        // Check permission via policy
        $this->authorize('update', $expensetype);

        $expensetype->is_active = $request->status;
        $expensetype->save();



        return response()->json(['success'=>'Status changed successfully.']);

    }



    // public function destroy($id)

    // {



    //     $cat = ExpenseType::where('id',$id)->where('company_id',auth()->user()->company_id)->where('is_deleted',0)->count();

    //     if($cat>0){

    //         session()->flash('destroy','Operation Denied. Expense Type Is Related To Expenses Table.');

    //         return redirect('expense-types');

    //     }

    //     else{

    //         $expense_type = ExpenseType::find($id);

    //         $expense_type->is_deleted = 1;

    //         $expense_type->save();

    //         session()->flash('destroy','Expense Type deleted Successfully');

    //         return redirect('expense-types');

    //     }

    // }

}

