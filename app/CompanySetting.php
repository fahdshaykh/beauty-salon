<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    //protected $fillable = ['service_id'];
     public function serviceBookings()
    {
        return $this->belongsTo('App\ServiceBooking');
    }

   
}
