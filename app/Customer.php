<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function serviceBookings()
    {
        return $this->belongsTo('App\ServiceBooking');
    }
    
}
