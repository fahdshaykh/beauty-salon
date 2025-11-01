<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodayScheduledEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('today_scheduled_emails', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable();
            $table->integer('template_id')->nullable();
            $table->string('schedule_date', 255);
            $table->string('schedule_time', 255);
            $table->integer('company_id')->nullable();
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
        Schema::dropIfExists('today_scheduled_emails');
    }
}
