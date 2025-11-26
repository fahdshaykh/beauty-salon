<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Basic, Pro, Enterprise
            $table->decimal('price_yearly', 10, 2);
            $table->integer('max_employees')->nullable();     // null = unlimited
            $table->integer('max_customers')->nullable();
            $table->integer('max_bookings')->nullable();
            $table->integer('max_quotations')->nullable();
            $table->integer('max_emails')->nullable();
            $table->integer('max_invoices')->nullable();
            $table->boolean('is_unlimited')->default(false);
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
        Schema::dropIfExists('subscription_plans');
    }
}
