<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->string('site_name', 100);
            $table->string('smart_name', 11);
            $table->string('site_logo', 255);
            $table->string('smart_logo', 255);
            $table->integer('email_permissions')->default(0);
            $table->integer('message_permissions')->default(0);
            $table->integer('review_permissions')->default(0);
            $table->text('review_url');
            $table->string('email', 255);
            $table->integer('tax_status')->default(0);
            $table->integer('vat');
            $table->integer('amount')->nullable();
            $table->text('voodoosms_api_key')->nullable();
            $table->integer('sms_count')->default(0);
            $table->string('booking_sms', 255)->default('Thanks for booking {services name} {date} {time}. For changes please contact us.');
            $table->string('re_sheduled_sms', 255)->default('this is predefined reschedule sms');
            $table->text('survey_sms')->nullable();
            $table->text('clicksend_api_key')->nullable();
            $table->string('clicksend_username', 255)->nullable();
            $table->string('clicksend_password', 255)->nullable();
            $table->integer('clicksend_returnaddress');
            $table->text('facebook')->nullable();
            $table->text('instagram')->nullable();
            $table->text('twitter')->nullable();
            $table->integer('sms_limit')->default(200);
            $table->integer('sms_optout_msg')->default(0);
            $table->date('financial_year')->nullable();
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
        Schema::dropIfExists('company_settings');
    }
}
