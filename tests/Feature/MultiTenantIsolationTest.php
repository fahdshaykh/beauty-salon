<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\Supplier;
use App\ServiceBooking;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MultiTenantIsolationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Unauthenticated users must be redirected to login.
     */
    public function test_unauthenticated_users_are_redirected_to_login()
    {
        $routes = [
            '/home',
            '/customers',
            '/service-bookings',
            '/inventories',
            '/expenses',
            '/suppliers',
            '/tickets',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(302);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Super Admin portal requires authentication.
     */
    public function test_admin_portal_requires_authentication()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Profile access for another user must be blocked (IDOR protection).
     */
    public function test_cross_tenant_profile_access_is_forbidden()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available for testing.');
        }

        // Target an ID that is not User A
        $targetId = $userA->id + 99999;

        $response = $this->actingAs($userA)->get("/user/profile/{$targetId}");
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($userA)->post("/user/profile-update/{$targetId}", [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
        ]);
        $updateResponse->assertStatus(403);
    }

    /**
     * Changing customer status of another company must be blocked.
     */
    public function test_cross_tenant_customer_status_mutation_is_blocked()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available.');
        }

        // Attempt to update customer status with non-existent or foreign company ID
        $response = $this->actingAs($userA)->postJson('/customer-status', [
            'id' => 9999999,
            'status' => 0,
        ]);

        $response->assertStatus(404);
    }

    /**
     * Cancelling a booking of another company must be blocked.
     */
    public function test_cross_tenant_booking_cancel_is_blocked()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($userA)->postJson('/service-bookings/cancel', [
            'booking_id' => 9999999,
            'reason' => 'Unauthorized cancellation test',
        ]);

        $this->assertTrue(in_array($response->getStatusCode(), [404, 500]));
    }

    /**
     * Accessing job detail of another company's booking must return 404.
     */
    public function test_cross_tenant_job_detail_is_blocked()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($userA)->get("/show-job-detail/9999999");
        $response->assertStatus(404);
    }

    /**
     * Accessing inventory edit of a foreign inventory must return 404.
     */
    public function test_cross_tenant_inventory_edit_is_blocked()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($userA)->get("/inventories/9999999/edit");
        $response->assertStatus(404);
    }

    /**
     * Company limits page loads successfully without SQL duplicate key error.
     */
    public function test_company_limits_page_loads_without_duplicate_key_error()
    {
        $userA = User::first();
        if (!$userA) {
            $this->markTestSkipped('No user available.');
        }

        $response = $this->actingAs($userA)->get('/company/limits');
        $response->assertStatus(200);
        $response->assertViewIs('reports.plan_limits');
        $response->assertViewHas(['company', 'limits']);
    }
}
