<?php

namespace App;

use App\Models\Supplier;
use App\Models\SupplierBalance;
use App\Models\SupplierPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchases extends Model
{
    use HasFactory;
    protected $table = 'inventories_purchases';
    protected $fillable = [
        'name',
        'inventory_id',
        'company_id',
        'description',
        'quantity',
        'price',
        'amount_paid',
        'amount_pending',
        'payment_type',
        'image',
        'supplier_id',
        'purchase_date',
        'expiry_date'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'inventory_purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}
