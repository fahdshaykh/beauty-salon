<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use Session;

use App\RolePermissions;

use App\ServiceBooking;

use App\Customer;

use DB;

class HomeController extends Controller

{

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct()

    {

        $this->middleware('auth');

    }



    public function index()

    {

         

        if(Auth::user()->is_active !== 1){

            Session::flush();

            auth()->logout();

            return redirect('/login')->with('inactive','Sorry Your Account have been In-Active.');

        }

        if( auth()->user()->company_id==1){

            $active_company = DB::table('companies')->where('is_active',1)->count();

            $inactive_company = DB::table('companies')->where('is_active',0)->count();

            $users = DB::table('users')->select('id','name')->where('company_id', auth()->user()->company_id)->where('is_deleted',0)->get();

            return view('home',compact('active_company','inactive_company', 'users'));

        }

        else{

            $users = DB::table('users')->select('id','name')->where('company_id', auth()->user()->company_id)->where('is_deleted',0)->get();

            return view('home',compact('users'));

        }



       

    }















    /**







     * Show the application dashboard.







     *







     * @return \Illuminate\Contracts\Support\Renderable







     */







    // public function index()







    // {







    //     return view('home');







    // }







}







