<?php

namespace App\Observers;

use App\Inventory;
use App\InventoryRemain;

class InventoryObserver
{
    /**
     * Handle the Inventory "created" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function created(Inventory $inventory)
    {
        // Check if an entry exists in inventory_books
        $book = InventoryRemain::firstOrNew(['inventory_id' => $inventory->inventory_id]);

        // Update or initialize the total_quantity
        $book->total_quantity = ($book->total_quantity ?? 0) + $inventory->quantity;
        $book->save();
    }

    /**
     * Handle the Inventory "updated" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        // Check if an entry exists in inventory_remain
        $book = InventoryRemain::firstOrNew(['inventory_id' => $inventory->inventory_id]);

        // Subtract the old quantity and add the new quantity
        $oldQuantity = $inventory->getOriginal('quantity');
        $newQuantity = $inventory->quantity;

        $book->total_quantity = ($book->total_quantity ?? 0) - $oldQuantity + $newQuantity;
        $book->save();
    }

    /**
     * Handle the Inventory "deleted" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function deleted(Inventory $inventory)
    {
        // Find the corresponding entry in inventory_remain
        $book = InventoryRemain::where('inventory_id', $inventory->inventory_id)->first();

        if ($book) {
            // Subtract the deleted inventory's quantity
            $book->total_quantity -= $inventory->quantity;

            // If total_quantity becomes zero or less, you can decide to delete the entry
            if ($book->total_quantity <= 0) {
                $book->delete();
            } else {
                $book->save();
            }
        }
    }

    /**
     * Handle the Inventory "restored" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function restored(Inventory $inventory)
    {
        //
    }

    /**
     * Handle the Inventory "force deleted" event.
     *
     * @param  \App\Inventory  $inventory
     * @return void
     */
    public function forceDeleted(Inventory $inventory)
    {
        //
    }

    
}
