<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailMarketingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_marketing_history', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->string('date', 255);
            $table->string('time', 255);
            $table->enum('type', ['schedule', 'sent'])->default('sent');
            $table->integer('template');
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
        Schema::dropIfExists('email_marketing_history');
    }
}
