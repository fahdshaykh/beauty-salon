<?php



namespace App\Providers;



use Illuminate\Support\Facades\Gate;
use App\ExpenseType;
use App\Expense;
use App\Policies\ExpenseTypePolicy;
use App\Policies\ExpensePolicy;
use App\Models\Award;
use App\Policies\AwardPolicy;
use App\Category;
use App\Policies\CategoryPolicy;
use App\Services;
use App\Policies\ServicePolicy;
use App\Customer;
use App\Policies\CustomerPolicy;
use App\ReferredCustomer;
use App\Policies\ReferredCustomerPolicy;
use App\ServiceBooking;
use App\Policies\ServiceBookingPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;



class AuthServiceProvider extends ServiceProvider

{

    /**

     * The policy mappings for the application.

     *

     * @var array

     */

    protected $policies = [
        ExpenseType::class => ExpenseTypePolicy::class,
        Expense::class => ExpensePolicy::class,
        Award::class => AwardPolicy::class,
        Category::class => CategoryPolicy::class,
        Services::class => ServicePolicy::class,
        Customer::class => CustomerPolicy::class,
        ServiceBooking::class => ServiceBookingPolicy::class,
        ReferredCustomer::class => ReferredCustomerPolicy::class,
    ];



    /**

     * Register any authentication / authorization services.

     *

     * @return void

     */

    public function boot()

    {
        // Allow admins to bypass policy checks (so role 'admin' can manage across companies)
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                return true;
            }
        });
        $this->registerPolicies();



        //

    }

}

