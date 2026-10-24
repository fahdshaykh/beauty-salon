<?php

namespace App;

use App\Models\CompanyLimit;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function limits()
    {
        return $this->hasOne(CompanyLimit::class);
    }

    public function currentLimits()
    {
        $currentMonth = now()->format('Y-m');
        $limits = $this->limits()->first();

        if (!$limits) {
            return $this->limits()->create([
                'subscription_plan_id' => $this->subscription_plan_id ?? 1,
                'current_month' => $currentMonth,
            ]);
        }

        if ($limits->current_month !== $currentMonth) {
            $limits->update([
                'current_month' => $currentMonth,
                'employees_used' => 0,
                'customers_used' => 0,
                'bookings_used' => 0,
                'quotations_used' => 0,
                'emails_used' => 0,
                'invoices_used' => 0,
            ]);
        }

        return $limits;
    }
}
