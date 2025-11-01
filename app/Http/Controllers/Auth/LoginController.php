<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\RolePermissions;
use Session;
use Auth;
use App\User;
use App\Role;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected function credentials(Request $request)
    {        
        return ['email' => $request->email, 'password' => $request->password];
    }
      /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   

    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_ip' => $request->ip(),
        ]);

        Session::put('user_company', auth()->user()->company_id);

        $company_settings = DB::table('company_settings')->where('company_id', $user->company_id)->first();

        if($user->company_id !== 1 && $company_settings){
            Session::put('site_logo', $company_settings->site_logo);
            Session::put('smart_logo', $company_settings->smart_logo);
            Session::put('site_name', $company_settings->site_name);
            Session::put('message_permissions', $company_settings->message_permissions);
            Session::put('email_permissions', $company_settings->email_permissions);
        }

        return redirect()->route('home');
    }



    protected function redirectTo(){
        return '/home';
        //return redirect('/home');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
