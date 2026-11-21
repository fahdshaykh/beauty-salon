<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Expense;
use App\ExpenseType;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        // Authorization
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('viewAny', Expense::class);
        }

        // Rows per page
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            $user->setSettings(['expenses_rows_per_page' => $rows]);
        }
        $rows = $user->getSetting('expenses_rows_per_page', $user->default_rows ?? 15);

        // Base query with joins (kept your structure)
        $query = Expense::select('expenses.*', 'expense_types.type', 'payment_methods.method')
            ->join('expense_types', function ($join) use ($user) {
                $join->on('expense_types.id', '=', 'expenses.expense_type')
                    ->where('expense_types.company_id', '=', $user->company_id);
            })
            ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')
            ->where('expenses.company_id', $user->company_id);

        // Filter by Expense Type
        if ($request->filled('expense_type_id') && $request->expense_type_id !== '') {
            $query->where('expenses.expense_type', $request->expense_type_id);
        }

        // Filter by Date Range (using expense date or created_at — adjust if needed)
        if ($request->filled('start_date')) {
            $query->whereDate('expenses.created_at', '>=', $request->start_date);
            // Or use: 'expenses.date' if you have a separate date column
        }

        if ($request->filled('end_date')) {
            $query->whereDate('expenses.created_at', '<=', $request->end_date);
        }

        // Execute with pagination + preserve all filters
        $expenses = $query->orderBy('expenses.created_at', 'desc')
                        ->paginate($rows)
                        ->withQueryString();

        // Load expense types for dropdown
        $expense_types = ExpenseType::where('company_id', $user->company_id)
                                            ->orderBy('type')
                                            ->get();

        return view('expenses.index', compact('expenses', 'rows', 'expense_types'));
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

        $payment_methods = PaymentMethod::where('is_active', 1)->get();

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
                'expense_type'   => 'required',
                'payable'        => 'nullable|date',
                'amount'         => 'required',
                'payment_method' => 'required',
                'payment_file'   => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            ],
            [
                'expense_type.required'   => 'Please Select Expense Type',
                'amount.required'         => 'Please Enter Amount',
                'payment_method.required' => 'Please Select Payment Method',
                'payment_file.max'        => 'Document file must not exceed 2MB in size',
                'payment_file.mimes'      => 'Document must be a valid file (jpg, png, pdf, doc, docx)',
            ],
        );

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id;
        $data['payable'] = $request->input('payable', date('Y-m-d'));
        if (!empty($request->payment_details)) {
            $data['payment_details'] = $request->payment_details;
        }

        if ($request->hasFile('payment_file')) {
            $file = $request->file('payment_file');
            $filename = 'proof_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('payment_proofs'), $filename);
            $data['payment_file'] = $filename;
        }

        Expense::create($data);

        return back()->with('success', 'Expense created successfully');
    }



    

    public function show($id)

    {

        //

    }



    

    public function edit($id)
    {
        $user = auth()->user();
        
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();
        
        $this->authorize('update', $expense);

        // Load related data for the form
        $expense_types = ExpenseType::where('company_id', $user->company_id)->get();
        $payment_methods = PaymentMethod::where('is_active', 1)->get();

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
                'expense_type'   => 'required',
                'payable'        => 'nullable|date',
                'amount'         => 'required',
                'payment_method' => 'required',
                'payment_file'   => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            ],
            [
                'expense_type.required'   => 'Please Select Expense Type',
                'amount.required'         => 'Please Enter Amount',
                'payment_method.required' => 'Please Select Payment Method',
                'payment_file.max'        => 'Document file must not exceed 2MB in size',
                'payment_file.mimes'      => 'Document must be a valid file (jpg, png, pdf, doc, docx)',
            ],
        );

        $data = $request->except('_token','_method');
        $data['company_id'] = auth()->user()->company_id;
        $data['payment_details'] = $request->payment_details ?? null;
        $user = auth()->user();
        
        $expense = Expense::where('id', $id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();
        
        $this->authorize('update', $expense);

        if ($request->hasFile('payment_file')) {
            if ($expense->payment_file && file_exists(public_path('payment_proofs/' . $expense->payment_file))) {
                @unlink(public_path('payment_proofs/' . $expense->payment_file));
            }
            $file = $request->file('payment_file');
            $filename = 'proof_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('payment_proofs'), $filename);
            $data['payment_file'] = $filename;
        }

        $expense->update($data);

        return back()->with('success', 'Expenses updated successfully');
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
        
        $expenseRecord = Expense::where('id', $expense->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();
        
        $this->authorize('delete', $expenseRecord);

        $expenseRecord->delete();
        session()->flash('destroy', 'Expense deleted Successfully');
        return back();
    }

}

