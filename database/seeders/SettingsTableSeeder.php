<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('settings')->insert([
            [
                'key' => 'pagination_per_page_options',
                'value' => json_encode([10, 20, 50]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
