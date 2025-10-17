<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Customer;
use App\User;
use App\Notifications\CustomerBirthdayNotification;
use Carbon\Carbon;

class SendCustomerBirthdayNotifications extends Command
{
    protected $signature = 'notify:customer-birthdays'; // Command to run
    protected $description = 'Send birthday notifications to company users for their customers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Checking for customer birthdays...');

        // Get today's date in MM-DD format
        $today = Carbon::today()->format('m-d');

        // Find all customers whose birthday is today
        $customers = Customer::whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = ?", [$today])->get();

        if ($customers->isEmpty()) {
            $this->info('No customer birthdays today.');
            return;
        }

        foreach ($customers as $customer) {
            // Get all users belonging to the same company as the customer
            $companyUsers = User::where('company_id', $customer->company_id)->get();

            foreach ($companyUsers as $user) {
                $user->notify(new CustomerBirthdayNotification($customer));
            }
        }

        $this->info('Customer birthday notifications sent successfully.');
    }
}
