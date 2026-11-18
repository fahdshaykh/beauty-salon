<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\ServiceBooking;
use App\Services\Bookings\BookingQueryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;

class BookingSearchFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected BookingQueryService $queryService;
    protected User $user;
    protected Customer $customer;
    protected ServiceBooking $booking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queryService = app(BookingQueryService::class);

        $this->user = User::first() ?? User::create([
            'name'       => 'Test Staff',
            'email'      => 'staff_filter_test@example.com',
            'password'   => bcrypt('password'),
            'company_id' => 1,
            'is_active'  => 1,
        ]);

        $this->customer = Customer::create([
            'company_id'      => $this->user->company_id,
            'first_name'      => 'Aurelius',
            'last_name'       => 'Searchman',
            'primary_phone'   => '07998877665',
            'secondary_phone' => '01223344556',
            'is_active'       => 1,
        ]);

        $this->booking = ServiceBooking::create([
            'company_id'     => $this->user->company_id,
            'user_id'        => $this->user->id,
            'customer_id'    => $this->customer->id,
            'invoice'        => 'INV-XYZ-9988',
            'service_status' => 1,
            'due_date'       => '2099-11-20',
            'due_time'       => '10:00:00',
            'service_time'   => 45,
            'total_price'    => 75.00,
        ]);
    }

    public function test_customer_search_by_name()
    {
        $request = new Request(['customer_search' => 'Aurelius']);
        $results = $this->queryService->buildQuery($this->user->company_id, $request)->get();

        $this->assertTrue($results->contains('id', $this->booking->id));

        $negativeRequest = new Request(['customer_search' => 'NonExistentName12345']);
        $negativeResults = $this->queryService->buildQuery($this->user->company_id, $negativeRequest)->get();

        $this->assertFalse($negativeResults->contains('id', $this->booking->id));
    }

    public function test_customer_search_by_phone()
    {
        $request = new Request(['customer_search' => '07998877665']);
        $results = $this->queryService->buildQuery($this->user->company_id, $request)->get();

        $this->assertTrue($results->contains('id', $this->booking->id));

        // Partial phone
        $partialRequest = new Request(['customer_search' => '887766']);
        $partialResults = $this->queryService->buildQuery($this->user->company_id, $partialRequest)->get();

        $this->assertTrue($partialResults->contains('id', $this->booking->id));
    }

    public function test_customer_search_backward_compatibility()
    {
        // customer_name param
        $requestName = new Request(['customer_name' => 'Searchman']);
        $resultsName = $this->queryService->buildQuery($this->user->company_id, $requestName)->get();
        $this->assertTrue($resultsName->contains('id', $this->booking->id));

        // primary_phone param
        $requestPhone = new Request(['primary_phone' => '07998877665']);
        $resultsPhone = $this->queryService->buildQuery($this->user->company_id, $requestPhone)->get();
        $this->assertTrue($resultsPhone->contains('id', $this->booking->id));
    }

    public function test_invoice_search_functionality()
    {
        // Full invoice string
        $requestFull = new Request(['invoice' => 'INV-XYZ-9988']);
        $resultsFull = $this->queryService->buildQuery($this->user->company_id, $requestFull)->get();
        $this->assertTrue($resultsFull->contains('id', $this->booking->id));

        // Partial invoice
        $requestPartial = new Request(['invoice' => 'XYZ-9988']);
        $resultsPartial = $this->queryService->buildQuery($this->user->company_id, $requestPartial)->get();
        $this->assertTrue($resultsPartial->contains('id', $this->booking->id));

        // Match by booking ID
        $requestId = new Request(['invoice' => (string)$this->booking->id]);
        $resultsId = $this->queryService->buildQuery($this->user->company_id, $requestId)->get();
        $this->assertTrue($resultsId->contains('id', $this->booking->id));

        // Negative check
        $requestNone = new Request(['invoice' => 'NON-EXISTENT-INV-00000']);
        $resultsNone = $this->queryService->buildQuery($this->user->company_id, $requestNone)->get();
        $this->assertFalse($resultsNone->contains('id', $this->booking->id));
    }

    public function test_service_bookings_index_view_renders_updated_labels()
    {
        $response = $this->actingAs($this->user)->get(route('service-bookings.index'));
        $response->assertStatus(200);

        // Check for "Customer Name or Phone" label
        $response->assertSee('Customer Name or Phone');
        $response->assertSee('name="customer_search"', false);

        // Check for "Invoice" filter label and input
        $response->assertSee('name="invoice"', false);
        $response->assertSee('Enter invoice number');
    }
}
