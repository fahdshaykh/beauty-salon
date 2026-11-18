<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\ServiceBooking;
use App\Services\Reports\BookingReportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyUsersReportFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected BookingReportService $reportService;
    protected User $userA;
    protected User $userB;
    protected Customer $customer;
    protected ServiceBooking $bookingA;
    protected ServiceBooking $bookingB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = app(BookingReportService::class);

        $this->userA = User::first() ?? User::create([
            'name'       => 'Staff User Alpha',
            'email'      => 'alpha_user@example.com',
            'password'   => bcrypt('password'),
            'company_id' => 1,
            'is_active'  => 1,
        ]);

        $this->userB = User::create([
            'name'       => 'Staff User Beta',
            'email'      => 'beta_user_' . uniqid() . '@example.com',
            'password'   => bcrypt('password'),
            'company_id' => $this->userA->company_id,
            'is_active'  => 1,
        ]);

        $this->customer = Customer::create([
            'company_id'    => $this->userA->company_id,
            'first_name'    => 'Report',
            'last_name'     => 'Client',
            'primary_phone' => '07111222333',
            'is_active'     => 1,
        ]);

        $this->bookingA = ServiceBooking::create([
            'company_id'     => $this->userA->company_id,
            'user_id'        => $this->userA->id,
            'customer_id'    => $this->customer->id,
            'invoice'        => 'INV-REP-ALPHA',
            'service_status' => 3,
            'due_date'       => '2099-12-01',
            'due_time'       => '11:00:00',
            'service_time'   => 30,
            'total_price'    => 50.00,
        ]);

        $this->bookingB = ServiceBooking::create([
            'company_id'     => $this->userA->company_id,
            'user_id'        => $this->userB->id,
            'customer_id'    => $this->customer->id,
            'invoice'        => 'INV-REP-BETA',
            'service_status' => 3,
            'due_date'       => '2099-12-01',
            'due_time'       => '12:00:00',
            'service_time'   => 30,
            'total_price'    => 80.00,
        ]);
    }

    public function test_reports_index_view_renders_company_users_filter()
    {
        $response = $this->actingAs($this->userA)->get(route('reports.index'));
        $response->assertStatus(200);

        // Check for User filter select and "All Users" option
        $response->assertSee('name="user_id"', false);
        $response->assertSee('id="user_id"', false);
        $response->assertSee('All Users');

        // Check that company users are listed in the select
        $response->assertSee($this->userA->name);
        $response->assertSee($this->userB->name);
    }

    public function test_booking_report_service_filters_by_company_user()
    {
        // Filter by userA
        $filtersA = [
            'start_date' => '2099-12-01',
            'end_date'   => '2099-12-01',
            'user_id'    => $this->userA->id,
        ];
        $reportDataA = $this->reportService->getReportData($this->userA->company_id, $filtersA);

        $this->assertNotEmpty($reportDataA);
        foreach ($reportDataA as $row) {
            $this->assertEquals($this->userA->name, $row['user_name']);
        }

        // Filter by userB
        $filtersB = [
            'start_date' => '2099-12-01',
            'end_date'   => '2099-12-01',
            'user_id'    => $this->userB->id,
        ];
        $reportDataB = $this->reportService->getReportData($this->userA->company_id, $filtersB);

        $this->assertNotEmpty($reportDataB);
        foreach ($reportDataB as $row) {
            $this->assertEquals($this->userB->name, $row['user_name']);
        }

        // Filter with user_id = 'no' returns all users' bookings
        $filtersAll = [
            'start_date' => '2099-12-01',
            'end_date'   => '2099-12-01',
            'user_id'    => 'no',
        ];
        $reportDataAll = $this->reportService->getReportData($this->userA->company_id, $filtersAll);
        $invoices = array_column($reportDataAll, 'invoice');
        $this->assertContains('INV-REP-ALPHA', $invoices);
        $this->assertContains('INV-REP-BETA', $invoices);
    }

    public function test_record_find_with_user_filter_displays_report_and_export_link()
    {
        $response = $this->actingAs($this->userA)->get(route('reports.record_find', [
            'start_date' => '2099-12-01',
            'end_date'   => '2099-12-01',
            'user_id'    => $this->userA->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('INV-REP-ALPHA');
        $response->assertDontSee('INV-REP-BETA');
        $response->assertSee('?user_id=' . $this->userA->id);
    }
}
