<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Helpers\LimitHelper;
use App\Imports\CustomersImport;
use App\Models\Setting;
use App\Models\UserSetting;
use App\ServiceBooking;
use App\Services\Customers\CustomerService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display a listing of customers with multi-column filtering and pagination.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int) $request->rows));
            Auth::user()->setSettings(['customers_rows_per_page' => $rows]);
        }
        $rows = Auth::user()->getSetting('customers_rows_per_page', auth()->user()->default_rows ?? 15);

        $customers = $this->customerService->getPaginatedCustomers(auth()->user()->company_id, $request, $rows);

        return view('customers.index', compact('customers', 'rows'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $this->authorize('create', Customer::class);

        $countries = DB::table('countries')->get();

        // Multi-tenant scoped referral sources
        $referreds = DB::table('referred_customers')
            ->where(function ($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->orWhereNull('company_id');
            })
            ->where('is_active', true)
            ->get();

        return view('customers.create', compact('countries', 'referreds'));
    }

    /**
     * Show CSV import view.
     */
    public function customer_import_view()
    {
        return view('customers.csvimport');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);

        if (!LimitHelper::canCreate('customers')) {
            return back()->with('warning', 'You have reached your monthly customer creation limit.');
        }

        $this->validate($request,
            [
                'first_name'    => 'required',
                'last_name'     => 'required',
                'country_code'  => 'required',
                'primary_phone' => 'required',
                'email'         => 'nullable|email',
            ],
            [
                'first_name.required'   => 'Please enter first name',
                'last_name.required'    => 'Please enter last name',
                'country_code.required' => 'Please Select Country Code',
                'primary_phone.required'=> 'Please Enter Phone number',
                'email.nullable'        => 'Please enter Email',
            ]
        );

        $existing = DB::table('customers')
            ->where('company_id', auth()->user()->company_id)
            ->where('primary_phone', '!=', '')
            ->where('primary_phone', $request->input('primary_phone'))
            ->get();

        if ($existing->count() > 0 && $existing[0]->is_deleted == 0) {
            if ($request->exists('now_date') && $request->filled('now_date')) {
                $date = $request->input('now_date');
                return redirect('get-booking-date/' . $date)->with('error', 'Customer already exists with this mobile# ' . $request->input('primary_phone'));
            }

            return back()->with('error', 'Customer already exists with this mobile# ' . $request->input('primary_phone'));
        }

        $customer = $this->customerService->createCustomer(auth()->user()->company_id, $request->all());

        if ($request->exists('now_date') && $request->filled('now_date')) {
            $date = $request->input('now_date');
            return redirect('get-booking-date/' . $date . '/success')->with('success', 'Customer created successfully');
        }

        return back()->with('success', 'Customer created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Return customer details and their booking history via AJAX.
     */
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
            'customer'     => $customer,
            'service_info' => $service_info,
        ]);
    }

    /**
     * Show the form for editing the customer.
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);

        $countries = DB::table('countries')->get();

        // Multi-tenant scoped referral sources
        $referreds = DB::table('referred_customers')
            ->where(function ($q) {
                $q->where('company_id', auth()->user()->company_id)
                  ->orWhereNull('company_id');
            })
            ->where('is_active', true)
            ->get();

        return view('customers.edit', compact('customer', 'countries', 'referreds'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $this->validate($request,
            [
                'last_name' => 'required',
                'address'   => 'required',
            ],
            [
                'last_name.required' => 'Please enter last name',
                'zip_code.required'  => 'Please enter PostCode',
            ]
        );

        $this->customerService->updateCustomer($customer, $request->except('_token', '_method'));

        return back()->with('success', 'Customer updated successfully');
    }

    /**
     * Toggle customer active status.
     */
    public function customerstatus(Request $request)
    {
        $customer = Customer::where('company_id', auth()->user()->company_id)
            ->where('id', $request->id)
            ->firstOrFail();

        $customer->is_active = $request->status;
        $customer->save();

        return response()->json(['success' => 'Status changed successfully.']);
    }

    /**
     * Soft-delete customer.
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $customer->is_deleted = 1;
        $customer->save();

        return back()->with('destroy', 'Customer deleted Successfully');
    }

    /**
     * High-scale chunked CSV export.
     */
    public function exportCsv(Request $request)
    {
        return $this->customerService->streamExportCsv(auth()->user()->company_id, $request);
    }

    /**
     * Customer booking history view with pagination, metrics, and filtering.
     */
    public function bookingsHistory(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;

        $customer = Customer::where('company_id', $companyId)
            ->where('id', $id)
            ->firstOrFail();

        $rows = (int) $request->get('rows', 10);
        if ($rows < 5 || $rows > 500) {
            $rows = 10;
        }

        $query = ServiceBooking::with(['bookingdetail.service', 'user'])
            ->where('company_id', $companyId)
            ->where('customer_id', $id)
            ->where('is_deleted', 0);

        // Status Filter
        if ($request->filled('status')) {
            $query->where('service_status', $request->status);
        }

        // Date Filters
        if ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }

        // Search Term (Invoice / ID / Service Name / Comment)
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('invoice', 'LIKE', $term)
                  ->orWhere('id', 'LIKE', $term)
                  ->orWhere('comment', 'LIKE', $term)
                  ->orWhere('booking_address', 'LIKE', $term)
                  ->orWhereHas('bookingdetail.service', function ($sq) use ($term) {
                      $sq->where('name', 'LIKE', $term);
                  });
            });
        }

        $service_info = $query->orderBy('due_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($rows)
            ->withQueryString();

        // Customer Lifetime Booking Statistics
        $stats = DB::table('service_bookings')
            ->where('company_id', $companyId)
            ->where('customer_id', $id)
            ->where('is_deleted', 0)
            ->selectRaw('
                COUNT(*) as total_bookings,
                COALESCE(SUM(CASE WHEN service_status = 3 THEN total_price ELSE 0 END), 0) as total_spent,
                COALESCE(SUM(CASE WHEN service_status = 3 THEN 1 ELSE 0 END), 0) as completed_bookings,
                COALESCE(SUM(CASE WHEN service_status = 4 THEN 1 ELSE 0 END), 0) as cancelled_bookings
            ')
            ->first();

        $currency = auth()->user()->companySetting->currency_symbol ?? '$';

        return view('customers.booking_history', compact('service_info', 'customer', 'rows', 'stats', 'currency'));
    }

    /**
     * Show Excel import form.
     */
    public function showImportForm()
    {
        return view('customers.import');
    }

    /**
     * Process Excel import with summary.
     */
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

    /**
     * Helper to parse CSV to array.
     */
    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Validate CSV columns.
     */
    public function checkfile($csvdata)
    {
        $data_should_be = ["first_name", "last_name", "primary_phone", "secondary_phone", "email", "zip", "address", "referred_by"];

        if (count($data_should_be) == count($csvdata)) {
            foreach ($data_should_be as $column) {
                if (!array_key_exists($column, $csvdata)) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Legacy CSV import handler.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,txt'
        ], [
            'import_file.required' => 'Please Upload CSV File',
            'import_file.mimes'    => 'Please Upload CSV File'
        ]);

        $file = request()->file('import_file');
        $customerArr = $this->csvToArray($file);

        if (!empty($customerArr) && $this->checkfile($customerArr[0])) {
            for ($i = 0; $i < count($customerArr); $i++) {
                $customerArr[$i]["company_id"] = auth()->user()->company_id;

                if (!empty($customerArr[$i]['address'])) {
                    $coords = $this->customerService->getLatLong($customerArr[$i]['address']);
                    $customerArr[$i]["longitude"] = $coords['long'];
                    $customerArr[$i]["latitude"]  = $coords['lat'];
                }

                Customer::firstOrCreate($customerArr[$i]);
            }

            return back()->with('success', 'Customers imported successfully.');
        }

        return back()->with('destroy', 'Please check file or download example file.');
    }

    /**
     * User profile view.
     */
    public function profile($id)
    {
        if ((int) $id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access to profile.');
        }

        $user = User::where('company_id', auth()->user()->company_id)->where('id', $id)->firstOrFail();

        return view('users.profile', compact('user'));
    }

    /**
     * User profile update.
     */
    public function profile_update(Request $request, $id)
    {
        if ((int) $id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access to profile.');
        }

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:2048',
            ]);

            $result = DB::table('users')->select('image')->where('id', $id)->first();
            if ($result && $result->image) {
                $image_path = public_path() . "/profiles/" . $result->image;
                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
            }

            $originalImage = $request->file('image');
            $user_image = 'img_' . Str::random(15) . '.' . $originalImage->getClientOriginalExtension();
            $originalImage->move(public_path() . '/profiles', $user_image);
            $user->image = $user_image;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        session()->flash('success', 'Your Profile has updated!');

        return back();
    }
}
