<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('service_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('inventory_id');
            $table->integer('quantity');
            $table->double('total_price', 8, 2);
            $table->string('due_date')->nullable();
            $table->text('comment')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('service_status')->default(1);
            $table->integer('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_bookings');
    }
}
