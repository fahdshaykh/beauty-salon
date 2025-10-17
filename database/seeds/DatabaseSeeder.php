<?php

use Database\Seeders\SettingsTableSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder

{

    /**

     * Seed the application's database.

     *

     * @return void

     */

    public function run()

    {

        // $this->call(UsersTableSeeder::class);

        // DB::table('settings')->insert([
        //     [
        //         'key' => 'pagination_per_page_options',
        //         'value' => json_encode([10, 20, 50]),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        // DB::table('work_schedules')->insert([
        //     ['day' => 'Monday', 'start_time' => '10:00:00', 'end_time' => '19:00:00'],
        //     ['day' => 'Tuesday', 'start_time' => '10:00:00', 'end_time' => '19:00:00'],
        //     ['day' => 'Wednesday', 'start_time' => '10:00:00', 'end_time' => '19:00:00'],
        //     ['day' => 'Thursday', 'start_time' => '10:00:00', 'end_time' => '19:00:00'],
        //     ['day' => 'Friday', 'start_time' => '10:00:00', 'end_time' => '19:00:00'],
        //     ['day' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '18:00:00'],
        // ]);

    }

}

