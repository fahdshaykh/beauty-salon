<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function serviceBookings()
    {
        return $this->hasMany(ServiceBooking::class, 'customer_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
