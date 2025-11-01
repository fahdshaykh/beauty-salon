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

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1️⃣ Find existing company
        $company = Company::where('company_name', 'Beauty Salon HQ')->first();

        if (! $company) {
            $this->command->error("❌ Company not found. Please run CompanySeeder first.");
            return;
        }

        // 2️⃣ Create a staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@beautysalon.com'],
            [
                'name'       => 'Salon Staff',
                'password'   => Hash::make('password123'),
                'company_id' => $company->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // 3️⃣ Find the Staff role for this company
        $staffRole = Role::where('name', 'Staff')
            ->where('company_id', $company->id)
            ->first();

        if (!$staffRole) {
            $this->command->error("❌ Staff role not found. Please run CompanySeeder first to create roles.");
            return;
        }

        // 4️⃣ Assign Staff role to the staff user
        $staff->assignRole($staffRole);

        $this->command->info("✅ Staff user '{$staff->name}' created and assigned Staff role for company '{$company->company_name}'.");
    }
}
