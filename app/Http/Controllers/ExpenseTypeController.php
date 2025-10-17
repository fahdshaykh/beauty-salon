<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\ExpenseType;

use DB;



class ExpenseTypeController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        $expense_types = ExpenseType::where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();

        return view('expense_types.index',compact('expense_types'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

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

        $expense_type = ExpenseType::find($id);

        return view('expense_types.edit',compact('expense_type'));

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



        $data = $request->except('_token','_method');

        $data['company_id'] = auth()->user()->company_id;

        DB::table('expense_types')->where('id', $id)->update($data);

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



        $expensetype = ExpenseType::find($request->id);



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

