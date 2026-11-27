<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingAutomation;
use App\Company;
use Illuminate\Support\Facades\DB;

class BookingAutomationController extends Controller
{
    /**
     * Display the automations hub for the company.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $company   = Company::findOrFail($companyId);

        $availableEvents = BookingAutomation::getAvailableEvents();
        $automations = BookingAutomation::where('company_id', $companyId)
            ->get()
            ->keyBy('trigger_event');

        return view('booking_automations.index', compact('company', 'availableEvents', 'automations'));
    }

    /**
     * Update an automation rule.
     */
    public function update(Request $request, string $event)
    {
        $companyId = auth()->user()->company_id;

        $request->validate([
            'is_enabled'     => 'nullable|boolean',
            'channel'        => 'required|in:sms,email,both',
            'sms_template'   => 'nullable|string|max:500',
            'email_subject'  => 'nullable|string|max:255',
            'email_template' => 'nullable|string|max:3000',
        ]);

        BookingAutomation::updateOrCreate(
            [
                'company_id'    => $companyId,
                'trigger_event' => $event,
            ],
            [
                'is_enabled'     => $request->has('is_enabled'),
                'channel'        => $request->channel,
                'sms_template'   => $request->sms_template,
                'email_subject'  => $request->email_subject,
                'email_template' => $request->email_template,
            ]
        );

        return back()->with('success', 'Automation rule updated successfully!');
    }
}
