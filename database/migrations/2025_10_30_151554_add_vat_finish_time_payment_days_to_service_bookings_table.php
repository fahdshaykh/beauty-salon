<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatFinishTimePaymentDaysToServiceBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->float('vat')->default(0)->after('total_price');
            $table->integer('finish_time')->nullable()->after('due_time');
            $table->integer('payment_days')->after('payment_instrument_number');
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
            $table->dropColumn(['vat', 'finish_time', 'payment_days']);
        });
    }
}
