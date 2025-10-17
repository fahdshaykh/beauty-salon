<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\ServiceBooking;

use App\Services;

use App\Customer;

use App\Expense;
use App\Models\SupplierPayment;
use DB;



class ReportController extends Controller

{

    public function index()

    {

        $categories = DB::table('categories')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->get();

        

        $services = DB::table('services')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->get();

        return view('reports.index',compact('categories','services'));

    }



    public function graph_report()

    {

        return view('reports.graph_report');

    }

    public function heat_graph_report()
    {
        $services = DB::table('services')->where('company_id', auth()->user()->company_id)->where('is_active',1)->get();
        // return view('reports.graph_report');
        return view('reports.heatmap',compact('services'));
        
    }
    public function customer_graph_report(Request $request)
    {
        
        $start_date = date("Y").'-'.date("m").'-'.'01';

        $end_date = date("Y").'-'.date("m").'-'.'31';



        if($request->start_date && $request->end_date){

            $start_date = date('Y-m-d', strtotime($request->start_date));

            $end_date = date('Y-m-d', strtotime($request->end_date));

        }
        $referredcustomers = DB::table('service_bookings')
        ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        ->whereDate('service_bookings.due_date', '>=' , $start_date)
        ->whereDate('service_bookings.due_date', '<=' , $end_date)
        ->where('service_bookings.service_status', '!=' , 4)
        ->where('service_bookings.service_status', '!=' , 5)
        ->select('customers.referred_by', DB::raw('count(*) as total'))
        ->where('service_bookings.company_id', auth()->user()->company_id)
        ->groupBy('customers.referred_by')
        ->get();
        // $referredcustomers = Customer::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])->where('company_id', auth()->user()->company_id)->select('referred_by', DB::raw('count(*) as total'))
        // ->groupBy('referred_by')
        // ->get();

        // dd($referredcustomers);
    
        //whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])
        return response()->json($referredcustomers);
    }

    public function customer_graph(){
        return view('reports.customer_graph');
    }



    public function customer_country_graph_report(Request $request)
    {
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");

        if ($request->start_date && $request->end_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
        }

        // // Get country-wise customer count
        // $customers_by_country = DB::table('service_bookings')
        //     ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        //     ->join('countries', DB::raw("TRIM(LEADING '+' FROM countries.phone_code)"), '=', 'customers.country_code')
        //     ->whereBetween('service_bookings.due_date', [$start_date, $end_date])
        //     ->where('service_bookings.company_id', auth()->user()->company_id)
        //     ->select('countries.name as country', DB::raw('COUNT(customers.id) as count'))
        //     ->groupBy('countries.name')
        //     ->orderByDesc('count')
        //     ->get();
    //     $customers_by_country = DB::table('service_bookings')
    // ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
    // ->join('countries', function ($join) {
    //     $join->on('customers.country_code', '=', 'countries.phone_code');
    // })
    // ->whereBetween('service_bookings.due_date', [$start_date, $end_date])
    // ->where('service_bookings.company_id', auth()->user()->company_id)
    // ->select('countries.name as country', DB::raw('COUNT(DISTINCT customers.id) as count'))
    // ->groupBy('countries.name')
    // ->orderByDesc('count')
    // ->get();


    $customers_by_country = DB::table('service_bookings')
        ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        ->join('countries', DB::raw("TRIM(LEADING '+' FROM countries.phone_code)"), '=', 'customers.country_code')
        ->whereDate('service_bookings.due_date', '>=' , $start_date)
        ->whereDate('service_bookings.due_date', '<=' , $end_date)
        // ->where('service_bookings.service_status', '!=' , 4)
        // ->where('service_bookings.service_status', '!=' , 5)
        // ->select('customers.country_code', DB::raw('count(*) as total'))
        ->select('countries.name as country', DB::raw('COUNT(DISTINCT customers.id) as total'))
        ->where('service_bookings.company_id', auth()->user()->company_id)
        ->groupBy('customers.country_code')
        ->get();

        return response()->json([
            'countries' => $customers_by_country
        ]);
    }


    public function customer_country_graph(){
        return view('reports.customer_country_graph');
    }

    public function profit_loss_report(){
        return view('reports.profit_loss_report');
    }

    public function zipcode_report(){
        return view('reports.postcode_graph');
    }

