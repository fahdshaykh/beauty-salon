<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingDetail extends Model
{
    protected $fillable = ['service_id'];
     public function serviceBookings()
    {
        return $this->belongsTo('App\ServiceBooking');
    }

    public function services()
    {
        return $this->hasMany('App\Services','id','service_id');
    }


    // new code for b saloon customer history service
    public function serviceBooking()
    {
        return $this->belongsTo('App\ServiceBooking', 'booking_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo('App\Services', 'service_id', 'id');
    }
   
}
