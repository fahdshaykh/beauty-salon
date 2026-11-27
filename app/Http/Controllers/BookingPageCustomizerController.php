<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingPageSetting;
use App\Company;
use App\CompanySetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingPageCustomizerController extends Controller
{
    /**
     * Show the booking page customizer.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $company   = Company::findOrFail($companyId);

        $setting = BookingPageSetting::firstOrCreate(
            ['company_id' => $companyId],
            [
                'slug'                     => Str::slug($company->company_name ?: 'company-' . $companyId),
                'page_title'               => $company->company_name ?: 'Service Booking',
                'tagline'                  => 'Book your appointment online in minutes',
                'about_text'               => 'Choose your preferred service, specialist, and time slot. Quality guaranteed.',
                'primary_color'            => '#4b49ac',
                'secondary_color'          => '#7978e9',
                'allowed_service_modes'    => 'both',
                'enable_technician_picker' => true,
                'enable_advance_payment'   => false,
                'enable_ai_assistant'      => true,
                'enable_customer_notes'    => true,
                'is_published'             => true,
            ]
        );

        $companySetting = DB::table('company_settings')->where('company_id', $companyId)->first();
        $blocks = $setting->getOrderedBlocks();

        return view('booking_page_builder.customizer', compact('company', 'setting', 'companySetting', 'blocks'));
    }

    /**
     * Save customizer settings and block layout.
     */
    public function save(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $setting   = BookingPageSetting::where('company_id', $companyId)->firstOrFail();

        $request->validate([
            'slug'                     => 'required|string|max:100|alpha_dash|unique:booking_page_settings,slug,' . $setting->id,
            'page_title'               => 'required|string|max:255',
            'tagline'                  => 'nullable|string|max:255',
            'about_text'               => 'nullable|string',
            'primary_color'            => 'required|string|max:20',
            'secondary_color'          => 'nullable|string|max:20',
            'allowed_service_modes'    => 'required|in:both,in_shop,home_service',
            'enable_technician_picker' => 'nullable|boolean',
            'enable_advance_payment'   => 'nullable|boolean',
            'enable_ai_assistant'      => 'nullable|boolean',
            'enable_customer_notes'    => 'nullable|boolean',
            'is_published'             => 'nullable|boolean',
            'custom_css'               => 'nullable|string',
            'layout_blocks'            => 'nullable|array',
            'banner_image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $data = [
            'slug'                     => Str::slug($request->slug),
            'page_title'               => $request->page_title,
            'tagline'                  => $request->tagline,
            'about_text'               => $request->about_text,
            'primary_color'            => $request->primary_color,
            'secondary_color'          => $request->secondary_color ?? '#7978e9',
            'allowed_service_modes'    => $request->allowed_service_modes,
            'enable_technician_picker' => $request->has('enable_technician_picker'),
            'enable_advance_payment'   => $request->has('enable_advance_payment'),
            'enable_ai_assistant'      => $request->has('enable_ai_assistant'),
            'enable_customer_notes'    => $request->has('enable_customer_notes'),
            'is_published'             => $request->has('is_published'),
            'custom_css'               => $request->custom_css,
        ];

        if ($request->filled('layout_blocks')) {
            $data['layout_blocks'] = $request->layout_blocks;
        }

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = 'banner_' . $companyId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/banners'), $filename);
            $data['banner_image'] = 'uploads/banners/' . $filename;
        }

        $setting->update($data);

        return back()->with('success', 'Booking Page settings updated successfully!');
    }

    /**
     * Save Gemini AI Assistant settings.
     */
    public function saveAiSettings(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'ai_assistant_name' => 'required|string|max:100',
            'gemini_api_key'    => 'nullable|string|max:255',
            'ai_instructions'   => 'nullable|string|max:2000',
        ]);

        DB::table('company_settings')->where('company_id', $companyId)->update([
            'ai_assistant_name' => $request->ai_assistant_name,
            'gemini_api_key'    => $request->gemini_api_key ? trim($request->gemini_api_key) : null,
            'ai_instructions'   => $request->ai_instructions,
        ]);

        return back()->with('success', 'AI Assistant settings updated successfully!');
    }
}
