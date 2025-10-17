<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;


use App\ServiceBooking;
use App\Http\Resources\CustomerResource;

use App\Customer;

use DB;

use Validator;
use Carbon\Carbon;


class CustomerController extends BaseController

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function __construct(){

        ini_set( 'precision', 7 );

        ini_set( 'serialize_precision', -1 );

    }

    

    public function index(Request $request)

    {
        // 
        $customers = Customer::select('*')->where('company_id', $request->company_id)->orderBy('created_at', 'desc')->get();



        if (count($customers) == 0) {

            return $this->sendResponse($customers->toArray(), 'There is no data.');

        }



        return $this->sendResponse($customers->toArray(), 'customers retrieved successfully.');

    }

    public function customer_listing_test(){
        \DB::enableQueryLog();
        $bookingdata = ServiceBooking::with('customer')->whereBetween('due_date', 
        [Carbon::now()->subDays(2), Carbon::now()->addDays(6)]
        )->orderBy('due_date', 'ASC')->get();
        dd(\DB::getQueryLog());
    }

    public function customer_listing(Request $request)

    {
        // 
        //$bookingdata = ServiceBooking::with('customer')->where('company_id', $request->company_id)->orderBy('due_date', 'ASC')->get();
    $bookingdata = ServiceBooking::with('customer')->whereBetween('due_date', 
        [Carbon::now()->subDays(2), Carbon::now()->addDays(6)]
    )->where('company_id', $request->company_id)->orderBy('due_date', 'ASC')->get();
    //     $customers = Customer::select('*')->whereBetween('created_at', 
    //     [Carbon::now()->startOfWeek(Carbon::SUNDAY), Carbon::now()->endOfWeek(Carbon::SUNDAY)]
    // )->where('company_id', $request->company_id)->orderBy('created_at', 'desc')->get();
        $customers = array();
        if (count($bookingdata)> 0){
            foreach($bookingdata as $data){
             
                $customers[] = $data->customer;
            }
        }

        if (count($customers) == 0) {

            return $this->sendResponse($customers, 'Something went wrong.');

        }



        return $this->sendResponse($customers, 'customers retrieved successfully.');

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'company_id' => 'required',

            'first_name' => 'required',

            'last_name' => 'required',

            'address' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }



        $mobile_number = $request->input('primary_phone');

        $data = $request->all();

        

        $existing = DB::table('customers')->where('company_id', $request->company_id)->where('primary_phone','!=','')->where('primary_phone',$request->input('primary_phone'))->get()->count();

        

        if($existing>0) {

            if(isset($data['now_date'])){

                $date = $data['now_date'];
                return $this->sendError($empty = [], 'Customer already exists!');  
                //return $this->sendResponse($empty = [],'Customer already exists!');

            }

            
            return $this->sendError($empty = [], 'Customer already exists!');  
            //return $this->sendResponse($empty = [],'Customer already exists!');



        } else {

            

            $exists = DB::table('customers')->where('company_id', $request->company_id)->where('primary_phone','!=','')->where('primary_phone',$request->input('primary_phone'))->get();

           

            if(!$exists->isEmpty() && isset($data['now_date'])) {

       

                $data = $request->all();

                $data = $request->except('_token','_method');



                $data = $request->except('now_date');

                

                $customer = tap(DB::table('customers')->where('id', $exists['0']->id))

                ->update($data)

                ->first();

                

                return $this->sendResponse($customer, 'Customer created successfully.');

            }

            else{

                if(isset($data['now_date'])){

                    $date = $data['now_date'];

                    $data = $request->except('now_date');

                    $success = "Customer created successfully!";

                    

                    $customer = Customer::create($data);

                    return $this->sendResponse($customer->toArray(), 'Customer created successfully.');

                }

                else{

                

                    $checkexists = DB::table('customers')->where('company_id', $request->company_id)->where('primary_phone',$request->input('primary_phone'))->get();

                    if(!$exists->isEmpty()){

                        $data = $request->except('_token','_method');

                  

                        

                        $customer = tap(DB::table('customers')->where('id', $exists['0']->id))

                        ->update($data)

                        ->first();

                        return $this->sendResponse($customer, 'Customer created successfully.');

                    

                    } else{

                        

                        $customer =  Customer::create($data);

                        return $this->sendResponse($customer->toArray(), 'Customer created successfully.');

                    }

                

                }

            

            }

        }

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show(Request $request)

    {



        $input = $request->all();



        $validator = Validator::make($input, [

            'id' => 'required',

            'company_id' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }



        $customer = Customer::where('company_id', $request->company_id)

                    ->where('id',$request->id)->get();



        if (count($customer) == 0) {
            return $this->sendError($customer->toArray(), 'Customer already exists!');  
            //return $this->sendResponse($customer->toArray(), 'Something went wrong.');

        }


        return $this->sendError($customer->toArray(), 'Customer already exists!'); 
        //return $this->sendResponse($customer->toArray(), 'customer retrieved successfully.');

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'id' => 'required',

            'company_id' => 'required',

            'first_name' => 'required',

            'last_name' => 'required',

            'address' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

        

        $data = $request->all();

        

        $customer = tap(DB::table('customers')->where('id', $request->id)->where('company_id', $request->company_id))

        ->update($data)

        ->first();

        if ($customer == null) {

            //return $this->sendResponse($customer, 'Something went wrong.');
            return $this->sendError($customer, 'Something went wrong.'); 

        }

        return $this->sendResponse($customer, 'Customer Updated successfully.');

    }



    public function status(Request $request)

    {

        $customer = tap(DB::table('customers')

        ->where('id', $request->id)->where('company_id', $request->company_id))

        ->update(['is_active' => $request->status])

        ->first();



        if ($customer) {

            return $this->sendResponse($customer, 'Status changed successfully.');

        } else {
            return $this->sendError((array)$customer, 'Something went wrong.'); 
            //return $this->sendResponse((array)$customer, 'Something went wrong.');

        }



    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    // public function destroy(Request $request)

    // {

    //     $input = $request->all();



    //     $validator = Validator::make($input, [

    //         'id' => 'required',

    //         'company_id' => 'required'

    //     ]);



    //     if($validator->fails()){

    //         return $this->sendError('Validation Error.', $validator->errors());       

    //     }



    //     $customer = tap(DB::table('customers')

    //     ->where('id', $request->id)->where('company_id', $request->company_id))

    //     ->update(['is_deleted' => 1])

    //     ->first();

        

    //     if ($customer == null) {

    //         return $this->sendError('customer not found.');

    //     }

        

    //     return $this->sendResponse($customer, 'Customer Deleted successfully.');

        

    // }

}

