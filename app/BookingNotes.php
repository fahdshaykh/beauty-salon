<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingNotes extends Model
{
    protected $fillable = ['booking_id','details','image'];
}
