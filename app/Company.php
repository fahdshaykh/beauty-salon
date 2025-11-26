<?php

namespace App;

use App\Models\CompanyLimit;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function limits()
    {
        return $this->hasOne(CompanyLimit::class);
    }

    public function currentLimits()
    {
        return $this->limits()->where('current_month', now()->format('Y-m'))->first();
    }
}
