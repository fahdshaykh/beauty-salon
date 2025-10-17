<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseType extends Model
{
    // protected $guarded = [];
    protected $fillable = [
        'type', 'description', 'company_id'
    ];

    // public function expense()
    // {
    //     return $this->belongsTo('App\Expense');
    // }

}
