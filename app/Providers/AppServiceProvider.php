<?php



namespace App\Providers;

use App\Inventory;
use App\InventoryBook;
use App\InventoryPurchases;
use App\Observers\InventoryObserver;
use App\Observers\InventoryBookObserver;
use App\Observers\InventoryPurchasesObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;



class AppServiceProvider extends ServiceProvider

{

    /**

     * Register any application services.

     *

     * @return void

     */

    public function register()

    {

        //

    }



    /**

     * Bootstrap any application services.

     *

     * @return void

     */

    public function boot()

    {

        Paginator::useBootstrap();

        Schema::defaultStringLength(191);

        require_once app_path('Helpers/Currency.php');
        
        // REGISTER SPATIE BLADE DIRECTIVES MANUALLY
        // Blade::if('role', function ($role) {
        //     return auth()->check() && auth()->user()->hasRole($role);
        // });

        // Blade::if('anyrole', function ($roles) {
        //     return auth()->check() && auth()->user()->hasAnyRole($roles);
        // });

        // Blade::if('can', function ($permission) {
        //     return auth()->check() && auth()->user()->can($permission);
        // });

        // Inventory::observe(InventoryObserver::class);
        InventoryPurchases::observe(InventoryPurchasesObserver::class);
        InventoryBook::observe(InventoryBookObserver::class);

    }

}

