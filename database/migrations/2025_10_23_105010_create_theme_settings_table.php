<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('theme_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 50);
            $table->string('smart_name', 11);
            $table->string('site_logo', 100)->nullable();
            $table->string('smart_logo', 100);
            $table->integer('company_id');
            $table->text('booking_sms');
            $table->text('whatsapp_sms');
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
        Schema::dropIfExists('theme_settings');
    }
}
