<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Company;

class BookingPageSetting extends Model
{
    use HasFactory;

    protected $table = 'booking_page_settings';

    protected $fillable = [
        'company_id',
        'slug',
        'page_title',
        'tagline',
        'about_text',
        'banner_image',
        'primary_color',
        'secondary_color',
        'allowed_service_modes',
        'enable_technician_picker',
        'enable_advance_payment',
        'enable_ai_assistant',
        'enable_customer_notes',
        'custom_css',
        'layout_blocks',
        'is_published',
    ];

    protected $casts = [
        'enable_technician_picker' => 'boolean',
        'enable_advance_payment'   => 'boolean',
        'enable_ai_assistant'      => 'boolean',
        'enable_customer_notes'    => 'boolean',
        'is_published'             => 'boolean',
        'layout_blocks'            => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/book/' . $this->slug);
    }

    public function getOrderedBlocks(): array
    {
        $defaultBlocks = [
            ['id' => 'hero', 'name' => 'Hero Banner & About', 'visible' => true],
            ['id' => 'modes', 'name' => 'Service Location (Shop vs Home)', 'visible' => true],
            ['id' => 'services', 'name' => 'Categories & Services', 'visible' => true],
            ['id' => 'technicians', 'name' => 'Technician / Specialist', 'visible' => true],
            ['id' => 'schedule', 'name' => 'Date & Time Slot Picker', 'visible' => true],
            ['id' => 'customer_info', 'name' => 'Customer Details & Address', 'visible' => true],
            ['id' => 'payment', 'name' => 'Summary & Payment', 'visible' => true],
        ];

        return is_array($this->layout_blocks) && count($this->layout_blocks) > 0
            ? $this->layout_blocks
            : $defaultBlocks;
    }
}
