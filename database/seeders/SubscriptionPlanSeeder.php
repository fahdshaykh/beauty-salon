<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionPlan::create([
            'name' => 'Basic',
            'price_yearly' => 100,
            'max_employees' => 10,
            'max_customers' => 50,
            'max_bookings' => 500,
            'max_quotations' => 200,
            'max_emails' => 200,
            'max_invoices' => 200,
        ]);

        SubscriptionPlan::create([
            'name' => 'Pro',
            'price_yearly' => 200,
            'max_employees' => 30,
            'max_customers' => 250,
            'max_bookings' => 1500,
            'max_quotations' => 500,
            'max_emails' => 500,
            'max_invoices' => 500,
        ]);

        SubscriptionPlan::create([
            'name' => 'Enterprise',
            'price_yearly' => 350,
            'is_unlimited' => true,
        ]);
    }
}
