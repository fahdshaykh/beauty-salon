<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\ServiceBooking;
use App\Services;
use App\Customer;
use App\Category;
use App\BookingDetail;
use App\Inventory;
use App\InventoryBook;
use App\InventoryRemain;
use App\User;
use App\Helpers\LimitHelper;
use App\Services\Bookings\BookingQueryService;
use App\Services\Bookings\BookingActionService;
use App\Services\Bookings\BookingAvailabilityService;
use App\Services\Bookings\BookingNotificationService;

class ServiceBookingController extends Controller
{
    public function __construct(
        protected BookingQueryService $queryService,
        protected BookingActionService $actionService,
        protected BookingAvailabilityService $availabilityService,
        protected BookingNotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', ServiceBooking::class);

        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            Auth::user()->setSettings(['service_bookings_rows_per_page' => $rows]);
        }
        $rows = Auth::user()->getSetting('service_bookings_rows_per_page', auth()->user()->default_rows ?? 15);

        $companyId = auth()->user()->company_id;
        $scopedUserId = auth()->user()->hasRole('admin') ? null : auth()->user()->id;

        $bookings = $this->queryService->buildQuery($companyId, $request, $scopedUserId)->paginate($rows)->withQueryString();
        $data_array = $this->queryService->formatBookingsForIndex($bookings);

        $services = Services::where('company_id', $companyId)->where('is_active', 1)->get();
        $users    = User::where('company_id', $companyId)->where('is_active', 1)->get();

