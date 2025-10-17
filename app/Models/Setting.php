<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

        protected $guarded = [];

    public static function getPaginationOptions()
    {
        $setting = self::where('key', 'pagination_per_page_options')->first();
        return $setting ? json_decode($setting->value, true) : [10, 20, 50];
    }
}