    public function zipcode_data(Request $request){

        $start_date = date("Y").'-'.date("m").'-'.'01';

        $end_date = date("Y").'-'.date("m").'-'.'31';



        if($request->start_date && $request->end_date){

            $start_date = date('Y-m-d', strtotime($request->start_date));

            $end_date = date('Y-m-d', strtotime($request->end_date));

        }
        $referredcustomers = DB::table('service_bookings')
        ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        ->whereDate('service_bookings.due_date', '>=' , $start_date)
        ->whereDate('service_bookings.due_date', '<=' , $end_date)
        ->where('service_bookings.service_status', '!=' , 4)
        ->where('service_bookings.service_status', '!=' , 5)
        ->select(DB::raw('(CASE WHEN LENGTH(zip)>5 THEN SUBSTRING(zip, 1, 3) ELSE SUBSTRING(zip, 1, 2) END) as zip'), DB::raw('count(*) as total'))
        ->where('service_bookings.company_id', auth()->user()->company_id)
        ->groupBy(DB::raw('(CASE WHEN LENGTH(zip)>5 THEN SUBSTRING(zip, 1, 3) ELSE SUBSTRING(zip, 1, 2) END)'))
        ->orderBy('customers.zip','DESC')
        ->get();
        // $referredcustomerswith3zipcode = DB::table('service_bookings')
        // ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        // ->whereDate('service_bookings.due_date', '>=' , $start_date)
        // ->whereDate('service_bookings.due_date', '<=' , $end_date)
        // ->where('service_bookings.service_status', '!=' , 4)
        // ->select(DB::raw('SUBSTRING(zip, 1, 3) as zip'), DB::raw('count(*) as total'))
        // ->where('service_bookings.company_id', auth()->user()->company_id)
        // ->groupBy(DB::raw('substr(zip,1,3)'))
        // ->get();
        // $alllist = $referredcustomers->merge($referredcustomerswith3zipcode);
     
    //dd($referredcustomers);
     
        
        return response()->json($referredcustomers);
    }
    
    public function profit_loss_data(Request $request)
    {
        $start_date = date("Y-m-01");
        $end_date = date("Y-m-t");

        if ($request->start_date && $request->end_date) {
            $start_date = date('Y-m-d', strtotime($request->start_date));
            $end_date = date('Y-m-d', strtotime($request->end_date));
        }

        $company_id = auth()->user()->company_id;

        // 1. Fetch total general expenses
        $expenses = Expense::whereDate('payable', '>=', $start_date)
            ->whereDate('payable', '<=', $end_date)
            ->where('company_id', $company_id)
            ->select(DB::raw('SUM(amount) as totalexpense'))
            ->first();

        // 2. Fetch total supplier payments (inventory purchases)
        $supplierPayments = SupplierPayment::where('company_id', $company_id)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->select(DB::raw('SUM(amount) as totalpaid'))
            ->first();

            // dd($supplierPayments->totalpaid);
        // 3. Fetch total sales
        $sales = ServiceBooking::whereDate('due_date', '>=', $start_date)
            ->whereDate('due_date', '<=', $end_date)
            ->where('company_id', $company_id)
            ->whereNotIn('service_status', [4, 5])
            ->select(DB::raw('SUM(total_price) as totalsales'))
            ->first();

        // 4. Combine expenses
        $expenseAmount = floatval($expenses->totalexpense) + floatval($supplierPayments->totalpaid);
        $salesAmount = floatval($sales->totalsales);

        $data = [
            'expense' => $expenseAmount,
            'sales' => $salesAmount,
            'revenue' => $salesAmount - $expenseAmount
        ];

        // dd($data);
        return response()->json($data);
    }


    public function get_booking_chart(Request $request)

    {

        $start_date = date("Y").'-'.date("m").'-'.'01';

        $end_date = date("Y").'-'.date("m").'-'.'31';



        if($request->start_date && $request->end_date){

            $start_date = date('Y-m-d', strtotime($request->start_date));

            $end_date = date('Y-m-d', strtotime($request->end_date));

        }

        

        $service_info = DB::table('service_bookings')
        ->join('booking_details', 'service_bookings.id', '=', 'booking_details.booking_id')
        ->join('services', 'booking_details.service_id', '=', 'services.id')
        ->join('customers', 'service_bookings.customer_id', '=', 'customers.id')
        ->whereDate('service_bookings.due_date', '>=' , $start_date)
        ->whereDate('service_bookings.due_date', '<=' , $end_date)
        ->where('service_bookings.service_status', '!=' , 4)
        ->where('service_bookings.service_status', '!=' , 5)
        ->select(DB::raw('count(*) as total'), DB::raw('SUM(service_bookings.total_price) as total_amount'), 'services.name','services.service_color','customers.address')
        ->where('service_bookings.company_id', auth()->user()->company_id)
        ->groupBy('booking_details.service_id')
        ->get();

               // dd($service_info);



        return response()->json($service_info);

        

    }

