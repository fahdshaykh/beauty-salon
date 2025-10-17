<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\ExpenseType;
use App\Expense;
use DB;
use Validator;

class ExpenseTypeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expense_types = ExpenseType::select('*')->where('company_id',$request->company_id)->orderBy('created_at', 'desc')->get();
        
        if (count($expense_types) == 0) {
            return $this->sendResponse($expense_types->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($expense_types->toArray(), 'Expense types retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            ],
        );
        $existing = ExpenseType::where('type',$request->input('type'))
        ->count();
        if($existing > 0) {
            return $this->sendError('Expense Type has already exist.');
        } else {
            $exists = DB::table('expense_types')
            ->where('type',$request->input('type'))
            ->get();
            if(!$exists->isEmpty()) {
                
                $data = $request->all();
                $data = $request->except('_token','_method');
          

                $expense_type = tap(DB::table('expense_types')->where('id', $exists['0']->id))
                ->update($data)
                ->first();

                return $this->sendResponse($expense_type, 'Expense types created successfully.');
            }
        }
        $data = $request->all();
        $data['company_id'] = $request->company_id;
        $expense_type = ExpenseType::create($data);
        return $this->sendResponse($expense_type->toArray(), 'Expense types created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $expense_type = ExpenseType::where('id', $request->id)
        ->where('company_id', $request->company_id)->get();
        
        if(count($expense_type) == 0){
            return $this->sendResponse($expense_type->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($expense_type->toArray(), 'Expense types retrieve successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
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
        $data['company_id'] = $request->company_id;
        $expense_type = tap(DB::table('expense_types')->where('id', $request->id))
                ->update($data)
                ->first();

        if ($expense_type == null) {
            return $this->sendError('Something went wrong.');
        }
        
        return $this->sendResponse($expense_type, 'Expense types updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     $cat = Expense::where('id',$request->id)->where('company_id', $request->company_id)->where('is_deleted',0)->count();

    //     if($cat>0){
    //         return $this->sendError('Operation Denied. Expense Type Is Related To Expenses Table.');
    //     }
    //     else{

    //         if($cat == 0){
    //             $expense_type = tap(DB::table('expense_types')
    //             ->where('id', $request->id)->where('company_id', $request->company_id))
    //             ->update(['is_deleted' => 1])
    //             ->first();

    //             return $this->sendResponse($expense_type, 'Expense Type Deleted successfully.');
    //         }

    //         return $this->sendError('Something went wrong?');

    //     }
    // }
}
