<?php

namespace App\Services;

use App\Models\BookingPageSetting;
use App\Models\BookingAutomation;
use App\Company;
use App\Category;
use App\Services as SalonService;
use App\User;
use App\Customer;
use App\ServiceBooking;
use App\BookingDetail;
use App\CompanySetting;
use App\Services\Bookings\BookingAvailabilityService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicBookingService
{
    protected $availabilityService;
    protected $automationService;

    public function __construct(
        BookingAvailabilityService $availabilityService,
        BookingAutomationService $automationService
    ) {
        $this->availabilityService = $availabilityService;
        $this->automationService   = $automationService;
    }

    /**
     * Resolve public booking page data by slug.
     */
    public function resolvePageData(string $slug): ?array
    {
        $setting = BookingPageSetting::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$setting) {
            return null;
        }

        $companyId = $setting->company_id;
        $company   = Company::find($companyId);

        if (!$company || !$company->is_active || $company->is_deleted) {
            return null;
        }

        // Active categories with services
        $categories = Category::where('company_id', $companyId)
            ->where('is_active', 1)
            ->with(['services_booking' => function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            }])
            ->get();

        // Active staff / technicians
        $technicians = User::where('company_id', $companyId)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->select('id', 'name', 'phone', 'email', 'image', 'position')
            ->get();

        // Company Settings for tax, currency, and payments
        $companySetting = DB::table('company_settings')->where('company_id', $companyId)->first();

        $services = $categories->pluck('services_booking')->flatten();

        return [
            'setting'         => $setting,
            'company'         => $company,
            'categories'      => $categories,
            'services'        => $services,
            'technicians'     => $technicians,
            'companySetting'  => $companySetting,
        ];
    }

    /**
     * Compute open real-time slots for a given date and service duration.
     */
    public function getAvailableSlots(int $companyId, string $date, ?int $userId = null, int $durationMinutes = 30): array
    {
        $targetDate = Carbon::parse($date)->startOfDay();
        $today = now()->startOfDay();

        if ($targetDate->lt($today)) {
            return [];
        }

        // Standard working hours: 09:00 AM to 08:00 PM (30 min increments)
        $startHour = 9;
        $endHour   = 20;

        $slots = [];
        $now = now();

        $activeStaff = User::where('company_id', $companyId)
            ->where('is_active', 1)
            ->where('is_deleted', 0)
            ->pluck('id')
            ->toArray();

        if (empty($activeStaff)) {
            return [];
        }

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            foreach ([0, 30] as $minute) {
                $slotTime = sprintf('%02d:%02d:00', $hour, $minute);
                $slotCarbon = Carbon::parse($date . ' ' . $slotTime);

                // Skip past times if today
                if ($targetDate->equalTo($today) && $slotCarbon->lte($now)) {
                    continue;
                }

                $timeLabel = $slotCarbon->format('h:i A');

                if ($userId && in_array($userId, $activeStaff)) {
                    // Check specific technician
                    $check = $this->availabilityService->checkAvailability(
                        $companyId,
                        $userId,
                        $date,
                        $slotTime,
                        $durationMinutes
                    );
                    $isAvailable = $check['available'];
                } else {
                    // Check if AT LEAST ONE technician is available
                    $isAvailable = false;
                    foreach ($activeStaff as $staffId) {
                        $check = $this->availabilityService->checkAvailability(
                            $companyId,
                            $staffId,
                            $date,
                            $slotTime,
                            $durationMinutes
                        );
                        if ($check['available']) {
                            $isAvailable = true;
                            break;
                        }
                    }
                }

                if ($isAvailable) {
                    $slots[] = [
                        'time_24'   => $slotCarbon->format('H:i'),
                        'time_12'   => $timeLabel,
                        'available' => true,
                    ];
                }
            }
        }

        return $slots;
    }

    /**
     * Store a public booking, auto-resolving customer and assigning technician.
     */
    public function storePublicBooking(int $companyId, array $data): array
    {
        $services = SalonService::whereIn('id', (array)$data['service_ids'])
            ->where('company_id', $companyId)
            ->get();

        if ($services->isEmpty()) {
            return ['success' => false, 'message' => 'Please select at least one valid service.'];
        }

        $totalPrice = (float) $services->sum('price');
        $totalDuration = (int) $services->sum('duration');
        if ($totalDuration <= 0) {
            $totalDuration = 30;
        }

        $dueDate = $data['due_date'];
        $dueTime = Carbon::parse($data['due_time'])->format('H:i:s');

        // 1. Assign Technician
        $userId = !empty($data['user_id']) ? (int)$data['user_id'] : null;
        if (!$userId) {
            // Find first available technician
            $technicians = User::where('company_id', $companyId)
                ->where('is_active', 1)
                ->where('is_deleted', 0)
                ->get();

            foreach ($technicians as $tech) {
                $check = $this->availabilityService->checkAvailability(
                    $companyId,
                    $tech->id,
                    $dueDate,
                    $dueTime,
                    $totalDuration
                );
                if ($check['available']) {
                    $userId = $tech->id;
                    break;
                }
            }

            if (!$userId && $technicians->isNotEmpty()) {
                $userId = $technicians->first()->id;
            }
        }

        // Verify slot availability
        $check = $this->availabilityService->checkAvailability(
            $companyId,
            $userId,
            $dueDate,
            $dueTime,
            $totalDuration
        );

        if (!$check['available']) {
            return [
                'success' => false,
                'message' => 'The selected time slot is no longer available. Please pick another time.'
            ];
        }

        // 2. Resolve / Create Customer
        $customer = $this->resolveOrCreateCustomer($companyId, $data);

        // 3. Create Booking Transactionally
        $booking = DB::transaction(function () use (
            $companyId,
            $customer,
            $userId,
            $dueDate,
            $dueTime,
            $totalDuration,
            $totalPrice,
            $services,
            $data
        ) {
            // Thread-safe invoice generation
            $lastInvoice = ServiceBooking::where('company_id', $companyId)
                ->whereNotNull('invoice')
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->value('invoice');
            $nextInvoice = ($lastInvoice ? (int)$lastInvoice : 0) + 1;

            $isHomeService  = !empty($data['is_home_service']) ? 1 : 0;
            $bookingAddress = $isHomeService ? ($data['booking_address'] ?? $customer->address) : null;

            $booking = ServiceBooking::create([
                'company_id'      => $companyId,
                'customer_id'     => $customer->id,
                'user_id'         => $userId,
                'invoice'         => $nextInvoice,
                'due_date'        => $dueDate,
                'due_time'        => $dueTime,
                'service_time'    => $totalDuration,
                'total_price'     => $totalPrice,
                'comment'         => $data['notes'] ?? 'Public Online Booking',
                'service_status'  => 0, // 0 = Pending/Confirmed based on review
                'is_home_service' => $isHomeService,
                'booking_address' => $bookingAddress,
            ]);

            // Attach services to booking_details
            foreach ($services as $srv) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $srv->id,
                ]);
            }

            return $booking;
        });

        // 4. Trigger Instant Confirmation Automation
        $this->automationService->trigger('booking_confirmed', $booking);

        return [
            'success'      => true,
            'booking'      => $booking,
            'invoice'      => $booking->invoice,
            'redirect_url' => url('/book/' . ($data['company_slug'] ?? '') . '/confirmed/' . $booking->invoice),
            'message'      => 'Your appointment has been successfully booked!',
        ];
    }

    /**
     * Look up existing customer by phone or email, or create new.
     */
    protected function resolveOrCreateCustomer(int $companyId, array $data): Customer
    {
        $phone = trim($data['customer_phone'] ?? '');
        $email = trim($data['customer_email'] ?? '');
        $firstName = trim($data['first_name'] ?? '');
        $lastName  = trim($data['last_name'] ?? '');
        $address   = trim($data['booking_address'] ?? '');

        $customer = Customer::where('company_id', $companyId)
            ->where(function ($q) use ($phone, $email) {
                if (!empty($phone)) {
                    $q->where('primary_phone', $phone);
                }
                if (!empty($email)) {
                    $q->orWhere('email', $email);
                }
            })
            ->first();

        if ($customer) {
            // Update missing fields
            $updates = [];
            if (empty($customer->email) && !empty($email)) $updates['email'] = $email;
            if (empty($customer->address) && !empty($address)) $updates['address'] = $address;
            if (!empty($updates)) {
                $customer->update($updates);
            }
            return $customer;
        }

        return Customer::create([
            'company_id'    => $companyId,
            'first_name'    => $firstName ?: 'Client',
            'last_name'     => $lastName,
            'primary_phone' => $phone,
            'email'         => $email,
            'address'       => $address,
            'is_active'     => 1,
        ]);
    }
}
