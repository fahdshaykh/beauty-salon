<?php

namespace App\Models;

use App\Customer;
use App\ServiceBooking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskyCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'user_id', 'customer_id', 'signature' ,'risky_reason'];

    public function booking()
    {
        return $this->belongsTo(ServiceBooking::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
