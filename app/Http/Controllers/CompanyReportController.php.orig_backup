<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ServiceBooking;
use App\Services;
use App\Customer;
use App\Expense;
use App\InventoryRemain;
use DB;

class CompanyReportController extends Controller
{



    public function company_graph_report()

    {

        return view('company_reports.graph_report');

    }



    public function company_booking_chart(Request $request)

    {

        $start_date = date("Y").'-'.date("m").'-'.'01';

        $end_date = date("Y").'-'.date("m").'-'.'31';



        if($request->start_date && $request->end_date){

            $start_date = date('Y-m-d', strtotime($request->start_date));

            $end_date = date('Y-m-d', strtotime($request->end_date));

        }

        

        $company_summary = DB::table('service_bookings')
        ->join('companies', 'service_bookings.company_id', '=', 'companies.id')
        ->whereDate('service_bookings.due_date', '>=', $start_date)
        ->whereDate('service_bookings.due_date', '<=', $end_date)
        ->whereNotIn('service_bookings.service_status', [4, 5])
        ->select(
            DB::raw('COUNT(service_bookings.id) as total_bookings'),
            DB::raw('SUM(service_bookings.total_price) as total_amount'),
            'companies.company_name as company_name'
        )
        ->groupBy('service_bookings.company_id', 'companies.company_name')
        ->get();
    
        // dd($company_summary);

        return response()->json($company_summary);

        

    }

    



    public function expense_index()

    {

        // $expense_types = DB::table('expense_types')->where('company_id', auth()->user()->company_id)->get();

        $payment_methods = DB::table('payment_methods')->get();

        $companies = DB::table('companies')->get();

        return view('company_reports.expense_index',compact('payment_methods', 'companies'));

    }

    public function inventory_index()

    {

        $companies = DB::table('companies')->get();

        return view('company_reports.inventory_index',compact('companies'));

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





    public function expense_record(Request $request)

    {

        // dd($request->all());


        // dd($request->filled('expense_type'));

        $this->validate($request, 

            [

                'company_id' => 'required',

                // 'start_date' => 'required',

                // 'end_date' => 'required',

            ],

            [

                'company_id.required' => 'Please select Expense type',

            ],

        );



        $start_date = $request->start_date;

        $end_date = $request->end_date;

        $type = $request->company_id;

        //getting data for report

        $expenses = Expense::select('expenses.*','expense_types.type','payment_methods.method', 'companies.company_name')

        ->join('expense_types', 'expense_types.id', '=', 'expenses.expense_type')

        ->join('companies', 'companies.id', '=', 'expenses.company_id')

        ->join('payment_methods', 'payment_methods.id', '=', 'expenses.payment_method');

        //->where('expenses.is_deleted', 0);

        if($start_date!="" && $end_date!=""){

            $expenses->whereBetween('expenses.payable', [$request->start_date, $request->end_date]);

        }

        if($request->company_id!='no'){

            $expenses->where('expenses.company_id', $request->company_id);

        }

        if($request->payment_method!='no'){

            $expenses->where('expenses.payment_method', $request->payment_method);

        } 

        $expense_report = $expenses->orderBy('expenses.created_at', 'desc')->get();

        if($start_date==''){

            $start_date = 'no_start_date';

        }

        if($end_date==''){

            $end_date = 'no_end_date';

        }

        // dd($expense_report);
        

        $view = view('company_reports.expense_reports', [

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


    public function inventory_record(Request $request)
    {

        $this->validate($request, 

            [

                'company_id' => 'required',

                // 'start_date' => 'required',

                // 'end_date' => 'required',

            ],

            [

                'company_id.required' => 'Please select Expense type',

            ],

        );



        $start_date = $request->start_date;

        $end_date = $request->end_date;

        $company_id = $request->company_id;

        //getting data for report

        $query = InventoryRemain::with('inventory')
                        ->where('company_id', $company_id);

        //->where('expenses.is_deleted', 0);

        if($start_date!="" && $end_date!=""){

            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);

        }

        if($request->company_id!='no'){

            $query->where('company_id', $request->company_id);

        }

        $inventory_report = $query->orderBy('created_at', 'desc')->get();

        if($start_date==''){

            $start_date = 'no_start_date';

        }

        if($end_date==''){

            $end_date = 'no_end_date';

        }

        // dd($expense_report);

        $view = view('company_reports.inventory_reports', [

            'inventory_report' => $inventory_report,

            'start_date' => $start_date,

            'end_date' => $end_date,

            'type' => $company_id,

            'method'=>$request->payment_method

                 ])->render();

                 return response()->json(['view'=> $view]);



    }




}
