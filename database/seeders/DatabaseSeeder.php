<?php


use Database\Seeders\SettingsTableSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\StaffSeeder;
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

        // $this->call(CompanySeeder::class);

        $this->call([
            CompanySeeder::class
        ]);
        // $this->call(UsersTableSeeder::class);

        // DB::table('settings')->insert([
        //     [
        //         'key' => 'pagination_per_page_options',
        //         'value' => json_encode([10, 20, 50]),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        

    }

}

