<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Company;
use App\User;
use Illuminate\Support\Facades\DB;

class ExpireCompanySubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:expire-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate companies and their users when plan expires';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = today()->format('Y-m-d');

        // Find all companies whose plan has expired and are still active
        $expiredCompanies = Company::where('is_active', 1)
            ->whereNotNull('plan_expire_date')
            ->whereDate('plan_expire_date', '<', $today)
            ->get();

        if ($expiredCompanies->count() === 0) {
            $this->info('No expired companies found today.');
            return 0;
        }

        $companyIds = $expiredCompanies->pluck('id')->toArray();

        // Deactivate companies
        Company::whereIn('id', $companyIds)
            ->update([
                'is_active' => 0,
                'deactivated_at' => now(),
            ]);

        // Deactivate all users belonging to these companies
        User::whereIn('company_id', $companyIds)
            ->update([
                'is_active' => 0,
            ]);

        $this->info("Successfully deactivated {$expiredCompanies->count()} companies and their users.");

        // Optional: Log or notify admin
        foreach ($expiredCompanies as $company) {
            $this->line("→ {$company->company_name} (ID: {$company->id}) expired on {$company->plan_expire_date}");
        }

        return 0;
    }
}
