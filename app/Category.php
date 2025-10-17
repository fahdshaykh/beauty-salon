<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    public function services_booking()
    {
        return $this->hasMany(Services::class);
    }

    public function services()
    {
        // return $this->belongsTo(Services::class);
        return $this->hasMany(Services::class);
    }

}
