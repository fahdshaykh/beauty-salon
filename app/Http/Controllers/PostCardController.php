<?php



namespace App\Http\Controllers;

use App\Category;

use App\Services;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use DB;

use Str;

use File;

class PostCardController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        

        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();

        $customers = DB::table('customers')->where('company_id', auth()->user()->company_id)->get();

        return view('postcard.index',compact('settings','customers'));

            

        

    }



    public function postcard_info(){

        return view('postcard.info');

    }



    public function postcard_samples(){

        return view('postcard.sample');

    }



    public function postcard_history(){

        $settings = DB::table('company_settings')->select('clicksend_api_key', 'clicksend_username', 'clicksend_password','clicksend_returnaddress')->where('company_id', auth()->user()->company_id)->first();

        if($settings->clicksend_api_key=="" || $settings->clicksend_username=="" || $settings->clicksend_password==""){

            $url = '<a target="_blank" href="https://clicksend.com/?u=267536">Click Send</a>';

            return redirect('marketing')->with('error', 'You are not able to use this service. Please create account on '.$url.'  to use this service and update settings.');

        }

        else{

            $url = 'https://rest.clicksend.com/v3/post/postcards/history';

            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Authorization: Basic ' . base64_encode("$settings->clicksend_username:$settings->clicksend_password")) );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            $history=json_decode($response);

            if($history->http_code!=200){

                return redirect('marketing')->with('error', $history->response_msg);

            }

            $history_detail = $history->data;



        }

        



        return view('postcard.history',compact('history_detail'));

    }



    public function sendpost_card(Request $request){

        //dd($request->all());

        $settings = DB::table('company_settings')->select('clicksend_api_key', 'clicksend_username', 'clicksend_password','clicksend_returnaddress')->where('company_id', auth()->user()->company_id)->first();

        //print_r($settings); exit;

        if($settings->clicksend_api_key=="" || $settings->clicksend_username=="" || $settings->clicksend_password==""){

            $url = '<a target="_blank" href="https://clicksend.com/?u=267536">Click Send</a>';

            return redirect('marketing')->with('error', 'You are not able to use this service. Please create account on '.$url.'  to use this service and update settings.');

        }

        else{

            $schedule = 0;

            if($request->customer_ids==""){

                return response()->json(['error'=>'Please select customers.']);

            }

            if($request->scheduledate){

                //echo $request->scheduledate; exit;

                if(strtotime($request->scheduledate)>strtotime(date("m/d/Y"))){

                    $schedule = strtotime($request->scheduledate);

                }

               

            }

           

            $customerids = explode(",",$request->customer_ids);

                $customers_details =  DB::table('customers')->select('first_name','last_name', 'address')->whereIn('id',  $customerids)->get()->toArray();

                foreach($customers_details as $detail){

                    

                    $address_fields =explode(",",$detail->address);

                    

                        $recipients[] =  array( 

                        "schedule"=> $schedule,  

                        "address_name" =>$detail->first_name.' '.$detail->last_name, 

                        "address_city" =>  $address_fields[1], 

                        "address_state" => $address_fields[1], 

                        "address_postal_code" => $address_fields[2], 

                        "address_country" => "GB", 

                        "address_line_1" => $address_fields[0], 

                        "return_address_id"=> $settings->clicksend_returnaddress,

                        "custom_string" => $request->custom_message

                        ); 

                

                }

                $recipients =  json_encode($recipients); 

                if($request->file()){

                    $validate = Validator::make($request->all(), [

                        'post_card' => 'mimes:pdf',

                ]);

               

                if ($validate->fails()) {    

                    return response()->json(['error'=>'Only Acceptable file format is PDF']);

                }

                

                //echo $recipients; exit;

            

                $image = $request->file('post_card');

                $image_name = '';

                $destinationPath = public_path().'/postcards/';

                $image->move($destinationPath, $image_name = 'img_'.Str::random(15).'.'. $image->getClientOriginalExtension());

                $file_url = 'https://app.diarymaster.co.uk/postcards/'.$image_name;

                

                $body = '{"recipients": '.$recipients.', "file_urls":["'. $file_url.'"] }';

               

                $url = 'https://rest.clicksend.com/v3/post/postcards/send';

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Authorization: Basic ' . base64_encode("$settings->clicksend_username:$settings->clicksend_password")) );

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                

                curl_setopt($curl, CURLOPT_POSTFIELDS,$body);

                $response = curl_exec($curl);

                $data=json_decode($response);

                //print_r($data); exit;

                if($data->http_code==200){

                    return response()->json(['success'=>$data->response_msg]);

                    //return redirect('/marketing')->with('success', 'Postcard queued for delivery.');

                }

                else{

                   return response()->json(['error'=>$data->response_msg]);

                    //return redirect('/marketing')->with('error', 'Inavlid File Upload. Please try again.');

                }

                

               

            }

            else{

               

                $body = '{"recipients": '.$recipients.', "file_urls":["'. $request->file_url1.'","'. $request->file_url2.'"] }';

               

                $url = 'https://rest.clicksend.com/v3/post/postcards/send';

                $curl = curl_init($url);

                curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Authorization: Basic ' . base64_encode("$settings->clicksend_username:$settings->clicksend_password")) );

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                

                curl_setopt($curl, CURLOPT_POSTFIELDS,$body);

                $response = curl_exec($curl);

                $data=json_decode($response);

                //print_r($data); exit;

                if($data->http_code==200){

                    return response()->json(['success'=>$data->response_msg]);

                    //return redirect('/marketing')->with('success', 'Postcard queued for delivery.');

                }

                else{

                   return response()->json(['error'=>$data->response_msg]);

                    //return redirect('/marketing')->with('error', 'Inavlid File Upload. Please try again.');

                }



            }



        }

        



    }







}