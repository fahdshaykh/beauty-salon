<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $table = 'booking_payments';

    protected $fillable = [
        'company_id',
        'booking_id',
        'amount',
        'payment_method',
        'payment_details',
        'payment_file',
        'payment_date',
        'payment_type',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'float',
        'payment_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(\App\ServiceBooking::class, 'booking_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
