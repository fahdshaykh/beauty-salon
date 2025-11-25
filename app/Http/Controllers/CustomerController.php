<?php

namespace App\Http\Controllers;

use App\Customer;
use App\ServiceBooking;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Http\Request;

use Session;

use DB;
use Illuminate\Support\Facades\Auth;
use App\Imports\CustomersImport;
use App\User;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller

{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        // Rows per page logic
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            Auth::user()->setSettings(['customers_rows_per_page' => $rows]);
        }
        $rows = Auth::user()->getSetting('customers_rows_per_page', auth()->user()->default_rows ?? 15);

        // Build query
        $query = Customer::where('company_id', auth()->user()->company_id)
                        ->where('is_deleted', 0);

        // Multi-field Search (name, phone, email, referred_by)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Search in first_name OR last_name OR full name combination
                $q->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('primary_phone', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('referred_by', 'LIKE', "%{$search}%");
            });
        }

        // Filter by registration date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute query with pagination + preserve all filters
        $customers = $query->latest('created_at')
                        ->paginate($rows)
                        ->withQueryString();

        // Hide unwanted field (optional - kept from your original)
        $customers->getCollection()->transform(function ($customer) {
            return $customer->makeHidden(['is_active']);
        });

        return view('customers.index', compact('customers', 'rows'));
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $this->authorize('create', Customer::class);

        $countries = DB::table('countries')->get();

        $referreds = DB::table('referred_customers')->where('is_active', true)->get();

        return view('customers.create', compact('countries', 'referreds'));

    }



    public function customer_import_view()

    {

        return view('customers.csvimport');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->authorize('create', Customer::class);

        $this->validate($request, 

            [

                'first_name' => 'required',

                'last_name' => 'required',

                'country_code' => 'required',

                'primary_phone' => 'required',

                'email' => 'nullable',

               

                // 'address' => 'required'

            ],

            [

                'first_name.required' => 'Please enter first name',

                'last_name.required' => 'Please enter last name',

                'country_code.required' => 'Please Select Country Code',

                'primary_phone.required' => 'Please Enter Phone number',

                // 'email.required' => 'Please enter Email',

             

                // 'address.required' => 'Please enter customer address',

            ],



        );

    

        $data = $request->all();

      

        //print_r($data); exit;

        $existing = DB::table('customers')->where('company_id', auth()->user()->company_id)->where('primary_phone','!=','')->where('primary_phone',$request->input('primary_phone'))->get();

        // if($existing->count()>0 && $existing[0]->is_active==1) {

            

        //     if($request->exists('now_date') && $request->filled('now_date')){

        //         $date = $data['now_date'];

        //         return redirect('get-booking-date/'.$date.'/success')->with('error','Customer already exists with this mobile# '.$request->input("primary_phone"));

        //     }



        //     return back()->with('error', 'Customer already exists with this mobile# '.$request->input("primary_phone"));



        // } 
        if($existing->count()>0 && $existing[0]->is_deleted==0) {



            // DB::table('customers')->where('id', $existing['0']->id)->update(['is_active'=>1]);

            if($request->exists('now_date') && $request->filled('now_date')){

                $date = $data['now_date'];

                return redirect('get-booking-date/'.$date)->with('error','Customer already exists with this mobile# '.$request->input("primary_phone"));

            }

            return redirect()->back()->with('error','Customer already exists with this mobile# '.$request->input("primary_phone"));



        }

        else{

            //print_r($data); exit;

            if($request->exists('now_date')){

               

	           $date = $data['now_date'];

	           $data = $request->except('now_date');

            }

            $data['company_id'] = auth()->user()->company_id;

            Customer::create($data);

            if($request->exists('now_date') && $request->filled('now_date')){

            return redirect('get-booking-date/'.$date.'/success')->with('success','Customer created successfully');

            }

            return redirect()->back()->with('success', 'Customer created successfully');  

        }



    }

    public function csvToArray($filename = '', $delimiter = ',')

        {

            if (!file_exists($filename) || !is_readable($filename))

                return false;



            $header = null;

            $data = array();

            if (($handle = fopen($filename, 'r')) !== false)

            {

                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)

                {

                    if (!$header)

                        $header = $row;

                    else

                        $data[] = array_combine($header, $row);

                }

                fclose($handle);

            }



            return $data;

        }

    public function checkfile($csvdata){

        $data_should_be = array("first_name","last_name","primary_phone","secondary_phone","email","zip","address","referred_by");

        // dd(count(current($csvdata)));

        if(sizeof($data_should_be)==sizeof($csvdata)){

            foreach($data_should_be as $column){

                if(array_key_exists($column, $csvdata)==false){

                    break;

                    return false;

                }

            }

            return true;

        }

        else{

            return false;

        }

        

    }



    public function importCsv(Request $request)

    {

        $request->validate([

            'import_file' => 'required|mimes:csv,txt'

        ],

        [

                    

            'import_file.required' => 'Please Upload CSV File',

            'import_file.mimes' => 'Please Upload CSV File'

        ]);

        $file = request()->file('import_file');



        $customerArr = $this->csvToArray($file);

        if($this->checkfile($customerArr[0])){

            for ($i = 0; $i < count($customerArr); $i ++)

            {

                //dd(array_column($customerArr[$i], 0));

                $customerArr[$i]["company_id"] = auth()->user()->company_id;

                if($customerArr[$i]['address']!=""){

                $latlong    =   $this->get_lat_long($customerArr[$i]['address']); // create a function with the name "get_lat_long" given as below

                $map        =   explode(',' ,$latlong);

                $customerArr[$i]["longitude"] = $map[0];

                $customerArr[$i]["latitude"] = $map[1];

                }

               $result =  Customer::firstOrCreate($customerArr[$i]);

               

            }

    

            return back()->with('success', 'Customers imported successfully.'); 

        }

        else{

            return back()->with('destroy', 'Please check file or download example file.'); 

        }

        

    }



    public function get_lat_long($address){



        $address = str_replace(" ", "+", $address);



        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=AIzaSyBCcV570TyjIEhvmOrSaMgxBLiUHa5RxXU");

        $json = json_decode($json);

        $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};

        $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

        return $lat.','.$long;

    }



    // public function import(Request $request)

    // {

    //     $request->validate([

    //         'import_file' => 'required'

    //     ]);

    //     Excel::import(new CustomerImport, request()->file('import_file'));

    //     return back()->with('success', 'Customers imported successfully.');

    // }







    /**



     * Display the specified resource.



     *



     * @param  \App\Customer  $customer



     * @return \Illuminate\Http\Response



     */



    public function show(Customer $customer)



    {



        //



    }







    // public function customer_detail(Request $request)
    // {



    //     $customer_detail = DB::table('customers')

    //                             ->where('company_id', auth()->user()->company_id)

    //                             ->where('id',$request->customer_id)

    //                             ->get();

    //     return response()->json($customer_detail);



    // }

    public function customer_detail(Request $request)
    {
        $customer = Customer::where('company_id', auth()->user()->company_id)
                            ->where('id', $request->customer_id)
                            ->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $service_info = ServiceBooking::with(['bookingdetail.service'])
            ->where('company_id', auth()->user()->company_id)
            ->where('customer_id', $customer->id)
            ->get();

        return response()->json([
            'customer' => $customer,
            'service_info' => $service_info
        ]);
    }






    /**



     * Show the form for editing the specified resource.



     *



     * @param  \App\Customer  $customer



     * @return \Illuminate\Http\Response



     */



    public function edit(Customer $customer)



    {

        $this->authorize('update', $customer);

        $countries = DB::table('countries')->get();

        $referreds = DB::table('referred_customers')->where('is_active', true)->get();


        return view('customers.edit',compact('customer', 'countries', 'referreds'));



    }







    /**



     * Update the specified resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @param  \App\Customer  $customer



     * @return \Illuminate\Http\Response



     */



    public function update(Request $request, Customer $customer)



    {

        $this->authorize('update', $customer);

        $this->validate($request, 



            [



                //'first_name' => 'required',



                'last_name' => 'required',



                // 'primary_phone' => 'numeric',

                // 'zip' => 'required',

                'address' => 'required'



            ],



            [



                //'first_name.required' => 'Please enter first name',



                'last_name.required' => 'Please enter last name',

                'zip_code.required' => 'Please enter PostCode',

                // 'primary_phone.numeric' => 'Please enter a valid phone',



                // 'address.required' => 'Please enter customer address',



            ],



        );



        



        $data = $request->except('_token','_method');



        $data['company_id'] = auth()->user()->company_id;



        DB::table('customers')->where('id', $customer->id)->update($data);



        return redirect('customers')->with('success', 'Cusotmer updated successfully');



    }



    public function customerstatus(Request $request)

    {

        $customer = Customer::find($request->id);

        $customer->is_active = $request->status;

        $customer->save();

        return response()->json(['success'=>'Status changed successfully.']);

    }





    /**



     * Remove the specified resource from storage.



     *



     * @param  \App\Customer  $customer



     * @return \Illuminate\Http\Response



     */



    public function destroy(Customer $customer)

    {

        // $customer = Customer::find($id);

        $this->authorize('update', $customer);

        $customer->is_deleted = 1;

        $customer->save();

        session()->flash('destroy','Customer deleted Successfully');

        return redirect('customers');

    }







    public function exportCsv(Request $request)



    {

        $date = date('d-m-Y');



        $fileName = $date.' Customers.csv';



        $tasks = Customer::where('company_id', auth()->user()->company_id)->get();







            $headers = array(



                "Content-type"        => "text/csv",



                "Content-Disposition" => "attachment; filename=$fileName",



                "Pragma"              => "no-cache",



                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",



                "Expires"             => "0"



            );







            $columns = array('First Name', 'Last Name', 'country code', 'Primary Phone', 'Date Of Birth', 'Email', 'Address');







            $callback = function() use($tasks, $columns) {



                $file = fopen('php://output', 'w');



                fputcsv($file, $columns);



                



                foreach ($tasks as $task) {



                    $row['First Name']  = $task->first_name;



                    
                    $row['Last Name']    = $task->last_name;
                    
                    
                    $row['country code']    = $task->country_code;

                    $row['Primary Phone']    = $task->primary_phone;



                    $row['Secondary Phone']  = $task->date_of_birth;



                    $row['Email']  = $task->email;




                    $row['Address']  = $task->address;



                    fputcsv($file, array($row['First Name'], $row['Last Name'], $row['country code'], $row['Primary Phone'], $row['Secondary Phone'], $row['Email'], $row['Address']));



                }







                fclose($file);



            };







        return response()->stream($callback, 200, $headers);



    }

    public function bookingsHistory($id)
    {
        
        // $service_info = DB::table('service_bookings')
        // ->join('booking_details', 'service_bookings.id', '=', 'booking_details.booking_id')
        // ->join('services', 'booking_details.service_id', '=', 'services.id')
        // ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        // ->where('service_bookings.company_id', auth()->user()->company_id)
        // ->where('service_bookings.customer_id', $id)
        // // ->groupBy('service_bookings.id')
        // ->get();
        $service_info = ServiceBooking::with(['bookingdetail.service'])
        ->where('company_id', auth()->user()->company_id)
        ->where('customer_id', $id)
        ->get();

        $customer = Customer::find($id);


        return view('customers.booking_history', compact('service_info', 'customer'));
    }

    //import excel file
    public function showImportForm()
    {
        return view('customers.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $import = new CustomersImport();
            Excel::import($import, $request->file('file'));
            $summary = $import->getImportSummary();

            $message = "Imported {$summary['importedRows']} customers successfully.";
            if ($summary['skippedRows'] > 0) {
                $message .= " Skipped {$summary['skippedRows']} rows due to invalid data or duplicates.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Customer import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error importing file: ' . $e->getMessage(),
            ], 500);
        }
    }



    //adding profile module from user conroller
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


}



