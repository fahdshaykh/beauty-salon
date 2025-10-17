<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class EmployeeAttendance extends Model
{
    use HasFactory;
    public $incrementing = true;
    protected $table = 'employee_attendances';
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'leave_type',
        'reason',
        'check_in',
        'check_in_location',
        'check_out',
        'check_out_location',
        'company_id',
        'is_late',
        'transport_assistance',
        'overtime_minutes',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
