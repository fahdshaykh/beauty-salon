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

        // Inventory::observe(InventoryObserver::class);
        InventoryPurchases::observe(InventoryPurchasesObserver::class);
        InventoryBook::observe(InventoryBookObserver::class);

    }

}

