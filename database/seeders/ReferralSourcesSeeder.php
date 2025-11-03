<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReferralSourcesSeeder extends Seeder
{
    public function run(): void
    {
        $referralSources = [
            'repeat client',
            'recommendation',
            'email',
            'google',
            'leaflet',
            'facebook',
            'instagram',
            'paper advert',
        ];

        foreach ($referralSources as $source) {
            DB::table('referred_customers')->insert([
                'company_id' => 1, // This will be overridden by CompanySeeder
                'referred_by' => $source,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}