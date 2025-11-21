<?php

namespace App\Models;

use App\InventoryPurchases;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'company_id', 'inventory_purchase_id', 'amount', 'note'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(InventoryPurchases::class, 'inventory_purchase_id');
    }
}
