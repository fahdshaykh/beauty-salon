<?php



namespace App\Http\Controllers;



use App\Company;

use App\User;

use Illuminate\Http\Request;

use Hash;

use DB;

use Mail;



class CompanyController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        // $companies = DB::table('companies')->where('is_deleted',0)->get();

        $companies = DB::table('companies')

        ->select('companies.*','users.email','company_settings.sms_count','company_settings.sms_limit')

        ->join('users','users.company_id','=','companies.id')

        ->join('company_settings','company_settings.company_id','=','companies.id')

        ->where('users.role_id',1)

        ->groupBy('users.company_id')->get();

        // dd($companies);

        return view('companies.index',compact('companies'));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('companies.create');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {



        $this->validate($request, 

            [

                'company_name' => 'required',

                'owner_name' => 'required',

                'company_phone' => 'required',

                'email' => 'required|email|unique:users',

                'password' => 'required',

                // 'plan_name' => 'required',

                // 'plan_price' => 'required',

                // 'plan_end_date' => 'required',

                'company_address' => 'required'

            ],

            [

                'company_name.required' => 'Please enter company name.',

                'owner_name.required' => 'Please enter owner name.',

                'company_phone.required' => 'Please enter company phone.',

                'email.required' => 'Please enter email address.',

                'password.required' => 'Please enter password.',

                // 'plan_name.required' => 'Please enter plan name.',

                // 'plan_price.required' => 'Please enter plan price.',

                // 'plan_end_date.required' => 'Please enter plan end date',

                'company_address.required' => 'Please enter plan end date'

            ],

        );



        $company = new Company;

        $company->company_name = $request->input('company_name');

        $company->smart_name = $request->input('smart_name');

        $company->owner_name = $request->input('owner_name');

        $company->company_phone = $request->input('company_phone');

        $company->plan_name = $request->input('plan_name');

        $company->plan_price = $request->input('plan_price');

        $company->plan_end_date = $request->input('plan_end_date');

        $company->company_address = $request->input('company_address');

        $company->save();

        $company_id = $company->id;



        $user = new User;

        $user->name = $request->input('owner_name');

        $user->email = $request->input('email');

        $user->role_id = '1';

        $user->company_id = $company_id;

        if($request->input('password')!=""){

            $user->password = Hash::make($request->input('password'));

        }

        $user->save();

        

        $message_permission = $request->input('message_permission');

        if(!$message_permission){

            $message_permission = 0;

        }



        $email_permission = $request->input('email_permission');

        if(!$email_permission){

            $email_permission = 0;

        }

        DB::table('company_settings')->insert([

            'company_id' => $company_id,

            'site_name' =>$request->input('company_name'),

            'smart_name' =>$request->input('smart_name'),

            'email_permissions' => $email_permission,

            'message_permissions' =>$message_permission,

            'review_permissions' => '0',

            'review_url' => '',

            'email' => $request->input('email'),

            'sms_count' => 0, //$request->input('sms_count'),

            'sms_limit' => 0, //$request->input('sms_limit'),

            // 'whatsapp_count' => $request->input('whatsapp_count'),

            'tax_status' => '0',

            'vat' => '0',

            'created_at' =>  date('Y-m-d H:i:s'),

            'updated_at' => date('Y-m-d H:i:s'),

            ]);

            

        DB::table('email_settings')->insert([

            'email' =>  $request->input('email'),

            'company_id' => $company_id,

            'permission' => $email_permission,

            'created_at' =>  date('Y-m-d H:i:s'),

            'updated_at' => date('Y-m-d H:i:s'),

            ]);

        // DB::table('message_settings')->insert([

        //     'description' => 'Message Notification',

        //     'is_active' => 0,

        //     'company_id' => $company_id,

        //     'permission' => $message_permission,

        //     'created_at' =>  date('Y-m-d H:i:s'),

        //     'updated_at' => date('Y-m-d H:i:s'),

        //     ]);

        // DB::table('theme_settings')->insert([

        //     'site_name' => $request->input('company_name'),

        //     'smart_name' => $request->input('smart_name'),

        //     'site_logo' => '',

        //     'company_id' => $company_id,

        //     'created_at' =>  date('Y-m-d H:i:s'),

        //     'updated_at' => date('Y-m-d H:i:s'),

        //     ]);

        // DB::table('review_settings')->insert([

        //     'company_id' => $company_id,

        //     'is_active' => 0,

        //     'created_at' =>  date('Y-m-d H:i:s'),

        //     'updated_at' => date('Y-m-d H:i:s'),

        //     ]);



        //code for send register email data

        $email = $request->input('email');

        $password = $request->input('password');

        

        // $this->send_mail($email, $password, $request->owner_name);



        return redirect('companies')->with('success','Company created successfully');

    }

    



    public function send_mail($email, $password, $owner_name)

    {

        $register_message = "Dear ".$owner_name.". Your Account has been successfully created at <a href='http://app.diarymaster.co.uk/'>B Saloon</a>."."<br>".

        "You can start using this service by logging in to your account using following credentials:"."<br>".

        "E-Mail: ".$email."<br>"."Password: ".$password."<br>".

        "Please <a href='https://diarymaster.co.uk/contact/'>Contact Us</a> if you have any queries."."<br>".

        "Thanks for using B Saloon";



        $data = array("register_message" => $register_message);



        $data["header"] = 'B Saloon Booking & Marketing Software';

        $data["footer"] = '5 Church Road South,</br>

        Woolton, Liverpool, L25 7RJ';



        \Mail::send('emails.register', $data, function($message) use ($email) {

            $message->to($email)->subject('B Saloon Registration');

            $message->from('app@diarymaster.co.uk','B Saloon');

        });

        // check for failures

        if (Mail::failures()) {

            return back()->with('error','Email Sending Error, Please try again!');

        }

        

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\Company  $company

     * @return \Illuminate\Http\Response

     */

    public function show(Company $company)

    {

        //

    }



    public function company_status(Company $company, Request $request)

    {

        DB::table('companies')

            ->where('id', $request->id)

            ->update(['is_active' => $request->status]);



        DB::table('users')

            ->where('company_id', $request->id)

            ->update(['is_active' => $request->status]);

        return response()->json(['success'=>'Status changed successfully.']);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Company  $company

     * @return \Illuminate\Http\Response

     */

    public function edit(Company $company)

    {

        $company_detail = DB::table('companies')

        ->select('companies.*','users.email as useremail','company_settings.*')

        ->join('users','users.company_id','=','companies.id')

        ->join('company_settings','company_settings.company_id','=','companies.id')

        ->where('companies.id',$company->id)

        ->where('users.role_id',1)

        ->get();

       



        // dd($message_permission[0]->permission);

        return view('companies.edit',compact('company_detail'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Company  $company

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, Company $company)

    {

  

        $result =  DB::table('users')->select('id')->where('company_id', $company->id)->where('role_id', 1)->first(); 

        $this->validate($request, 

            [

                'company_name' => 'required',

                'owner_name' => 'required',

                'company_phone' => 'required',

                'email' => 'required|email|unique:users,email,'.$result->id,

                // 'plan_name' => 'required',

                // 'plan_price' => 'required',

                // 'plan_end_date' => 'required',

                'company_address' => 'required'

            ],

            [

                'company_name.required' => 'Please enter company name.',

                'owner_name.required' => 'Please enter owner name.',

                'company_phone.required' => 'Please enter company phone.',

                'email.required' => 'Please enter email address.',

                // 'plan_name.required' => 'Please enter plan name.',

                // 'plan_price.required' => 'Please enter plan price.',

                // 'plan_end_date.required' => 'Please enter plan end date',

                'company_address.required' => 'Please enter plan end date'

            ],

        );



        $company_data = Company::find($company->id);

        $company_data->company_name = $request->input('company_name');

        $company_data->smart_name = $request->input('smart_name');

        $company_data->owner_name = $request->input('owner_name');

        $company_data->company_phone = $request->input('company_phone');

        $company_data->plan_name = $request->input('plan_name');

        $company_data->plan_price = $request->input('plan_price');

        $company_data->plan_end_date = $request->input('plan_end_date');

        $company_data->company_address = $request->input('company_address');

        $company_data->save();



        $user_data = array(

            'name' => $request->input('owner_name'),

            'email' => $request->input('email'),

        );

        if($request->input('password')!=""){

            $user_data['password'] = Hash::make($request->input('password'));

        }

        // print_r($user_data);exit;

        DB::table('users')->where('company_id', $company->id)->where('role_id', 1)->update($user_data);



        $message_permission = $request->input('message_permission');

        if(!$message_permission){

            $message_permission = 0;

        }



        $email_permission = $request->input('email_permission');

        if(!$email_permission){

            $email_permission = 0;

        }

        DB::table('company_settings')->where('company_id', $company->id)->update([

            'message_permissions' => $message_permission,

            'email_permissions'=>$email_permission,

            'sms_count' => $request->input('sms_count'),

            'sms_limit' => $request->input('sms_limit'),

            // 'whatsapp_count' => $request->input('whatsapp_count'),

        ]);

        return redirect('companies')->with('update', 'Renter Information Updated successfully');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Company  $company

     * @return \Illuminate\Http\Response

     */

    public function destroy(Company $company, $id)

    {

        DB::table('companies')

            ->where('id', $id)

            ->update(['is_deleted' => 1]);



        DB::table('users')

            ->where('company_id', $id)

            ->update(['is_deleted' => 1]);

        return back()->with('destroy','Company deleted successfully!');

    }

}

