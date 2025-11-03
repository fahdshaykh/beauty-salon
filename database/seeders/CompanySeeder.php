<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Company;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create default company
        $company = Company::create([
            'company_name'   => 'Beauty Salon HQ',
            'owner_name'     => 'Admin Owner',
            'company_phone'  => '03001234567',
            'plan_name'      => 'Pro Plan',
            'plan_price'     => 4999,
            'plan_end_date'  => Carbon::now()->addMonths(6),
            'company_address'=> 'Main Road, Lahore, Pakistan',
        ]);

        $company_id = $company->id;

        // 2. Create the owner (Admin) user
        $user = User::create([
            'name'       => $company->owner_name,
            'email'      => 'admin@beautysalon.com',
            'password'   => Hash::make('password123'),
            'company_id' => $company_id,
        ]);

        // 3. Create admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
            'company_id' => $company->id,
        ]);

        // 4. Create permissions and get all permissions
        // $this->call(PermissionsSeeder::class);
        $permissions = Permission::all()->pluck('name')->toArray();

        // 5. Give permissions
        $adminRole->syncPermissions($permissions);

        // 6. SET TEAM ID (CRITICAL!)
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($company_id);

        // 7. Assign role
        $user->assignRole($adminRole);

        // 8. Seed payment methods and update company_id
        // $this->call(PaymentMethodsSeeder::class);
        // DB::update('UPDATE payment_methods SET company_id = ? WHERE company_id = 1', [$company_id]);

        // 9. Seed referral sources and update company_id
        // $this->call(ReferralSourcesSeeder::class);
        // DB::update('UPDATE referred_customers SET company_id = ? WHERE company_id = 1', [$company_id]);

        // 10. Create default work schedule
        DB::table('work_schedules')->insert([
            ['company_id' => $company_id, 'day' => 'Monday',    'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Tuesday',   'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Wednesday', 'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Thursday',  'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Friday',    'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Saturday',  'start_time' => '09:00:00', 'end_time' => '18:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 11. Insert company settings
        DB::table('company_settings')->insert([
            'company_id' => $company_id,
            'site_name' => $company->company_name,
            'smart_name' => 'beauty_salon',
            'email_permissions' => 1,
            'message_permissions' => 1,
            'review_permissions' => 0,
            'review_url' => '',
            'email' => $user->email,
            'sms_count' => 0,
            'sms_limit' => 0,
            'tax_status' => '0',
            'vat' => '0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('email_settings')->insert([
            'email' => $user->email,
            'company_id' => $company_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->command->info("Company '{$company->company_name}' created with admin user and full permissions.");
    }
}