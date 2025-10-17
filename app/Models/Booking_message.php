<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking_message extends Model
{
    use HasFactory;
    protected $table = 'booking_messages';

        protected $fillable = [
        'booking_id',
        'reason'
    ];
}
