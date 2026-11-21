<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    // protected $guarded = [];
    protected $fillable = [
        'expense_type', 'payable', 'amount', 'description', 'company_id', 'payment_method', 'payment_details'
    ];

    protected $casts = [
        'payment_details' => 'array',
    ];

    // public function expenseType()
    // {
    //     return $this->belongsTo('App\ExpenseType');
    // }
    
    

}
