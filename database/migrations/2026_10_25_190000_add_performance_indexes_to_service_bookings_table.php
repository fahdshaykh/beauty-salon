<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexesToServiceBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->index(['company_id', 'due_date', 'service_status'], 'service_bookings_company_due_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropIndex('service_bookings_company_due_status_idx');
        });
    }
}
