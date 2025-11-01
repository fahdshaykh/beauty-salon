<?php

namespace App\Helpers;

use App\Models\Role;
use App\Models\Permission;
use App\User;

class CompanyRoleSeeder
{
    public static function seedForCompany($company_id, $user_id = null)
    {
        // ✅ Create default permissions
        $permissions = [
            'manage users',
            'manage employees',
            'manage bookings',
            'view reports',
            'manage inventory',
            'manage expenses',
            'manage payroll',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                [
                    'name' => $perm,
                    'company_id' => $company_id,
                    'guard_name' => 'web',
                ]
            );
        }

        // ✅ Create roles
        $adminRole = Role::firstOrCreate(
            [
                'name' => 'admin',
                'company_id' => $company_id,
                'guard_name' => 'web',
            ]
        );

        $staffRole = Role::firstOrCreate(
            [
                'name' => 'staff',
                'company_id' => $company_id,
                'guard_name' => 'web',
            ]
        );

        $managerRole = Role::firstOrCreate(
            [
                'name' => 'manager',
                'company_id' => $company_id,
                'guard_name' => 'web',
            ]
        );

        // ✅ Assign all permissions to Admin
        $adminRole->givePermissionTo(Permission::where('company_id', $company_id)->get());

        // ✅ Assign limited permissions to Manager
        $managerRole->givePermissionTo([
            'manage bookings',
            'view reports',
            'manage employees',
        ]);

        // ✅ Assign limited permissions to Staff
        $staffRole->givePermissionTo([
            'manage bookings',
            'view reports',
        ]);

        // ✅ Assign Admin role to the company owner (if user exists)
        if ($user_id) {
            $user = User::find($user_id);
            if ($user) {
                $user->assignRole($adminRole);
            }
        }

        return true;
    }
}
