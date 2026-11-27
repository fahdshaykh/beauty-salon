<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Company;

class BookingAutomation extends Model
{
    use HasFactory;

    protected $table = 'booking_automations';

    protected $fillable = [
        'company_id',
        'trigger_event',
        'is_enabled',
        'channel',
        'sms_template',
        'email_subject',
        'email_template',
        'delay_minutes',
    ];

    protected $casts = [
        'is_enabled'    => 'boolean',
        'delay_minutes' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public static function getAvailableEvents(): array
    {
        return [
            'booking_confirmed'     => [
                'name'        => 'Booking Confirmed',
                'description' => 'Sent instantly when an appointment is booked and confirmed.',
                'icon'        => 'fas fa-check-circle text-success',
            ],
            'reminder_24h'          => [
                'name'        => '24-Hour Reminder',
                'description' => 'Sent 24 hours prior to appointment to prevent no-shows.',
                'icon'        => 'fas fa-clock text-warning',
            ],
            'reminder_2h'           => [
                'name'        => '2-Hour Final Reminder',
                'description' => 'Sent 2 hours before the scheduled time as a quick alert.',
                'icon'        => 'fas fa-bell text-info',
            ],
            'technician_dispatched' => [
                'name'        => 'Technician Dispatched / En Route',
                'description' => 'Sent when the technician is on the way to the customer location.',
                'icon'        => 'fas fa-truck text-primary',
            ],
            'job_completed'         => [
                'name'        => 'Job Completed & Review Request',
                'description' => 'Sent after completion with a digital receipt and review rating link.',
                'icon'        => 'fas fa-star text-warning',
            ],
            'recall_30d'            => [
                'name'        => '30-Day Customer Win-Back Recall',
                'description' => 'Sent 30 days after last completed service to encourage rebooking.',
                'icon'        => 'fas fa-redo text-danger',
            ],
        ];
    }
}
