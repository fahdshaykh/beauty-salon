<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('title')->nullable();
            $table->string('subject')->nullable();
            $table->string('image')->nullable();
            $table->longText('header')->nullable();
            $table->longText('footer')->nullable();
            $table->unsignedBigInteger('company_id')->index()->nullable();
            $table->boolean('permission')->default(0);
            $table->longText('marketing_header')->nullable();
            $table->longText('marketing_footer')->nullable();
            $table->longText('quotation_header')->nullable();
            $table->longText('quotation_footer')->nullable();
            $table->timestamps();

            // If you have a companies table
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_settings');
    }
}
