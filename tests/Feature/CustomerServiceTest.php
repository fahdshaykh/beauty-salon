<?php

namespace Tests\Feature;

use App\Customer;
use App\Services\Customers\CustomerService;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected CustomerService $customerService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customerService = app(CustomerService::class);
    }

    public function test_get_paginated_customers_scopes_by_company_and_filters_deleted()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $customer = Customer::create([
            'first_name'    => 'TestUniqueFirst',
            'last_name'     => 'TestUniqueLast',
            'primary_phone' => '99912345678',
            'address'       => 'Test Address',
            'company_id'    => $user->company_id,
            'is_active'     => 1,
            'is_deleted'    => 0,
        ]);

        $request = new Request(['search' => 'TestUniqueFirst']);
        $results = $this->customerService->getPaginatedCustomers($user->company_id, $request, 15);

        $this->assertNotEmpty($results->items());
        $this->assertEquals('TestUniqueFirst', $results->items()[0]->first_name);

        // Verify other company cannot see this customer
        $otherCompanyResults = $this->customerService->getPaginatedCustomers($user->company_id + 99999, $request, 15);
        $this->assertEmpty($otherCompanyResults->items());
    }

    public function test_create_and_update_customer()
    {
        $user = User::first();
        if (!$user) {
            $this->markTestSkipped('No user found');
        }

        $data = [
            'first_name'    => 'John',
            'last_name'     => 'DoeArch',
            'primary_phone' => '1234509876',
            'address'       => '123 Main St',
            'email'         => 'john.doe.arch@example.com',
        ];

        $customer = $this->customerService->createCustomer($user->company_id, $data);

        $this->assertNotNull($customer->id);
        $this->assertEquals('John', $customer->first_name);
        $this->assertEquals($user->company_id, $customer->company_id);

        // Update customer
        $updateData = [
            'first_name'    => 'Johnny',
            'last_name'     => 'DoeUpdated',
            'primary_phone' => '1234509876',
            'address'       => '456 Secondary St',
            'email'         => 'johnny.updated@example.com',
        ];

        $this->customerService->updateCustomer($customer, $updateData);
        $customer->refresh();

        $this->assertEquals('Johnny', $customer->first_name);
        $this->assertEquals('DoeUpdated', $customer->last_name);
    }
}
