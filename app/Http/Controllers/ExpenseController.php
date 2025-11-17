<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Expense;
use App\ExpenseType;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller

{

        /**
     * Use policy-based authorization for resource actions.
     * This replaces route/middleware checks and enforces policies on direct URL access.
     */
    public function __construct()
    {
        // We authorize in each method instead of using authorizeResource
        // because our routes use {id} instead of {expense}
    }

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check viewAny permission via policy
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('viewAny', Expense::class);
        }

        // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['expenses_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('expenses_rows_per_page', auth()->user()->default_rows);

        // Build query with joins and enforce company isolation
        $query = Expense::select('expenses.*','expense_types.type','payment_methods.method')
            ->join('expense_types', function($join) use ($user) {
                $join->on('expense_types.id', '=', 'expenses.expense_type')
                     ->where('expense_types.company_id', '=', $user->company_id);
            })
            ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')
            ->where('expenses.company_id', $user->company_id); // Always enforce company isolation
        
        $expenses = $query->orderBy('expenses.created_at', 'desc')
            ->paginate($rows);



        return view('expenses.index',compact('expenses', 'rows'));

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
            $this->authorize('create', Expense::class);
        }

        // Load expense types - enforce company isolation
        $expense_types = ExpenseType::where('is_active', 1)
            ->where('company_id', $user->company_id)
            ->get();

        $payment_methods = DB::table('payment_methods')->get();

        return view('expenses.create', compact('expense_types','payment_methods'));

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

                'expense_type' => 'required',

                'payable' => 'required',

                'amount' => 'required',

                'payment_method' => 'required',

            ],

            [

                'expense_type.required' => 'Please Select Expense Type',

                'payable.required' => 'Please Enter Date',

                'amount.required' => 'Please Enter Amount',

                'payment_method.required' => 'Please Select Payment Method',

            ],

        );

        

        $data = $request->all();

        //$data['payment_method'] = $request->payment_method;

        $data['company_id'] = auth()->user()->company_id;

        //print_r($data); exit;

        Expense::create($data);



        return redirect('expenses')->with('success', 'Expense created successfully');

    }



    

    public function show($id)

    {

        //

    }



    

    public function edit($id)
    {
        $user = auth()->user();
        
        // For non-admins, only load expenses from their company
        $query = Expense::where('id', $id);
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $query->where('company_id', $user->company_id);
        }
        
        $expense = $query->firstOrFail();
        
        // Still check the policy for permission-based access
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('update', $expense);
        }

        // Load related data for the form
        $expense_types = ExpenseType::where('company_id', $user->company_id)->get();
        $payment_methods = DB::table('payment_methods')->get();

        return view('expenses.edit', compact('expense', 'expense_types', 'payment_methods'));

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

                'expense_type' => 'required',

                'payable' => 'required',

                'amount' => 'required',

                'payment_method' => 'required',

            ],

            [

                'expense_type.required' => 'Please Select Expense Type',

                'payable.required' => 'Please Enter Date',

                'amount.required' => 'Please Enter Amount',

                'payment_method.required' => 'Please Select Payment Method',

            ],

        );



        $data = $request->except('_token','_method');

        $data['company_id'] = auth()->user()->company_id;

        $user = auth()->user();
        
        // For non-admins, only update expenses from their company
        $query = Expense::where('id', $id);
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $query->where('company_id', $user->company_id);
        }
        
        $expense = $query->firstOrFail();
        
        // Still check the policy for permission-based access
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('update', $expense);
        }

        $expense->update($data);

        return redirect('expenses')->with('success', 'Expenses updated successfully');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */

    public function destroy(Expense $expense)
    {
        $user = auth()->user();
        
        // For non-admins, only delete expenses from their company
        $query = Expense::where('id', $expense->id);
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $query->where('company_id', $user->company_id);
        }
        
        $expense = $query->firstOrFail();
        
        // Check delete permission via policy
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('delete', $expense);
        }

        $expense->delete();
        session()->flash('destroy', 'Expense deleted Successfully');
        return redirect('expenses');
    }

}

