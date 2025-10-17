<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    //protected $fillable = ['service_id'];
     public function serviceBookings()
    {
        return $this->belongsTo('App\ServiceBooking');
    }

   
}