    public function show_heat_report(Request $request)
    {
        DB::enableQueryLog();
        if($request->graphs_dates){
            $dates = explode("-", $request->graphs_dates);
            $start_date = date('Y-m-d', strtotime($dates[0]));
            $end_date = date('Y-m-d', strtotime($dates[1]));
        }
        $service_info = DB::table('service_bookings')
        ->join('booking_details', 'service_bookings.id', '=', 'booking_details.booking_id')
        ->join('services', 'booking_details.service_id', '=', 'services.id')
        ->join('customers', 'service_bookings.customer_id', '=', 'customers.id');
        if($request->service!=""){
            $info = $service_info->where('booking_details.service_id', $request->service);
        }
        $info = $service_info->whereDate('service_bookings.due_date', '>=' , $start_date)
        ->whereDate('service_bookings.due_date', '<=' , $end_date)
        ->select('services.name','services.service_color','customers.zip','customers.first_name','customers.last_name')
        ->where('service_bookings.service_status', '!=' , 5)
        ->where('service_bookings.company_id', auth()->user()->company_id)->get();
                // $query = DB::getQueryLog();
                // print_r($query); exit;

        return response()->json($info);

    }



    public function customer_index()

    {

        $customers = DB::table('customers')->where('company_id', auth()->user()->company_id)->get();

       

        return view('reports.customer_index',compact('customers'));

    }



    public function expense_index()

    {

        $expense_types = DB::table('expense_types')->where('company_id', auth()->user()->company_id)->get();

        $payment_methods = DB::table('payment_methods')

        ->get();

        return view('reports.expense_index',compact('expense_types','payment_methods'));

    }



    public function record(Request $request)

    {



        //fetching all data

        $fetching_data = $request->all();

        



        //creating data variables

        $start_date = $request->start_date;

        $end_date = $request->end_date;

        $category_id = $request->category_id;

        $service_id = $request->service_id;

        $status_id = $request->status_id;

        $payment_instrument = $request->payment_instrument;

        //date validtaing



            //echo $start_date.'>>>>>>'.$end_date; exit;

            if($category_id=="no" && $service_id=="no" && $status_id=="no" && $payment_instrument=="no"){

              

                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

               

                $data = $service_bookings->groupBy('invoice');

    

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                    }

                    

                } else {

                    return back()->with('error','Sorry no record found.');

                }

         

                //echo "<pre>"; print_r($data_array); exit;

