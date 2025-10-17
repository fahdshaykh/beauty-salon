<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class Services extends Model

{
    protected $guarded = [];

    public function bookingdetail()

    {

        return $this->hasMany('App\BookingDetail');

    }



    public function category()
    {

        return $this->hasOne('App\Category');

    }

    public function categories()
    {

        return $this->belongsTo('App\Category','category_id');

    }

    public function employees()
    {
        return $this->belongsToMany(User::class, 'service_user', 'service_id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(ServiceBooking::class);
    }

}

