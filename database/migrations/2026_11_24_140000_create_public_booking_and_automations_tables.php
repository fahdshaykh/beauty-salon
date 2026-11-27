<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Create booking_page_settings table
        if (!Schema::hasTable('booking_page_settings')) {
            Schema::create('booking_page_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->unique();
                $table->string('slug', 100)->unique();
                $table->string('page_title', 255)->nullable();
                $table->string('tagline', 255)->nullable();
                $table->text('about_text')->nullable();
                $table->string('banner_image', 255)->nullable();
                $table->string('primary_color', 20)->default('#4b49ac');
                $table->string('secondary_color', 20)->default('#7978e9');
                $table->string('allowed_service_modes', 20)->default('both'); // 'both', 'in_shop', 'home_service'
                $table->boolean('enable_technician_picker')->default(true);
                $table->boolean('enable_advance_payment')->default(false);
                $table->boolean('enable_ai_assistant')->default(true);
                $table->boolean('enable_customer_notes')->default(true);
                $table->text('custom_css')->nullable();
                $table->json('layout_blocks')->nullable();
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                $table->index(['company_id', 'is_published']);
            });
        }

        // 2. Create booking_automations table
        if (!Schema::hasTable('booking_automations')) {
            Schema::create('booking_automations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->index();
                $table->string('trigger_event', 50); // booking_confirmed, reminder_24h, reminder_2h, technician_dispatched, job_completed, recall_30d
                $table->boolean('is_enabled')->default(true);
                $table->string('channel', 20)->default('both'); // 'sms', 'email', 'both'
                $table->text('sms_template')->nullable();
                $table->string('email_subject', 255)->nullable();
                $table->text('email_template')->nullable();
                $table->integer('delay_minutes')->default(0);
                $table->timestamps();

                $table->unique(['company_id', 'trigger_event'], 'comp_trigger_unique');
            });
        }

        // 3. Add AI settings to company_settings table
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'gemini_api_key')) {
                $table->text('gemini_api_key')->nullable()->after('complete_booking_email_permission');
            }
            if (!Schema::hasColumn('company_settings', 'ai_assistant_name')) {
                $table->string('ai_assistant_name', 100)->default('Smart Booking Assistant')->after('gemini_api_key');
            }
            if (!Schema::hasColumn('company_settings', 'ai_instructions')) {
                $table->text('ai_instructions')->nullable()->after('ai_assistant_name');
            }
        });

        // 4. Seed Default Settings & Automations for Existing Companies
        $companies = DB::table('companies')->get();
        $now = now();

        $defaultBlocks = [
            ['id' => 'hero', 'name' => 'Hero Banner & About', 'visible' => true],
            ['id' => 'modes', 'name' => 'Service Location (Shop vs Home)', 'visible' => true],
            ['id' => 'services', 'name' => 'Categories & Services', 'visible' => true],
            ['id' => 'technicians', 'name' => 'Technician / Specialist', 'visible' => true],
            ['id' => 'schedule', 'name' => 'Date & Time Slot Picker', 'visible' => true],
            ['id' => 'customer_info', 'name' => 'Customer Details & Address', 'visible' => true],
            ['id' => 'payment', 'name' => 'Summary & Payment', 'visible' => true],
        ];

        $defaultAutomations = [
            [
                'trigger_event' => 'booking_confirmed',
                'is_enabled'    => true,
                'channel'       => 'both',
                'sms_template'  => 'Hi {customer_name}, your booking for {service_name} on {due_date} at {due_time} is confirmed! Tracking: {tracking_link}',
                'email_subject' => 'Booking Confirmation - {company_name}',
                'email_template'=> "Hello {customer_name},\n\nYour service booking for {service_name} has been successfully confirmed.\n\nDate: {due_date}\nTime: {due_time}\nStaff: {technician_name}\nLocation: {booking_address}\nInvoice #: {invoice_no}\n\nTrack or manage your booking here: {tracking_link}\n\nThank you,\n{company_name}",
            ],
            [
                'trigger_event' => 'reminder_24h',
                'is_enabled'    => true,
                'channel'       => 'both',
                'sms_template'  => 'Reminder: Hi {customer_name}, your appointment for {service_name} with {company_name} is tomorrow at {due_time}.',
                'email_subject' => 'Appointment Reminder (Tomorrow) - {company_name}',
                'email_template'=> "Hello {customer_name},\n\nThis is a friendly reminder that you have an appointment tomorrow.\n\nService: {service_name}\nDate: {due_date}\nTime: {due_time}\nStaff: {technician_name}\nAddress: {booking_address}\n\nSee you soon!\n{company_name}",
            ],
            [
                'trigger_event' => 'reminder_2h',
                'is_enabled'    => true,
                'channel'       => 'sms',
                'sms_template'  => 'Hi {customer_name}, your {service_name} appointment is in 2 hours ({due_time}). Please be ready! - {company_name}',
                'email_subject' => 'Your appointment is in 2 hours - {company_name}',
                'email_template'=> "Hello {customer_name},\n\nYour upcoming service {service_name} starts in 2 hours at {due_time}.\n\nThank you,\n{company_name}",
            ],
            [
                'trigger_event' => 'technician_dispatched',
                'is_enabled'    => true,
                'channel'       => 'sms',
                'sms_template'  => 'Good news {customer_name}! Technician {technician_name} is now on the way to your location for {service_name}.',
                'email_subject' => 'Technician En Route - {company_name}',
                'email_template'=> "Hello {customer_name},\n\nYour technician {technician_name} has been dispatched and is currently en route to your service address.\n\n{company_name}",
            ],
            [
                'trigger_event' => 'job_completed',
                'is_enabled'    => true,
                'channel'       => 'both',
                'sms_template'  => 'Thank you for choosing {company_name}! How was your service? Please rate us: {review_link}',
                'email_subject' => 'Thank you for choosing {company_name} - How did we do?',
                'email_template'=> "Hello {customer_name},\n\nThank you for choosing {company_name}! Your booking for {service_name} is complete.\n\nWe would appreciate your feedback. Please leave us a review here: {review_link}\n\nBest regards,\n{company_name}",
            ],
            [
                'trigger_event' => 'recall_30d',
                'is_enabled'    => true,
                'channel'       => 'both',
                'sms_template'  => 'Hi {customer_name}, it has been a while since your last service with {company_name}! Book your next visit here: {booking_link}',
                'email_subject' => 'Time for your next service? - {company_name}',
                'email_template'=> "Hello {customer_name},\n\nIt has been a month since your last appointment with us! Keeping up regular maintenance and care ensures top results.\n\nReady to book again? Click here: {booking_link}\n\nWarm regards,\n{company_name}",
            ],
        ];

        foreach ($companies as $comp) {
            $compName = $comp->company_name ?? ($comp->smart_name ?? ('company-' . $comp->id));
            // Generate clean unique slug
            $baseSlug = Str::slug($compName ?: 'company-' . $comp->id);
            if (empty($baseSlug)) {
                $baseSlug = 'company-' . $comp->id;
            }
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('booking_page_settings')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            // Create booking_page_settings
            $exists = DB::table('booking_page_settings')->where('company_id', $comp->id)->exists();
            if (!$exists) {
                DB::table('booking_page_settings')->insert([
                    'company_id'              => $comp->id,
                    'slug'                    => $slug,
                    'page_title'              => $compName ?: 'Service Booking',
                    'tagline'                 => 'Book your appointment online in minutes',
                    'about_text'              => 'Choose your preferred service, specialist, and time slot. We guarantee top-quality service delivered on time.',
                    'primary_color'           => '#4b49ac',
                    'secondary_color'         => '#7978e9',
                    'allowed_service_modes'   => 'both',
                    'enable_technician_picker'=> true,
                    'enable_advance_payment'  => false,
                    'enable_ai_assistant'     => true,
                    'enable_customer_notes'   => true,
                    'layout_blocks'           => json_encode($defaultBlocks),
                    'is_published'            => true,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]);
            }

            // Create booking_automations
            foreach ($defaultAutomations as $auto) {
                $autoExists = DB::table('booking_automations')
                    ->where('company_id', $comp->id)
                    ->where('trigger_event', $auto['trigger_event'])
                    ->exists();

                if (!$autoExists) {
                    DB::table('booking_automations')->insert(array_merge($auto, [
                        'company_id' => $comp->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_automations');
        Schema::dropIfExists('booking_page_settings');

        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'ai_instructions')) {
                $table->dropColumn('ai_instructions');
            }
            if (Schema::hasColumn('company_settings', 'ai_assistant_name')) {
                $table->dropColumn('ai_assistant_name');
            }
            if (Schema::hasColumn('company_settings', 'gemini_api_key')) {
                $table->dropColumn('gemini_api_key');
            }
        });
    }
};
