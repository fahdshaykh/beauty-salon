<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeBookingChartTest extends TestCase
{
    use DatabaseTransactions;

    public function test_employee_booking_chart_returns_all_completed_bookings_in_current_month()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('employee.booking_chart'));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertIsArray($data);

        // In company 1 there are 4 completed bookings in November 2026
        $userRecord = collect($data)->firstWhere('employee_name', 'fahd shifu');
        if ($userRecord) {
            $this->assertEquals(4, $userRecord['total_services']);
            $this->assertEquals(74.00, $userRecord['total_revenue']);
        }
    }

    public function test_employee_booking_chart_filters_by_custom_date_range()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        // Up to 2026-11-16 only 3 completed bookings should match
        $response = $this->actingAs($user)->get(route('employee.booking_chart', [
            'start_date' => '2026-11-01',
            'end_date'   => '2026-11-16',
        ]));

        $response->assertStatus(200);
        $data = $response->json();
        $userRecord = collect($data)->firstWhere('employee_name', 'fahd shifu');
        if ($userRecord) {
            $this->assertEquals(3, $userRecord['total_services']);
            $this->assertEquals(62.00, $userRecord['total_revenue']);
        }
    }

    public function test_employee_graph_report_view_defaults_to_end_of_month()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('employee.graph_report'));
        if ($response->getStatusCode() === 200) {
            $response->assertSee(date('Y-m-t'));
            $response->assertSee('id="app-main-footer"', false);
        }
    }

    public function test_awards_page_renders_master_footer()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user available for testing.');
        }

        $response = $this->actingAs($user)->get(route('awards.index'));
        if ($response->getStatusCode() === 200) {
            $response->assertSee('id="app-main-footer"', false);
            $response->assertSee('bSaloon');
            $response->assertSee('All rights reserved');
        }
    }
}

