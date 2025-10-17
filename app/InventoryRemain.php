<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryRemain extends Model
{
    use HasFactory;
    protected $table = 'inventory_remains';
    protected $fillable = [
        'inventory_id',
        'total_quantity'
    ];
    
    // public function inventory()
    // {
    //     return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    // }

    // public function books()
    // {
    //     return $this->hasMany(InventoryBook::class, 'inventory_id', 'inventory_id');
    // }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }

}
