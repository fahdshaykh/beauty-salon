<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class email_template extends Model
{
    protected $fillable=[
        'template_preview',
        'template_id',
        'template_name'
       
    ];
}
