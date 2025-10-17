<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';
    protected $fillable = [
        'name',
        'low_alert',
        'max_alert',
        'company_id',
        'description'
    ];

    public function books()
    {
        return $this->hasOne(InventoryBook::class, 'inventory_id', 'inventory_id');
    }

    public function purchases()
    {
        return $this->hasMany(InventoryPurchases::class, 'inventory_id', 'id');
    }

}
