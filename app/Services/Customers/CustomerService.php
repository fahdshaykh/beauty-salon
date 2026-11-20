<?php

namespace App\Services\Customers;

use App\Customer;
use App\Helpers\LimitHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    /**
     * Build filtered and paginated customer query.
     */
    public function getPaginatedCustomers(int $companyId, Request $request, int $rows)
    {
        $query = Customer::where('company_id', $companyId)
            ->where('is_deleted', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhere('primary_phone', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('referred_by', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $customers = $query->latest('created_at')
            ->paginate($rows)
            ->withQueryString();

        $customers->getCollection()->transform(function ($customer) {
            return $customer->makeHidden(['is_active']);
        });

        return $customers;
    }

    /**
     * Resolve latitude and longitude from an address via Google Geocoding API.
     */
    public function getLatLong(?string $address): array
    {
        if (empty($address)) {
            return ['lat' => 0, 'long' => 0];
        }

        $prepAddr = str_replace(' ', '+', $address);
        $apiKey   = config('app.google_maps_api_key', '');

        try {
            $geocode = @file_get_contents("https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key={$apiKey}");
            $output  = json_decode($geocode);

            if ($output && !empty($output->results[0]->geometry->location)) {
                return [
                    'lat'  => $output->results[0]->geometry->location->lat,
                    'long' => $output->results[0]->geometry->location->lng,
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed for address: ' . $address . ' - ' . $e->getMessage());
        }

        return ['lat' => 0, 'long' => 0];
    }

    /**
     * Create a new customer record.
     */
    public function createCustomer(int $companyId, array $data): Customer
    {
        $coordinates = $this->getLatLong($data['address'] ?? '');

        $allowed = [
            'first_name',
            'last_name',
            'country_code',
            'primary_phone',
            'secondary_phone',
            'email',
            'date_of_birth',
            'zip',
            'address',
            'private_notes',
            'referred_by',
        ];

        $payload = array_intersect_key($data, array_flip($allowed));
        $payload['company_id']           = $companyId;
        $payload['is_active']            = $data['is_active'] ?? 1;
        $payload['is_deleted']           = 0;
        $payload['show_booking_history'] = !empty($data['show_booking_history']) ? 1 : 0; //isset($data['show_booking_history']) ? (!empty($data['show_booking_history']) ? 1 : 0) : 1;
        $payload['latitude']             = $coordinates['lat'] ?? 0;
        $payload['longitude']            = $coordinates['long'] ?? 0;

        $customer = Customer::create($payload);

        LimitHelper::increment('customers');

        return $customer;
    }

    /**
     * Update an existing customer record.
     */
    public function updateCustomer(Customer $customer, array $data): bool
    {
        $allowed = [
            'first_name',
            'last_name',
            'country_code',
            'primary_phone',
            'secondary_phone',
            'email',
            'date_of_birth',
            'zip',
            'address',
            'private_notes',
            'referred_by',
        ];

        $payload = array_intersect_key($data, array_flip($allowed));
        $payload['show_booking_history'] = !empty($data['show_booking_history']) ? 1 : 0;

        // Recompute coordinates if address changed
        if (!empty($data['address']) && $data['address'] !== $customer->address) {
            $coords = $this->getLatLong($data['address']);
            $payload['latitude']  = $coords['lat'] ?? 0;
            $payload['longitude'] = $coords['long'] ?? 0;
        }

        return $customer->update($payload);
    }

    /**
     * Stream CSV export directly to output in chunks.
     * Prevents out-of-memory errors for millions of customer records.
     */
    public function streamExportCsv(int $companyId, Request $request)
    {
        $fileName = date('d-m-Y') . '-Customers.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $query = Customer::where('company_id', $companyId)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        }

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'First Name',
                'Last Name',
                'Country Code',
                'Primary Phone',
                'Secondary Phone',
                'Email',
                'Address',
                'Zip',
                'Date Of Birth',
                'Referred By',
                'Registration Date',
            ]);

            $query->chunk(500, function ($customers) use ($handle) {
                foreach ($customers as $c) {
                    fputcsv($handle, [
                        $c->id,
                        $c->first_name,
                        $c->last_name,
                        $c->country_code,
                        $c->primary_phone,
                        $c->secondary_phone,
                        $c->email,
                        $c->address,
                        $c->zip,
                        $c->date_of_birth,
                        $c->referred_by,
                        $c->created_at ? $c->created_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
