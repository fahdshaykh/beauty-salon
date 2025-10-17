<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Expense;

use App\ExpenseType;

use DB;
use App\Models\Setting;
use App\Models\UserSetting;


class ExpenseController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

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


        $expenses = Expense::select('expenses.*','expense_types.type','payment_methods.method')

        ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')

        ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')

        ->where('expenses.company_id', auth()->user()->company_id)

        ->orderBy('expenses.created_at', 'desc')

        ->paginate($perPage)->appends(['per_page' => $perPage]);



        return view('expenses.index',compact('expenses', 'paginationOptions'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $expense_types = ExpenseType::where('is_active', 1)

        ->where('company_id', auth()->user()->company_id)

        ->get();

        $payment_methods = DB::table('payment_methods')

        ->get();

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

        $expense_types = ExpenseType::where('company_id', auth()->user()->company_id)

        ->get();

        $payment_methods = DB::table('payment_methods')

        ->get();

        $expense = Expense::find($id);

        return view('expenses.edit',compact('expense','expense_types','payment_methods'));

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

        //print_r($data); exit;

        DB::table('expenses')->where('id', $id)->update($data);

        return redirect('expenses')->with('success', 'Expenses updated successfully');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {



        // $cat = ExpenseType::where('id',$id)->where('company_id',auth()->user()->company_id)->where('is_deleted',0)->count();

        // if($cat>0){

        //     session()->flash('destroy','Operation Denied. Expense Type Is Related To Services Table.');

        //     return redirect('expenses');

        // }

        // else{

            $expense_type = Expense::destroy($id);

            session()->flash('destroy','Expense deleted Successfully');

            return redirect('expenses');

        // }

    }

}

