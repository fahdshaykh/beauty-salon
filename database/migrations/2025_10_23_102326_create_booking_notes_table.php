<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->text('details')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            // Optional foreign key constraint
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_notes');
    }
}
