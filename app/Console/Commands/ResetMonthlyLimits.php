<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CompanyLimit;
use Carbon\Carbon;

class ResetMonthlyLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'limits:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all company usage counters at the start of each month';

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
        $currentMonth = Carbon::now()->format('Y-m');

        // Option 1: Delete old records and let the system auto-create new ones on first use
        CompanyLimit::where('current_month', '!=', $currentMonth)->delete();

        // Option 2 (Alternative): Or just reset counters and update month
        // CompanyLimit::query()->update([
        //     'employees_used' => 0,
        //     'customers_used' => 0,
        //     'bookings_used' => 0,
        //     'quotations_used' => 0,
        //     'emails_used' => 0,
        //     'invoices_used' => 0,
        //     'current_month' => $currentMonth,
        // ]);

        $this->info('Monthly limits reset successfully for ' . $currentMonth);
    }
}
