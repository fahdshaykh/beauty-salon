<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            'Cash',
            'Card',
            'Bank Transfer',
            'Cheque',
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert([
                'company_id' => 1, // This will be overridden by CompanySeeder
                'method' => $method,
            ]);
        }
    }
}