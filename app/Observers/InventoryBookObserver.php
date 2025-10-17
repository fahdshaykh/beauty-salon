<?php

namespace App\Observers;

use App\InventoryBook;
use App\InventoryRemain;
use Illuminate\Support\Facades\Log;

class InventoryBookObserver
{
    /**
     * Handle the InventoryBook "created" event.
     *
     * @param  \App\InventoryBook  $inventoryBook
     * @return void
     */
    public function created(InventoryBook $inventoryBook)
    {
        // Fetch or initialize the InventoryRemain record
        $book = InventoryRemain::firstOrNew(['inventory_id' => $inventoryBook->inventory_id]);

        // Ensure the total_quantity is not less than 0 after subtraction
        $book->total_quantity = max(0, ($book->total_quantity ?? 0) - $inventoryBook->quantity);

    
        $book->save();
    }

    /**
     * Handle the InventoryBook "updated" event.
     *
     * @param  \App\InventoryBook  $inventoryBook
     * @return void
     */
    public function updated(InventoryBook $inventoryBook)
    {
        //
    }

    /**
     * Handle the InventoryBook "deleted" event.
     *
     * @param  \App\InventoryBook  $inventoryBook
     * @return void
     */
    public function deleted(InventoryBook $inventoryBook)
    {
        //
    }

    /**
     * Handle the InventoryBook "restored" event.
     *
     * @param  \App\InventoryBook  $inventoryBook
     * @return void
     */
    public function restored(InventoryBook $inventoryBook)
    {
        //
    }

    /**
     * Handle the InventoryBook "force deleted" event.
     *
     * @param  \App\InventoryBook  $inventoryBook
     * @return void
     */
    public function forceDeleted(InventoryBook $inventoryBook)
    {
        //
    }
}
