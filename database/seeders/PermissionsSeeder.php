<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // dashboard
            'view calendar',

            // dashboard bookings
            'calendar create booking', 'calendar view job notes', 'calendar update booking status', 'calendar view invoice', 'calendar update booking',

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
            'view bookings', 'create bookings', 'edit bookings', 'delete bookings', 'view invoices', 'booking inventory',

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
            // 'view users', 'create users', 'edit users', 'delete users',
            // 'view roles', 'create roles', 'edit roles', 'delete roles',

            //quotation
            'view quotations', 'send quotations',

            // Templates
            'view templates',

            // Email Templates
            'view email templates', 'create email templates', 'edit email templates', 'delete email templates',

            // SMS Templates
            'view sms templates', 'create sms templates', 'edit sms templates', 'delete sms templates',

            // Marketing
            'view marketing',

            // Email Marketing
            'email marketing', 'email history', 'sms marketing', 'sms history',

            // Holidays
            'view holidays', 'delete holidays',

            // Awards
            'view awards', 'create awards', 'edit awards', 'delete awards',

            // Settings
            // 'view settings',

            // Company Settings
            // 'general settings', 'email settings',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }
    }
}