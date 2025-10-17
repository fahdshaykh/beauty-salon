<?php

namespace App\Models;

use App\InventoryPurchases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','name', 'email', 'phone', 'address', 'payment_terms'];

    public function inventoryPurchases()
    {
        return $this->hasMany(InventoryPurchases::class);
    }

    public function purchases()
    {
        return $this->hasMany(InventoryPurchases::class, 'supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }

}
