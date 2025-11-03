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

        // 4. Create permissions
        // $permissions = [
        //     'view employees',
        //     'view services',
        //     'view payroll',
        //     'view bookings',
        //     'view clients',
        //     'view reports',
        // ];
        $permissions = [

            // Category
            'view categories', 'create categories', 'edit categories', 'delete categories',

            // Inventories
            'view inventories', 'create inventories', 'edit inventories', 'delete inventories',
            'view inventory purchases', 'create inventory purchases', 'edit inventory purchases', 'delete inventory purchases',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers', 'supplier summary',

            //available inventory
            'view available inventories',

            // Services
            'view services', 'create services', 'edit services', 'delete services',

            // Customers
            'view customers', 'create customers', 'edit customers', 'delete customers',

            // Reffered Sources
            'view referred customers', 'create referred customers', 'edit referred customers', 'delete referred customers',

            //customers import/export
            'import customers', 'export customers',

            // Bookings
            'view bookings', 'create bookings', 'edit bookings', 'delete bookings', 'view invoices',

            // Reports
            'view reports',

            //sub-reports
            'view services report', 'view bookings report', 'view employee report', 'view customer report', 'view customer graph', 'view customer country graph', 'view expense report', 'view profit & loss graph',

            // Expenses
            'view expenses', 'create expenses', 'edit expenses', 'delete expenses',
            'view expense types', 'create expense types', 'edit expense types', 'delete expense types',

            // Employee Attendance
            'view attendance', 'create attendance', 'edit attendance', 'delete attendance',
            'view work hours',
            'view payrolls', 'download payrolls',

            // User Settings
            'view users', 'create users', 'edit users', 'delete users',
            'view roles', 'create roles', 'edit roles', 'delete roles',

            //quotation
            'view quotations', 'send quotations',

            // Marketing
            'view marketing',

            // Email Marketing
            'email marketing', 'email history',

            // SMS Marketing
            //'sms marketing', 'sms history', 'send postcards',

            // Templates
            'view templates', 'create templates', 'edit templates', 'delete templates',

            // Holidays
            'view holidays', 'delete holidays',

            // Awards
            'view awards', 'create awards', 'edit awards', 'delete awards',

            // Settings
            'view settings',

            // Company Settings
            'general settings', 'email settings',

        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 5. Give permissions
        $adminRole->syncPermissions($permissions);

        // 6. SET TEAM ID (CRITICAL!)
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($company_id);

        // 7. Assign role
        $user->assignRole($adminRole); // Now works!


        $paymentMethods = [
            'Cash',
            'Card',
            'Bank Transfer',
            'Cheque',
        ];

        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert([
                'company_id' => $company_id,
                'method' => $method,
            ]);
        }


        //adding refferred by customers
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
                'company_id' => $company_id,
                'referred_by' => $source,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        //default work schedule
        DB::table('work_schedules')->insert([
            ['company_id' => $company_id, 'day' => 'Monday',    'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Tuesday',   'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Wednesday', 'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Thursday',  'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Friday',    'start_time' => '10:00:00', 'end_time' => '19:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['company_id' => $company_id, 'day' => 'Saturday',  'start_time' => '09:00:00', 'end_time' => '18:00:00', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // 8. Insert settings
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