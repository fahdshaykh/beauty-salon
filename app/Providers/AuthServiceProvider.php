<?php



namespace App\Providers;



use Illuminate\Support\Facades\Gate;
use App\ExpenseType;
use App\Expense;
use App\Policies\ExpenseTypePolicy;
use App\Policies\ExpensePolicy;

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

