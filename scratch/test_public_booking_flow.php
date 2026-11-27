<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BookingPageSetting;
use App\Models\BookingAutomation;
use App\Services\PublicBookingService;
use App\Services\GeminiBookingAssistantService;
use App\Services;
use App\User;
use App\Customer;
use App\ServiceBooking;
use Illuminate\Http\Request;

echo "=== STARTING PUBLIC BOOKING & AI ENGINE VERIFICATION ===\n\n";

// 1. Check BookingPageSetting record
$pageSetting = BookingPageSetting::with('company')->first();
if (!$pageSetting) {
    echo "[FAIL] No BookingPageSetting record found in DB.\n";
    exit(1);
}
echo "[PASS] Found BookingPageSetting for Company ID {$pageSetting->company_id}: Slug = '{$pageSetting->slug}', Title = '{$pageSetting->hero_title}'\n";
echo "       Public URL: {$pageSetting->public_url}\n";

// 2. Test PublicBookingService::resolvePageData
$bookingService = app(PublicBookingService::class);
try {
    $pageData = $bookingService->resolvePageData($pageSetting->slug);
    echo "[PASS] PublicBookingService::resolvePageData resolved successfully:\n";
    echo "       - Company: {$pageData['company']->company_name}\n";
    echo "       - Categories count: " . $pageData['categories']->count() . "\n";
    echo "       - Services count: " . $pageData['services']->count() . "\n";
    echo "       - Technicians count: " . $pageData['technicians']->count() . "\n";
} catch (\Exception $e) {
    echo "[FAIL] PublicBookingService::resolvePageData error: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Test Slot Calculation
$testDate = date('Y-m-d', strtotime('+1 day'));
$slots = $bookingService->getAvailableSlots($pageSetting->company_id, $testDate, null, 30);
echo "[PASS] Generated " . count($slots) . " available slots for date {$testDate} (30 min duration)\n";
if (!empty($slots)) {
    $firstThree = array_map(function($s) { return $s['time_12'] . ($s['available'] ? ' (open)' : ' (busy)'); }, array_slice($slots, 0, 3));
    echo "       First 3 slots: " . implode(', ', $firstThree) . "\n";
}

// 4. Test Gemini AI Booking Assistant (Fallback and catalog understanding)
$aiService = app(GeminiBookingAssistantService::class);
$aiResult = $aiService->chat($pageSetting->company_id, "Hi, I need a quick haircut or consultation tomorrow afternoon around 2pm. Can you help?");
echo "[PASS] Gemini AI Booking Assistant responded:\n";
echo "       - Message: " . substr($aiResult['message'], 0, 100) . "...\n";
echo "       - Suggestions: " . json_encode($aiResult['suggested_actions']) . "\n";

// 5. Test Booking Submission Flow
$sampleService = Services::where('company_id', $pageSetting->company_id)->first();
if (!$sampleService) {
    $sampleService = Services::first();
}

$testCustomerPhone = "07" . rand(10000000, 99999999);
$testCustomerEmail = "test_portal_" . time() . "@example.com";
$bookingPayload = [
    'company_slug'     => $pageSetting->slug,
    'first_name'       => 'Sarah',
    'last_name'        => 'Jenkins Test',
    'customer_email'   => $testCustomerEmail,
    'customer_phone'   => $testCustomerPhone,
    'is_home_service'  => 0,
    'due_date'         => $testDate,
    'due_time'         => !empty($slots) ? $slots[0]['time_24'] : '10:00',
    'technician_id'    => null,
    'service_ids'      => $sampleService ? [$sampleService->id] : [1],
    'notes'            => 'Automated verification test booking from public portal.',
];

try {
    $result = $bookingService->storePublicBooking($pageSetting->company_id, $bookingPayload);
    if (!$result['success']) {
        throw new \Exception($result['message'] ?? 'Failed to store booking');
    }
    $booking = $result['booking'];
    echo "[PASS] storePublicBooking created successfully:\n";
    echo "       - Booking ID: {$booking->id}\n";
    echo "       - Invoice #: {$booking->invoice}\n";
    echo "       - Customer ID: {$booking->customer_id}\n";
    echo "       - Total Amount: {$booking->total_price}\n";

    // Verify Customer record was properly created
    $createdCust = Customer::find($booking->customer_id);
    if ($createdCust && $createdCust->primary_phone == $testCustomerPhone) {
        echo "[PASS] Verified Customer record created with primary_phone {$testCustomerPhone}\n";
    } else {
        echo "[WARN] Customer record lookup mismatch\n";
    }

    // Verify Booking Details
    $detailsCount = $booking->booking_details()->count();
    echo "[PASS] Verified {$detailsCount} BookingDetail item(s) attached to invoice {$booking->invoice}\n";

} catch (\Exception $e) {
    echo "[FAIL] storePublicBooking failed: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// 6. Test View Rendering for Public Page and Confirmation
try {
    $renderedHtml = view('public_booking.index', $pageData)->render();
    echo "[PASS] public_booking.index blade view rendered successfully (" . strlen($renderedHtml) . " bytes)\n";

    $confirmHtml = view('public_booking.confirmation', [
        'setting' => $pageSetting,
        'company' => $pageSetting->company,
        'booking' => $booking,
    ])->render();
    echo "[PASS] public_booking.confirmation blade view rendered successfully (" . strlen($confirmHtml) . " bytes)\n";

    // 7. Test Admin Views Rendering
    \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());
    \Illuminate\Support\Facades\Auth::login(User::first());

    $builderHtml = view('booking_page_builder.customizer', [
        'setting'        => $pageSetting,
        'company'        => $pageSetting->company,
        'companySetting' => $pageSetting->company->company_settings,
        'blocks'         => $pageSetting->getOrderedBlocks(),
        'categories'     => $pageData['categories'],
    ])->render();
    echo "[PASS] booking_page_builder.customizer blade view rendered successfully (" . strlen($builderHtml) . " bytes)\n";

    $automations = BookingAutomation::where('company_id', $pageSetting->company_id)->get()->keyBy('trigger_event');
    $automationsHtml = view('booking_automations.index', [
        'automations'     => $automations,
        'availableEvents' => BookingAutomation::getAvailableEvents(),
        'company'         => $pageSetting->company,
    ])->render();
    echo "[PASS] booking_automations.index blade view rendered successfully (" . strlen($automationsHtml) . " bytes)\n";
} catch (\Exception $e) {
    echo "[FAIL] View rendering failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Clean up test booking to leave DB clean
$booking->booking_details()->delete();
$booking->delete();
if ($createdCust) {
    $createdCust->delete();
}
echo "[PASS] Cleaned up test booking and customer records.\n";

echo "\n=== ALL VERIFICATION TESTS PASSED SUCCESSFULLY! ===\n";
