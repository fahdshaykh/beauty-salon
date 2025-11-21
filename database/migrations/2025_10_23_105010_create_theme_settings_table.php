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
            $table->string('site_name', 50)->nullable();
            $table->string('smart_name', 11)->nullable();
            $table->string('site_logo', 100)->nullable();
            $table->string('smart_logo', 100)->nullable();
            $table->integer('company_id')->nullable();
            $table->text('booking_sms')->nullable();
            $table->text('whatsapp_sms')->nullable();
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
