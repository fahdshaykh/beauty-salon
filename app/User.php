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
        'company_id',
        'name',
        'email',
        'image',
        'is_active',
        'settings',
        'default_rows',
        'is_deleted',
        'last_login_ip',
        'email_verified_at',
        'password',
        'remember_token',
        'phone',
        'address',
        'device_token',
        'position',
        'base_salary',
        'commission_rate',
        'is_transport',
        'transport_allowance',
    ];

    /**

     * The attributes that are mass assignable.

     *

     * @var array

     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'settings' => 'array'
    ];

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

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class, 'user_id', 'id');
    }

    // get a user's setting
    public function getSetting(string $var, $default = null)
    {
        $settingModel = $this->relationLoaded('userSetting') ? $this->userSetting : $this->userSetting()->first();
        if ($settingModel && is_array($settingModel->settings)) {
            if (array_key_exists($var, $settingModel->settings)) {
                return $settingModel->settings[$var];
            }
        }
        return $default;
    }

    // update one or more settings and optionally save the model
    public function setSettings(array $update, bool $save = true): self
    {
        $settingModel = $this->userSetting()->first();
        if (!$settingModel) {
            $settingModel = new UserSetting(['user_id' => $this->id, 'settings' => []]);
        }
        $currentSettings = is_array($settingModel->settings) ? $settingModel->settings : [];
        $settingModel->settings = array_merge($currentSettings, $update);
        if ($save) {
            $settingModel->save();
        }

        return $this;
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

        'password',
        'remember_token',

    ];



    /**

     * The attributes that should be cast to native types.

     *

     * @var array

     */
}
