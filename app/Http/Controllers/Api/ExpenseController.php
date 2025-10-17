<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\ExpenseType;
use App\Expense;
use DB;
use Validator;

class ExpenseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expenses = Expense::select('expenses.*','expense_types.type')
        ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')
        ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method')
        ->where('expenses.company_id', $request->company_id)
        ->orderBy('expenses.created_at', 'desc')
        ->get();
        
        if (count($expenses) == 0) {
            return $this->sendResponse($expenses->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($expenses->toArray(), 'Expense types retrieved successfully.');
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
                'expense_type' => 'required',
                'payable' => 'required',
                'amount' => 'required',
            ],
            [
                'expense_type.required' => 'Please enter Expense Type',
                'payable.required' => 'Please enter Expense Type',
                'amount.required' => 'Please enter Expense Type',
            ],
        );
        
        $data = $request->all();
        $expense = Expense::create($data);

        return $this->sendResponse($expense->toArray(), 'Expense types created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $expense = Expense::where('id',$request->id)->where('company_id', $request->company_id)->get();

        if (count($expense) == 0) {
            return $this->sendResponse($expense->toArray(), 'Something went wrong.');
        }

        return $this->sendResponse($expense->toArray(), 'Expense retirive successfully.');
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
        $data = $request->all();
        $expense = tap(DB::table('expenses')->where('id', $request->id))
                ->update($data)
                ->first();
        if ($expense == null) {
            return $this->sendError('Something went wrong.');
        }

        return $this->sendResponse($expense, 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $expense =DB::table('expenses')->where('id', $request->id)->where('company_id', $request->company_id)->delete();

        if ($expense == null) {
            return $this->sendError('Something went wrong.');
        }
        
        return $this->sendResponse($expense, 'Expense Deleted successfully.');
    }
}
