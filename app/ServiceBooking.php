<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceBooking extends Model
{
    protected $guarded = [];


    public function getDueTimeAttribute($value)
    {
        if (!$value) {
            return null; // Or 'N/A' if preferred
        }
    
        try {
            // If value contains "AM" or "PM", it's already in 12-hour format
            if (str_contains($value, 'AM') || str_contains($value, 'PM')) {
                return $value; // Return as it is
            }
    
            // Otherwise, assume it's in 24-hour format and convert
            return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
        } catch (\Exception $e) {
            return $value; // Return original value if formatting fails
        }
    }

    // public function services()
    // {
    //     return $this->hasManyThrough('App\Services','App\BookingDetail', 'service_id','id');
    // }
    public function services()
    {
        return $this->hasManyThrough(
            'App\Services',  // Correct model name (assuming it's Service, not Services)
            'App\BookingDetail',
            'booking_id',   // Foreign key on BookingDetail (booking_id)
            'id',           // Foreign key on Service (id)
            'id',           // Local key on ServiceBooking (id)
            'service_id'    // Local key on BookingDetail (service_id)
        );
    }

    public function bookingdetail()
    {

        return $this->hasMany('App\BookingDetail','booking_id','id');

    }

    public function companysetting()
    {

        return $this->hasOne('App\CompanySetting','company_id','company_id');

    }
    public function customer()
    {

        return $this->belongsTo('App\Customer');

    }

    public function user()
    {

        return $this->belongsTo('App\User');

    }

    public function inventory_books()
    {

        return $this->hasMany('App\InventoryBook','booking_id','id');

    }

    // public function inventory()
    // {
    //     return $this->belongsTo('App\Inventory','inventory_id', 'id');
    // }
}
