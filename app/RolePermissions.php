<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermissions extends Model
{
    protected $guarded = [];

    public function role()
    {
        return $this->belongsTo('App\Role');
    }
}
