<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\ServiceBooking;
use App\Services\Bookings\BookingAvailabilityService;
use App\Services\Bookings\BookingActionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookingServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected BookingAvailabilityService $availabilityService;
    protected BookingActionService $actionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityService = app(BookingAvailabilityService::class);
        $this->actionService       = app(BookingActionService::class);
        ServiceBooking::where('due_date', '2099-05-20')->delete();
    }

    protected function tearDown(): void
    {
        ServiceBooking::where('due_date', '2099-05-20')->delete();
        parent::tearDown();
    }

    public function test_booking_availability_detects_time_conflicts_correctly()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        // Use a far-future date guaranteed to be empty
        $dueDate = '2099-05-20';
        $dueTime = '14:00:00';

        // 1. Check availability on an open slot
        $initialCheck = $this->availabilityService->checkAvailability(
            $user->company_id,
            $user->id,
            $dueDate,
            $dueTime,
            60
        );
        $this->assertTrue($initialCheck['available']);

        // 2. Create a test booking at that slot
        $booking = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => Customer::where('company_id', $user->company_id)->value('id') ?? 1,
            'service_status' => 1,
            'due_date'       => $dueDate,
            'due_time'       => $dueTime,
            'service_time'   => 60,
            'total_price'    => 100,
        ]);

        // 3. Overlapping slot check (e.g. 14:30) must return false
        $overlapCheck = $this->availabilityService->checkAvailability(
            $user->company_id,
            $user->id,
            $dueDate,
            '14:30:00',
            30
        );
        $this->assertFalse($overlapCheck['available']);
        $this->assertCount(1, $overlapCheck['conflicts']);

        // 4. Non-overlapping slot check (e.g. 16:00) must return true
        $freeSlotCheck = $this->availabilityService->checkAvailability(
            $user->company_id,
            $user->id,
            $dueDate,
            '16:00:00',
            30
        );
        $this->assertTrue($freeSlotCheck['available']);
    }

    public function test_check_if_financial_year_evaluates_dates_correctly()
    {
        $today = date('Y-m-d');
        $this->assertTrue($this->actionService->checkIfFinancialYear($today));
        $this->assertFalse($this->actionService->checkIfFinancialYear(null));
        $this->assertFalse($this->actionService->checkIfFinancialYear(''));
    }

    public function test_single_booking_with_multiple_services_displays_names_and_detects_overdue_without_extra_bookings()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        $queryService = app(\App\Services\Bookings\BookingQueryService::class);

        // Count existing bookings before test
        $initialCount = ServiceBooking::where('company_id', $user->company_id)->count();

        // 1. Create a single booking in In-Progress status (2) with scheduled time in past
        $booking = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => Customer::where('company_id', $user->company_id)->value('id') ?? 1,
            'service_status' => 2, // In-Progress
            'due_date'       => now()->subHours(2)->format('Y-m-d'),
            'due_time'       => now()->subHours(2)->format('H:i:s'),
            'service_time'   => 30, // 30 minutes duration, elapsed 2 hours ago -> overdue!
            'total_price'    => 100,
        ]);

        try {
            // 2. Attach two services in the relational table booking_details
            $serviceA = \App\Services::where('company_id', $user->company_id)->first();
            $serviceB = \App\Services::where('company_id', $user->company_id)->skip(1)->first();

            if ($serviceA) {
                \App\BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $serviceA->id,
                ]);
            }
            if ($serviceB) {
                \App\BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $serviceB->id,
                ]);
            }

            \Illuminate\Support\Facades\DB::table('service_bookings')
                ->where('id', $booking->id)
                ->update([
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ]);
            $booking->refresh();

            // 3. Query and format bookings using the query service
            $booking->load(['services', 'bookingdetail.service', 'customer', 'user']);
            $formatted = $queryService->formatBookingsForIndex([$booking]);

            // 4. Assert only 1 booking formatted (not duplicated)
            $this->assertCount(1, $formatted);
            $firstItem = $formatted[0];

            // 5. Assert services are displayed
            $this->assertNotEmpty($firstItem['services']);

            // 6. Assert overdue alert is flagged
            $this->assertTrue($firstItem['is_overdue']);
            $this->assertGreaterThan(0, $firstItem['overdue_minutes']);

            // 7. Verify NO extra bookings were created in service_bookings table
            $finalCount = ServiceBooking::where('company_id', $user->company_id)->count();
            $this->assertEquals($initialCount + 1, $finalCount);
        } finally {
            \App\BookingDetail::where('booking_id', $booking->id)->delete();
            $booking->delete();
        }
    }

    public function test_show_job_detail_renders_successfully_with_booking_details()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        $service = \App\Services::where('company_id', $user->company_id)->first();
        if (!$service) {
            $service = \App\Services::create([
                'company_id'    => $user->company_id,
                'name'          => 'Color Test Service',
                'price'         => 35,
                'service_time'  => 30,
                'service_color' => '#845ef7',
            ]);
        }

        $booking = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => Customer::where('company_id', $user->company_id)->value('id') ?? 1,
            'service_status' => 1,
            'due_date'       => date('Y-m-d'),
            'due_time'       => '12:00:00',
            'service_time'   => 30,
            'total_price'    => 50,
        ]);

        try {
            \App\BookingDetail::create([
                'booking_id' => $booking->id,
                'service_id' => $service->id,
            ]);

            $response = $this->actingAs($user)->get("/show-job-detail/{$booking->id}");
            $response->assertStatus(200);
            $response->assertViewHas('booking_detail');
            $response->assertViewHas('alljobs_customer');
            $response->assertSee(date('d-m-Y', strtotime($booking->due_date)));
            $response->assertSee('badge-pill');
            $response->assertSee($service->name);
            if (!empty($service->service_color)) {
                $response->assertSee($service->service_color);
            }
        } finally {
            \App\BookingDetail::where('booking_id', $booking->id)->delete();
            $booking->delete();
        }
    }

    public function test_fetch_job_notes_returns_json_array_for_jsgrid()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        $booking = ServiceBooking::where('company_id', $user->company_id)->first();
        $isTempBooking = false;
        if (!$booking) {
            $booking = ServiceBooking::create([
                'company_id'     => $user->company_id,
                'user_id'        => $user->id,
                'customer_id'    => Customer::where('company_id', $user->company_id)->value('id') ?? 1,
                'service_status' => 1,
                'due_date'       => date('Y-m-d'),
                'due_time'       => '12:00:00',
                'service_time'   => 30,
                'total_price'    => 50,
            ]);
            $isTempBooking = true;
        }

        // Insert a test note
        $noteId = \Illuminate\Support\Facades\DB::table('booking_notes')->insertGetId([
            'booking_id' => $booking->id,
            'details'    => 'Test Job Note Content',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $response = $this->actingAs($user)->getJson("/fetch-job-notes/{$booking->id}");
            $response->assertStatus(200);

            $data = $response->json();
            $this->assertIsArray($data);
            $this->assertNotEmpty($data);
            $this->assertEquals('Test Job Note Content', $data[0]['details']);
            $this->assertArrayHasKey('id', $data[0]);
            $this->assertArrayHasKey('img', $data[0]);
            $this->assertArrayHasKey('bookingid', $data[0]);
        } finally {
            \Illuminate\Support\Facades\DB::table('booking_notes')->where('id', $noteId)->delete();
            if ($isTempBooking) {
                $booking->delete();
            }
        }
    }
}
