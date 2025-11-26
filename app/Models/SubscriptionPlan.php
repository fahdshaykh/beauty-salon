<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price_yearly', 'max_employees', 'max_customers', 
        'max_bookings', 'max_quotations', 'max_emails', 'max_invoices', 'is_unlimited'
    ];
}
