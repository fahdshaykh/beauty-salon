<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Customer;
use App\Services;
use App\Category;
use App\ServiceBooking;
use App\CompanySetting;
use App\Services\Bookings\BookingActionService;
use App\Services\Bookings\BookingNotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class GoogleCalendarAndPlacesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected int $companyId;
    private ?object $originalSettings = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'admin@beautysalon.com')->first() ?? User::first();

        if (!$this->user) {
            $this->user = User::create([
                'name'       => 'Admin Tester',
                'email'      => 'admin_cal_' . uniqid() . '@example.com',
                'password'   => bcrypt('password'),
                'company_id' => 1,
                'is_active'  => 1,
            ]);
        }

        $this->companyId = $this->user->company_id ?: 1;
        setPermissionsTeamId($this->companyId);

        $this->originalSettings = DB::table('company_settings')->where('company_id', $this->companyId)->first();

        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'web',
            'company_id' => $this->companyId,
        ]);

        if (!$this->user->hasRole('admin')) {
            $this->user->assignRole($role);
        }

        // Ensure company settings row exists
        DB::table('company_settings')->updateOrInsert(
            ['company_id' => $this->companyId],
            [
                'site_name'            => 'Test Salon',
                'google_calendar_sync' => 0,
                'google_calendar_id'   => 'primary',
                'google_access_token'  => null,
                'google_account_email' => null,
            ]
        );
    }

    protected function tearDown(): void
    {
        if ($this->originalSettings) {
            DB::table('company_settings')->where('company_id', $this->companyId)->update((array)$this->originalSettings);
        }
        parent::tearDown();
    }

    public function test_google_calendar_settings_can_be_updated()
    {
        $testCalendarId = 'salon_test_' . uniqid() . '@group.calendar.google.com';

        $response = $this->actingAs($this->user)->post(route('settings.google_calendar_update'), [
            'google_calendar_sync' => 1,
            'google_calendar_id'   => $testCalendarId,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Google Calendar settings updated successfully.',
        ]);

        $this->assertDatabaseHas('company_settings', [
            'company_id'           => $this->companyId,
            'google_calendar_sync' => 1,
            'google_calendar_id'   => $testCalendarId,
        ]);
    }

    public function test_google_calendar_settings_can_be_disabled()
    {
        $response = $this->actingAs($this->user)->post(route('settings.google_calendar_update'), [
            'google_calendar_id' => '',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_settings', [
            'company_id'           => $this->companyId,
            'google_calendar_sync' => 0,
            'google_calendar_id'   => null,
        ]);
    }

    public function test_theme_settings_page_contains_google_calendar_tab_and_connect_button()
    {
        $response = $this->actingAs($this->user)->get(route('theme.settings'));

        $response->assertStatus(200);
        $response->assertSee('Google Calendar');
        $response->assertSee('googlecalendar-tab');
        $response->assertSee('google_client_id');
        $response->assertSee('google_client_secret');
        $response->assertSee('google_calendar_id');
        $response->assertSee('Connect Google Calendar');
    }

    public function test_company_google_client_id_and_secret_can_be_saved()
    {
        $response = $this->actingAs($this->user)->post(route('settings.google_calendar_update'), [
            'google_client_id'     => 'test-client-id-123.apps.googleusercontent.com',
            'google_client_secret' => 'test-client-secret-xyz',
            'google_calendar_id'   => 'primary',
            'google_calendar_sync' => '1',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('company_settings', [
            'company_id'           => $this->companyId,
            'google_client_id'     => 'test-client-id-123.apps.googleusercontent.com',
            'google_client_secret' => 'test-client-secret-xyz',
            'google_calendar_id'   => 'primary',
            'google_calendar_sync' => 1,
        ]);

        $client = \App\Http\Controllers\GoogleCalendarOAuthController::createOAuthClient($this->companyId);
        $this->assertNotNull($client);
        $this->assertEquals('test-client-id-123.apps.googleusercontent.com', $client->getClientId());
        $this->assertEquals('test-client-secret-xyz', $client->getClientSecret());
    }

    public function test_google_oauth_disconnect_clears_tokens_and_disables_sync()
    {
        // First simulate connected state
        DB::table('company_settings')->where('company_id', $this->companyId)->update([
            'google_access_token'  => json_encode(['access_token' => 'mock_token', 'refresh_token' => 'mock_refresh']),
            'google_account_email' => 'owner@example.com',
            'google_calendar_sync' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('google.calendar.disconnect'));

        $this->assertDatabaseHas('company_settings', [
            'company_id'           => $this->companyId,
            'google_access_token'  => null,
            'google_account_email' => null,
            'google_calendar_sync' => 0,
        ]);
    }

    public function test_booking_create_form_renders_google_maps_places_script()
    {
        config(['services.google.maps_api_key' => 'AIzaSyTestApiKeyForVerification']);

        $response = $this->actingAs($this->user)->get(route('service-bookings.create'));

        $response->assertStatus(200);
        $response->assertSee('maps.googleapis.com/maps/api/js?key=AIzaSyTestApiKeyForVerification&libraries=places', false);
        $response->assertSee('initGooglePlacesAutocomplete');
        $response->assertSee('booking_address');
    }

    public function test_sync_google_calendar_event_handles_missing_credentials_gracefully()
    {
        DB::table('company_settings')->where('company_id', $this->companyId)->update([
            'google_calendar_sync' => 1,
            'google_calendar_id'   => 'dummy@group.calendar.google.com',
            'google_access_token'  => null,
        ]);

        config(['google-calendar.auth_profiles.service_account.credentials_json' => storage_path('non_existent_creds.json')]);

        $notificationService = app(BookingNotificationService::class);
        $result = $notificationService->syncGoogleCalendarEvent(999999, $this->companyId);

        $this->assertNull($result);
    }

    public function test_booking_creation_completes_safely_when_google_calendar_sync_is_active()
    {
        DB::table('company_settings')->where('company_id', $this->companyId)->update([
            'google_calendar_sync' => 1,
            'google_calendar_id'   => 'dummy@group.calendar.google.com',
        ]);

        $customer = Customer::create([
            'first_name'    => 'Jane',
            'last_name'     => 'Doe ' . uniqid(),
            'email'         => 'jane.doe.' . uniqid() . '@example.com',
            'primary_phone' => '07123456789',
            'company_id'    => $this->companyId,
            'is_active'     => 1,
        ]);

        $category = Category::create([
            'name'       => 'Test Cat ' . uniqid(),
            'company_id' => $this->companyId,
            'is_active'  => 1,
        ]);

        $service = Services::create([
            'name'        => 'Test Cut ' . uniqid(),
            'category_id' => $category->id,
            'company_id'  => $this->companyId,
            'price'       => 35.00,
            'is_active'   => 1,
            'is_deleted'  => 0,
        ]);

        $bookingData = [
            'customer_id'     => $customer->id,
            'user_id'         => $this->user->id,
            'service_id'      => [$service->id],
            'total_price'     => 35.00,
            'due_date'        => date('Y-m-d', strtotime('+' . rand(30, 90) . ' days')),
            'due_time'        => sprintf('%02d:00:00', rand(8, 18)),
            'service_time'    => 45,
            'is_home_service' => 1,
            'booking_address' => '10 Downing Street, London',
        ];

        $actionService = app(BookingActionService::class);
        $result = $actionService->createBooking($this->companyId, $bookingData, $this->user);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['booking']);
        $this->assertEquals('10 Downing Street, London', $result['booking']->booking_address);
        $this->assertEquals(1, $result['booking']->is_home_service);
    }

    public function test_booking_cancellation_calls_google_calendar_deletion_safely()
    {
        $customer = Customer::create([
            'first_name'    => 'Bob',
            'last_name'     => 'Cancel ' . uniqid(),
            'email'         => 'bob.' . uniqid() . '@example.com',
            'primary_phone' => '07999888777',
            'company_id'    => $this->companyId,
            'is_active'     => 1,
        ]);

        $booking = ServiceBooking::create([
            'company_id'      => $this->companyId,
            'customer_id'     => $customer->id,
            'user_id'         => $this->user->id,
            'total_price'     => 50.00,
            'due_date'        => date('Y-m-d', strtotime('+' . rand(30, 90) . ' days')),
            'due_time'        => '14:00:00',
            'service_status'  => 1,
            'google_event_id' => 'mock_google_event_' . uniqid(),
        ]);

        $actionService = app(BookingActionService::class);
        $cancelled = $actionService->cancelBooking($booking->id, $this->companyId, 'Customer requested cancellation');

        $this->assertTrue($cancelled);
        $this->assertDatabaseHas('service_bookings', [
            'id'             => $booking->id,
            'service_status' => 4,
        ]);
    }
}