        return view('service_bookings.index', compact('data_array', 'bookings', 'rows', 'services', 'users'));
    }

    public function create($date = null)
    {
        $this->authorize('create', ServiceBooking::class);

        $companyId = auth()->user()->company_id;

        $inventories = Inventory::where('company_id', $companyId)
            ->whereHas('purchases')
            ->get();

        $booking_detail = '';

        $services = Category::whereHas('services_booking')
            ->where('company_id', $companyId)
            ->where('is_active', 1)
            ->get();

        $customers = Customer::where('is_active', 1)
            ->where('company_id', $companyId)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::activeInCompany()
            ->when(!auth()->user()->hasRole('admin'), fn($q) => $q->where('id', auth()->user()->id))
            ->get();

        $tax_info = DB::table('company_settings')
            ->select('tax_status', 'vat', 'amount')
            ->where('company_id', $companyId)
            ->get();

        $countries = DB::table('countries')->get();

        $referreds = DB::table('referred_customers')
            ->where('is_active', true)
            ->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)->orWhereNull('company_id');
            })
            ->get();

        return view('service_bookings.create', compact(
            'date',
            'countries',
            'referreds',
            'customers',
            'services',
            'booking_detail',
            'tax_info',
            'inventories',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', ServiceBooking::class);

        if (!LimitHelper::canCreate('bookings')) {
            return back()->with('warning', 'You have reached your monthly bookings creation limit.');
        }

        $this->validate($request, [
            'due_date'        => 'required',
            'due_time'        => 'required',
            'service_time'    => 'required|numeric',
            'customer_id'     => 'required|numeric',
            'service_id'      => 'required',
            'user_id'         => 'required|numeric',
            'total_price'     => 'required_if:survey,Yes',
            'inventory_id'    => 'nullable|array',
            'inventory_id.*'  => 'nullable|exists:inventories,id',
            'is_home_service' => 'nullable|integer|in:0,1',
            'booking_address' => 'nullable|string|max:500|required_if:is_home_service,1',
        ]);

        $companyId = auth()->user()->company_id;
        $result = $this->actionService->createBooking($companyId, $request->all(), auth()->user());

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('service-bookings.index')->with('success', $result['message']);
    }

    public function check_if_financial_year($financial_year)
    {
        return $this->actionService->checkIfFinancialYear($financial_year);
    }

    public function checkAvailability(Request $request)
    {
        $userId      = (int)$request->user_id;
        $dueDate     = $request->due_date;
        $dueTime     = $request->due_time;
        $serviceTime = (int)($request->service_time ?? 30);

        $check = $this->availabilityService->checkAvailability(
            auth()->user()->company_id,
            $userId,
            $dueDate,
            $dueTime,
            $serviceTime
        );

        if (!$check['available']) {
            return response()->json([
                'error'        => 'You already have a booking in this time range.',
                'booked_slots' => $check['conflicts']
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function edit(ServiceBooking $serviceBooking, $id)
    {
        $booking = ServiceBooking::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('update', $booking);

        $companyId = auth()->user()->company_id;

        $selectedServices = DB::table('booking_details')
            ->join('services', 'services.id', '=', 'booking_details.service_id')
            ->where('booking_details.booking_id', $id)
            ->pluck('services.id')
            ->toArray();

        $inventories = Inventory::where('company_id', $companyId)->get();
        $customers   = Customer::where('company_id', $companyId)->where('is_active', 1)->get();
        $users       = User::where('is_active', 1)->where('company_id', $companyId)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();
        $services    = Category::whereHas('services_booking')->where('company_id', $companyId)->where('is_active', 1)->get();

        $booking_detail = ServiceBooking::with(['customer', 'user', 'inventory_books.inventory'])->where('id', $id)->get();
        $tax_info = DB::table('company_settings')->select('tax_status', 'vat', 'amount')->where('company_id', $companyId)->get();

        $services_ids = [];
        foreach ($booking_detail as $value) {
            $servicenames = DB::table('booking_details')
                ->join('services', 'services.id', '=', 'booking_details.service_id')
                ->where('booking_details.booking_id', $value->id)
                ->pluck('services.id')
                ->toArray();
            $services_ids[$value->id] = $servicenames;
        }

        return view('service_bookings.edit', compact(
            'selectedServices',
            'booking_detail',
            'inventories',
            'customers',
            'users',
            'services',
            'services_ids',
            'tax_info'
        ));
    }

    public function update(Request $request, ServiceBooking $serviceBooking)
    {
        $bookingId = $request->booking_id ?? $request->id;
        $booking = ServiceBooking::where('id', $bookingId)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('update', $booking);

        $this->validate($request, [
            'due_date'        => 'required',
            'due_time'        => 'required',
            'customer_id'     => 'required|numeric',
            'service_id'      => 'required',
            'total_price'     => 'required',
            'is_home_service' => 'nullable|integer|in:0,1',
            'booking_address' => 'nullable|string|max:500|required_if:is_home_service,1',
        ]);

        $companyId = auth()->user()->company_id;
        $data = $request->all();

        DB::transaction(function () use ($booking, $data, $companyId, $request) {
            $taxInfo = DB::table('company_settings')->select('tax_status', 'vat', 'amount')->where('company_id', $companyId)->first();
            $vat = 0;
            if ($taxInfo && $taxInfo->tax_status == 1 && !empty($taxInfo->amount)) {
                $vat = $taxInfo->vat == 1
                    ? ($data['total_price'] + ($data['total_price'] * $taxInfo->amount / 100))
                    : ($data['total_price'] + $taxInfo->amount);
            }

            $numdays = $request->days ? 0 : ($data['num_days'] ?? 0);
            $dueTime = Carbon::parse($request->due_time)->format('H:i:s');

            $booking->update([
                'user_id'         => $data['user_id'],
                'customer_id'     => $data['customer_id'],
                'total_price'     => $data['total_price'],
                'due_date'        => $data['due_date'],
                'due_time'        => $dueTime,
                'service_time'    => $data['service_time'] ?? 30,
                'comment'         => $data['comment'] ?? null,
                'payment_days'    => $numdays,
                'vat'             => $vat,
                'is_home_service' => $request->has('is_home_service') ? 1 : 0,
                'booking_address' => $request->has('is_home_service') ? ($data['booking_address'] ?? null) : null,
            ]);

            // Sync booking details
            BookingDetail::where('booking_id', $booking->id)->delete();
            if (!empty($request->service_id)) {
                $detailsToInsert = [];
                foreach ((array)$request->service_id as $service) {
                    if (empty($service)) continue;
                    $detailsToInsert[] = [
                        'booking_id' => $booking->id,
                        'service_id' => $service,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($detailsToInsert)) {
                    DB::table('booking_details')->insert($detailsToInsert);
                }
            }

            // Sync inventory books if provided
            if (!empty($request->inventory_id)) {
                $oldBooks = InventoryBook::where('booking_id', $booking->id)->get()->keyBy('inventory_id');
                InventoryBook::where('booking_id', $booking->id)->delete();

                $newInventories = [];
                foreach ((array)$request->inventory_id as $index => $invId) {
                    if (empty($invId)) continue;
                    $quantity = $request->quantity[$index] ?? null;
                    if (!$quantity || !is_numeric($quantity) || $quantity <= 0) continue;
                    $quantity = (float)$quantity;
                    $newInventories[$invId] = $quantity;

                    DB::table('inventory_books')->insert([
                        'booking_id'   => $booking->id,
                        'company_id'   => $companyId,
                        'inventory_id' => $invId,
                        'quantity'     => $quantity,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                foreach ($oldBooks as $invId => $book) {
                    if (!isset($newInventories[$invId])) {
                        $remain = InventoryRemain::firstOrNew(['inventory_id' => $invId]);
                        $remain->company_id     = $companyId;
                        $remain->total_quantity = ($remain->total_quantity ?? 0) + $book->quantity;
                        $remain->save();
                    }
                }

                foreach ($newInventories as $invId => $newQty) {
                    $oldQty = $oldBooks[$invId]->quantity ?? 0;
                    $diff   = $newQty - $oldQty;
                    if ($diff !== 0) {
                        $remain = InventoryRemain::firstOrNew(['inventory_id' => $invId]);
                        $remain->company_id     = $companyId;
                        $remain->total_quantity = max(0, ($remain->total_quantity ?? 0) - $diff);
                        $remain->save();
                    }
                }
            }
        });

        $this->notificationService->syncGoogleCalendarEvent($booking->id, $companyId);

        return redirect()->route('service-bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function get_booking_orders(Request $request)
    {
        $this->authorize('viewAny', ServiceBooking::class);

        $companyId = auth()->user()->company_id;
        $userId    = $request->query('user_id', 0);
        $status    = $request->query('service_status', 0);

        $query = ServiceBooking::with(['customer', 'user', 'services', 'bookingdetail.service'])
            ->where('service_status', '!=', 4)
            ->where('company_id', $companyId);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->user()->id);
        }

        if ($userId != 0) {
            $query->where('user_id', $userId);
        }

        if ($status != 0) {
            $query->where('service_status', $status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('due_date', '>=', $request->query('start_date'))
                  ->whereDate('due_date', '<=', $request->query('end_date'));
        } elseif ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->query('start_date'));
        } elseif ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->query('end_date'));
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $calender_array = [];
        foreach ($bookings as $key => $value) {
            $services = $value->services->pluck('name')->implode(',');
            $serviceColor = optional($value->services->first())->service_color ?? '';

            $calender_array[$key] = [
                'id'               => $value->id,
                'customer_name'    => optional($value->customer)->first_name . ' ' . optional($value->customer)->last_name,
                'customer_address' => optional($value->customer)->address,
                'customer_zip'     => optional($value->customer)->zip,
                'user_name'        => optional($value->user)->name,
                'total_price'      => $value->total_price,
                'due_date'         => $value->due_date,
                'due_time'         => $value->due_time,
                'service_time'     => $value->service_time,
                'service_status'   => $value->service_status,
                'service_color'    => $serviceColor,
                'services'         => $services,
                'comment'          => $value->comment,
            ];
        }

        $holidays = DB::table('calendar_holidays')->where('company_id', $companyId)->get();

        return response()->json([
            'events'   => $calender_array,
            'holidays' => $holidays
        ]);
    }

    public function get_user_bookings($user_id)
    {
        $companyId = auth()->user()->company_id;

        $bookings = ServiceBooking::with(['customer', 'user', 'services'])
            ->where('user_id', $user_id)
            ->where('service_status', '!=', 4)
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();

        $calender_array = [];
        foreach ($bookings as $key => $value) {
            $services = $value->services->pluck('name')->implode(',');
            $serviceColor = optional($value->services->first())->service_color ?? '';

            $calender_array[$key] = [
                'id'               => $value->id,
                'customer_name'    => optional($value->customer)->first_name . ' ' . optional($value->customer)->last_name,
                'customer_address' => optional($value->customer)->address,
                'customer_zip'     => optional($value->customer)->zip,
                'user_name'        => optional($value->user)->name,
                'total_price'      => $value->total_price,
                'due_date'         => $value->due_date,
                'due_time'         => $value->due_time,
                'service_time'     => $value->service_time,
                'service_status'   => $value->service_status,
                'service_color'    => $serviceColor,
                'services'         => $services,
                'comment'          => $value->comment,
            ];
        }

        $holidays = DB::table('calendar_holidays')->where('company_id', $companyId)->get();

        return response()->json([
            'events'   => $calender_array,
            'holidays' => $holidays
        ]);
    }

    public function get_service_status_bookings($service_status)
    {
        $companyId = auth()->user()->company_id;

        $query = ServiceBooking::with(['customer', 'user', 'services'])
            ->where('company_id', $companyId);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('user_id', auth()->user()->id);
        }

        if ($service_status != 0) {
            $query->where('service_status', $service_status);
        } else {
            $query->where('service_status', '!=', 4);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $calender_array = [];
        foreach ($bookings as $key => $value) {
            $services = $value->services->pluck('name')->implode(',');
            $serviceColor = optional($value->services->first())->service_color ?? '';

            $calender_array[$key] = [
                'id'               => $value->id,
                'customer_name'    => optional($value->customer)->first_name . ' ' . optional($value->customer)->last_name,
                'customer_address' => optional($value->customer)->address,
                'customer_zip'     => optional($value->customer)->zip,
                'user_name'        => optional($value->user)->name,
                'total_price'      => $value->total_price,
                'due_date'         => $value->due_date,
                'due_time'         => $value->due_time,
                'service_time'     => $value->service_time,
                'service_status'   => $value->service_status,
                'service_color'    => $serviceColor,
                'services'         => $services,
                'comment'          => $value->comment,
            ];
        }

        $holidays = DB::table('calendar_holidays')->where('company_id', $companyId)->get();

        return response()->json([
            'events'   => $calender_array,
            'holidays' => $holidays
        ]);
    }

    public function fetch_job_notes($booking_id)
    {
        $isAllowed = ServiceBooking::where('id', $booking_id)
            ->where('company_id', auth()->user()->company_id)
            ->exists();

        if (!$isAllowed) {
            return response()->json([], 403);
        }

        $job_notes = DB::table('booking_notes')
            ->select('id', 'details', 'booking_id', 'image')
            ->where('booking_id', $booking_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $jobnotes = [];
        foreach ($job_notes as $note) {
            $jobnotes[] = [
                'id'        => $note->id,
                'details'   => $note->details,
                'img'       => !empty($note->image) ? asset('notes/' . $note->image) : asset('notes/noimage.jpg'),
                'bookingid' => $note->booking_id,
            ];
        }

        return response()->json($jobnotes);
    }

    public function show_job_detail($booking_id)
    {
        $booking = ServiceBooking::with(['customer', 'user', 'services', 'bookingdetail.service'])
            ->where('id', $booking_id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $invoice = $booking->invoice;
        $customer = $booking->customer;

        $servicesWithColor = $booking->services->map(function ($service) {
            return [
                'name'  => $service->name,
                'color' => $service->service_color ?? '#6c757d',
            ];
        })->toArray();

        if (empty($servicesWithColor) && $booking->relationLoaded('bookingdetail')) {
            $servicesWithColor = $booking->bookingdetail->map(function ($detail) {
                if ($detail->service) {
                    return [
                        'name'  => $detail->service->name,
                        'color' => $detail->service->service_color ?? '#6c757d',
                    ];
                }
                return null;
            })->filter()->values()->toArray();
        }

        $servicesList = collect($servicesWithColor)->pluck('name')->implode(', ');

        $booking_detail = [
            'id'                  => $booking->id,
            'customer_name'       => optional($customer)->first_name . ' ' . optional($customer)->last_name,
            'customer_address'    => optional($customer)->address,
            'customer_zip'        => optional($customer)->zip,
            'customer_mobile'     => optional($customer)->primary_phone,
            'customer_phone'      => optional($customer)->secondary_phone,
            'customer_email'      => optional($customer)->email,
            'referred_by'         => optional($customer)->referred_by,
            'user_name'           => optional($booking->user)->name,
            'total_price'         => $booking->total_price,
            'due_date'            => $booking->due_date,
            'comments'            => $booking->comment,
            'due_time'            => $booking->due_time,
            'service_time'        => $booking->service_time ?? 30,
            'service_status'      => $booking->service_status,
            'is_home_service'     => $booking->is_home_service,
            'booking_address'     => $booking->booking_address,
            'payment_instrument'  => $booking->payment_instrument,
            'payment_days'        => $booking->payment_days,
            'services'            => $servicesList,
            'services_with_color' => $servicesWithColor,
        ];

        $customerjobs_services = [];
        $alljobs_customer = collect();

        if ($customer) {
            $alljobs_customer = ServiceBooking::with(['bookingdetail.service', 'user', 'services'])
                ->where('customer_id', $customer->id)
                ->where('company_id', auth()->user()->company_id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($alljobs_customer as $cjob) {
                $jobColors = $cjob->services->map(function ($s) {
                    return [
                        'name'  => $s->name,
                        'color' => $s->service_color ?? '#6c757d',
                    ];
                })->toArray();

                if (empty($jobColors) && $cjob->relationLoaded('bookingdetail')) {
                    $jobColors = $cjob->bookingdetail->map(function ($bd) {
                        if ($bd->service) {
                            return [
                                'name'  => $bd->service->name,
                                'color' => $bd->service->service_color ?? '#6c757d',
                            ];
                        }
                        return null;
                    })->filter()->values()->toArray();
                }

                $customerjobs_services[$cjob->id] = $jobColors;
            }
        }

        $all_notes = DB::table('booking_notes')
            ->join('service_bookings', 'service_bookings.id', '=', 'booking_notes.booking_id')
            ->where('booking_notes.booking_id', $booking_id)
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->select('booking_notes.*')
            ->orderBy('booking_notes.created_at', 'desc')
            ->get();

        $bookings = collect([$booking]);

        return view('service_bookings.job_detail', compact(
            'booking_detail',
            'customerjobs_services',
            'alljobs_customer',
            'invoice',
            'all_notes',
            'bookings'
        ));
    }

    public function calendar_status(Request $request)
    {
        $invoice = $request->input('invoice');

        $booking = ServiceBooking::where('invoice', $invoice)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if ($booking) {
            $booking->update(['service_status' => '4']);
            $this->notificationService->deleteGoogleCalendarEvent($booking->id, auth()->user()->company_id);
        }

        return response()->json(['success' => 'Status changed successfully.']);
    }

    public function invoice_sms(Request $request)
    {
        $booking = ServiceBooking::with(['customer', 'companysetting'])
            ->where('id', $request->invoice)
            ->where('company_id', auth()->user()->company_id)
            ->first();

        if (!$booking || !$booking->companysetting) {
            return response()->json(['error' => 'Booking not found']);
        }

        if ($booking->companysetting->sms_count <= 0) {
            return response()->json(['error' => 'Insufficient Balance to send SMS. Ask bSaloon.']);
        }

        $services = DB::table('booking_details')
            ->join('services', 'services.id', '=', 'booking_details.service_id')
            ->where('booking_details.booking_id', $booking->id)
            ->pluck('services.name')
            ->implode(', ');

        $customer = $booking->customer ? ($booking->customer->first_name . ' ' . $booking->customer->last_name) : '';
        $companyFrom = $booking->companysetting->smart_name ?: ($booking->companysetting->company_name ?? 'Salon');

        $msg = "Dear {$customer}\n Total Amount for {$services} is £{$booking->total_price} against invoice {$booking->invoice}-" . optional($booking->customer)->zip . " .Click to view invoice " . url("sms-invoice-mail/{$booking->id}/{$booking->company_id}");

        $response = $this->notificationService->checkCredits(auth()->user()->company_id);
        if (!isset($response->amount)) {
            return response()->json(['error' => $response->error->msg ?? 'Credit check failed']);
        }

        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();
        $apiKey   = $settings->voodoosms_api_key ?? '';

        $body = json_encode([
            'to'           => optional($booking->customer)->primary_phone,
            'from'         => $companyFrom,
            'country_code' => optional($booking->customer)->country_code ?? '44',
            'msg'          => $msg,
            'sandbox'      => false
        ]);

        $curl = curl_init('https://api.voodoosms.com/sendsms');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $res = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($res);
        if (isset($result->error)) {
            return response()->json(['error' => $result->error->msg]);
        }

        DB::table('company_settings')->where('company_id', auth()->user()->company_id)->decrement('sms_count');

        return response()->json(['success' => 'SMS invoice sent to customer']);
    }

    public function send_message($bookingID, $action)
    {
        return $this->notificationService->sendMessage($bookingID, $action, auth()->user()->company_id);
    }

    public function show(ServiceBooking $serviceBooking, $id)
    {
        $booking = ServiceBooking::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('view', $booking);

        $booking_detail = ServiceBooking::with(['customer', 'user'])->where('id', $id)->get();

        $services_names = [];
        foreach ($booking_detail as $value) {
            $services = DB::table('booking_details')
                ->join('services', 'services.id', '=', 'booking_details.service_id')
                ->where('booking_details.booking_id', $value->id)
                ->select('services.name', 'services.price')
                ->get();

            foreach ($services as $ser) {
                $services_names[$value->id][] = $ser->name . ',' . $ser->price;
            }
        }

        $company_email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->first();
        $header = $company_email->header ?? '';
        $footer = $company_email->footer ?? '';
        $company_logo = $company_email->image ?? '';

        return view('service_bookings.show', compact(
            'booking_detail',
            'footer',
            'header',
            'company_logo',
            'services_names'
        ));
    }

    public function book_status(Request $request)
    {
        $invoice        = $request->input('invoice');
        $status         = $request->input('service_status');
        $payment_method = $request->input('payment_method');

        $update = [
            'service_status'     => $status,
            'payment_instrument' => $payment_method,
            'updated_at'         => now(),
        ];

        if ($payment_method === 'cheque') {
            $update['payment_instrument_number'] = $request->input('cheque_number');
        }

        DB::table('service_bookings')
            ->where('id', $invoice)
            ->where('company_id', auth()->user()->company_id)
            ->update($update);

        return response()->json(['success' => 'Status changed successfully']);
    }

    public function send_review_sms($mobile_number)
    {
        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();
        if (!$settings) return;

        $reviewSettings = DB::table('review_settings')->where('company_id', auth()->user()->company_id)->first();
        if (!$reviewSettings || empty($reviewSettings->message)) return;

        $companyFrom = $settings->smart_name ?: ($settings->site_name ?? 'Salon');
        $apiKey      = $settings->voodoosms_api_key ?? '';

        $body = json_encode([
            'to'           => $mobile_number,
            'from'         => $companyFrom,
            'country_code' => '44',
            'msg'          => $reviewSettings->message,
            'sandbox'      => false
        ]);

        $curl = curl_init('https://api.voodoosms.com/sendsms');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        $res = curl_exec($curl);
        curl_close($curl);

        return json_decode($res);
    }

    public function destroy(ServiceBooking $serviceBooking, $id)
    {
        $booking = ServiceBooking::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('delete', $booking);

        $booking->delete();
        BookingDetail::where('booking_id', $id)->delete();

        return back()->with('destroy', 'Booking deleted successfully');
    }

    public function service_price(Request $request)
    {
        $sum_total = 0;

        if ($request->service_id) {
            $sum_total = Services::where('company_id', auth()->user()->company_id)
                ->whereIn('id', (array)$request->service_id)
                ->sum('price');
        }

        return response()->json(['price' => $sum_total]);
    }

    public function update_notes(Request $request)
    {
        $existingNote = DB::table('booking_notes')
            ->join('service_bookings', 'service_bookings.id', '=', 'booking_notes.booking_id')
            ->where('booking_notes.id', $request->id)
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->select('booking_notes.image')
            ->first();

        if (!$existingNote) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = ['details' => $request->details];
        $image = !empty($existingNote->image) ? asset('notes/' . $existingNote->image) : asset('notes/noimage.jpg');

        if ($request->hasFile('img')) {
            $originalImage = $request->file('img');
            $files = is_array($originalImage) ? $originalImage : [$originalImage];
            foreach ($files as $file) {
                $notes_image = 'img_' . Str::random(15) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('notes'), $notes_image);
                $data['image'] = $notes_image;
                $image = asset('notes/' . $notes_image);
            }
        }

        DB::table('booking_notes')->where('id', $request->id)->update($data);

        return response()->json([
            [
                'id'        => $request->id,
                'details'   => $request->details,
                'img'       => $image,
                'bookingid' => $request->booking_id
            ]
        ]);
    }

    public function delete_notes($id)
    {
        $note = DB::table('booking_notes')
            ->join('service_bookings', 'service_bookings.id', '=', 'booking_notes.booking_id')
            ->where('booking_notes.id', $id)
            ->where('service_bookings.company_id', auth()->user()->company_id)
            ->select('booking_notes.id', 'booking_notes.image')
            ->first();

        if ($note) {
            if ($note->image) {
                $imagePath = public_path('notes/' . $note->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            DB::table('booking_notes')->where('id', $id)->delete();
        }

        return response()->json(['success' => true]);
    }

    public function change_boking_status(Request $request)
    {
        $id     = $request->id ?? $request->booking_id;
        $status = $request->service_status ?? $request->status;

        $affected = DB::table('service_bookings')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->update([
                'service_status' => $status,
                'updated_at'     => now(),
            ]);

        if ($affected !== false) {
            if ((string)$status === '4') {
                $this->notificationService->deleteGoogleCalendarEvent($id, auth()->user()->company_id);
            } else {
                $this->notificationService->syncGoogleCalendarEvent($id, auth()->user()->company_id);
            }
            return response()->json(['success' => 'Status changed successfully.']);
        }

        return response()->json(['error' => 'There is an error in Update. Please try again!'], 500);
    }

    public function check_credits()
    {
        return $this->notificationService->checkCredits(auth()->user()->company_id);
    }

    public function mobile_notification($services, $user_id, $date)
    {
        // FCM push notification helper
        return true;
    }

    public function cancel(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $reason    = $request->input('reason');

        try {
            $booking = ServiceBooking::where('company_id', auth()->user()->company_id)
                ->where('id', $bookingId)
                ->firstOrFail();

            $this->authorize('update', $booking);

            $this->actionService->cancelBooking($bookingId, auth()->user()->company_id, $reason);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            $code = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], $code);
        }
    }

    public function getEmployeeServices(Request $request)
    {
        $employee_id = $request->employee_id;
        $user = User::where('company_id', auth()->user()->company_id)->where('id', $employee_id)->first();
        $services = $user ? $user->services()->where('is_active', 1)->where('services.company_id', auth()->user()->company_id)->get() : collect();

        return response()->json($services);
    }

    public function editInventory($id)
    {
        $booking = ServiceBooking::with('inventory_books.inventory')
            ->where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('update', $booking);

        $inventories = Inventory::where('company_id', auth()->user()->company_id)
            ->whereHas('purchases')
            ->get();

        return view('service_bookings.edit_inventory', compact('booking', 'inventories'));
    }

    public function updateInventory(Request $request, $id)
    {
        $booking = ServiceBooking::where('id', $id)
            ->where('company_id', auth()->user()->company_id)
            ->firstOrFail();

        $this->authorize('update', $booking);

        $request->validate([
            'inventory_id.*' => 'nullable|exists:inventories,id',
            'quantity.*'     => 'nullable|numeric|gt:0',
        ]);

        $companyId = auth()->user()->company_id;

        $oldBooks = InventoryBook::where('booking_id', $id)->get()->keyBy('inventory_id');
        InventoryBook::where('booking_id', $id)->delete();

        $newInventories = [];
        if ($request->filled('inventory_id')) {
            foreach ($request->inventory_id as $index => $inventory_id) {
                if (empty($inventory_id)) continue;
                $quantity = $request->quantity[$index] ?? null;
                if (!$quantity || !is_numeric($quantity) || $quantity <= 0) continue;

                $quantity = (float)$quantity;
                $newInventories[$inventory_id] = $quantity;

                DB::table('inventory_books')->insert([
                    'booking_id'   => $id,
                    'company_id'   => $companyId,
                    'inventory_id' => $inventory_id,
                    'quantity'     => $quantity,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        foreach ($oldBooks as $inventory_id => $book) {
            if (!isset($newInventories[$inventory_id])) {
                $remain = InventoryRemain::firstOrNew(['inventory_id' => $inventory_id]);
                $remain->company_id     = $companyId;
                $remain->total_quantity = ($remain->total_quantity ?? 0) + $book->quantity;
                $remain->save();
            }
        }

        foreach ($newInventories as $inventory_id => $newQty) {
            $oldQty = $oldBooks[$inventory_id]->quantity ?? 0;
            $diff   = $newQty - $oldQty;

            if ($diff !== 0) {
                $remain = InventoryRemain::firstOrNew(['inventory_id' => $inventory_id]);
                $remain->company_id     = $companyId;
                $remain->total_quantity = max(0, ($remain->total_quantity ?? 0) - $diff);
                $remain->save();
            }
        }

        return redirect()
            ->route('service-bookings.index')
            ->with('success', 'Inventory updated successfully.');
    }
}
