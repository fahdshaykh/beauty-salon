<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function rolePermissions()
    {
        return $this->belongsTo('App\RolePermissions');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
