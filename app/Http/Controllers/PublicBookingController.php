<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PublicBookingService;
use App\Services\GeminiBookingAssistantService;
use App\ServiceBooking;
use App\Models\BookingPageSetting;
use App\Company;

class PublicBookingController extends Controller
{
    protected $bookingService;
    protected $aiService;

    public function __construct(
        PublicBookingService $bookingService,
        GeminiBookingAssistantService $aiService
    ) {
        $this->bookingService = $bookingService;
        $this->aiService      = $aiService;
    }

    /**
     * Show the public booking page for a company.
     */
    public function show(string $slug)
    {
        $pageData = $this->bookingService->resolvePageData($slug);

        if (!$pageData) {
            abort(404, 'Booking page not found or currently inactive.');
        }

        return view('public_booking.index', $pageData);
    }

    /**
     * Get real-time available time slots for selected date.
     */
    public function getSlots(Request $request, string $slug)
    {
        $setting = BookingPageSetting::where('slug', $slug)->firstOrFail();
        $date = $request->get('date', now()->format('Y-m-d'));
        $userId = $request->filled('user_id') ? (int)$request->user_id : null;
        $duration = (int)$request->get('duration', 30);

        $slots = $this->bookingService->getAvailableSlots($setting->company_id, $date, $userId, $duration);

        return response()->json([
            'success' => true,
            'slots'   => $slots,
        ]);
    }

    /**
     * Submit booking from public portal.
     */
    public function submit(Request $request, string $slug)
    {
        $setting = BookingPageSetting::where('slug', $slug)->firstOrFail();

        $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'customer_phone'  => 'required|string|max:20',
            'customer_email'  => 'nullable|email|max:150',
            'due_date'        => 'required|date|after_or_equal:today',
            'due_time'        => 'required',
            'service_ids'     => 'required|array|min:1',
            'service_ids.*'   => 'exists:services,id',
            'user_id'         => 'nullable|integer',
            'is_home_service' => 'nullable|boolean',
            'booking_address' => 'nullable|string|max:500',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $data = $request->all();
        $data['company_slug'] = $slug;

        $result = $this->bookingService->storePublicBooking($setting->company_id, $data);

        return response()->json($result);
    }

    /**
     * Show confirmation page after successful booking.
     */
    public function confirmed(string $slug, string $invoice)
    {
        $setting = BookingPageSetting::where('slug', $slug)->firstOrFail();
        $company = Company::findOrFail($setting->company_id);

        $booking = ServiceBooking::where('company_id', $setting->company_id)
            ->where('invoice', $invoice)
            ->with(['customer', 'user', 'services'])
            ->firstOrFail();

        return view('public_booking.confirmation', compact('setting', 'company', 'booking'));
    }

    /**
     * Conversational AI Assistant endpoint (Gemini API).
     */
    public function aiChat(Request $request, string $slug)
    {
        $setting = BookingPageSetting::where('slug', $slug)->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        $userMessage = $request->message;
        $history     = $request->get('history', []);

        $result = $this->aiService->chat($setting->company_id, $userMessage, $history);

        return response()->json($result);
    }
}
