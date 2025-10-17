<?php

namespace App\Observers;

use App\InventoryPurchases;
use App\InventoryRemain;

class InventoryPurchasesObserver
{
    /**
     * Handle the InventoryPurchases "created" event.
     *
     * @param  \App\Models\InventoryPurchases  $inventoryPurchases
     * @return void
     */
    public function created(InventoryPurchases $purchase)
    {
        $remain = InventoryRemain::firstOrNew(['inventory_id' => $purchase->inventory_id]);
        $remain->total_quantity = ($remain->total_quantity ?? 0) + $purchase->quantity;
        $remain->company_id = auth()->user()->company_id;
        $remain->save();
    }

    /**
     * Handle the InventoryPurchases "updated" event.
     */
    public function updated(InventoryPurchases $purchase)
    {
        $oldQuantity = $purchase->getOriginal('quantity');
        $newQuantity = $purchase->quantity;

        // if inventory_id changed, update old remain and new remain separately
        if ($purchase->isDirty('inventory_id')) {
            $oldInventoryId = $purchase->getOriginal('inventory_id');

            // Subtract from old
            $oldRemain = InventoryRemain::where('inventory_id', $oldInventoryId)->first();
            if ($oldRemain) {
                $oldRemain->total_quantity -= $oldQuantity;
                $oldRemain->total_quantity <= 0 ? $oldRemain->delete() : $oldRemain->save();
            }

            // Add to new
            $newRemain = InventoryRemain::firstOrNew(['inventory_id' => $purchase->inventory_id]);
            $newRemain->total_quantity = ($newRemain->total_quantity ?? 0) + $newQuantity;
            $newRemain->save();
        } else {
            // inventory_id same, just adjust quantity
            $remain = InventoryRemain::firstOrNew(['inventory_id' => $purchase->inventory_id]);
            $remain->total_quantity = ($remain->total_quantity ?? 0) - $oldQuantity + $newQuantity;
            $remain->company_id = auth()->user()->company_id;
            $remain->save();
        }
    }

    /**
     * Handle the InventoryPurchases "deleted" event.
     */
    public function deleted(InventoryPurchases $purchase)
    {
        $remain = InventoryRemain::where('inventory_id', $purchase->inventory_id)->first();

        if ($remain) {
            $remain->total_quantity -= $purchase->quantity;

            if ($remain->total_quantity <= 0) {
                $remain->delete();
            } else {
                $remain->save();
            }
        }
    }

    /**
     * Handle the InventoryPurchases "restored" event.
     *
     * @param  \App\Models\InventoryPurchases  $inventoryPurchases
     * @return void
     */
    public function restored(InventoryPurchases $inventoryPurchases)
    {
        //
    }

    /**
     * Handle the InventoryPurchases "force deleted" event.
     *
     * @param  \App\Models\InventoryPurchases  $inventoryPurchases
     * @return void
     */
    public function forceDeleted(InventoryPurchases $inventoryPurchases)
    {
        //
    }
}
