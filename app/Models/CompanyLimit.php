<?php

namespace App\Models;

use App\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'subscription_plan_id',
        'employees_used', 'customers_used', 'bookings_used', 'quotations_used', 'emails_used', 'invoices_used',
        'override_employees', 'override_customers', 'override_bookings', 'override_quotations', 'override_emails', 'override_invoices',
        'current_month',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