                return view('reports.reports_by_types')->with('report_data', $data_array);

               }

            elseif($category_id =="no" && $service_id=="no" && $status_id=="no" && $payment_instrument!="no"){

           

                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->where('payment_instrument',$payment_instrument)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();



                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);







            }

            elseif($category_id =="no" && $service_id=="no" && $status_id!="no" && $payment_instrument=="no"){

           

                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->where('service_status',$status_id)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

               



                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);







            }

            elseif($category_id =="no" && $service_id!="no" && $status_id=="no" && $payment_instrument=="no"){

               



                $servicenames = DB::table('booking_details')->where('service_id',$service_id)->get();

              if(count($servicenames)>0){

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id=="no" && $status_id=="no" && $payment_instrument=="no"){

              



                $servicenames = DB::table('services')->where('category_id',$category_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            else if($category_id=="no" && $service_id=="no" && $status_id!="no" && $payment_instrument!="no"){

              

                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

               

                $data = $service_bookings->groupBy('invoice');

    

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                    }

                    

                } else {

                    return back()->with('error','Sorry no record found.');

                }

         

            

                return view('reports.reports_by_types')->with('report_data', $data_array);

               }

            elseif($category_id =="no" && $service_id!="no" && $status_id=="no" && $payment_instrument=="no"){

               



                $servicenames = DB::table('booking_details')->where('service_id',$service_id)->get();

              if(count($servicenames)>0){

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id =="no" && $service_id!="no" && $status_id=="no" && $payment_instrument!="no"){

               



                $servicenames = DB::table('booking_details')->where('service_id',$service_id)->get();

              if(count($servicenames)>0){

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id=="no" && $status_id=="no" && $payment_instrument!="no"){

              



                $servicenames = DB::table('services')->where('category_id',$category_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id =="no" && $service_id!="no" && $status_id!="no" && $payment_instrument=="no"){

               



                $servicenames = DB::table('booking_details')->where('service_id',$service_id)->get();

              if(count($servicenames)>0){

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id=="no" && $status_id!="no" && $payment_instrument=="no"){

              



                $servicenames = DB::table('services')->where('category_id',$category_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id=="no" && $status_id!="no" && $payment_instrument!="no"){

              



                $servicenames = DB::table('services')->where('category_id',$category_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

                    

            }

            elseif($category_id =="no" && $service_id!="no" && $status_id!="no" && $payment_instrument!="no"){

               



                $servicenames = DB::table('booking_details')->where('service_id',$service_id)->get();

              if(count($servicenames)>0){

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

             

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id!="no" && $status_id=="no" && $payment_instrument!="no"){

              



                $servicenames = DB::table('services')->where('category_id',$category_id)->where('id',$service_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('payment_instrument',$payment_instrument)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id!="no" && $status_id!="no" && $payment_instrument=="no"){

                $servicenames = DB::table('services')->where('category_id',$category_id)->where('id',$service_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id!="no" && $status_id=="no" && $payment_instrument=="no"){

                $servicenames = DB::table('services')->where('category_id',$category_id)->where('id',$service_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            elseif($category_id !="no" && $service_id!="no" && $status_id!="no" && $payment_instrument!="no"){

                $servicenames = DB::table('services')->where('category_id',$category_id)->where('id',$service_id)->get();

                if(count($servicenames)>0){

              

                foreach($servicenames as $service_name){

                  $services_id[]= $service_name->id;

                }

                

                $servicenames = DB::table('booking_details')->whereIn('service_id',$services_id)->get();

              

                foreach($servicenames as $service_name){

                  $booking_id[]= $service_name->booking_id;

                }

                



                $service_bookings = ServiceBooking::whereBetween('due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"])->whereIn('id',$booking_id)->where('company_id', auth()->user()->company_id)->where('service_status',$status_id)->where('payment_instrument')->orderBy('due_date', 'desc')->get();

                

                $data = $service_bookings->groupBy('invoice');

               

                if(count($data) > 0)

                {

               

                    foreach($data as $key=>$value){

                        

                        $services = '';

                        if(count($value)> 0){

                          

                            foreach($value as $val){

                                

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();

                               

                                //$services ='';

                                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $val['id'])->get();

                                foreach($servicenames as $service_name){

                                  $services .= $service_name->name.',';

                                

                                }

                                

                                $customer_name = DB::table('customers')

                                ->where(['id' => $val['customer_id']])

                                ->first();

                             

                                $data_array[$key] = array(

                                    'user_name' => $user_name->name,

                                    'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 

                                    'total_price' => $val['total_price'],

                                    'payment_type' => $val['payment_instrument'],

                                    'due_date' => $val['due_date'],

                                    'address' => $customer_name->address,

                                    'booking_status' => $val['service_status'],

                                );

                            

                            }

                        

                            $services = rtrim($services, ',');

                            array_push($data_array[$key],$services,$start_date,$end_date);

                            

                        }

                     } 

                    }

                    else {

                        return back()->with('error','Sorry no record found.');

                    }

                }

                else {

                    return back()->with('error','Sorry no record found.');

                }

                

                    return view('reports.reports_by_types')->with('report_data', $data_array);

            }

            

                else{

                    return back()->with('error','Sorry no record found.'); 

                }

            

            



    }



    public function customer_record(Request $request)

    {

        $this->validate($request, 

            [

                'customer_id' => 'required',

            ],

            [

                'customer_id.required' => 'Please select customer',

            ],

        );

        $start_date = 'no-date';

        $end_date = 'no-date';

        $customer_id = $request->customer_id;

        $bookings = ServiceBooking::with('customer','user')->where('company_id', auth()->user()->company_id);

        if($request->start_date && $request->end_date){

            $bookings->whereBetween('created_at', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);

            $start_date = $request->start_date." 00:00:00";

            $end_date = $request->end_date." 23:59:59";

        }

        if($request->customer_id){

            $bookings->where('customer_id',$request->customer_id);

        }

        $service_bookings = $bookings->orderBy('created_at', 'desc')->get();

        if(count($service_bookings)>0)

        {

            $total_amount = 0;

            foreach($service_bookings as $key=>$value)

            {

              

                $services ='';

               

                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id']);

                $snames = $servicenames->get()->toArray();

                $services = array_column($snames,'name');

                $services = implode (", ", $services);

                $total_amount = $total_amount + $value->total_price;

                $data_array[$key] = array(

                    'id' => $value->id,

                    'status' => $value->status,

                    'customer_name' => $value->customer->first_name.' '.$value->customer->last_name,

                    'customer_address'=>$value->customer->address,

                    'customer_zip'=>$value->customer->zip,

                    'user_name' => $value->user->name,

                    'total_price' => $value->total_price,

                    'due_date' => $value->due_date,

                    'due_time' => $value->due_time,

                    'finish_time' => $value->finish_time,

                    'service_status' => $value->service_status,

                    'services' => $services,

                    'payment_instrument'=> $value->payment_instrument,

                    'payment_days'=> $value->payment_days,

                );

            }

           

            return view('reports.customer_reports', compact('data_array','total_amount','start_date','end_date','customer_id'));

        }

        else{

            return back()->with('error','Sorry no record found.');

        }

    }



    public function expense_record(Request $request)

    {

        // dd($request->all());

        // dd($request->filled('expense_type'));

        $this->validate($request, 

            [

                'expense_type' => 'required',

                // 'start_date' => 'required',

                // 'end_date' => 'required',

            ],

            [

                'expense_type.required' => 'Please select Expense type',

            ],

        );



        $start_date = $request->start_date;

        $end_date = $request->end_date;

        $type = $request->expense_type;

        //getting data for report

        $expenses = Expense::select('expenses.*','expense_types.type','payment_methods.method')

        ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')

        ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method');

        //->where('expenses.is_deleted', 0);

        if($start_date!="" && $end_date!=""){

            $expenses->whereBetween('expenses.payable', [$request->start_date, $request->end_date]);

        }

        if($request->expense_type!='no'){

            $expenses->where('expenses.expense_type', $request->expense_type);

        }

        if($request->payment_method!='no'){

            $expenses->where('expenses.payment_method', $request->payment_method);

        } 

        $expense_report = $expenses->where('expenses.company_id', auth()->user()->company_id)->orderBy('expenses.created_at', 'desc')->get();

        if($start_date==''){

            $start_date = 'no_start_date';

        }

        if($end_date==''){

            $end_date = 'no_end_date';

        }

        

        $view = view('reports.expense_reports', [

            'expense_report' => $expense_report,

            'start_date' => $start_date,

            'end_date' => $end_date,

            'type' => $type,

            'method'=>$request->payment_method

                 ])->render();

                 return response()->json(['view'=> $view]);

        // if($expense_report->isEmpty()){

        //     return redirect('expense-reports')->with('error','Sorry no record found');

        // }

        

        // return view('reports.expense_reports',compact('expense_report','start_date','end_date','type'));



    }



    public function reportCsv(Request $request, $start_date, $end_date, $category_id, $service_id, $status_id, $payment_type)

    {

        $date = date('d-m-Y');

        $fileName = $date.'Reports.csv';



        

            if($category_id==0 && $service_id=="no" && $status_id=="no_status" && $payment_type=="no_payment"){

             $service_bookings = ServiceBooking::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();

             $data = $service_bookings->groupBy('invoice');

             

                 foreach($data as $key=>$value){

                     $services = '';

                     if(count($value)> 0){

                         foreach($value as $val){

                            $user_name = DB::table('users')

                            ->where(['id' => $val['user_id']])

                            ->first();

                             

                            $service_name = DB::table('services')

                             ->where(['id' => $val['service_id']])

                             ->first();

 

                             $customer_name = DB::table('customers')

                             ->where(['id' => $val['customer_id']])

                             ->first();

 

                             $services .= $service_name->name.',';

                             

                             $data_array[$key] = array(

                                 'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                 'user_name' => $user_name->name,

                                 'total_price' => $val['total_price'],

                                 'due_date' => $val['due_date'],

                                 'due_time' => $val['due_time'],

                                 'service_status' => $val['service_status'],

                                 'payment_instrument' => $val['payment_instrument'],

                             );

                         }

                         $services = rtrim($services, ',');

                         array_push($data_array[$key],$services,$start_date,$end_date);

                        }

                    } 

            }

            elseif($status_id !=="no_status"){

             $service_bookings = ServiceBooking::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])

             ->orderBy('created_at', 'desc')->where('company_id', auth()->user()->company_id)->where('service_status', $status_id)->get();

             $data = $service_bookings->groupBy('invoice');

            

                 if(count($data) > 0){

                     foreach($data as $key=>$value){

                         $services = '';

                         if(count($value)> 0){

                             foreach($value as $val){

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();



                                $service_name = DB::table('services')

                                 ->where(['id' => $val['service_id']])

                                 ->first();

         

                                 $customer_name = DB::table('customers')

                                 ->where(['id' => $val['customer_id']])

                                 ->first();

         

                                 $services .= $service_name->name.',';

                                 

                                 $data_array[$key] = array(

                                     'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                     'user_name' => $user_name->name,

                                     'total_price' => $val['total_price'],

                                     'due_date' => $val['due_date'],

                                     'due_time' => $val['due_time'],

                                     'service_status' => $val['service_status'],

                                     'payment_instrument' => $val['payment_instrument'],

                                 );

                             }

                             $services = rtrim($services, ',');

                             array_push($data_array[$key],$services,$start_date,$end_date);

                         }

                     }

                 }

            }

            elseif($payment_type !=="no_payment"){

             $service_bookings = ServiceBooking::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])

             ->orderBy('created_at', 'desc')->where('company_id', auth()->user()->company_id)->where('payment_instrument', $payment_type)->get();

             $data = $service_bookings->groupBy('invoice');

                 if(count($data) > 0){

                     foreach($data as $key=>$value){

                         $services = '';

                         if(count($value)> 0){

                             foreach($value as $val){

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();



                                $service_name = DB::table('services')

                                 ->where(['id' => $val['service_id']])

                                 ->first();

         

                                 $customer_name = DB::table('customers')

                                 ->where(['id' => $val['customer_id']])

                                 ->first();

         

                                 $services .= $service_name->name.',';

                                 

                                 $data_array[$key] = array(

                                     'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                     'user_name' => $user_name->name,

                                     'total_price' => $val['total_price'],

                                     'due_date' => $val['due_date'],

                                     'due_time' => $val['due_time'],

                                     'service_status' => $val['service_status'],

                                     'payment_instrument' => $val['payment_instrument'],

                                 );

                             }

                             $services = rtrim($services, ',');

                             array_push($data_array[$key],$services,$start_date,$end_date);

                         }

                     }

                 }

            }

            elseif($category_id!=0){

                if($service_id==0){

                    // get all services ids from services table against above category id

                    $all_services = DB::table('services')->where('company_id', auth()->user()->company_id)->where('category_id', $category_id)->get();

       

                    $services_ids = array();

                    foreach($all_services as $key => $service_value){

                        

                        $services_ids[] = $service_value->id;

                    }

 

                    $service_bookings = ServiceBooking::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])->whereIn('service_id',$services_ids)->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();

 

                    $data = $service_bookings->groupBy('invoice');

                    if(count($data) > 0)

                     {

                         foreach($data as $key=>$value){

                             $services = '';

                             if(count($value)> 0){

                                 foreach($value as $val){

                                    $user_name = DB::table('users')

                                    ->where(['id' => $val['user_id']])

                                    ->first();



                                    $service_name = DB::table('services')

                                     ->where(['id' => $val['service_id']])

                                     ->first();

 

                                     $customer_name = DB::table('customers')

                                     ->where(['id' => $val['customer_id']])

                                     ->first();

 

                                     $services .= $service_name->name.',';

                                     

                                     $data_array[$key] = array(

                                         'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                         'user_name' => $user_name->name,

                                         'total_price' => $val['total_price'],

                                         'due_date' => $val['due_date'],

                                         'due_time' => $val['due_time'],

                                         'service_status' => $val['service_status'],

                                         'payment_instrument' => $val['payment_instrument'],

                                     );

                                 }

                                 $services = rtrim($services, ',');

                                 array_push($data_array[$key],$services,$start_date,$end_date);

                             }

                         }

                     }

                 }

                 else{

                 $service_bookings = ServiceBooking::whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"])->where('company_id', auth()->user()->company_id)->where('service_id',$service_id)->orderBy('created_at', 'desc')->get();

                 $data = $service_bookings->groupBy('invoice');

                 if(count($data) > 0)

                    {

                     foreach($data as $key=>$value){

                         $services = '';

                         if(count($value)> 0){

                             foreach($value as $val){

                                $user_name = DB::table('users')

                                ->where(['id' => $val['user_id']])

                                ->first();



                                $service_name = DB::table('services')

                                 ->where(['id' => $val['service_id']])

                                 ->first();

 

                                 $customer_name = DB::table('customers')

                                 ->where(['id' => $val['customer_id']])

                                 ->first();

 

                                 $services .= $service_name->name.',';

                                 

                                 $data_array[$key] = array(

                                     'customer_names' => $customer_name->first_name.' '.$customer_name->last_name,

                                     'user_name' => $user_name->name,

                                     'total_price' => $val['total_price'],

                                     'due_date' => $val['due_date'],

                                     'due_time' => $val['due_time'],

                                     'service_status' => $val['service_status'],

                                     'payment_instrument' => $val['payment_instrument'],

                                 );

                             }

                             $services = rtrim($services, ',');

                             array_push($data_array[$key],$services,$start_date,$end_date);

                            }

                        }

                    }

                 

                }

            }



            //  echo "<pre>";print_r($data_array);exit;

         



            $headers = array(

                "Content-type"        => "text/csv",

                "Content-Disposition" => "attachment; filename=$fileName",

                "Pragma"              => "no-cache",

                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",

                "Expires"             => "0"

            );



            $columns = array('Booking User','Customer Name', 'Total Price', 'Due Date', 'Due Time', 'Status', 'Payment Method' ,'Services');

            // $column_total = array('Total Amount');

            $callback = function() use($data_array, $columns,$start_date,$end_date) {

                $file = fopen('php://output', 'w');

                

                // fputcsv($file, $column_total);

                $row['Start Date'] = $start_date;

                $row['End Date'] = $end_date;

                

                fputcsv($file, array("Start Date: ".$row['Start Date'].", End Date: ".$row['End Date']));



                $total_amount = 0;

                foreach ($data_array as $task) {

                    $total_amount = $total_amount + $task['total_price'];

                }

                $row['Total Amount'] = $total_amount;

                fputcsv($file, array("Total-Amount: ".$row['Total Amount']));



                fputcsv($file, $columns);

                

                foreach ($data_array as $task) {

                    // echo "<pre>";print_r($task);exit;

                    if($task['service_status'] == 1){

                        $status_name = "Booked";

                    }

                    if($task['service_status'] == 2){

                        $status_name = "In-Progress";

                    }

                    if($task['service_status'] == 3){

                        $status_name = "Completed";

                    }

                    if($task['service_status'] == 4){

                        $status_name = "Cancel";

                    }

                    $row['Booking User']  = $task['user_name'];

                    $row['Customer Name']  = $task['customer_names'];

                    $row['Total Price']    = $task['total_price'];

                    $row['Due Date']    = $task['due_date'];

                    $row['Due Time']  = $task['due_time'];

                    $row['Status']  = $status_name;

                    $row['Payment Method']  = $task['payment_instrument'];

                    $row['Services']  = $task['0'];

                    fputcsv($file, array($row['Booking User'],$row['Customer Name'], $row['Total Price'], $row['Due Date'], $row['Due Time'], $row['Status'], $row['Payment Method'] ,$row['Services']));

                }



                fclose($file);

            };



        return response()->stream($callback, 200, $headers);

    }



    public function reportCustomerCsv(Request $request, $start_date, $end_date, $customer_id)

    {

        $service_bookings = ServiceBooking::with('user','customer')->where('customer_id',$customer_id)->where('company_id', auth()->user()->company_id);

        if($start_date != 'no-date'){

            $service_bookings->whereBetween('created_at', [$start_date." 00:00:00", $end_date." 23:59:59"]);

        }

        $data = $service_bookings->orderBy('created_at', 'desc')->get();

        

        $total_amount = 0;

        foreach($data as $key=>$value){

            $total_amount = $total_amount+$value->total_price+$value->vat;

        }

        $date = date('d-m-Y');

       

        $fileName = $date.'Customer Report.csv';

        $headers = array(

            "Content-type"        => "text/csv",

            "Content-Disposition" => "attachment; filename=$fileName",

            "Pragma"              => "no-cache",

            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",

            "Expires"             => "0"

        );

        

        $columns = array('Customer','Customer Address', 'Booking User', 'Services', 'Total Price', 'Vat', 'Booking Date', 'Status','Payment Days', 'Payment Method');

        

        $callback = function() use($data, $columns,$total_amount,$start_date,$end_date) {

            $file = fopen('php://output', 'w');

            $row['Total Amount'] = $total_amount;

            $row['Start Date'] = $start_date;

            $row['End Date'] = $end_date;

            fputcsv($file, array("Start Date: ".$row['Start Date'].", End Date: ".$row['End Date']));

            fputcsv($file, array("Total-Amount: ".$row['Total Amount']));

            fputcsv($file, $columns);

            foreach($data as $key=>$value){

                $services ='';

                $status = '';

                switch($value->service_status){

                    case '1':

                        $status = 'Booked';

                        break;

                    case '2':

                        $status = 'In Progress';

                        break;

                    case '3':

                        $status = 'Completed';

                        break;

                    case '4':

                        $status = 'Canceled';

                        break;

                }

                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id']);

                $snames = $servicenames->get()->toArray();

                $services = array_column($snames,'name');

                $services = implode (", ", $services);

                $row['Customer']  = $value->customer->first_name.' '.$value->customer->last_name;

                $row['Customer Address']    = $value->customer->address;

                $row['Booking User']    = $value->user->name;

                $row['Services']    = $services;

                $row['Total Price']  = $value->total_price;

                $row['Vat']  = $value->vat;

                $row['Booking Date']    = $value->due_date.' Time: '.$value->due_time;

                $row['Status']  = $status;

                $row['Payment Days']  = $value->payment_days;

                $row['Payment Method']  = $value->payment_instrument;

                fputcsv($file, array($row['Customer'],$row['Customer Address'],$row['Booking User'], $row['Services'], $row['Total Price'],$row['Vat'],$row['Booking Date'],$row['Status'], $row['Payment Days'],$row['Payment Method']));

            }

            fclose($file);

        };

           



        return response()->stream($callback, 200, $headers);

    }



    public function reportExpenseCsv(Request $request, $start_date, $end_date, $type,$method)

    {

        

        // echo $start_date; echo "<br>"; echo $end_date; echo "<br>"; echo $type;exit;

        $expenses = Expense::select('expenses.*','expense_types.type','payment_methods.method')

        ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')

        ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method');

        //->where('expenses.is_deleted', 0);

        if($start_date!="no_start_date" && $end_date!="no_end_date"){

            $expenses->whereBetween('expenses.payable', [$request->start_date, $request->end_date]);

        }

        if($type!='no'){

            $expenses->where('expenses.expense_type', $request->expense_type);

        }

        if($method!='no'){

            $expenses->where('expenses.payment_method', $request->payment_method);

        } 

        $expense_report = $expenses->where('expenses.company_id', auth()->user()->company_id)->orderBy('expenses.created_at', 'desc')->get();



         //dd($expense_report);

        $date = date('d-m-Y');

        $fileName = $date.'Expense Reports.csv';



        $headers = array(

            "Content-type"        => "text/csv",

            "Content-Disposition" => "attachment; filename=$fileName",

            "Pragma"              => "no-cache",

            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",

            "Expires"             => "0"

        );



        $columns = array('Expense Type','Amount','Payment Method', 'Date');



        $callback = function() use($expense_report, $columns,$start_date,$end_date) {

            $file = fopen('php://output', 'w');



            $row['Start Date'] = $start_date;

            $row['End Date'] = $end_date;

            

            fputcsv($file, array("Start Date: ".$row['Start Date'].", End Date: ".$row['End Date']));



            $total_amount = 0;

            foreach ($expense_report as $expense) {

                $total_amount = $total_amount + $expense['amount'];

            }

            $row['Total Amount'] = $total_amount;

            fputcsv($file, array("Total-Amount: ".$row['Total Amount']));



            fputcsv($file, $columns);

            

            foreach ($expense_report as $expense) {

                $row['Expense Type']  = $expense['type'];

                $row['Amount']    = $expense['amount'];

                $row['Payment Method']    = $expense['method'];

                $row['Date']    = $expense['payable'];

                fputcsv($file, array($row['Expense Type'],$row['Amount'],$row['Payment Method'], $row['Date'] ));

            }



            fclose($file);

        };



        return response()->stream($callback, 200, $headers);



    }



}

