<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypesToServiceBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('payment_instrument')->nullable()->after('service_status');
            $table->string('payment_instrument_number')->nullable()->after('payment_instrument');
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
            $table->string('payment_instrument')->nullable()->after('service_status');
            $table->string('payment_instrument_number')->nullable()->after('payment_instrument');
        });
    }
}
