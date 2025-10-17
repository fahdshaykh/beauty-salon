<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'base_salary',
        'commission',
        'overtime_payment',
        'deductions',
        'net_pay',
        'pay_period_start',
        'pay_period_end',
    ];
}
