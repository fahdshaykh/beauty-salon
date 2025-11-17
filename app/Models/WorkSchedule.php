<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    protected $fillable = ['company_id', 'start_time', 'end_time'];

    public function getStartTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

    public function getEndTimeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('H:i');
    }

}
