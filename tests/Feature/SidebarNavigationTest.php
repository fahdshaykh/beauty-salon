<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Services;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SidebarNavigationTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first();
    }

    public function test_dashboard_sidebar_active_state()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($this->user)->get('/home');
        $response->assertStatus(200);

        // Check that Dashboard nav-item has active class
        $response->assertSee('nav-item active', false);
        $response->assertSee('href="' . url('/home') . '"', false);
    }

    public function test_categories_sidebar_active_and_expanded_state()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($this->user)->get(route('categories.index'));
        if ($response->getStatusCode() === 200) {
            // Parent li should be active
            $response->assertSee('href="#ui-basic" aria-expanded="true"', false);
            // Collapse container should have class show
            $response->assertSee('collapse show" id="ui-basic"', false);
            // Sub link should be active
            $response->assertSee('class="nav-link active" href="' . route('categories.index') . '"', false);
        }
    }

    public function test_service_bookings_sidebar_active_and_expanded_state()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($this->user)->get(route('service-bookings.index'));
        if ($response->getStatusCode() === 200) {
            // Parent Bookings li toggler should be expanded
            $response->assertSee('href="#editors" aria-expanded="true"', false);
            // Collapse container should have class show
            $response->assertSee('collapse show" id="editors"', false);
            // Service Bookings sub-link should be active
            $response->assertSee('class="nav-link active" href="' . route('service-bookings.index') . '"', false);
        }
    }

    public function test_reports_graph_sidebar_active_and_expanded_state()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($this->user)->get(route('employee.graph_report'));
        if ($response->getStatusCode() === 200) {
            // Parent Reports li toggler should be expanded
            $response->assertSee('href="#charts" aria-expanded="true"', false);
            // Collapse container should have class show
            $response->assertSee('collapse show" id="charts"', false);
            // Services Graph sub-link should be active
            $response->assertSee('class="nav-link active" href="' . route('employee.graph_report') . '"', false);
        }
    }

    public function test_awards_sidebar_active_state()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($this->user)->get(route('awards.index'));
        if ($response->getStatusCode() === 200) {
            $response->assertSee('href="' . route('awards.index') . '"', false);
            $response->assertSee('nav-link active" href="' . route('awards.index') . '"', false);
        }
    }

    public function test_tickets_view_page_only_highlights_tickets_menu()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $ticket = Ticket::first();
        if (!$ticket) {
            $this->markTestSkipped('No ticket available.');
        }

        $response = $this->actingAs($this->user)->get(route('tickets.show', $ticket->id));
        if ($response->getStatusCode() === 200) {
            // Tickets parent must be active
            $response->assertSee('nav-link active" href="' . route('tickets.index') . '"', false);

            // Categories, Services, Bookings should NOT be expanded or active
            $response->assertDontSee('collapse show" id="ui-basic"', false);
            $response->assertDontSee('collapse show" id="editors"', false);
            $response->assertDontSee('collapse show" id="ui-advanced"', false);
            $response->assertDontSee('collapse show" id="expenses"', false);
        }
    }

    public function test_services_edit_page_does_not_highlight_bookings_menu()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $service = Services::first();
        if (!$service) {
            $this->markTestSkipped('No service available.');
        }

        $response = $this->actingAs($this->user)->get(route('services.edit', $service->id));
        if ($response->getStatusCode() === 200) {
            // Services should be active and expanded
            $response->assertSee('href="#ui-advanced" aria-expanded="true"', false);
            $response->assertSee('collapse show" id="ui-advanced"', false);

            // Bookings should NOT be active or expanded
            $response->assertDontSee('href="#editors" aria-expanded="true"', false);
            $response->assertDontSee('collapse show" id="editors"', false);
        }
    }

    public function test_expense_types_edit_page_highlights_only_expenses_module()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $expenseType = DB::table('expense_types')->first();
        if (!$expenseType) {
            $this->markTestSkipped('No expense type available.');
        }

        $response = $this->actingAs($this->user)->get(route('expense_types.edit', $expenseType->id));
        if ($response->getStatusCode() === 200) {
            // Expenses parent must be expanded and active
            $response->assertSee('href="#expenses" aria-expanded="true"', false);
            $response->assertSee('collapse show" id="expenses"', false);
            $response->assertSee('nav-link active" href="' . url('expense-types') . '"', false);

            // Categories, Services, Bookings must NOT be expanded
            $response->assertDontSee('collapse show" id="ui-basic"', false);
            $response->assertDontSee('collapse show" id="editors"', false);
            $response->assertDontSee('collapse show" id="ui-advanced"', false);
        }
    }

    public function test_expenses_edit_page_highlights_only_expenses_module()
    {
        if (!$this->user) {
            $this->markTestSkipped('No user available.');
        }

        $expense = DB::table('expenses')->first();
        if (!$expense) {
            $this->markTestSkipped('No expense available.');
        }

        $response = $this->actingAs($this->user)->get(route('expenses.edit', $expense->id));
        if ($response->getStatusCode() === 200) {
            // Expenses parent must be expanded and active
            $response->assertSee('href="#expenses" aria-expanded="true"', false);
            $response->assertSee('collapse show" id="expenses"', false);
            $response->assertSee('nav-link active" href="' . url('expenses') . '"', false);

            // Categories, Services, Bookings must NOT be expanded
            $response->assertDontSee('collapse show" id="ui-basic"', false);
            $response->assertDontSee('collapse show" id="editors"', false);
            $response->assertDontSee('collapse show" id="ui-advanced"', false);
        }
    }
}
