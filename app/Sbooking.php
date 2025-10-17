<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sbooking extends Model
{
    use HasFactory;
    protected $table = 's_bookings';
    protected $fillable = [
        'inventory_id',
        'quantity'
    ];
}
