<?php



namespace App;

use App\Models\EmployeeAttendance;
use App\Models\Payroll;
use App\Models\UserSetting;
use App\ServiceBooking;
use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\PasswordReset;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable

{

    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'base_salary',
        'commission_rate',
        'is_transport',
        'transport_allowance',
        'address',
        'image',
        'company_id',  // ← ADD THIS LINE
    ];

        /**

     * The attributes that are mass assignable.

     *

     * @var array

     */

    public function role()

    {

        return $this->belongsTo('App\Role');

    }


    public function serviceBookings()
    {
        return $this->hasMany(ServiceBooking::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(EmployeeAttendance::class, 'user_id');
    }

    public function services()
    {
        return $this->belongsToMany(Services::class, 'service_user', 'user_id', 'service_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function companySetting()
    {
        return $this->hasOne(CompanySetting::class, 'company_id', 'company_id');
    }

    public function setting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function getPerPagePreference()
    {
        return $this->setting ? $this->setting->per_page : 10;
    }

    public function scopeActiveInCompany($query)
    {
        return $query->where('is_active', 1)
                    ->where('company_id', auth()->user()->company_id)
                    ->where('is_deleted', 0);
    }

    public function scopeNotCheckedInToday($query)
    {
        return $query->whereDoesntHave('attendances', function ($query) {
            $query->whereDate('created_at', now()->toDateString());
        });
    }

    // public function services()
    // {
    //     return $this->hasMany(EmployeeService::class, 'user_id');
    // }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'user_id');
    }
   /**

 * Send the password reset notification.

 *

 * @param  string  $token

 * @return void

 */

public function sendPasswordResetNotification($token)

{

     

    $this->notify(new PasswordReset($token));

}



    use Notifiable;



    





    /**

     * The attributes that should be hidden for arrays.

     *

     * @var array

     */

    protected $hidden = [

        'password', 'remember_token',

    ];



    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];



   

}



