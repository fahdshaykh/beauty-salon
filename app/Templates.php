<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    protected $fillable = ['type','title','description','template_json','company_id'];
    // protected $casts = [
    //     'template_json' => 'array',
    // ];
}
