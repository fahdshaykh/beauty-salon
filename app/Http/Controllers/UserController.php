<?php







namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;



use App\User;
use PDF;
use Carbon\Carbon;

use App\Role;
use App\Models\Setting;
use App\Models\UserSetting;
use DB;

use Hash;

use File;

use Str;







class UserController extends Controller



{



    public function index(Request $request)

    {

        $paginationOptions = Setting::getPaginationOptions();
        $perPage = $request->input('per_page', auth()->user()->getPerPagePreference());
        if (!in_array($perPage, $paginationOptions)) {
            $perPage = auth()->user()->getPerPagePreference();
        }
        if ($request->has('per_page')) {
            if (auth()->user()->setting) {
                auth()->user()->setting->update(['per_page' => $perPage]);
            } else {
                UserSetting::create(['user_id' => auth()->user()->id, 'per_page' => $perPage]);
            }
        }



        $users = User::where('is_deleted', 0)->where('company_id', auth()->user()->company_id)
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage)
                                ->appends(['per_page' => $perPage]);

        return view('users.index',compact('users', 'paginationOptions'));


    }







    public function create()



    {



        $roles = Role::where('is_deleted',0)->where('company_id', auth()->user()->company_id)->get();



        return view('users.create',compact('roles'));



    }







    public function add(Request $request)



    {



        $this->validate($request, 



            [



                'name' => 'required',



                'email' => 'required|email|unique:users',



                'password' => 'required',



                'role_id' => 'required'



            ],



            [



                'name.required' => 'Please enter user name.',



                'email.required' => 'Please enter email address.',



                'password.required' => 'Please enter Password.',



                'role_id.required' => 'Please select role.',



            ],



        );



        $user = new User;

        $user->name = $request->input('name');

        $user->email = $request->input('email');

        $user->role_id = $request->input('role_id');

        $user->phone = $request->input('phone');

        $user->position = $request->input('position');
        $user->base_salary = $request->input('base_salary');
        $user->commission_rate = $request->input('commission_rate');
        $user->is_transport = $request->input('is_transport');
        $user->transport_allowance = $request->input('transport_allowance');

        $user->address = $request->input('address');

        if($request->image){



            $originalImage = $request->file('image');



            $originalImage->move(public_path().'/profiles', $user_image = 'img_'.Str::random(15).'.'.$originalImage->getClientOriginalExtension());

            $user->image = $user_image;

        }



       



        if($request->input('password')!=""){



            $user->password = Hash::make($request->input('password'));



        }



        $user->company_id = auth()->user()->company_id;



        $user->save();



        return redirect('users')->with('success','User created successfully');



    }







    public function user_status(Request $request)



    {



        // dd($request->status);



        $user = User::find($request->id);



        $user->is_active = $request->status;



        $user->save();



        return response()->json(['success'=>'Status changed successfully.']);



       



    }







    public function edit($id)



    {



        $roles = Role::where('is_deleted',0)->where('company_id', auth()->user()->company_id)->get();



        $user = User::find($id);



        return view('users.edit',compact('user','roles'));



    }







    public function user_update(Request $request, $id)



    {



        $validate = $request->validate([



            'name' => 'required',



            'email' => 'required|max:255|unique:users,email,'.$id,



        ]);



   

        $user = User::find($id);



        $user->name = $request->input('name');



        $user->email = $request->input('email');

        $user->phone = $request->input('phone');


        $user->position = $request->input('position');
        $user->base_salary = $request->input('base_salary');
        $user->commission_rate = $request->input('commission_rate');
        $user->is_transport = $request->input('is_transport');
        $user->transport_allowance = $request->input('transport_allowance');


        $user->address = $request->input('address');

        $user->role_id = $request->input('role_id');

        if($request->image){

            $validate = $request->validate([



                'image' => 'mimes:jpeg,jpg,png,gif|required|max:2048',



            ]);

            $result =  DB::table('users')->select('image')->where('id', $id)->first(); 

            $image_path = public_path()."/profiles/".$result->image;  // Value is not URL but directory file path

            if(File::exists($image_path)) {

                File::delete($image_path);

            }

            $originalImage = $request->file('image');



            $originalImage->move(public_path().'/profiles/', $user_image = 'img_'.Str::random(15).'.'.$originalImage->getClientOriginalExtension());

            $user->image = $user_image;

        }



        if($request->input('password')!=""){



            $user->password = Hash::make($request->input('password'));



        }



        $user->save();



        return redirect('users')->with('success','User updated successfully');



    }







    public function profile($id)



    {



        $user = User::find($id);



        return view('users.profile',compact('user'));



    }







    public function profile_update(Request $request, $id)



    {



        $validate = $request->validate([



            'name' => 'required',



            'email' => 'required|email|unique:users,email,'.$id,



        ]);



        $user = User::find($id);



        $user->name = $request->input('name');

        $user->email = $request->input('email');

        if($request->image){



            $validate = $request->validate([



                'image' => 'mimes:jpeg,jpg,png,gif|required|max:2048',



            ]);

            $result =  DB::table('users')->select('image')->where('id', $id)->first(); 

            $image_path = public_path()."/profiles/".$result->image;  // Value is not URL but directory file path

            if(File::exists($image_path)) {

                File::delete($image_path);

            }

            $originalImage = $request->file('image');



            $originalImage->move(public_path().'/profiles', $user_image = 'img_'.Str::random(15).'.'.$originalImage->getClientOriginalExtension());

            $user->image = $user_image;



        }



        if($request->input('password')!=""){



            $user->password = Hash::make($request->input('password'));



        }

        $user->save();



        session()->flash('success','Your Profile has updated!');



        return back();



    }







    public function destroy($id)



    {



        DB::table('users')



            ->where('id', $id)



            ->where('company_id', auth()->user()->company_id)



            ->update(['is_deleted' => 1]);



        return back()->with('destroy','User deleted successfully!');







    }



    public function deleted_users(){

        

        $users = User::where('is_deleted', 1)->where('company_id', auth()->user()->company_id)->get();        

        return view('users.deleted',compact('users'));

    }



    public function undelete($id){

        

        DB::table('users')



        ->where('id', $id)



        ->where('company_id', auth()->user()->company_id)



        ->update(['is_deleted' => 0]);



    return back()->with('success','User Updated successfully!');

    }



    public function delete_permanent($id){

        $result =  DB::table('users')->select('image')->where('id', $id)->first(); 

            $image_path = public_path()."/profiles/".$result->image;  // Value is not URL but directory file path

            if(File::exists($image_path)) {

                File::delete($image_path);

            }

        $destroy = User::destroy($id);        

        return back()->with('destroy','User deleted successfully!');

    }


    public function payslip($id)
    {

        // Monthly hours: 6 working days/week * 8 hours/day * 4.33 weeks ≈ 208 hours
        $monthlyWorkingHours = 6 * 8 * 4;

        $start = now()->startOfMonth();
        $end = now();
        $daysInMonth = $end->diffInDays($start) + 1;

        $user = User::where('id', $id)->where('is_deleted', 0)->first();

        if (!$user || is_null($user->base_salary)) {
            return redirect()->back()->with('error', 'This user has no base salary set. Cannot generate payslip.');
        }



        $baseSalary = $user->base_salary;

        // 1. Commission from services
        $commission = $user->serviceBookings()
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_price') * ($user->commission_rate / 100);

        // 2. Attendances in pay period
        $attendances = $user->attendances();

        // 3. Hourly Rate
        $hourlyRate = $monthlyWorkingHours > 0 ? $baseSalary / $monthlyWorkingHours : 0;

        // 4. Overtime Calculation
        $overtimeMinutes = $attendances->sum('overtime_minutes');
        $overtimeHours = $overtimeMinutes / 60;
        $overtimePayment = $overtimeHours * $hourlyRate;

        // 5. Leave Deduction (status = 'A')
        $unpaidLeaves = $attendances->where('status', 'A')->count();
        $leaveDeduction = 0;

        if ($unpaidLeaves > 2) {
            $extraLeaves = $unpaidLeaves - 2;
            $leaveHours = $extraLeaves * 8;
            $leaveDeduction = $leaveHours * $hourlyRate;
        }

        // 6. Transport Deduction
        $transportDeduction = ($user->is_transport === 'yes' && $user->transport_allowance)
            ? $user->transport_allowance
            : 0;

        // 7. Tax Deduction (10%)
        $taxableIncome = $baseSalary + $commission + $overtimePayment;
        $tax = 0.10 * $taxableIncome;

        // 8. Net Pay
        $totalDeductions = $leaveDeduction + $transportDeduction + $tax;
        $netPay = $taxableIncome - $totalDeductions;

        
        // 8. Generate PDF (Don't save to DB)
        $pdf = PDF::loadView('paystub', [
            'user' => $user,
            'base_salary' => round($baseSalary, 2),
            'commission' => round($commission, 2),
            'overtime_payment' => round($overtimePayment, 2),
            'deductions' => round($totalDeductions, 2),
            'transport_deduction' => round($transportDeduction, 2), // <== ADD THIS
            'net_pay' => round($netPay, 2),
            'pay_period_start' => $start,
            'pay_period_end' => $end,
        ]);

        $filename = 'paystub_' . $user->id . '_' . $start->format('Ym') . '.pdf';

        return $pdf->download($filename);


    }


}



