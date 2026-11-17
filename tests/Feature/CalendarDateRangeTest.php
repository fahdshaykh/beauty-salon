<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\ServiceBooking;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalendarDateRangeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        ServiceBooking::whereIn('due_date', ['2099-01-10', '2099-01-20', '2099-01-30'])->delete();
    }

    protected function tearDown(): void
    {
        ServiceBooking::whereIn('due_date', ['2099-01-10', '2099-01-20', '2099-01-30'])->delete();
        parent::tearDown();
    }

    protected function getAuthorizedUser(): User
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found.');
        }

        setPermissionsTeamId($user->company_id);

        if (!$user->can('view bookings')) {
            $user->givePermissionTo('view bookings');
        }

        return $user;
    }

    public function test_calendar_get_booking_orders_filters_by_date_range()
    {
        $user = $this->getAuthorizedUser();
        $customer = Customer::where('company_id', $user->company_id)->first() ?? Customer::first();

        // Create 3 bookings on different dates
        $booking1 = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => $customer->id,
            'due_date'       => '2099-01-10',
            'due_time'       => '10:00 AM',
            'service_time'   => 30,
            'total_price'    => 50,
            'service_status' => 1,
        ]);

        $booking2 = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => $customer->id,
            'due_date'       => '2099-01-20',
            'due_time'       => '11:00 AM',
            'service_time'   => 30,
            'total_price'    => 75,
            'service_status' => 1,
        ]);

        $booking3 = ServiceBooking::create([
            'company_id'     => $user->company_id,
            'user_id'        => $user->id,
            'customer_id'    => $customer->id,
            'due_date'       => '2099-01-30',
            'due_time'       => '12:00 PM',
            'service_time'   => 30,
            'total_price'    => 100,
            'service_status' => 1,
        ]);

        // 1. Query for date range covering only booking2 (2099-01-15 to 2099-01-25)
        $response = $this->actingAs($user)
            ->getJson(route('service-bookings.orders', [
                'start_date' => '2099-01-15',
                'end_date'   => '2099-01-25',
            ]));

        $response->assertStatus(200);
        $eventIds = collect($response->json('events'))->pluck('id')->all();

        $this->assertNotContains($booking1->id, $eventIds);
        $this->assertContains($booking2->id, $eventIds);
        $this->assertNotContains($booking3->id, $eventIds);

        // 2. Query for date range covering booking1 and booking2 (2099-01-01 to 2099-01-25)
        $responseWide = $this->actingAs($user)
            ->getJson(route('service-bookings.orders', [
                'start_date' => '2099-01-01',
                'end_date'   => '2099-01-25',
            ]));

        $wideIds = collect($responseWide->json('events'))->pluck('id')->all();
        $this->assertContains($booking1->id, $wideIds);
        $this->assertContains($booking2->id, $wideIds);
        $this->assertNotContains($booking3->id, $wideIds);

        // 3. Query with only start_date (>= 2099-01-20)
        $responseFrom = $this->actingAs($user)
            ->getJson(route('service-bookings.orders', [
                'start_date' => '2099-01-20',
            ]));

        $fromIds = collect($responseFrom->json('events'))->pluck('id')->all();
        $this->assertNotContains($booking1->id, $fromIds);
        $this->assertContains($booking2->id, $fromIds);
        $this->assertContains($booking3->id, $fromIds);

        // 4. Query with only end_date (<= 2099-01-15)
        $responseTo = $this->actingAs($user)
            ->getJson(route('service-bookings.orders', [
                'end_date' => '2099-01-15',
            ]));

        $toIds = collect($responseTo->json('events'))->pluck('id')->all();
        $this->assertContains($booking1->id, $toIds);
        $this->assertNotContains($booking2->id, $toIds);
        $this->assertNotContains($booking3->id, $toIds);
    }

    public function test_calendar_view_renders_date_pickers()
    {
        $user = $this->getAuthorizedUser();

        $response = $this->actingAs($user)->get(route('calendar'));
        $response->assertStatus(200);
        $response->assertSee('calendar_start_date');
        $response->assertSee('calendar_end_date');
        $response->assertSee('clear_calendar_dates');
        $response->assertSee('From Date');
        $response->assertSee('To Date');
    }
}
