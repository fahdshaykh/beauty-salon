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
            'last_login_ip' => $request->ip(), // Save IP address to user table
        ]);
        
        $role_id = Auth::user()->role_id;

	    $role_name = Role::where('id', $role_id)->first()->role;
    
        Session::put('userole', $role_name);

        $role_permissions = RolePermissions::select('permission_id','access_id')->where('role_id', $role_id)->get();

        foreach($role_permissions as $key => $permissions){
            $permissions_array[] = $permissions->permission_id.','.$permissions->access_id;
        }
        Session::push('all_rp',$permissions_array);
		
        Session::put('user_company', auth()->user()->company_id);
		// dd(session()->get('user_company'));
        $company_setttings = DB::table('company_settings')->where('company_id', $user->company_id)->first();
       
        if($user->company_id !== 1 && $company_setttings){
            Session::put('site_logo', $company_setttings->site_logo);
            Session::put('smart_logo', $company_setttings->smart_logo);
            Session::put('site_name', $company_setttings->site_name);
            Session::put('message_permissions', $company_setttings->message_permissions);
            Session::put('email_permissions', $company_setttings->email_permissions);
        }
        // $this->redirectTo();
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
