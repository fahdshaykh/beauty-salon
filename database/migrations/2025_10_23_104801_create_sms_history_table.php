<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_history', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 255);
            $table->string('date', 255);
            $table->string('time', 255);
            $table->enum('type', ['sent', 'schedule'])->default('sent');
            $table->integer('template');
            $table->string('schedule_id', 255)->default('0');
            $table->integer('company_id');
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
        Schema::dropIfExists('sms_history');
    }
}
