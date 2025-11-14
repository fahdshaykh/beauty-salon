<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // For sending emails
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\BookingMessage;


use App\ServiceBooking;

use App\Services;

use App\InventoryBook;


use App\InventoryRemain;

use App\Customer;

use App\Category;

use App\BookingDetail;

use App\Inventory;

use App\InventoryPurchases;

use App\User;

use App\Mail\NotificationEmail;
use App\Models\Setting;
use App\Models\UserSetting;
use Carbon\Carbon;

class ServiceBookingController extends Controller



{



    /**

     * Display a listing of the resource.

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        // Get pagination options from settings
        // Authorization: ensure user can view service bookings
        $this->authorize('viewAny', ServiceBooking::class);

        // Get pagination options from settings
        $paginationOptions = Setting::getPaginationOptions();

        // Get user's preferred per_page, fallback to request or default
        $defaultPerPage = auth()->user()->getPerPagePreference();
        $perPage = $request->input('per_page', $defaultPerPage);

        // Validate per_page against available options
        if (!in_array($perPage, $paginationOptions)) {
            $perPage = $defaultPerPage;
        }

        // Save user's per_page preference
        if ($request->has('per_page')) {
            if (auth()->user()->setting) {
                auth()->user()->setting->update(['per_page' => $perPage]);
            } else {
                UserSetting::create([
                    'user_id' => auth()->user()->id,
                    'per_page' => $perPage,
                ]);
            }
        }

        // Start building the query
        $query = ServiceBooking::with('customer', 'user', 'services')
            ->where('company_id', auth()->user()->company_id);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->user()->id);
        }

        // Apply service filter
        if ($request->filled('service_id')) {
            $query->whereHas('bookingdetail', function ($q) use ($request) {
                $q->where('service_id', $request->input('service_id'));
            });
        }

        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->input('end_date'));
        }

        // Apply customer name filter
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->input('customer_name') . '%']);
            });
        }

        // Apply primary phone filter
        if ($request->filled('primary_phone')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('primary_phone', 'LIKE', '%' . $request->input('primary_phone') . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('service_status', $request->input('status'));
        }

        // Fetch paginated bookings
        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page')); // Preserve filter parameters in pagination links

        $data_array = [];

        if ($bookings->count() > 0) {
            foreach ($bookings as $key => $value) {
                $services = $value->services->pluck('name')->implode(',');

                $data_array[$key] = [
                    'id' => $value->id,
                    'status' => $value->status,
                    'customer_name' => optional($value->customer)->first_name . ' ' . optional($value->customer)->last_name,
                    'primary_phone' => optional($value->customer)->primary_phone,
                    'customer_address' => optional($value->customer)->address,
                    'customer_zip' => optional($value->customer)->zip,
                    'user_name' => optional($value->user)->name,
                    'price_type' => $value->price_type,
                    'total_price' => $value->total_price,
                    'due_date' => $value->due_date,
                    'due_time' => $value->due_time,
                    'finish_time' => $value->finish_time,
                    'service_status' => $value->service_status,
                    'services' => $services,
                    'payment_days' => $value->payment_days,
                ];
            }
        }

        // Fetch all services for the filter dropdown
        $services = Services::where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        return view('service_bookings.index', compact('data_array', 'bookings', 'paginationOptions', 'services'));
    }



    public function create($date=null)

    { 

        // $inventories = Inventory::where('company_id', auth()->user()->company_id)->get();
            // Authorization: ensure user can create service bookings
            $this->authorize('create', ServiceBooking::class);

            // $inventories = Inventory::where('company_id', auth()->user()->company_id)->get();
        $inventories = Inventory::where('company_id', auth()->user()->company_id)
                        ->whereHas('purchases')
                        ->get();
        // $user = User::all();



        $booking_detail = '';



        $services = Category::whereHas('services_booking')->where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        $customers = Customer::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();

        // $users = User::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();
        $users = User::activeInCompany()
                    ->when(!auth()->user()->hasRole('admin'), fn($q) => $q->where('id', auth()->user()->id))
                    ->get();

        $tax_info = Db::table('company_settings')->select('tax_status','vat','amount')->where('company_id', auth()->user()->company_id)->get();

        $countries = DB::table('countries')->get();

        $referreds = DB::table('referred_customers')->where('is_active', true)->get();

        return view('service_bookings.create',compact('date', 'countries', 'referreds', 'customers','services','booking_detail','tax_info','inventories', 'users'));



    }



    
    public function store(Request $request)
    {
        // Authorization: ensure user can create service bookings
        $this->authorize('create', ServiceBooking::class);

        $this->validate($request, [
            'due_date' => 'required',
            'due_time' => 'required',
            'finish_time' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'service_id' => 'required',
            'user_id' => 'required|numeric',
            'total_price' => 'required_if:survey,Yes',
            'inventory_id' => 'nullable|exists:inventories,id',
        ]);
       
        $userId = $request->user_id;
        $dueDate = $request->due_date;
        // Convert due_date and due_time into a single DateTime object
        $dueTime = Carbon::parse($request->due_time)->format('H:i:s'); // Convert to 24-hour format
    
        // Check if there's any existing booking in the last 30 minutes
        $existingBooking = ServiceBooking::where('user_id', $userId)
            ->where('due_date', $dueDate)
            ->where(function ($query) use ($dueTime) {
                $query->whereRaw("? BETWEEN due_time AND ADDTIME(due_time, SEC_TO_TIME(finish_time * 60))", [$dueTime]);
            })
            ->exists();
           
        if ($existingBooking) {
            return redirect()->back()->with('error', 'A booking already exists within the last 30 minutes. Please try again later.');
        }
        
        // Retrieve tax info
        $tax_info = DB::table('company_settings')->select('tax_status', 'vat', 'amount', 'financial_year')
            ->where('company_id', auth()->user()->company_id)
            ->get();
    
        $financialyear = $this->check_if_financial_year($tax_info[0]->financial_year);
        $lastInvoiceID = $financialyear ? 0 : ServiceBooking::orderBy('id', 'desc')
            ->where('invoice', '<>', null)
            ->where('company_id', auth()->user()->company_id)
            ->pluck('invoice')
            ->first();
           
        $lastInvoiceID = $lastInvoiceID + 1;
    
        $data = $request->all();
        $vat = 0;
    
        if ($tax_info[0]->tax_status == 1 && $tax_info[0]->amount != "" && $request->survey == "no") {
            $vat = $tax_info[0]->vat == 1
                ? $data['total_price'] * $tax_info[0]->amount / 100
                : $tax_info[0]->amount;
        }
       
        $numdays = 0;
        $service_status = '1';


        $dueTime = Carbon::parse($request->due_time)->format('H:i:s'); // Convert to 24-hour format
        $data['due_time'] = $dueTime;
       
        $booking_array = [
            'invoice' => $lastInvoiceID,
            'user_id' =>   $data['user_id'],
            'company_id' => auth()->user()->company_id,
            'customer_id' => $data['customer_id'],
            'total_price' => $data['total_price'],
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'],
            'finish_time' => $data['finish_time'],
            'comment' => $data['comment'],
            'vat' => $vat,
            'service_status' => $service_status,
            'payment_days' => $numdays,
            'user_id' => $data['user_id'], 
        ];
       
        $booking = ServiceBooking::create($booking_array);
    
        foreach ($data['service_id'] as $service) {
            $booking_detail = [
                'booking_id' => $booking->id,
                'service_id' => $service,
            ];
    
            DB::table('booking_details')->insert($booking_detail);
        }

        // code for manage inventory start here 

        if (isset($data['inventory_id']) && !empty($data['inventory_id'])) {

            foreach ($data['inventory_id'] as $index => $inventory_id) {
                $quantity = $data['quantity'][$index] ?? 0;

                if (empty($inventory_id) || $quantity <= 0) {
                    \Log::warning("Skipping invalid entry: Inventory ID: {$inventory_id}, Quantity: {$quantity}");
                    continue;
                }

                DB::table('inventory_books')->insert([
                    'booking_id' => $booking->id,
                    'inventory_id' => $inventory_id,
                    'quantity' => $quantity,
                ]);

                $inventory_remain = InventoryRemain::firstOrNew(['inventory_id' => $inventory_id]);
                $inventory_remain->total_quantity = max(0, ($inventory_remain->total_quantity ?? 0) - $quantity);
                $inventory_remain->company_id = auth()->user()->company_id;
                
                $inventory_remain->save();
            }
        }

        // code for manage inventory start here end
    
        return redirect('service-bookings')->with('success', 'Booking created successfully.');
        
    }
    
  
    public function check_if_financial_year($financial_year){
        $currentyearmonth =  date('Y-m');
        $fyear = date('Y-m', strtotime($financial_year));
        if($fyear==$currentyearmonth){
            $financial_year = date('Y-m-d',strtotime($financial_year . " + 365 day"));
            DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update([

                'financial_year' => $financial_year,

            ]);
            return true;
        }
        else{
            return false;
        }
    }


    public function checkAvailability(Request $request)
    {
        $userId = $request->user_id;
        $dueDate = $request->due_date;
        $dueTime = Carbon::parse($request->due_time)->format('H:i:s'); // Convert to 24-hour format

        // Check if any booking overlaps with selected due_time
        $existingBooking = ServiceBooking::where('user_id', $userId)
            ->where('due_date', $dueDate)
            ->where(function ($query) use ($dueTime) {
                $query->whereRaw("? BETWEEN due_time AND ADDTIME(due_time, SEC_TO_TIME(finish_time * 60))", [$dueTime]);
            })
            ->exists();


        if ($existingBooking) {
            // Fetch all conflicting bookings
            $bookedSlots = ServiceBooking::where('user_id', $userId)
                ->where('due_date', $dueDate)
                ->where(function ($query) use ($dueTime) {
                    $query->whereRaw("? BETWEEN due_time AND ADDTIME(due_time, SEC_TO_TIME(finish_time * 60))", [$dueTime]);
                })
                ->get(['due_date', 'due_time', 'finish_time']);

            return response()->json([
                'error' => 'You already have a booking in this time range.',
                'booked_slots' => $bookedSlots
            ]);
        }

        return response()->json(['success' => true]);
    }


     /**



     * Show the form for editing the specified resource.



     *



     * @param  \App\ServiceBooking  $serviceBooking



     * @return \Illuminate\Http\Response



     */



