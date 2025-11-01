<?php







namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;



use App\User;
use PDF;
use Carbon\Carbon;

use App\Models\Role;
use App\Models\Setting;
use App\Models\UserSetting;
use DB;

use File;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;





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
    // Get only roles from current company
    $roles = Role::where('company_id', auth()->user()->company_id)
                 ->where('name', '!=', 'admin') // optional: hide admin
                 ->get();

    return view('users.create', compact('roles'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'phone' => 'nullable|string|max:15',
        'base_salary' => 'nullable|numeric',
        'commission_rate' => 'nullable|numeric',
        'is_transport' => 'required|in:yes,no',
        'transport_allowance' => 'nullable|numeric|required_if:is_transport,yes',
        'address' => 'nullable|string',
        'role' => 'required|exists:roles,name', // Spatie uses 'name'
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ], [
        'role.required' => 'Please select a role.',
        'role.exists' => 'Selected role is invalid.',
        'transport_allowance.required_if' => 'Transport allowance is required when transport is yes.'
    ]);

    // CREATE USER
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'base_salary' => $request->base_salary,
        'commission_rate' => $request->commission_rate,
        'is_transport' => $request->is_transport,
        'transport_allowance' => $request->is_transport === 'yes' ? $request->transport_allowance : 0,
        'address' => $request->address,
        'company_id' => auth()->user()->company_id,
    ]);

    // UPLOAD IMAGE
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = 'img_' . Str::random(15) . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('profiles'), $filename);
        $user->image = $filename;
        $user->save();
    }

    // SET TEAM ID (CRITICAL!)
    app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

    // ASSIGN ROLE (Spatie)
    $user->assignRole($request->role);

    return redirect()->route('users.index')
        ->with('success', 'User created successfully');
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
        $user = User::findOrFail($id);

        // SECURITY: Only allow editing users from same company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $roles = Role::where('company_id', auth()->user()->company_id)
                 ->where('name', '!=', 'admin') // optional: hide admin
                 ->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // SECURITY: Only allow update from same company
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:15',
            'base_salary' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric',
            'is_transport' => 'required|in:yes,no',
            'transport_allowance' => 'nullable|numeric|required_if:is_transport,yes',
            'address' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:6'
        ]);

        // UPDATE USER
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'base_salary' => $request->base_salary,
            'commission_rate' => $request->commission_rate,
            'is_transport' => $request->is_transport,
            'transport_allowance' => $request->is_transport === 'yes' ? $request->transport_allowance : null,
            'address' => $request->address,
        ]);

        // UPDATE PASSWORD IF PROVIDED
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // UPDATE IMAGE
        if ($request->hasFile('image')) {
            // Delete old image
            if ($user->image && file_exists(public_path('profiles/' . $user->image))) {
                unlink(public_path('profiles/' . $user->image));
            }

            $image = $request->file('image');
            $filename = 'img_' . Str::random(15) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profiles'), $filename);
            $user->image = $filename;
            $user->save();
        }

        // SET TEAM ID
        app(PermissionRegistrar::class)->setPermissionsTeamId(auth()->user()->company_id);

        // SYNC ROLE (Spatie)
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
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



