<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id'); // matches int unsigned auto_increment
            $table->string('first_name', 20)->nullable();
            $table->string('last_name', 20)->nullable();
            $table->string('country_code', 255)->nullable();
            $table->string('primary_phone', 20)->nullable();
            $table->string('secondary_phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('date_of_birth', 55)->nullable();
            $table->string('zip', 15)->nullable();
            $table->text('address')->nullable();
            $table->text('private_notes')->nullable();
            $table->string('referred_by', 20)->nullable();
            $table->integer('company_id')->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->integer('optout')->default(0);
            $table->integer('sms_optout')->default(0);
            $table->integer('sms_optout_msg')->default(0);
            $table->integer('total_bookings')->nullable();
            $table->integer('is_deleted')->default(0);
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('customers');
    }
}