    public function edit(ServiceBooking $serviceBooking, $id)

    {

            // Authorize update on this booking (loads booking and checks company scope)
            $this->authorize('update', ServiceBooking::where('id', $id)->where('company_id', auth()->user()->company_id)->firstOrFail());

        $selectedServices = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $id)->pluck('services.id')->toArray();

        // $inventories_purchases = InventoryPurchases::where('company_id', auth()->user()->company_id)->get();
        $inventories = Inventory::where('company_id', auth()->user()->company_id)->get();

        $customers = Customer::select('*')->where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        $users = User::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();

        $services = Category::whereHas('services_booking')->where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        $booking_detail = ServiceBooking::with('customer','user')->where('id', $id)->get();

        // dd($booking_detail[0]->inventory_books);

        $tax_info = Db::table('company_settings')->select('tax_status','vat','amount')->where('company_id', auth()->user()->company_id)->get();

        foreach($booking_detail as $key=>$value)

            {

                $services_ids = array();



                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id'])->get();

                foreach($servicenames as $service){

                    $services_ids[$value->id][]= $service->id; 

                }



            }

            //echo "<pre>"; print_r($services); exit;

        return view('service_bookings.edit',compact('selectedServices','booking_detail','inventories', 'customers','users','services','services_ids','tax_info'));

    }







    /**



     * Update the specified resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @param  \App\ServiceBooking  $serviceBooking



     * @return \Illuminate\Http\Response



     */



    public function update(Request $request, ServiceBooking $serviceBooking)

    {

            // Authorization: ensure user can update this booking
            $this->authorize('update', $serviceBooking);

        $this->validate($request, 

            [

                'due_date' => 'required',

                'due_time' => 'required',

                'customer_id' => 'required|numeric',

                'service_id' => 'required',

                'total_price' => 'required',

               

            ],

            [

                'due_date.required' => 'Please select date',

                'due_time.required' => 'Please select time',

                'customer_id.required' => 'Please select customer name',

                'service_id.required' => 'Please select service',

                'total_price.min' => 'Negative Value is not allowed',

                'total_price.not_in' => 'Price should be greater than zero',

            ],



        );

        

            $data = $request->all();

            $tax_info = DB::table('company_settings')->select('tax_status', 'vat','amount')->where('company_id', auth()->user()->company_id)->get();

            $vat = 0;

            if($tax_info[0]->tax_status==1 && $tax_info[0]->amount!=""){

                if($tax_info[0]->vat==1){

                    $vat = $data['total_price']+($data['total_price']*$tax_info[0]->amount/100);

                }

                else{

                    $vat = $data['total_price']+$tax_info[0]->amount;

                }

    

            }

            if($request->days){

                $numdays = 0;

            }

            else{

                $numdays = $data['num_days'];

            }

            $dueTime = Carbon::parse($request->due_time)->format('H:i:s'); // Convert to 24-hour format
            $data['due_time'] = $dueTime;

            $data_array = array(

                'user_id' => $data['user_id'],

                'customer_id' => $data['customer_id'],

                'user_id' => $data['user_id'],

                'total_price' => $data['total_price'],

                'due_date' => $data['due_date'],

                'due_time' => $data['due_time'],

                'finish_time' => $data['finish_time'],

                'comment' => $data['comment'],

                'payment_days' => $numdays,

                'vat' => $vat,

            );

            ServiceBooking::where('id', $request->booking_id)->update($data_array);

            BookingDetail::where('booking_id', $request->booking_id)->delete();

            foreach($request->service_id as $key=>$service){

                $booking_detail[$key] = array(

                    'booking_id' => $data['booking_id'],

                    'service_id' => $service

                );

            }

     

            BookingDetail::insert($booking_detail);

            // adding inventory module 

            // InventoryBook::where('booking_id', $request->booking_id)->delete();

            // foreach ($data['inventory_id'] as $index => $inventory_id) {

            //     $quantity = $data['quantity'][$index] ?? 0;
        
            //     if (empty($inventory_id) || $quantity <= 0) {
            //         \Log::warning("Skipping invalid entry: Inventory ID: {$inventory_id}, Quantity: {$quantity}");
            //         continue;
            //     }

            //     DB::table('inventory_books')->insert([
            //         'booking_id' => $request->booking_id,
            //         'inventory_id' => $inventory_id,
            //         'quantity' => $quantity,
            //     ]);
        
            //     $inventory_remain = InventoryRemain::firstOrNew(
            //         ['inventory_id' => $inventory_id], 
            //         ['company_id' => auth()->user()->company_id]
            //     );

            //     $inventory_remain->company_id = auth()->user()->company_id;

            //     // Check if the inventory_id or total_quantity is being updated
            //     $originalInventoryId = $inventory_remain->getOriginal('inventory_id');
            //     $originalQuantity = $inventory_remain->getOriginal('total_quantity');

            //     // Update the quantity
            //     $inventory_remain->total_quantity = max(0, ($inventory_remain->total_quantity ?? 0) - $quantity);

            //     // Check if inventory_id or total_quantity has changed
            //     if ($inventory_remain->isDirty(['inventory_id', 'total_quantity'])) {
            //         if ($inventory_remain->inventory_id != $originalInventoryId) {
            //             // Log inventory_id change
            //             logger("Inventory ID changed from {$originalInventoryId} to {$inventory_remain->inventory_id}");
            //         }

            //         if ($inventory_remain->total_quantity != $originalQuantity) {
            //             // Log total_quantity change
            //             logger("Total Quantity changed from {$originalQuantity} to {$inventory_remain->total_quantity}");
            //         }

            //         // Save the changes
            //         $inventory_remain->save();
            //     } else {
            //         logger('No changes detected for InventoryRemain.');
            //     }
            // }

            // 1. Get existing inventory books
            $oldBooks = InventoryBook::where('booking_id', $request->booking_id)->get()->keyBy('inventory_id');

            // 2. Delete all existing inventory books
            InventoryBook::where('booking_id', $request->booking_id)->delete();

            $newInventories = [];
            foreach ($data['inventory_id'] as $index => $inventory_id) {
                $quantity = $data['quantity'][$index] ?? 0;

                if (empty($inventory_id) || $quantity <= 0) {
                    \Log::warning("Skipping invalid entry: Inventory ID: {$inventory_id}, Quantity: {$quantity}");
                    continue;
                }

                $newInventories[$inventory_id] = $quantity;

                // Insert new inventory book
                DB::table('inventory_books')->insert([
                    'booking_id' => $request->booking_id,
                    'inventory_id' => $inventory_id,
                    'quantity' => $quantity,
                ]);
            }

            // 3. Compare old vs new and update InventoryRemain
            // Handle removed inventories: restore their quantity
            foreach ($oldBooks as $inventory_id => $book) {
                if (!isset($newInventories[$inventory_id])) {
                    // Inventory was removed, so add back the quantity
                    $remain = InventoryRemain::firstOrNew(['inventory_id' => $inventory_id]);
                    $remain->total_quantity += $book->quantity;
                    $remain->save();
                }
            }

            // Handle new/changed inventories
            foreach ($newInventories as $inventory_id => $newQty) {
                $oldQty = $oldBooks[$inventory_id]->quantity ?? 0;

                // Calculate the difference
                $diff = $newQty - $oldQty;

                if ($diff != 0) {
                    $remain = InventoryRemain::firstOrNew(['inventory_id' => $inventory_id]);
                    $remain->total_quantity = max(0, ($remain->total_quantity ?? 0) - $diff);
                    $remain->save();
                }
            }


            // code for manage inventory here end

                //getting message status 

        //         $this->mobile_notification($request->service_id,auth()->user()->id,$data['due_date']);

        // $message_status = DB::table('company_settings')->select('*')->where('company_id', auth()->user()->company_id)->get();

        // if($message_status['0']->message_permissions == 1){

        //     if($message_status['0']->sms_count<=0){

        //         return redirect('service-bookings')->with('success', 'Booking Updated successfully. Message not sent due to insufficient SMS balance.');

        //     }

        //    $response =  $this->send_message($request->booking_id,'update');

        //    if(isset($response->error)){

        //         return redirect('service-bookings')->with('destroy', 'Booking Updated successfully. Message not sent to customer. Ask bSaloon to check this error: '.$response->error->msg.'.');

        //     }else{

        //         $remaining_sms = $message_status['0']->sms_count-1;

        //         DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update([

        //             'sms_count' => $remaining_sms,

        //         ]);

        //         return redirect('service-bookings')->with('success', 'Booking Updated successfully & Message sent to customer.');

        //     }



        // }

        // else{

        

        return redirect('service-bookings')->with('success', 'Booking Updated successfully');

        // }

    }





    public function get_booking_orders()

    {

        // $start_date = date("Y").'-'.date("m").'-'.'01';
            // Authorization: ensure user can view bookings
            $this->authorize('viewAny', ServiceBooking::class);

            // $start_date = date("Y").'-'.date("m").'-'.'01';

        // $end_date = date("Y").'-'.date("m").'-'.'31';

        $bookings = ServiceBooking::with('customer','user')->where('service_status',"!=",4)->where('company_id', auth()->user()->company_id);

        if (! (auth()->user() && auth()->user()->hasRole('admin'))) {

            $bookings->where('user_id', auth()->user()->id);

        }

        $data = $bookings->orderBy('created_at', 'desc')->get();

        if(count($data) > 0)

        {

            foreach($data as $key=>$value)

            {

              

                $services ='';

                $service_color = '';

                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id']);

                $snames = $servicenames->get();

                // echo "<pre>"; print_r($snames); exit;

                foreach($snames as $service){

                   $services .= $service->name.',';

                   $service_color =  $service->service_color;

                }

                $services = rtrim($services, ',');



                $calender_array[$key] = array(

                    'id' => $value->id,

                    'customer_name' => $value->customer->first_name.' '.$value->customer->last_name,

                    'customer_address'=>$value->customer->address,

                    'customer_zip'=>$value->customer->zip,

                    'user_name' => $value->user->name,

                    'total_price' => $value->total_price,

                    'due_date' => $value->due_date,

                    'due_time' => $value->due_time,

                    'finish_time' => $value->finish_time,

                    'service_status' => $value->service_status,

                    'service_color' => $service_color, 

                    'services' => $services,

                    'comment' =>  $value->comment,

                );

            }

            $holidays = DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->get();
            return response()->json(['events'=>$calender_array,'holidays'=>$holidays]);

           

        } else {

            $calender_array = [];
            $holidays = [];
            return response()->json(['events'=>$calender_array,'holidays'=>$holidays]);

        }

    }



    public function get_user_bookings($user_id)

    {
        // Authorization: ensure user can view bookings
        $this->authorize('viewAny', ServiceBooking::class);



        $bookings = ServiceBooking::with('customer','user')->where('service_status',"!=",4)->where('company_id', auth()->user()->company_id);

        if($user_id){

            $bookings->where('user_id', $user_id);

        }

        $data = $bookings->orderBy('created_at', 'desc')->get();

        if(count($data) > 0)

        {

            foreach($data as $key=>$value)

            {

              

                $services ='';

                $service_color = '';

                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id']);

                $snames = $servicenames->get();

                // echo "<pre>"; print_r($snames); exit;

                foreach($snames as $service){

                   $services .= $service->name.',';

                   $service_color =  $service->service_color;

                }

                $services = rtrim($services, ',');



                $calender_array[$key] = array(

                    'id' => $value->id,

                    'customer_name' => $value->customer->first_name.' '.$value->customer->last_name,

                    'customer_address'=>$value->customer->address,

                    'customer_zip'=>$value->customer->zip,

                    'user_name' => $value->user->name,

                    'total_price' => $value->total_price,

                    'due_date' => $value->due_date,

                    'due_time' => $value->due_time,

                    'finish_time' => $value->finish_time,

                    'service_status' => $value->service_status,

                    'service_color' => $service_color, 

                    'services' => $services,

                    'comment' =>  $value->comment,

                );

            }

            
            $holidays = DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->get();
            return response()->json(['events'=>$calender_array,'holidays'=>$holidays]);
            // return response()->json($calender_array);

        } else {

            $calender_array = [];
            $holidays = [];
            return response()->json(['events'=>$calender_array,'holidays'=>$holidays]);
            // $calender_array = [];
            // return response()->json($calender_array);

        }



    }



    public function fetch_job_notes($booking_id){

        $job_notes = DB::table('booking_notes')->select('id','details','booking_id','image')->where('booking_id', $booking_id)->orderBy('created_at', 'desc')->get();

       

       if(!$job_notes->isEmpty()){

            foreach($job_notes as $notes){

                $jobnotes[] = array(

                    'id' => $notes->id,

                    'details' => $notes->details,

                    'img' => !empty($notes->image) ? asset('notes/'.$notes->image) : asset('notes/noimage.jpg'),

                    'bookingid' => $notes->booking_id

                );

            }

       }

       else{

        $jobnotes="";

       }

        

        echo json_encode($jobnotes);



    }



    public function show_job_detail($booking_id){

        //getting invoice

        $invoice=DB::table('service_bookings')->select('invoice')->where('id',$booking_id)->get();

        foreach($invoice as $data){

          $invoice =  $data->invoice;

        }

        // getting job notes

       

        $bookings = ServiceBooking::with('customer','user')->where('id', $booking_id);

        $data = $bookings->orderBy('created_at', 'desc')->get();

        //echo "<pre>"; print_r($data); exit;   

        if($data)

        {

            foreach($data as $key=>$value)

            {

                //getting all jobs by this customer

                $customerjobs_services = array();

                $alljobs_customer =  ServiceBooking::where('customer_id', $value->customer->id)->get();

                //echo "<pre>"; print_r($alljobs_customer); exit;

                foreach($alljobs_customer as $key=>$customerjobs){

                    $cjobs = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $customerjobs->id)->get();

                    foreach($cjobs as $cservice){

                        $customerjobs_services[$customerjobs->id][]= $cservice->name; 

                    }

                

                }

                //echo "<pre>"; print_r($customerjobs_services); exit;



                $services ='';

                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value->id);

                $snames = $servicenames->get();

                // echo "<pre>"; print_r($snames); exit;

                foreach($snames as $service){

                   $services .= $service->name.','; 

                }

                $services = rtrim($services, ',');



                $booking_detail = array(

                    'id' => $value->id,

                    'customer_name' => $value->customer->first_name.' '.$value->customer->last_name,

                    'customer_address'=>$value->customer->address,

                    'customer_zip'=>$value->customer->zip,

                    'customer_mobile'=>$value->customer->primary_phone,

                    'customer_phone'=>$value->customer->secondary_phone,

                    'customer_email'=>$value->customer->email,

                    'referred_by'=>$value->customer->referred_by,

                    'user_name' => $value->user->name,

                    'total_price' => $value->total_price,

                    'due_date' => $value->due_date,

                    'comments' => $value->comment,

                    'due_time' => $value->due_time,

                    'finish_time' => $value->finish_time,

                    'service_status' => $value->service_status,

                    'payment_instrument' => $value->payment_instrument,

                    'payment_days' => $value->payment_days,

                    'services' => $services,

                );

            }

            

            //echo "<pre>"; print_r($booking_detail); exit;

           

            return view('service_bookings.job_detail',compact('booking_detail','customerjobs_services','alljobs_customer','invoice'));

           

        } else {

            $booking_detail = [];

            return view('service_bookings.job_detail',compact('booking_detail'));

        } 

       

    }



    public function calendar_status(Request $request)

    {

        $invoice = $request->input('invoice');

        $status = '4';        

        $work_flow = DB::table('service_bookings')

                        ->where('invoice', $invoice)

                        ->where('company_id', auth()->user()->company_id)

                        ->update(['service_status' => $status]);





        return response()->json([

            'success'=>'Status changed successfully.'

        ]);

    }



    



    







    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */









   







    /**



     * Store a newly created resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @return \Illuminate\Http\Response



     */



    public function invoice_sms(Request $request){

        $response = $this->check_credits();

        if(isset($response->amount)){

             //Getting proper data like customer name, services name for this invoice to send message

             $booking_detail = ServiceBooking::with('customer','companysetting')->where('id', $request->invoice)->get();

            if($booking_detail[0]->companysetting->sms_count<=0){

                return response()->json(['error' => 'Insufficient Balance to send SMS. Ask bSaloon.']);

            }

            $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $request->invoice)->get()->toArray();

            $service_names = array_column($servicenames, 'name');

            $services = implode (", ", $service_names);

            $company_from = ($booking_detail[0]->companysetting->smart_name=='')? $booking_detail[0]->companysetting->company_name:$booking_detail[0]->companysetting->smart_name;

            $customer = $booking_detail[0]->customer->first_name .' '.$booking_detail[0]->customer->last_name;

            $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

             $body = '{

 

                 "to": "'.$booking_detail[0]->customer->primary_phone.'",

                 "from": "'.$company_from.'",

                 "country_code": "44",

                 "msg": "Dear '.$customer.'\n Total Amount for '.$services.' is £'.$booking_detail[0]->total_price.' against invoice '.$booking_detail[0]->invoice.'-'.$booking_detail[0]->customer->zip.' .Click to view invoice https://app.bSaloon.co.uk/sms-invoice-mail/'.$request->invoice.'/'.$booking_detail[0]->company_id.'",

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
            

            if(isset($result->error)){

                return response()->json(['error' => $result->error->msg]);

            }

            else{

                $remaining_sms = $booking_detail[0]->companysetting->sms_count-1;

                DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update([

                    'sms_count' => $remaining_sms,

                ]);

                return response()->json(['success' => 'SMS invoice sent to customer']);

            }

         }

         else{

            return response()->json(['error' => $response->error->msg]);

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

                "country_code": "44",

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

    /**



     * Display the specified resource.



     *



     * @param  \App\ServiceBooking  $serviceBooking



     * @return \Illuminate\Http\Response



     */



    public function show(ServiceBooking $serviceBooking, $id)



    {
        // Authorize view for this booking
        $this->authorize('view', ServiceBooking::where('id', $id)->where('company_id', auth()->user()->company_id)->firstOrFail());



        $booking_detail = ServiceBooking::with('customer','user')->where('id', $id)->get();

        foreach($booking_detail as $key=>$value)

            {

                $services_names = array();



                $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $value['id'])->get();

                foreach($servicenames as $service){

                    $services_names[$value->id][]= $service->name.','.$service->price; 

                }



            }





        $footer = '';

        $header = '';

        $company_logo = '';



        $company_email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->first();

         

        if(!empty($company_email)){

            $header = $company_email->header;

            $footer = $company_email->footer;



            $company_logo = $company_email->image;

          

        }

        //echo "<pre>"; print_r($booking_detail); exit;

        return view('service_bookings.show',compact('booking_detail', 'footer','header','company_logo','services_names'));



    }







    public function book_status(Request $request)

    {

        

        $invoice        = $request->input('invoice');

        $status         = $request->input('service_status');

        $payment_method = $request->input('payment_method');

        // dd($invoice, $payment_method, $status);

        if($payment_method == 'cheque'){

            $cheque_number = $request->input('cheque_number');

            $work_flow = DB::table('service_bookings')

            ->where('id', $invoice)

            ->update([ 'service_status' => $status, 'payment_instrument' => $payment_method, 'payment_instrument_number' => $cheque_number]);

        }

        else {

            $work_flow = DB::table('service_bookings')

            ->where('id', $invoice)

            ->update(['service_status' => $status,  'payment_instrument' => $payment_method]);

        }

        return response()->json(['success' => 'Status changed successfully']);          

    }



    //code for complete message





    public function send_review_sms($mobile_number)

    {

        

        

        $review = DB::table('company_settings')

        ->select('smart_name','review_url','review_permissions','sms_count')

        ->where('company_id', auth()->user()->company_id)

        ->get();

        $api_key = 'acApZrJKNW89EaYYIj2QRzPGGz2iJz9kSAPssZ26w050RU';

        if($review[0]->sms_count<=0){

            return response()->json(['error' => 'You have insuffiecient sms Balance. Contact bSaloon.']);

        }

        if($review[0]->review_permissions == 1 && $review[0]->review_url!="")

        {

            $response = $this->check_credits();

            if(isset($response->amount)){

                $msg2 = json_encode(

                    [

                        'to' => $mobile_number,

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

                    

                    return response()->json(['error' => $results->error->msg]);

                }else{

                    $remaining_sms = $review[0]->sms_count-1;

                    DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update([

                        'sms_count' => $remaining_sms,

                    ]);

                    return response()->json(['success' => 'Review Message Sent Successfully.']);

                }

               

            }

            else{

                return response()->json(['error' => $response->error->msg]);

            }



        }

        else{

            return response()->json(['error' => 'Please activate your review from settings or URL should not be empty!']);

        }

        



    }

   





    /**



     * Remove the specified resource from storage.



     *



     * @param  \App\ServiceBooking  $serviceBooking



     * @return \Illuminate\Http\Response



     */



     public function destroy(ServiceBooking $serviceBooking, $id)



     {
 
     
 
 
 
         ServiceBooking::where('id', $id)->delete();
 
         BookingDetail::where('booking_id', $id)->delete();
 
 
 
         return back()->with('destroy','Booking deleted successfully');
 
 
 
     }
 






    public function service_price(Request $request)



    {



        $sum_total = 0;



        if($request->service_id){

            $tax_info = DB::table('company_settings')->select('tax_status', 'vat','amount')->where('company_id', auth()->user()->company_id)->get();



            foreach($request->service_id as $ser){



                $price = Services::select('price')->where('company_id', auth()->user()->company_id)->where('id',$ser)->get();

                

                foreach($price as $sum){

                    // $price = 0;

                    // if($tax_info[0]->tax_status==1 && $tax_info[0]->amount!=""){

                    //     if($tax_info[0]->vat==1){

                    //         $price = $sum->price+( $sum->price*$tax_info[0]->amount/100);

                    //     }

                    //     else{

                    //         $price = $sum->price+$tax_info[0]->amount;

                    //     }



                    // }

                    // else{

                    //     $price = $sum->price;

                    // }

                    $sum_total = $sum_total + $sum->price;



                }



            }



        }



        return $sum_total;



    }



    public function update_notes(Request $request){

        $data = array(

            'details' => $request->details,

        );

        $notes_image="";

        if($request->hasFile('img')){



            $originalImage = $request->file('img');

            foreach ($originalImage as $file) {//this statement will loop through all files.

                $file_name = $file->getClientOriginalName(); //Get file original name

                

                $file->move(public_path().'/notes' , $notes_image = 'img_'.Str::random(15).'.'.$file->getClientOriginalExtension()); // move files to destination folder

                $data['image'] = $notes_image;

            }

            

        }

        $result = DB::table('booking_notes')->where('id', $request->id)->update($data);

        if($request->hasFile('img')){

            $image = 'public/notes/'.$notes_image;

        }

        $response[] = array(

            'id' => $request->id,

            'details' => $request->details,

            'img' => $image,

            'bookingid' => $request->booking_id

        );

        echo json_encode($response);

    }





    public function delete_notes($id){

        DB::table('booking_notes')->where('id', $id)->delete();

    }



    public function change_boking_status(Request $request){

        

        $id = $request->id;

        $status = $request->service_status;

    

        $affected = DB::table('service_bookings')->where('id', $id)->update(['service_status' => $status]);

        if($affected){

            return response()->json(['success'=>'Status changed successfully.']);

        }

        else{

            return response()->json(['error'=>'There is an error in Update. Please try again!']);

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



    public function mobile_notification($services, $user_id, $date){

        foreach ($services as $service) {

            $servicess[] = DB::table('services')

                ->select('name')

                ->where('id', $service)

                ->first();

        }

        $service_name = array_column($servicess, 'name');

        $service_name = implode("|", $service_name);



        $firebaseToken = User::select('device_token')

            ->where('id', $user_id)

            ->get();

        $SERVER_API_KEY = 'AAAAgxBV8ro:APA91bEHYf564XZw4oiY3eeZl9T798KSnSagwXlfYSUtZ_MVDXmIMK1uSE0IKCoVdpRZX75N6Z3ygRWbbit5l4OOZkTj7llpGiZbRxFVsS8by0hEOlPfSc_EPgwn7NlgVkRx0KbSBS_H';



        $data = [

            "to" => $firebaseToken[0]->device_token,

            "priority" => "high",

            "notification" => [

                "title" => "bSaloon",

                "body" => $service_name . " Booked On Date. " . date("d-m-Y",strtotime($date)),

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

        //print_r(json_encode($response)); exit;

    }
    public function cancel(Request $request)
{
    $bookingId = $request->input('booking_id');
    $reason = $request->input('reason');

    DB::beginTransaction();
    try {
        $booking = ServiceBooking::findOrFail($bookingId);
        $booking->service_status = 4;
        $booking->save();

        BookingMessage::create([
            'booking_id' => $bookingId,
            'reason' => $reason,
        ]);

        DB::commit();

        return redirect()->route('service-bookings.index')->with('success', 'Booking cancelled successfully.');
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Booking cancelled successfully.'
        // ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    public function getEmployeeServices(Request $request)
    {
        $employee_id = $request->employee_id;

        // Get only services assigned to the selected employee
        $user = User::find($employee_id);
        $services = $user ? $user->services()->where('is_active', 1)->get() : collect();

        return response()->json($services);
    }


    // public function getEmployeeServices(Request $request)
    // {
    //     $employee = User::find($request->employee_id);

    //     if (!$employee) {
    //         return response()->json([]);
    //     }

    //     // Get services grouped by category
    //     $categories = Category::with([
    //         'services' => function ($query) use ($employee) {
    //             $query->whereHas('employees', function ($q) use ($employee) {
    //                 $q->where('user_id', $employee->id);
    //             })->where('is_active', 1)->where('company_id', auth()->user()->company_id);
    //         }
    //     ])->get();

    //     // Get selected services if editing
    //     $selectedServices = ServiceBooking::where('user_id', $employee->id)
    //         ->latest()
    //         ->first()?->services()
    //         ->where('company_id', auth()->user()->company_id)
    //         ->pluck('services.id')
    //         ->toArray() ?? [];

    //     return response()->json([
    //         'categories' => $categories,
    //         'selected_services' => $selectedServices
    //     ]);
    // }

    
    

}



