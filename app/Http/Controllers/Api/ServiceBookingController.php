<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;



use App\ServiceBooking;

use App\Services;

use App\Customer;

use App\User;

use DB;

use Validator;

use Mail;

use Carbon\Carbon;

use App\BookingDetail;



class ServiceBookingController extends BaseController

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function __construct()

    {

        ini_set('precision', 7);

        ini_set('serialize_precision', -1);

        Carbon::setWeekStartsAt(Carbon::MONDAY);

        Carbon::setWeekEndsAt(Carbon::SATURDAY);

    }


    public function everydaytotal(Request $request){
    $data = [];
    $todaystotal =  ServiceBooking::where('company_id', $request->company_id)->where('service_status', '!=', 4)->groupBy('due_date')
   ->selectRaw('due_date,sum(total_price) as todaytotal')
   ->get()->toArray();
   if(count($todaystotal)>0){
        foreach($todaystotal as $total){
            $data[] = array(
                'date' => $total['due_date'],
               'totalamount' => $total['todaytotal']);
        }
        return $this->sendResponse($data, 'Data Retrieved Successfully');
    }
    else{
        return $this->sendError($data, 'There is no data.');
    }

      
    }
    public function index(Request $request)

    {

        $data = ServiceBooking::with('customer', 'user')

            ->where('company_id', $request->company_id)

            ->where('service_status', '!=', 4)

            //->where('created_at', '>', Carbon::now()->startOfWeek())

            //->where('created_at', '<', Carbon::now()->endOfWeek())

            ->orderBy('created_at', 'desc')

            ->get();



        if (count($data) > 0) {

            $calendar_bookings = [];

            $calendar_bookings['success'] = true;

            $user_bookings = [];

            foreach ($data as $key => $value) {

                $customer_name = $value->customer;

                $user_name = $value->user;

                $servicenames = DB::table('booking_details')

                    ->join('services', 'services.id', '=', 'booking_details.service_id')

                    ->where('booking_details.booking_id', $value['id']);



                $snames = $servicenames->get();



                $services = '';



                $colors = '';



                $services_id = '';



                if (!empty($value)) {

                    $service_row = [];



                    $services_color = [];



                    $services_id_row = [];



                    foreach ($snames as $service) {

                        $services .= $service->name . ',';

                    }

                    foreach ($snames as $service) {

                        $colors .= $service->service_color . ',';

                    }

                    foreach ($snames as $service) {

                        $services_id .= $service->id . ',';

                    }



                    $data_array = [

                        'booking_id' => $value['id'],

                        'customer_name' => $customer_name->first_name . ' ' . $customer_name->last_name,

                        'customer_id' => $customer_name->id,

                        'user_id' => $user_name->id,

                        'role_id' => $user_name->role_id,

                        'user_name' => $user_name->name,

                        'total_price' => $value['total_price'],

                        'payment_days' => $value['payment_days'],

                        'due_date' => $value['due_date'],

                        'start_time' => $value['due_time'],

                        'end_time' => $value['service_time'],

                        'comment' => $value['comment'],

                        'service_status' => $value['service_status'],

                        'payment_instrument' => $value['payment_instrument'],

                        'payment_instrument_number' => $value['payment_instrument_number'],

                        'company_id' => $value['company_id'],

                        'invoice' => $value['invoice'],
                        

                    ];



                    $customer_array = [

                        'customer_id' => $customer_name->id,

                        'first_name' => $customer_name->first_name,

                        'last_name' => $customer_name->last_name,

                        'primary_phone' => $customer_name->primary_phone,

                        'secondary_phone' => $customer_name->secondary_phone,

                        'zip' => $customer_name->zip,

                        'address' => $customer_name->address,

                        'longitude' => $customer_name->longitude,

                        'latitude' => $customer_name->latitude,

                        'referred_by' => $customer_name->referred_by,

                        'company_id' => $customer_name->company_id,

                        'is_active' => $customer_name->is_active,

                    ];
                    
                   
                    
                    
                    // arranging the service name in array

                    $services = rtrim($services, ',');

                    array_push($service_row, $services);



                    $data_array['services'] = $services;

                    //services names end here



                    // arranging the services color in array

                    $colors = rtrim($colors, ',');

                    array_push($services_color, $colors);



                    $data_array['services_color'] = $colors;

                    //services color end here



                    // arranging the service ids in array

                    $services_id = rtrim($services_id, ',');

                    array_push($services_id_row, $services_id);



                    $data_array['services_id'] = $services_id;

                    //services id end here



                    $data_array['customer_detail'] = $customer_array;



                    array_push($user_bookings, $data_array);

                }

            }


          
            $calendar_bookings['message'] = 'Bookings retrieved successfully';



            $calendar_bookings['data'] = array_values(array_filter($user_bookings));



            echo json_encode($calendar_bookings);

            exit();

        } else {

            return $this->sendResponse($data->toArray(), 'There is no data');

        }

    }



    public function booking_listings(Request $request)

    {

        $data = ServiceBooking::with('customer', 'user')

            ->where('company_id', $request->company_id)

            //->where('created_at', '>', Carbon::now()->startOfWeek())

            //->where('created_at', '<', Carbon::now()->endOfWeek())

            ->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])

            ->orderBy('due_date', 'desc')

            ->get();



        if (count($data) > 0) {

            $calendar_bookings = [];

            $calendar_bookings['success'] = true;

            $user_bookings = [];



            foreach ($data as $key => $value) {

                $customer_name = $value->customer;

                $user_name = $value->user;

                $servicenames = DB::table('booking_details')

                    ->join('services', 'services.id', '=', 'booking_details.service_id')

                    ->where('booking_details.booking_id', $value['id']);



                $snames = $servicenames->get();



                $services = '';



                $colors = '';



                $services_id = '';



                if (!empty($value)) {

                    $service_row = [];



                    $services_color = [];



                    $services_id_row = [];



                    foreach ($snames as $service) {

                        $services .= $service->name . ',';

                    }

                    foreach ($snames as $service) {

                        $colors .= $service->service_color . ',';

                    }

                    foreach ($snames as $service) {

                        $services_id .= $service->id . ',';

                    }



                    $data_array = [

                        'booking_id' => $value['id'],

                        'customer_name' => $customer_name->first_name . ' ' . $customer_name->last_name,

                        'customer_id' => $customer_name->id,

                        'user_id' => $user_name->id,

                        'role_id' => $user_name->role_id,

                        'user_name' => $user_name->name,

                        'total_price' => $value['total_price'],

                        'payment_days' => $value['payment_days'],

                        'due_date' => $value['due_date'],

                        'start_time' => $value['due_time'],

                        'end_time' => $value['service_time'],

                        'comment' => $value['comment'],

                        'service_status' => $value['service_status'],

                        'payment_instrument' => $value['payment_instrument'],

                        'payment_instrument_number' => $value['payment_instrument_number'],

                        'company_id' => $value['company_id'],

                        'invoice' => $value['invoice'],

                    ];



                    $customer_array = [

                        'customer_id' => $customer_name->id,

                        'first_name' => $customer_name->first_name,

                        'last_name' => $customer_name->last_name,

                        'primary_phone' => $customer_name->primary_phone,

                        'secondary_phone' => $customer_name->secondary_phone,

                        'zip' => $customer_name->zip,

                        'address' => $customer_name->address,

                        'longitude' => $customer_name->longitude,

                        'latitude' => $customer_name->latitude,

                        'referred_by' => $customer_name->referred_by,

                        'company_id' => $customer_name->company_id,

                        'is_active' => $customer_name->is_active,

                    ];



                    // arranging the service name in array

                    $services = rtrim($services, ',');

                    array_push($service_row, $services);



                    $data_array['services'] = $services;

                    //services names end here



                    // arranging the services color in array

                    $colors = rtrim($colors, ',');

                    array_push($services_color, $colors);



                    $data_array['services_color'] = $colors;

                    //services color end here



                    // arranging the service ids in array

                    $services_id = rtrim($services_id, ',');

                    array_push($services_id_row, $services_id);



                    $data_array['services_id'] = $services_id;

                    //services id end here



                    $data_array['customer_detail'] = $customer_array;



                    array_push($user_bookings, $data_array);

                }

            }



            $calendar_bookings['message'] = 'Bookings retrieved successfully';



            $calendar_bookings['data'] = array_values(array_filter($user_bookings));



            echo json_encode($calendar_bookings);

            exit();

        } else {

            return $this->sendResponse($data->toArray(), 'Something went wrong');

        }

    }



    // public function send_notification(Request $request)

    // {

    //     $firebaseToken = User::select('device_token')->where('id', $request->userid)->get();



    //     $SERVER_API_KEY = 'AAAAgxBV8ro:APA91bEHYf564XZw4oiY3eeZl9T798KSnSagwXlfYSUtZ_MVDXmIMK1uSE0IKCoVdpRZX75N6Z3ygRWbbit5l4OOZkTj7llpGiZbRxFVsS8by0hEOlPfSc_EPgwn7NlgVkRx0KbSBS_H';



    //     $data = [

    //         "to" => $firebaseToken[0]->device_token,

    //         "priority" => "high",

    //         "notification" => [

    //             "title" => "Title of Your Notification",

    //             "body" => "Body of Your Notification"

    //         ]

    //     ];



    //     $dataString = json_encode($data);



    //     $headers = [

    //         'Authorization: key=' . $SERVER_API_KEY,

    //         'Content-Type: application/json',

    //     ];



    //     $ch = curl_init();



    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

    //     curl_setopt($ch, CURLOPT_POST, true);

    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);



    //     $response = curl_exec($ch);



    //     return response()->json($response, 200);

    // }



    public function get_booking_orders(Request $request)

    {

        if (count($request->all()) == 4) {

            return $this->sendError('Please submit 3 input request');

        }



        $input = $request->all();



        $validator = Validator::make($input, [

            'company_id' => 'required',

            'service_status' => 'required|integer|between:1,4',

        ]);



        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $start_date = date("Y") . '-' . date("m") . '-' . '01';

        $end_date = date("Y") . '-' . date("m") . '-' . '31';



        // if($request->role_id && $request->company_id && $request->service_status){



        //     if($request->role_id == 1){

        //         $service_bookings = ServiceBooking::whereDate('service_bookings.created_at', '>=' , $start_date)

        //         ->whereDate('service_bookings.created_at', '<=' , $end_date)

        //         ->where('is_deleted', 0)

        //         ->where('company_id', $request->company_id)

        //         ->where('service_status', $request->service_status)

        //         ->orderBy('created_at', 'desc')->get();

        //     } else {

        //         return $this->sendError('Something went wrong.');

        //     }

        // }else

        if ($request->user_id && $request->company_id && $request->service_status) {

            $service_bookings = ServiceBooking::whereDate('service_bookings.created_at', '>=', $start_date)

                ->whereDate('service_bookings.created_at', '<=', $end_date)

                ->where('user_id', $request->user_id)

                ->where('company_id', $request->company_id)

                ->where('service_status', $request->service_status)

                ->orderBy('created_at', 'desc')

                ->get();

        } else {

            $input = $request->all();



            $validator = Validator::make($input, [

                'role_id' => 'required',

                'user_id' => 'required',

            ]);



            if ($validator->fails()) {

                return $this->sendError('Validation Error.', $validator->errors());

            }

        }



        $data = $service_bookings->groupBy('invoice');



        if ($data->isEmpty()) {

            return $this->sendError('Something went wrong');

        }



        if (count($data) > 0) {

            foreach ($data as $key => $value) {

                $services = '';

                $services_color = '';

                if (count($value) > 0) {

                    foreach ($value as $val) {

                        $service_name = DB::table('services')

                            ->where('company_id', $request->company_id)

                            ->where(['id' => $val['service_id']])

                            ->first();



                        if ($service_name == null) {

                            return $this->sendError('Service does not exist.');

                        }



                        $customer_name = DB::table('customers')

                            ->where('company_id', $request->company_id)

                            ->where(['id' => $val['customer_id']])

                            ->first();



                        if ($customer_name == null) {

                            return $this->sendError('Customer does not exist.');

                        }



                        $services .= $service_name->name . ',';



                        $services_color .= $service_name->service_color . ',';



                        $data_array[$key] = [

                            'customer_name' => $customer_name->last_name,

                            'customer_id' => $customer_name->id,

                            'total_price' => $val['total_price'],

                            'due_date' => $val['due_date'],

                            'due_time' => $val['due_time'],

                            'service_status' => $val['service_status'],

                            'service_id' => $val['service_id'],

                            'comments' => $val['comment'],

                            'service_time' => $val['service_time'],

                            'booking_id' => $val['id'],

                            'invoice' => $key,

                        ];

                    }

                    $services = rtrim($services, ',');

                    $serv_list = [];

                    $serv_list['services_names'] = $services;



                    $services_color = rtrim($services_color, ',');

                    $serv_colors = [];

                    $serv_colors['services_colors'] = $services_color;



                    $calender_array[$key] = $this->combineWithKeys($data_array[$key], $serv_list);



                    array_push($calender_array[$key], $serv_colors);

                }

            }



            $monthly_bookings = array_values($calender_array);



            $response = [

                'success' => true,

                'data' => $monthly_bookings,

                'message' => "service booking retrieve",

            ];



            return response()->json($response, 200);



            // return $this->sendResponse($data_array, 'Service Bookings retrieved successfully.');

        } else {

            return $this->sendError('Bookings not found.');

        }

    }



    public function get_user_bookings(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'user_id' => 'required',

            'service_status' => 'required',

            'company_id' => 'required',

        ]);



        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $service_bookings = ServiceBooking::where('user_id', $request->user_id)

            ->where('company_id', $request->company_id)

            ->where('service_status', $request->service_status)

            ->orderBy('created_at', 'desc')

            ->get();



        $data = $service_bookings->groupBy('invoice');



        if (count($data) > 0) {

            foreach ($data as $key => $value) {

                $services = '';

                $services_color = '';

                if (count($value) > 0) {

                    foreach ($value as $val) {

                        $service_name = DB::table('services')

                            ->where('company_id', $request->company_id)

                            ->where(['id' => $val['service_id']])

                            ->first();



                        if ($service_name == null) {

                            return $this->sendError('Service not exist.');

                        }



                        $customer_name = DB::table('customers')

                            ->where('company_id', $request->company_id)

                            ->where(['id' => $val['customer_id']])

                            ->first();



                        if ($customer_name == null) {

                            return $this->sendError('Customer not exist.');

                        }



                        $services .= $service_name->name . ',';



                        $services_color .= $service_name->service_color . ',';



                        $data_array[$key] = [

                            'customer_name' => $customer_name->last_name,

                            'customer_id' => $customer_name->id,

                            'total_price' => $val['total_price'],

                            'due_date' => $val['due_date'],

                            'due_time' => $val['due_time'],

                            'service_status' => $val['service_status'],

                            'service_id' => $val['service_id'],

                            'comments' => $val['comment'],

                            'service_time' => $val['service_time'],

                            'id' => $val['id'],

                            'invoice' => $key,

                        ];

                    }

                    $services = rtrim($services, ',');

                    $serv_list = [];

                    $serv_list['services_names'] = $services;



                    $services_color = rtrim($services_color, ',');

                    $serv_colors = [];

                    $serv_colors['services_colors'] = $services_color;



                    $calender_array[$key] = $this->combineWithKeys($data_array[$key], $serv_list);



                    array_push($calender_array[$key], $serv_colors);

                }

            }



            $users_bookings_data = array_values($calender_array);



            $response = [

                'success' => true,

                'data' => $users_bookings_data,

                'message' => "service booking retrieve",

            ];



            return response()->json($response, 200);



            // return $this->sendResponse($data_array, 'Service Bookings retrieved successfully.');

        } else {

            return $this->sendError('Bookings not found.');

        }

    }



    public function combineWithKeys($array1, $array2)

    {

        foreach ($array1 as $key => $value) {

            $array2[$key] = $value;

        }

        asort($array2);

        return $array2;

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create(Request $request)

    {

        $booking_detail[] = [];



        $services = DB::table('services')

            ->where('company_id', $request->company_id)

            ->where('is_active', 1)

            ->get();



        $customers = Customer::select('*')

            ->where('is_active', 1)

            ->where('company_id', $request->company_id)

            ->get();

        $tax_info = DB::table('company_settings')

            ->select('tax_status', 'vat', 'amount')

            ->where('company_id', $request->company_id)

            ->get();

        $booking_detail['services'] = $services;

        $booking_detail['customers'] = $customers;

        $booking_detail['tax_info'] = $tax_info;



        return $this->sendResponse($booking_detail, 'data retrieved');

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $data = $request->all();
        // dd($data);

        // return $this->sendResponse($data, 'Appointment booked”.'); exit();

        $validator = Validator::make($data, [

            'user_id' => 'required',

            'company_id' => 'required',

            'due_date' => 'required',

            'due_time' => 'required',

            'customer_id' => 'required',

            'service_id' => 'required',

            // 'total_price' => 'required',

            // 'service_time' => 'required',

        ]);


        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }

        // === AUTO CALCULATE TOTAL PRICE FROM DATABASE ===
        $serviceIds = array_filter(array_map('trim', explode(',', $request->service_id)));

        if (empty($serviceIds)) {
            return $this->sendError('Validation Error.', ['service_id' => ['Please select at least one service.']]);
        }

        $services = Services::select('id', 'name', 'price')
            ->whereIn('id', $serviceIds)
            ->where('company_id', $request->company_id)
            ->where('is_active', 1)
            ->get();

        // Security check: make sure all requested services exist and belong to company
        if ($services->count() !== count($serviceIds)) {
            return $this->sendError('Invalid service selection.', ['service_id' => ['One or more services are invalid or not available.']]);
        }

        $data['total_price'] = $services->sum('price'); // This is now trusted & accurate
        // ===============================================

        $tax_info = DB::table('company_settings')->select('*')->where('company_id', $request->company_id)->get();

        $financialyear = $this->check_if_financial_year($tax_info[0]->financial_year,$request->company_id);
        if($financialyear){
            $lastInvoiceID = 0;
        }
        else{
            $lastInvoiceID = ServiceBooking::orderBy('id', 'desc')->where('company_id', $request->company_id)->pluck('invoice')->first();
        }
       
        $lastInvoiceID = $lastInvoiceID + 1;


        $lastInvoiceID = ($request->survey=="no")? $lastInvoiceID :null;
        if (!array_key_exists("comment", $data)) {

            $data['comment'] = '';

        }



        $vat = 0;

        if($tax_info[0]->tax_status==1 && $tax_info[0]->amount!=""  && $request->survey=="no"){

            if($tax_info[0]->vat==1){

                $vat = $data['total_price']*$tax_info[0]->amount/100;

            }

            else{

                $vat = $tax_info[0]->amount;

            }



        }
        // $service_status = ($request->survey=='no')?'1':'5';

        $service_status = 1; //($request->survey=='no')?'1':'5';

        $data_array = [

            'payment_days' => $data['paymentDays'],

            'invoice' => $lastInvoiceID,

            'user_id' => $data['user_id'],

            'customer_id' => $data['customer_id'],

            'total_price' => $data['total_price'],

            'vat' => $vat,

            'due_date' => $data['due_date'],

            'due_time' => $data['due_time'],

            'service_time' => $data['service_time'],

            'comment' => $data['comment'],

            'service_status'=> $service_status

        ];

        $data_array['company_id'] = $request->company_id;

        $service_booked_data = ServiceBooking::create($data_array);



        $services = explode(",", $data['service_id']);

        // return $this->sendResponse($services, 'Appointment booked”.'); exit();

        foreach ($services as $service) {

            $booking_detail = [

                'booking_id' => $service_booked_data->id,

                'service_id' => $service,

            ];

            $add = BookingDetail::insert($booking_detail);

        }
        //updating customer booking number by 1
        Customer::where('id',$data['customer_id'])->increment('total_bookings', 1);
        //Sending Notification to device

        foreach ($services as $service) {

            $servicess[] = DB::table('services')

                ->select('name')

                ->where('id', $service)

                ->first();

        }

        $service_name = array_column($servicess, 'name');

        $service_name = implode("|", $service_name);



        // $firebaseToken = User::select('device_token')

        //     ->where('id', $request->user_id)

        //     ->get();



        // $SERVER_API_KEY = 'AAAAgxBV8ro:APA91bEHYf564XZw4oiY3eeZl9T798KSnSagwXlfYSUtZ_MVDXmIMK1uSE0IKCoVdpRZX75N6Z3ygRWbbit5l4OOZkTj7llpGiZbRxFVsS8by0hEOlPfSc_EPgwn7NlgVkRx0KbSBS_H';



        // $data = [

        //     "to" => $firebaseToken[0]->device_token,

        //     "priority" => "high",

        //     "notification" => [

        //         "title" => "bSaloon",

        //         "body" => $service_name . "Booked. Date: ". $data['due_date']." Time: " .  $data['due_time'],

        //     ],

        // ];



        // $dataString = json_encode($data);



        // $headers = ['Authorization: key=' . $SERVER_API_KEY, 'Content-Type: application/json'];



        // $ch = curl_init();



        // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        // curl_setopt($ch, CURLOPT_POST, true);

        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);



        // $response = curl_exec($ch);



            // sending message

            $message_status = DB::table('company_settings')->select('message_permissions','sms_count')->where('company_id',$request->company_id)->get();

            if ($message_status['0']->message_permissions == 1) {

                if($message_status[0]->sms_count<=0){

                    return $this->sendResponse('destroy','Insufficient SMS Balance.');

                }
                $bookingmessage = ($request->survey=='no')?'Booking':'Survey';
                $response = $this->send_message($service_booked_data->id, $bookingmessage);

                if (isset($response->error)) {

                    return $this->sendResponse('destroy', $bookingmessage.' created successfully. Message is not sent to customer. Please check this error: ' . $response->error->msg . '.');;

                } else {

                    $remaining_sms = $message_status[0]->sms_count-1;

                    DB::table('company_settings')->where('company_id', $request->company_id)->update([

                        'sms_count' => $remaining_sms,

                    ]);

                    return $this->sendResponse('success', $bookingmessage.' created successfully & Message sent to customer.');

                }

            } else {

                return $this->sendResponse('success', 'Booking created successfully. Message is not sent to customer. You can activate that in Settings.');

            }

    



        // return $this->sendResponse($response, 'Appointment booked”.');

       

    }

    public function check_if_financial_year($financial_year, $companyid){
        $currentyearmonth =  date('Y-m');
        $fyear = date('Y-m', strtotime($financial_year));
        if($fyear==$currentyearmonth){
            $financial_year = date('Y-m-d',strtotime($financial_year . " + 365 day"));
            DB::table('company_settings')->where('company_id', $companyid)->update([

                'financial_year' => $financial_year,

            ]);
            return true;
        }
        else{
            return false;
        }
    }

    // public function send_message($data, $service_id)

    // {

    //     $booking_inputs = [];

    //     $booking_inputs = $data;

    //     $service_msg = '';

    //     $service_name = '';

    //     $customer_name = '';

    //     $customer_phone = '';

    //     $service_details = DB::table('services')

    //         ->where(['id' => $service_id])

    //         ->first();

    //     $customer_details = DB::table('customers')

    //         ->where(['id' => $booking_inputs['customer_id']])

    //         ->first();

    //     $service_msg = $service_details->customer_message;

    //     $service_name = $service_details->name;

    //     $customer_name = $customer_details->first_name . ' ' . $customer_details->last_name;

    //     $customer_phone = $customer_details->primary_phone;



    //     $message_status = DB::table('theme_settings')

    //         ->select('site_name')

    //         ->where('company_id', $booking_inputs['company_id'])

    //         ->get();



    //     if ($service_msg) {

    //         $company_name = DB::table('company_settings')

    //             ->select('site_name')

    //             ->where('company_id', $booking_inputs['company_id'])

    //             ->get();

    //         $company_from = $company_name[0]->site_name;



    //         $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';



    //         $body =

    //             '{



    //             "to": "' .

    //             $customer_phone .

    //             '",

    //             "from": "' .

    //             $company_from .

    //             '",

    //             "dynamic_data": [

    //               {

    //                 "customer name": "' .

    //             $customer_name .

    //             '",

    //                 "services name": "' .

    //             $service_name .

    //             '",

    //                 "date": "' .

    //             date("d-m-Y", strtotime($booking_inputs['due_date'])) .

    //             '",

    //                 "time": "' .

    //             $booking_inputs['due_time'] .

    //             '"

    //               }

    //             ],

    //             "msg": "' .

    //             $service_msg .

    //             '",

    //             "sandbox": false

    //           }';

    //         $curl = curl_init('https://api.voodoosms.com/sendsms');

    //         curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: ' . $api_key]);

    //         curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    //         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    //         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    //         $response = curl_exec($curl);



            // echo "<pre>"; print_r($response); exit;

    //     }

    // }



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

            'booking_id' => 'required',

            'company_id' => 'required',

        ]);



        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $detail = [];

        $data = ServiceBooking::with('customer', 'user')

            ->where('company_id', $request->company_id)

            ->where('id', $request->booking_id)

            ->get();



        $servicenames = DB::table('booking_details')

            ->join('services', 'services.id', '=', 'booking_details.service_id')

            ->where('booking_details.booking_id', $request->booking_id);

        $snames = $servicenames->get();



        if ($data->isEmpty()) {

            return $this->sendError('Something went wrong');

        }



        $company_email = DB::table('email_settings')

            ->select('email', 'image', 'header', 'footer')

            ->where('permission', 1)

            ->where('company_id', $request->company_id)

            ->get();



        $company_email = $company_email->toArray();



        if ($company_email) {

            foreach ($company_email as $key => $company) {

                $path = url('public/email_logo', $company->image);



                $company->image = $path;

            }

        }

        foreach ($data as $customer) {

            $customer = $customer->customer;
            if($customer->email!=null){
                $email = $customer->email;
            }
            else{
                $email = '';
            }
            if($customer->primary_phone!=null){
                $primary_phone = $customer->primary_phone;
            }
            else{
                $primary_phone = '';
            }
            if($customer->secondary_phone!=null){
                $secondary_phone = $customer->secondary_phone;
            }
            else{
                $secondary_phone = '';
            }
            $customer_detail = [

                'customer_name' => $customer->first_name,

                'last_name' => $customer->last_name,

                'primary_phone' => $primary_phone,
                'secondary_phone' => $secondary_phone,

                'email' => $email,

                'address' => $customer->address,

            ];

        }



        foreach ($data as $key => $book) {

            foreach ($snames as $service) {

                $services_data[] = [

                    'booking_id' => $data[0]->id,

                    'serivce_id' => $service->id,

                    'service_name' => $service->name,

                    'service_color' => $service->service_color,

                    'service_price' => $service->price,

                ];

            }



            $payment_method = 'Not Available';

            $chequenumber = '';

            if ($book->payment_instrument) {

                $payment_method = $book->payment_instrument;

                $chequenumber = $book->payment_instrument_number;

            }



            $book_detail[$key] = [

                'booking_id' => $book['id'],

                'invoice' => $book->invoice,

                'company_id' => $request->company_id,
                'invoice_date' => $book->due_date,
                'booking_date' => $book->created_at,

                'vat' => $book->vat,

                'payment_days' => $book->payment_days,

                'notes' => $book->comment,

                'total_price' => $book->total_price,

                'payment_method' => $payment_method,

                'chequenumber' => $chequenumber,

            ];

        }

        $detail['booking_details'] = $book_detail;

        $detail['services_data'] = $services_data;



        $detail['customer_details'] = $customer_detail;



        if ($company_email) {

            $detail['company_details'] = $company_email[0];

        }



        $response = [

            'success' => true,

            'data' => $detail,

            'message' => "service booking retrieve",

        ];

        return response()->json($response, 200);

    }

    //code for complete message



    public function complete_message($mobile_number,$company_id)

    {



        $mobile_number =$mobile_number;

        $company_id = $company_id ;

   

        $review = DB::table('company_settings')

        ->select('smart_name','review_url','review_permissions')

        ->where('company_id',$company_id )

        ->get();



        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

        if($review[0]->review_permissions == 1 && $review[0]->review_url!="")

        {

            $response = $this->check_credits();

            if(isset($response->amount)){

                $msg2 = json_encode(

                    [

                        'to' => $mobile_number,

                        'from' => $review[0]->smart_name,

                        'msg' =>  $review[0]->review_url,

                        'country_code' => 44,

                        'sandbox' => false

                    ]



                );

                $curl = curl_init('https://api.voodoosms.com/sendsms');

                curl_setopt($curl, CURLOPT_HTTPHEADER,  ['Authorization: ' . $api_key]); 

                curl_setopt($curl, CURLOPT_POSTFIELDS, $msg2);

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($curl);

                $results = json_decode($response);

               

                if(isset($results->error)){

                    

                    return $this->sendError('error', $results->error->msg);

                  

                }else{

                    return $this->sendResponse('success' ,'Review Message Sent Successfully.');

                }

               

            }

            else{

                return $this->sendError('error',$response->error->msg);

            

            }



        }

        else{

            return $this->sendError('error','Please activate your review from settings or URL should not be empty!');

        }

        



    }



    // code for complete message end here



    public function cancel_message($booking_id)

    { 

        $response = $this->check_credits();

       if(isset($response->amount)){

      

            $work_flow_message = DB::table('service_bookings')

                ->select('service_status','customer_id','company_id')

                ->where('id', $booking_id)

                ->get();

                $message_status = DB::table('company_settings')->select('*')->where('company_id', $work_flow_message['0']->company_id)->get();

        

                if($message_status['0']->sms_count<=0){

                    return $this->sendResponse($results , 'Status Changed. Msg not sent due to Insufficient SMS Balance. Contact bSaloon.');

                }

            $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';



            if ($work_flow_message['0']->service_status == 4) {

                $customer_details = DB::table('customers')

                    ->where(['id' => $work_flow_message['0']->customer_id])

                    ->first();



                $customer_phone = $customer_details->primary_phone;

                $company_from = ($message_status['0']->smart_name=='')? $message_status['0']->company_name:$message_status['0']->smart_name;

                $msg2 = json_encode([

                    'to' => $customer_phone,

                    'from' => $company_from,

                    'msg' => "Your Booking has been cancelled.",

                    'country_code' => '44',

                    'sandbox' => false,

                ]);



                $curl = curl_init('https://api.voodoosms.com/sendsms');

                curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: ' . $api_key]);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $msg2);

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($curl);



                $results = json_decode($response);

                if($results->error){

                    return $this->sendResponse($results , 'Status Changed. '.$results->error.' Contact bSaloon.');

                }

                else{

                    $remaining_sms = $message_status['0']->sms_count-1;

                    DB::table('company_settings')->where('company_id', $work_flow_message['0']->company_id)->update([

                        'sms_count' => $remaining_sms,

                    ]);

                    return $this->sendResponse($results , 'Bookling cancelled Successfully. SMS sent to customer.');

                }

                

            }

        }

        else{

            return $this->sendResponse($results , $response->error);

        }

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

            'booking_id' => 'required',

            'company_id' => 'required',

            'user_id' => 'required',

            'customer_id' => 'required',

            'service_id' => 'required',

            'due_date' => 'required',

            'due_time' => 'required',

            'service_time' => 'required',

            'total_price' => 'required',

        ]);



        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $data = $request->all();

        if (!array_key_exists("comment", $data)) {

            $data['comment'] = '';

        }

        $data_array = [

            'user_id' => $data['user_id'],

            'customer_id' => $data['customer_id'],

            'total_price' => $data['total_price'],

            'due_date' => $data['due_date'],

            'due_time' => $data['due_time'],

            'service_time' => $data['service_time'],

            'comment' => $data['comment'],

            'payment_days' => $data['paymentDays'],

        ];

        $service_booked_data = ServiceBooking::where('id', $request->booking_id)->update($data_array);

        BookingDetail::where('booking_id', $request->booking_id)->delete();



        $services = explode(",", $data['service_id']);

        foreach ($services as $service) {

            $booking_detail = [

                'booking_id' => $request->booking_id,

                'service_id' => $service,

            ];

            $add = BookingDetail::insert($booking_detail);

        }



        foreach ($services as $service) {

            $servicess[] = DB::table('services')

                ->select('name')

                ->where('id', $service)

                ->first();

        }

        $service_name = array_column($servicess, 'name');

        $service_name = implode("|", $service_name);

        //Sending Notification to device



        $firebaseToken = User::select('device_token')

            ->where('id', $request->user_id)

            ->get();



        $SERVER_API_KEY = 'AAAAgxBV8ro:APA91bEHYf564XZw4oiY3eeZl9T798KSnSagwXlfYSUtZ_MVDXmIMK1uSE0IKCoVdpRZX75N6Z3ygRWbbit5l4OOZkTj7llpGiZbRxFVsS8by0hEOlPfSc_EPgwn7NlgVkRx0KbSBS_H';



        $data = [

            "to" => $firebaseToken[0]->device_token,

            "priority" => "high",

            "notification" => [

                "title" => "bSaloon",

                "body" => $service_name . "Appointment updated against Invoice no. " . $request->invoice,

            ],

        ];



        $dataString = json_encode($data);



        $headers = ['Authorization: key=' . $SERVER_API_KEY, 'Content-Type: application/json'];



        $ch = curl_init();



        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);



        $response = curl_exec($ch);

        $book[] = [];

        $book['details'] = $data_array;

        $book['services'] = $service_name;

         // sending message

         $message_status = DB::table('company_settings')->select('message_permissions','sms_count')->where('company_id',$request->company_id)->get();

         if ($message_status[0]->message_permissions == 1) {

                if($message_status[0]->sms_count<=0){

                    return $this->sendResponse('destroy','Insufficient SMS Balance. Contact bSaloon');

                }

             $response = $this->send_message($request->booking_id, 'update');

             if (isset($response->error)) {

                 return $this->sendResponse('destroy', 'Booking updated successfully. Message is not sent to customer. Ask bSaloon to check this error: ' . $response->error->msg . '.');

             } else {

                $remaining_sms = $message_status[0]->sms_count-1;

                DB::table('company_settings')->where('company_id', $request->company_id)->update([

                    'sms_count' => $remaining_sms,

                ]);

                 return $this->sendResponse('success', 'Booking updated successfully & Message sent to customer.');

             }

         } else {

             return $this->sendResponse('success', 'Booking updated successfully. Message is not sent to customer. You can activate that in Settings.');

         }



        // return $this->sendResponse($book, 'Appointment updated successfully.');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request)

    {

        $input = $request->all();

        $validator = Validator::make($input, [

            'booking_id' => 'required',

            'company_id' => 'required',

        ]);

        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $book = tap(

            DB::table('service_bookings')

                ->where('id', $request->booking_id)

                ->where('company_id', $request->company_id)

        )

            ->first()

            ->delete();

        if ($book == null) {

            $booking_detail = DB::table('booking_details')

                ->where('booking_id', $request->booking_id)

                ->delete();

            dd($book, $booking_detail);

            return $this->sendResponse($book, 'Something went wrong.');

        }

        return $this->sendResponse($book, 'Booking deleted successfully.');

    }



    public function service_price(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'company_id' => 'required',

        ]);



        if ($validator->fails()) {

            return $this->sendError('Validation Error.', $validator->errors());

        }



        $data['service_id'] = explode(',', $input['service_id']);



        $sum_total = 0;

        if (count($data['service_id'])) {

            foreach ($data['service_id'] as $ser) {

                $price = Services::select('price')

                    ->where('company_id', $request->company_id)

                    ->where('id', $ser)

                    ->get();

                foreach ($price as $sum) {

                    $sum_total = $sum_total + $sum->price;

                }

            }

            return $this->sendResponse($sum_total, 'Service price retrieve successfully.');

        } else {

            return $this->sendResponse((array) $sum_total, 'Something went wrong.');

        }

    }



    public function receiving_notification(Request $request)

    {

        

        $customer = Customer::where('id', $request->customer_id)->get();

       

        $settings = DB::table('company_settings')

                ->select('message_permissions','site_name','smart_name','sms_count')

                ->where('company_id', $request->company_id)

                ->get();

             

        if ($settings['0']->message_permissions == 1) {

            if($settings['0']->sms_count<=0){

                return $this->sendResponse($customer->toArray(), 'Insufficient SMS balance. Contact bSaloon.');

            }

            $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

            

            $customer_phone = $customer['0']->primary_phone;

            $company_from = ($settings['0']->smart_name=='')? $settings['0']->company_name:$settings['0']->smart_name;

            $msg2 = json_encode([

                'to' => $customer_phone,

                'country_code' => "44",

                'from' => $company_from,

                'msg' => "$company_from will be with you in $request->time",

                'sandbox' => false,

            ]);

            $remaining_sms = $settings['0']->sms_count-1;

            DB::table('company_settings')->where('company_id', $request->company_id)->update([

                'sms_count' => $remaining_sms,

            ]);

            $curl = curl_init('https://api.voodoosms.com/sendsms');

            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: ' . $api_key]);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $msg2);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);



            $results = json_decode($response);

            if(isset($results->error)){
                $msg = $results->error->msg;
                if($results->error->code==5){
                    $msg = 'No Mobile Phone Registered';
                }
                return $this->sendError($empty = [], $msg); 
                //return $this->sendResponse($empty = [], $results->error->msg);

            }

            return $this->sendResponse($customer->toArray(), 'Message sent to customer.');

        } else {
            return $this->sendError($empty = [], 'Sorry Your SMS service is not enabled.'); 
            //return $this->sendResponse($empty = [], 'Sorry Your SMS service is not enabled.');

        }

    }



    public function send_invoice(Request $request)

    {

        $id = $request->booking_id;

        if ($id) {

            $booking_detail = ServiceBooking::with('customer')

                ->where('id', $id)

                ->get();

                //echo $booking_detail['0']->customer->first_name; exit;

            if (count($booking_detail) > 0) {

                if(!empty($booking_detail['0']->customer->email)){

                    $company_id = $booking_detail['0']->company_id;

                    $detail_array = "";

                    $payment_method = 'Not Available';

                    $chequenumber = '';



                    foreach ($booking_detail as $book) {

                        if ($book->payment_method != "") {

                            $payment_method = $book->payment_method;



                            $chequenumber = $book->chequenumber;

                        }

                        $detail_array = [

                            'invoice' => $book->invoice,

                            'due_date' => $book->due_date,

                            'total_price' => $book->total_price,

                            'first_name' => $book->customer->first_name,

                            'last_name' => $book->customer->last_name,

                            'primary_phone' => $book->customer->primary_phone,

                            'email' => $book->customer->email,

                            'address' => $book->customer->address,

                            'comment' => $book->comment,

                            'payment_method' => $payment_method,

                            'payment_days' => $book->payment_days,

                            'vat' => $book->vat,

                            'chequenumber' => $chequenumber,

                            'postcode' => $book->customer->zip,

                            'services' => $this->get_services($book->id),

                        ];

                    }



                    $title = 'ABC Company';

                    $subject = 'B Saloon Invoice';



                    $detail_array["header"] = '';

                    $detail_array["footer"] = '';

                    $detail_array["company_logo"] = '';



                    $company_email = DB::table('email_settings')

                        ->where('company_id', $company_id)

                        ->get();

                    if (!$company_email->isEmpty()) {

                        $title = $company_email[0]->title;

                        $subject = $company_email[0]->subject;

                        $detail_array["header"] = $company_email[0]->header;

                        $detail_array["footer"] = $company_email[0]->footer;

                        $detail_array["company_logo"] = $company_email[0]->image;

                    }

                    $pdf = \PDF::loadView('emails.invoicepdf', compact('detail_array'));



                    $to_name = $detail_array['last_name'];

                    $to_email = $detail_array['email'];



                    \Mail::send('emails.invoice', compact('detail_array'), function ($message) use ($to_name, $to_email, $title, $subject, $pdf) {

                        $message->to($to_email, $to_name)->subject($subject);

                       $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);

                        $message->attachData($pdf->output(), "invoice.pdf");

                    });

                    // check for failures

                    $failures = Mail::failures();

                    if (count($failures) > 0) {

                        return $this->sendError($detail_array->toArray(), 'Something went wrong');

                    } else {

                        return $this->sendResponse($detail_array, 'E-mail has been sent successfully');

                    }

                }

                else{

                    //$data['Error!'] =  True;

                    return $this->sendError('','Email not sent.  Customer does not have email registered.');

                }

            } else {

                return $this->sendError('','Something Went Wrong');

            }

        }

    }







    public function book_status(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'booking_id' => 'required',

            'company_id' => 'required',

            'service_status' => 'required',

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }



        $booking_id = $request->booking_id;

        $status = $request->service_status;



        $message_status = DB::table('company_settings')->select('message_permissions')->where('company_id', $request->company_id)->get();

        

        if($status=='3'){

            $payment_method = $request->input('payment_method');



            if($payment_method == 'cheque'){



                $cheque_number = $request->input('cheque_number');



                $work_flow = tap(DB::table('service_bookings')

                            ->where('id', $booking_id)->where('company_id', $request->company_id))

                            ->update([

                                'service_status' => $status,

                                'payment_instrument' => $payment_method,

                                'payment_instrument_number' => $cheque_number

                                ])->first();



                if($work_flow == null){

                    return $this->sendError($work_flow, 'Something went wrong');

                }

            }

            else {                

                $work_flow = tap(DB::table('service_bookings')

                            ->where('id', $booking_id)->where('company_id', $request->company_id))

                            ->update([

                                'service_status' => $status,

                                'payment_instrument' => $payment_method

                                ])->first();



                if($work_flow == null){

                    return $this->sendError($work_flow, 'Something went wrong.');

                }                

            }

            

            

            return $this->sendResponse($work_flow, 'Booking status changed successfully.');

        }

        else {

            $work_flow = tap(DB::table('service_bookings')

            ->where('id',$booking_id)->where('company_id', $request->company_id))

            ->update(['service_status' => $status])->first();

           

            if ($work_flow == null) {

                return $this->sendError($work_flow, 'Something went wrong.');

            }

            else{

                if($status == '4' && $message_status['0']->message_permissions == 1){

                    

                    $this->cancel_message($booking_id);

                }

                return $this->sendResponse($work_flow, 'Booking status changed successfully');

            }

            

        }   



    }























    public function get_services($id)

    {

        $services = [];

        $servicenames = DB::table('booking_details')

            ->join('services', 'services.id', '=', 'booking_details.service_id')

            ->where('booking_details.booking_id', $id)

            ->get();

        foreach ($servicenames as $service) {

            $services[] = [

                'service_name' => $service->name,

                'service_price' => $service->price,

            ];

        }

        return $services;

    }



    public function tax_info(Request $request)

    {

        $company_id = $request->company_id;

        if ($company_id > 1) {

            $tax_info = DB::table('company_settings')

                ->select('tax_status', 'vat', 'amount')

                ->where('company_id', $request->company_id)

                ->get();

            if (count($tax_info) > 0) {

                return $this->sendResponse($tax_info, 'data retrieved');

            } else {

                $tax_info = '';

                return $this->sendResponse($tax_info, 'data retrieved');

            }

        } else {

            return $this->sendResponse('Something Went Wrong');

        }

    }

    public function send_message($bookingID,$action)



    {

        //echo $bookingID; exit;

        $response = $this->check_credits();

       if(isset($response->amount)){

            //Getting proper data like customer name, services name for this invoice to send message

            $booking_detail = ServiceBooking::with('customer','companysetting')->where('id', $bookingID)->get();

            

            $services = $customer = $due_date = $due_time = $mobile_number = $company_name= $smart_name = $booking_sms ='';

            foreach($booking_detail as $detail){

                

                $customer = $detail->customer->first_name.' '.$detail->customer->last_name;

                $due_date = $detail->due_date;

                $due_time = $detail->due_time;

                $mobile_number = $detail->customer->primary_phone;

                $company_name = $detail->companysetting->site_name;

                $smart_name = $detail->companysetting->smart_name;

                if($action=='Booking'){

                    $booking_sms = $detail->companysetting->booking_sms;

                }

                elseif($action=='Survey'){
                    $booking_sms = $detail->companysetting->survey_sms;
                }

                else{

                    $booking_sms = $detail->companysetting->re_sheduled_sms;

                }
                if($action!='Survey'){
                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $detail['id'])->get();

                foreach($servicenames as $service){

                    $services .= $service->name.','; 

                }

            }

            }

            

            $services = rtrim($services, ',');

            $company_from = ($smart_name=='')? $company_name:$smart_name;

            $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

            $body = '{



                "to": "'.$mobile_number.'",

                "from": "'.$company_from.'",

                "country_code":"44",

                "dynamic_data": [

                  {

                    "customer name": "'.$customer.'",

                    "services name": "'.$services.'",

                    "date": "'.date("d-m-Y",strtotime($due_date)).'",

                    "time": "'.$due_time.'"

                  }

                ],

                "msg": "'.$booking_sms.'",

                "sandbox": false

              }';

            $curl = curl_init('https://api.voodoosms.com/sendsms');

            curl_setopt($curl, CURLOPT_HTTPHEADER,  [

                'Authorization: ' . $api_key

            ]); 

            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($curl);

            curl_close($curl);

            $result = json_decode($response);

            return $result;

        }

        else{

            return $response;

        }

    }

    public function check_credits(){

        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

        $ch = curl_init('https://api.voodoosms.com/credits');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [

            'Authorization: ' . $api_key

        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response); 

    }


    public function send_review_sms(Request $request)
    {
        
        
        if($request->mobile_number=='' || $request->company_id=='' || $request->mobile_number==NULL || $request->company_id==NULL){
            
            return $this->sendError('', 'Parameter missing. Mobile number & compnay Id can not be null.');  
        }
        $review = DB::table('company_settings')
        ->select('smart_name','review_url','review_permissions','sms_count')
        ->where('company_id', $request->company_id)
        ->get();
        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';
        if($review[0]->sms_count<=0){
            return $this->sendError('', 'You have insuffiecient sms Balance. Contact bSaloon.');
        }
        if($review[0]->review_permissions == 1 && $review[0]->review_url!="")
        {
            $response = $this->check_credits();
            if(isset($response->amount)){
                $msg2 = json_encode(
                    [
                        'to' => $request->mobile_number,
                        "country_code"=> "44",
                        'from' => $review[0]->smart_name,
                        'msg' =>  $review[0]->review_url,
                        'sandbox' => false
                    ]

                );
                $curl = curl_init('https://api.voodoosms.com/sendsms');
                curl_setopt($curl, CURLOPT_HTTPHEADER,  ['Authorization: ' . $api_key]); 
                curl_setopt($curl, CURLOPT_POSTFIELDS, $msg2);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                $results = json_decode($response);
               
                if(isset($results->error)){
                    
                    return $this->sendError('', $results->error->msg);
                }else{
                    $remaining_sms = $review[0]->sms_count-1;
                    DB::table('company_settings')->where('company_id', $request->company_id)->update([
                        'sms_count' => $remaining_sms,
                    ]);
                    return $this->sendResponse('', 'Review Message Sent Successfully.');
                }
               
            }
            else{
             
                return $this->sendError('', $response->error->msg);
            }

        }
        else{
            return $this->sendError('', 'Check your review setting in website');
        }
        

    }

}

