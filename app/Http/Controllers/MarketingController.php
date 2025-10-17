<?php

namespace App\Http\Controllers;
use DB;
use App\ServiceBooking;
use Mail;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class MarketingController extends Controller
{
    public function index(){
        
        // if($templates['body']!=""){

        // } 
        //for services
        $services=DB::table('services')->select('id','name')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->groupBy('name')->get();
        // for zip-code
        $zipcode=DB::table('customers')->select('zip')->where('company_id', auth()->user()->company_id)->groupBy('zip')->get();
     
        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();

     

        $templates_data=DB::table('templates')->select('type')->where('status',1)->where('company_id', auth()->user()->company_id)->groupBy('type')->get();
        return view('marketing.index',compact('zipcode','templates_data','services','settings'));
        
    }

    public function postcard(){
        //for services
        $services=DB::table('services')->select('id','name')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->groupBy('name')->get();
        // for zip-code
        $zipcode=DB::table('customers')->select('zip')->where('company_id', auth()->user()->company_id)->groupBy('zip')->get();
     
        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();

     

        $templates_data=DB::table('templates')->select('type')->where('status',1)->where('company_id', auth()->user()->company_id)->groupBy('type')->get();
        return view('marketing.postcard',compact('zipcode','templates_data','services','settings'));
        
    }


    public function find_customers(Request $request)
    {   
        DB::enableQueryLog();
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $service_id=$request->service_id;
        $zip=$request->zip;
        $ref=$request->referred_by; 
        //echo $start_date.'>>>'.$end_date.'>>>>'.$service_id.'>>>>>'.$zip.'>>>>'.$ref; exit;
        if($start_date=="" && $end_date=="" && $ref=="" && $service_id=="" && $zip==""){   $customers="no";
     
            return view('marketing.response',compact('customers'));
        }
        else if($service_id==""){
            //dd($request->all());
        
            // $customers = DB::table('customers')->where('sms_optout','!=', 1)->where('primary_phone','!=', "");
            $customers = DB::table('customers')->select('customers.*')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->where('customers.sms_optout','!=', 1)->where('customers.primary_phone','!=', "");
            if($zip!=""){
               
                $customers = $customers->Where(function ($query) use($zip) {
                
                    for ($i = 0; $i < count($zip); $i++){
                                $query->orwhere('customers.zip', 'like',  '%' . substr($zip[$i], 0, 3) .'%');
                            }      
                        });
                //$customers = $customers->whereIn('zip', $zip);
            }
            if($ref!=""){
               
                $customers = $customers->whereIn('customers.referred_by', $ref);
            }
            if($start_date && $end_date){
              
                $customers = $customers->whereBetween('service_bookings.due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customers = $customers->where('customers.company_id', auth()->user()->company_id)->where('customers.is_active', 1)->groupBy('customers.primary_phone')->orderBy('due_date', 'desc')->get();
            

            // and then you can get query log
            
            // $query = DB::getQueryLog();
            // print_r($query); exit;
            if(count($customers)>0){
                
                  //dd($customers); exit;
                $view = view('marketing.response',compact('customers'))->render();
                return response()->json($view);
            }
            else{
              
                $customers="no";
                return view('marketing.response',compact('customers'));
            } 
        }
       
       else if($service_id!=""){
          
            $booking_ids = DB::table('booking_details')->whereIn('service_id',$service_id)->groupBy('booking_id')->pluck('booking_id')->toArray();

            $customers = DB::table('customers')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->where('customers.sms_optout','!=', 1)->where('customers.primary_phone','!=', "")->whereIn('service_bookings.id',$booking_ids);
            if($zip!=""){
               
                $customers = $customers->Where(function ($query) use($zip) {
                
                    for ($i = 0; $i < count($zip); $i++){
                                $query->orwhere('customers.zip', 'like',  '%' . substr($zip[$i], 0, 3) .'%');
                            }      
                        });
                //$customers = $customers->whereIn('zip', $zip);
            }
            if($ref!=null){
                // echo "here"; exit;
                $customers = $customers->whereIn('customers.referred_by', $ref);
            }
            if($start_date!=null && $end_date!=null){
                $customers = $customers->whereBetween('service_bookings.due_date', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customers = $customers->where('customers.is_active', 1)->where('customers.company_id', auth()->user()->company_id)->groupBy('customers.primary_phone')->get();
            //echo "<pre>"; print_r($customers); exit;
            if(count($customers)>0){
                $all_services_by_customer = array();
                foreach($customers as $data){
                    $services ='';
                    $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $data->id)->whereIn('booking_details.service_id',$service_id);
                    $snames = $servicenames->get();
                    // echo "<pre>"; print_r($snames); exit;
                    foreach($snames as $service){
                    $services .= $service->name.','; 
                    }
                    $services = rtrim($services, ',');
                    $all_services_by_customer[$data->id] = $services;
                }
                // echo "<pre>"; print_r($all_services_by_customer); exit;
                return view('marketing.response',compact('customers','all_services_by_customer'));
            }
            else{
                $customers="no";
                return view('marketing.response',compact('customers'));
            }
                // $customers = DB::table('customers')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->whereIn('service_bookings.id',$booking_ids);
           
        }
           
        else{ 
     
            $customers="no";
            return view('marketing.response',compact('customers'));
        }
        
    }

    public function postcard_customers(Request $request)
    {   
      
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $service_id=$request->service_id;
        $zip=$request->zip;
        $ref=$request->referred_by; 
        if($start_date==null && $end_date==null && $ref==null && $service_id==null && $zip==null){        $customers="no";
            return view('marketing.postcardresponse',compact('customers'));
        }
        else if($service_id==null && ($zip!=null || $ref!=null)){
            $customers = DB::table('customers');
            if($zip!=null){
                $customers = $customers->Where(function ($query) use($zip) {
                
                    for ($i = 0; $i < count($zip); $i++){
                                $query->orwhere('zip', 'like',  '%' . substr($zip[$i], 0, 3) .'%');
                            }      
                        });
                //$customers = $customers->whereIn('zip', $zip);
            }
            if($ref!=null){
                $customers = $customers->whereIn('referred_by', $ref);
            }
            if($start_date!=null && $end_date!==null){
                $customers = DB::table('customers')->whereBetween('created_at', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customers = $customers->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();
           
            if(count($customers)>0){
                  //dd($customers); exit;
                $view = view('marketing.postcardresponse',compact('customers'))->render();
                return response()->json($view);
            }
            else{
                $customers="no";
                return view('marketing.postcardresponse',compact('customers'));
            } 
        }
       
       else if($service_id!=null){
        
            $booking_ids = DB::table('booking_details')->whereIn('service_id',$service_id)->groupBy('booking_id')->pluck('booking_id')->toArray();

            $customers = DB::table('customers')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->whereIn('service_bookings.id',$booking_ids);
            if($zip!=null){
                $customers = $customers->whereIn('customers.zip', $zip);
            }
            if($ref!=null){
                // echo "here"; exit;
                $customers = $customers->whereIn('customers.referred_by', $ref);
            }
            if($start_date!=null && $end_date!=null){
                $customers = whereBetween('service_bookings.created_at', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customers = $customers->get();
            //echo "<pre>"; print_r($customers); exit;
            if(count($customers)>0){
                $all_services_by_customer = array();
                foreach($customers as $data){
                    $services ='';
                    $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $data->id)->whereIn('booking_details.service_id',$service_id);
                    $snames = $servicenames->get();
                    // echo "<pre>"; print_r($snames); exit;
                    foreach($snames as $service){
                    $services .= $service->name.','; 
                    }
                    $services = rtrim($services, ',');
                    $all_services_by_customer[$data->id] = $services;
                }
                // echo "<pre>"; print_r($all_services_by_customer); exit;
                return view('marketing.postcardresponse',compact('customers','all_services_by_customer'));
            }
            else{
                $customers="no";
                return view('marketing.postcardresponse',compact('customers'));
            }
                // $customers = DB::table('customers')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->whereIn('service_bookings.id',$booking_ids);
           
        }
           
        else{ 
            $customers="no";
            return view('marketing.postcardresponse',compact('customers'));
        }
        
    }
    function find_templates(Request $request){
        $type=$request->template_type;
        $template_title=DB::table('templates')->select('id','title')->where('type',$type)->where('company_id', auth()->user()->company_id)->get();
        return response()->json($template_title);
    }

    function send_message(Request $request){
        //echo "<pre>"; print_r($request->all()); exit;
        if($request->type==1){
            //print_r($request->phoneumber); exit;
           $template_text =  DB::table('templates')->where('id', $request->title)->pluck('description')->first();
           $customers =  DB::table('customers')->select('id','primary_phone','first_name','last_name','sms_optout_msg')->whereIn('id', $request->phoneumber)->get()->toArray();
            //setting optout link for customer only for one time
            
            $result = "";

           $phonenumbers = array_column($customers, 'primary_phone');

           $numbers = implode (", ", $phonenumbers);
           $company_settings =  DB::table('company_settings')->select('*')->where('company_id',  auth()->user()->company_id)->get();
           $total_numbers = count($phonenumbers);
           if($company_settings[0]->sms_count<=0){
                return redirect('marketing')->with('destroy', 'You have insufficient SMS balance. Please contact bSaloon.');
           }
           elseif($company_settings[0]->sms_count<$total_numbers){
                return redirect('marketing')->with('destroy', 'You Can not Send SMS. Selected customers are '.$total_numbers.' & Remaining Balance is '.$company_settings[0]->sms_count.' Please contact bSaloon.');
           }
           $from = ($company_settings[0]->smart_name=="")? $company_settings[0]->site_name : $company_settings[0]->smart_name ;
           if($from==""){
               $from = "B Saloon";
           }
        //    $smstype = ($total_numbers==1)? 'sendsms': 'bulksms' ;
           foreach($customers as $cusotmer){
            $optoutlink = "";
            if($cusotmer->sms_optout_msg==0){
                $optoutlink = ' For Opt Out Click Following Link https://app.bSaloon.co.uk/smsoptout/'.auth()->user()->company_id.'/'.$cusotmer->id.'/1';
                DB::table('customers')->where('id', $cusotmer->id)->update([
                    'sms_optout_msg' => 1,
                ]);
            } 
           
            if($request->schedule!=0){
                $schedule =  $request->schedule_date.' '.$request->schedule_time;
                $msg = '{



                    "to": "'.$cusotmer->primary_phone.'",
    
                    "from": "'.$from.'",
    
                    "country_code": "44",
                    "schedule": "'.$schedule.'" ,
                    "dynamic_data": [
    
                      {
    
                        "customer name": "'.$cusotmer->first_name.' '.$cusotmer->last_name. '"
    
                      }
    
                    ],
    
                    "msg": "'.$template_text.' '.$optoutlink.'",
    
                    "sandbox": false
    
                  }';
                    // $msg = json_encode(
                    //     [
                    //         'to' => $numbers,
                    //         'from' => $from,
                    //         'msg' => $template_text.' '.$optoutlink,
                    //         "country_code"=> "44",
                    //         "schedule" => "$schedule" ,
                    //         "sandbox"=> false,
                    //     ]
                        
                    // );
               }
               else{
                   
                $msg = '{



                    "to": "'.$cusotmer->primary_phone.'",
    
                    "from": "'.$from.'",
    
                    "country_code": "44",
    
                    "dynamic_data": [
    
                      {
    
                        "customer name": "'.$cusotmer->first_name.' '.$cusotmer->last_name. '"
    
                      }
    
                    ],
    
                    "msg": "'.$template_text.' '.$optoutlink.'",
    
                    "sandbox": false
    
                  }';
               
               }
            $ch = curl_init('https://api.voodoosms.com/sendsms');

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response);
          
        }
           
           
            
            
            if(isset($result->error)){
                return redirect('marketing')->with('destroy', 'Message not sent to customers. Ask bSaloon to check this error: '.$result->error->msg.'.');
            }else{
                $remaining_sms = $company_settings[0]->sms_count-$total_numbers;
                DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update([
                    'sms_count' => $remaining_sms,
                ]);
                $message = 'Message sent to customers!';
                
                if($request->schedule!=0){
                    $message = 'Messages are scheduled!';
                    $index = 0;
                    foreach($customers as $customer){
                        $sms_history = array(
                            'customer_name' => $customer->first_name.' '.$customer->last_name,
                            'date' => $request->schedule_date,
                            'time' => $request->schedule_time,
                            'type' => "schedule",
                            'template' =>  $request->title,
                            // 'schedule_id' => $result->messages[$index]->recipient,
                            'company_id' => auth()->user()->company_id
                        );
                        DB::table('sms_history')->insert($sms_history);
                        $index++;
                        $schedule_list = $this->get_scheduled_list();
                    }
                }
                else{
                    foreach($customers as $customer){
                        $sms_history = array(
                            'customer_name' => $customer->first_name.' '.$customer->last_name,
                            'date' => date("Y-m-d"),
                            'time' => date("h:i a"),
                            'type' => "sent",
                            'template' =>  $request->title,
                            'company_id' => auth()->user()->company_id
                        );
                        DB::table('sms_history')->insert($sms_history);
                    }
                }
                return redirect('marketing')->with('success', $message);
            }

        }
        else if($request->type==3){
          
            $customer_emails =  DB::table('customers')->select('email')->whereIn('id', $request->phoneumber)->where('email', '<>', '')->get()->toArray();
           
            $all_email = array_column($customer_emails, 'email');
            //print_r($all_email); exit;
        //    foreach($customer_emails as $cemails){
        //        $all_email .= "'$cemails->email'".',';
        //    }
        //    $all_email = rtrim($all_email, ",");
           
            $template =  DB::table('templates')->select('description','title')->where('id', $request->title)->get();
            $title = 'B Saloon';
            $subject = $template[0]->title;
           $descripton = $template[0]->description;
            $detail_array["header"] = '';
            $detail_array["footer"] = '';
            $detail_array["company_logo"] = '';
            $detail_array["descripton"] = $descripton ;
            $company_email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->get();
            if(!$company_email->isEmpty()){
                $title = $company_email[0]->title;
                $subject = $template[0]->title;
                $detail_array["header"] = $company_email[0]->marketing_header;
                $detail_array["footer"] = $company_email[0]->marketing_footer;
                $detail_array["company_logo"] = $company_email[0]->image;
            }
            //echo $title; exit;
            $emails = $all_email;
            //sending mail function
            Mail::send('emails.marketing', $detail_array, function($message) use ($emails, $title,$subject)
            {    
                $message->to($emails)->subject($subject);
                $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);  
            });
            $failures =  Mail:: failures();
            if(count($failures)>0){
                return redirect('marketing')->with('destroy', 'There is error in sending mail');
            }
            else{
                return redirect('marketing')->with('success', 'Email has been Sent Successfully.');
            }
            

        }
        

    }


    

    public function get_scheduled_list(){
        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';
        $ch = curl_init('https://api.voodoosms.com/schedule');

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $api_key
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response);
            return $result;
    }
    public function sms_history()
    {
        $type = 'all';
        $start_date = '';
        $end_date = '';
        $history=DB::table('sms_history')->join('templates','templates.id','=','sms_history.template')->select('sms_history.*','templates.title')->where('sms_history.company_id', auth()->user()->company_id)->get();
    
        return view('marketing.sms_history',compact('history','type','start_date','end_date'));
    }

    public function sms_history_filter(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $type=$request->type;
        //dd($request->all());
        $history_detail =DB::table('sms_history')->join('templates','templates.id','=','sms_history.template')->select('sms_history.*','templates.title');
        if($start_date!=null && $end_date!=null){
            $history = $history_detail->whereBetween('sms_history.date', [$request->start_date, $request->end_date]);
        }
        if($type!='all'){
            $history = $history_detail->where('sms_history.type', '=',$type);
        }
        $history = $history_detail->where('sms_history.company_id', auth()->user()->company_id)->get();
        return view('marketing.sms_history',compact('history','type','start_date','end_date'));
    }

    public function cancel_scheduled_sms(){
        //echo "hkasjdfksadf"; exit;
        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';           
            $ch = curl_init('https://api.voodoosms.com/schedule/5813799');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: ' . $api_key
            ]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }
            curl_close($ch);
            
            if (isset($error_msg)) {
                //echo "<pre>"; print_r($error_msg); exit;
                return back()->with('destroy','Oops! There is an error. Please try again.');
            }
            //curl_close($ch);
            $result = json_decode($response);
            //echo "<pre>"; print_r($result); exit;
            return back()->with('destroy','SMS Cancelled Successfully');
    }
    public function emailoptout($companyid,$customerid, $optout){
        $customer = DB::table('customers')->where('company_id', $companyid)->where('id', $customerid)->first();
        $company_settings =  DB::table('company_settings')->select('*')->where('company_id',  $companyid)->get();
        if($customer){
            DB::table('customers')->where('id', $customerid)->where('company_id', $companyid)->update([
                'optout' => 1,
            ]);
            return view('customers.optout',compact('company_settings','customer'));
        }
        else{
            return view('customers.optout',compact('company_settings','customer'));
        }
    }

    public function smsoptout($companyid,$customerid, $optout){
        $customer = DB::table('customers')->where('company_id', $companyid)->where('id', $customerid)->first();
        $company_settings =  DB::table('company_settings')->select('*')->where('company_id',  $companyid)->get();
        if($customer){
            DB::table('customers')->where('id', $customerid)->where('company_id', $companyid)->update([
                'sms_optout' => 1,
            ]);
          
            return view('customers.optout',compact('company_settings','customer'));
        }
        else{
            return view('customers.optout',compact('company_settings','customer'));
        }
    }
}
