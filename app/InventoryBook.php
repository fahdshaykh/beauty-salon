<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryBook extends Model
{
    use HasFactory;
    
    public function remain()
    {
        return $this->belongsTo(InventoryRemain::class, 'inventory_id', 'inventory_id');
    }
    

    public function service_books()
    {

        return $this->belongsTo('App\ServiceBooking');

    }

    // public function inventory()
    // {

    //     return $this->belongsTo('App\InventoryPurchases');

    // }
    public function inventory()
    {
        return $this->belongsTo('App\Inventory','inventory_id', 'id');
    }
}
