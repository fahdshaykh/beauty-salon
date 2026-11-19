<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'google_calendar_sync')) {
                $table->boolean('google_calendar_sync')->default(0)->after('currency_symbol');
            }
            if (!Schema::hasColumn('company_settings', 'google_calendar_id')) {
                $table->string('google_calendar_id', 255)->nullable()->after('google_calendar_sync');
            }
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'google_event_id')) {
                $table->string('google_event_id', 255)->nullable()->after('booking_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'google_calendar_id')) {
                $table->dropColumn('google_calendar_id');
            }
            if (Schema::hasColumn('company_settings', 'google_calendar_sync')) {
                $table->dropColumn('google_calendar_sync');
            }
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('service_bookings', 'google_event_id')) {
                $table->dropColumn('google_event_id');
            }
        });
    }
};
