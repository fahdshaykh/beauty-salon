<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class CompanyLimitController extends Controller
{
    public function edit(Company $company)
    {
        $limits = $company->currentLimits() ?? $company->limits()->create(['current_month' => now()->format('Y-m')]);
        $plans = SubscriptionPlan::all();
        return view('admin.subscription_plans.limits', compact('company', 'limits', 'plans'));
    }

    public function update(Request $request, Company $company)
    {
        $limits = $company->currentLimits() ?? $company->limits()->create(['current_month' => now()->format('Y-m')]);

        $limits->update([
            'subscription_plan_id' => $request->subscription_plan_id,
            'override_employees' => $request->override_employees,
            'override_customers' => $request->override_customers,
            'override_bookings' => $request->override_bookings,
            'override_quotations' => $request->override_quotations,
            'override_emails' => $request->override_emails,
            'override_invoices' => $request->override_invoices,
        ]);

        return back()->withSuccess('Limits updated!');
    }
}
