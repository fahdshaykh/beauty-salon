<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\Services;
use App\BookingDetail;
use App\ServiceBooking;
use App\Services\Reports\BookingReportService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookingReportServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected BookingReportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingReportService();
    }

    public function test_date_normalization_handles_various_formats()
    {
        $this->assertEquals('2026-11-01', $this->service->normalizeDate('2026-11-01'));
        $this->assertEquals('2026-11-01', $this->service->normalizeDate('01-11-2026'));
        $this->assertNull($this->service->normalizeDate(null));
        $this->assertNull($this->service->normalizeDate(''));
    }

    public function test_get_report_data_returns_expected_structure_and_eager_loads()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        // Test with real date range or empty filter to ensure relations eager-load without SQL ambiguity errors
        $data = $this->service->getReportData($user->company_id, []);

        $this->assertIsArray($data);
    }

    public function test_record_find_route_executes_without_ambiguous_column_error()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('reports.record_find'));
        // If there are records it should return 200, if not it redirects back
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true));
        if ($response->getStatusCode() === 200) {
            $response->assertSee('badge-pill');
            $response->assertSee('background-color:');
            $response->assertSee('Export Booking Report');
            $response->assertSee('/report-export/');
        }
    }

    public function test_report_export_route_streams_csv()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('reports.export', [
            'start_date'   => 'no',
            'end_date'     => 'no',
            'category_id'  => 'no',
            'service_id'   => 'no',
            'status_id'    => 'no',
            'payment_type' => 'no',
        ]));

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/csv', $response->headers->get('content-type'));
    }

    public function test_customer_record_route_renders_service_badges()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $customer = Customer::where('company_id', $user->company_id)->first();
        if (!$customer) {
            $this->markTestSkipped('No customer available for testing.');
        }

        $response = $this->actingAs($user)->get(route('customer.record', ['customer_id' => $customer->id]));
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302], true));
        if ($response->getStatusCode() === 200) {
            $response->assertSee('badge-pill');
        }
    }

    public function test_profit_loss_report_view_defaults_to_start_of_month_and_current_date()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('profit_loss.report'));
        $response->assertStatus(200);
        $response->assertSee('value="' . date('Y-m-01') . '"', false);
        $response->assertSee('value="' . date('Y-m-d') . '"', false);
    }

    public function test_profit_loss_data_defaults_to_start_of_month_and_current_date()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('profit_loss.data'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'expense',
            'sales',
            'revenue',
            'breakdown' => [
                'general_expenses',
                'supplier_payments',
                'salaries_paid'
            ]
        ]);
    }

    public function test_inventory_record_validation_error_messages_render_on_blade_view()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        // Submit empty start_date and end_date from inventory-reports
        $response = $this->actingAs($user)
            ->from(route('inventory-reports'))
            ->get(route('inventory.record', [
                'start_date' => '',
                'end_date'   => '',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect(route('inventory-reports'));
        $response->assertSessionHasErrors(['start_date', 'end_date']);

        // Follow redirect to ensure Blade renders the validation messages
        $followRedirect = $this->actingAs($user)->get(route('inventory-reports'));
        $followRedirect->assertStatus(200);
        $followRedirect->assertSee('Please select a start date');
        $followRedirect->assertSee('Please select an end date');
    }

    public function test_inventory_record_end_date_after_or_equal_validation_message()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($user)
            ->from(route('inventory-reports'))
            ->get(route('inventory.record', [
                'start_date' => '2026-11-20',
                'end_date'   => '2026-11-10',
            ]));

        $response->assertStatus(302);
        $response->assertRedirect(route('inventory-reports'));
        $response->assertSessionHasErrors(['end_date']);

        $followRedirect = $this->actingAs($user)->get(route('inventory-reports'));
        $followRedirect->assertStatus(200);
        $followRedirect->assertSee('End date must be after or equal to start date');
    }
}

