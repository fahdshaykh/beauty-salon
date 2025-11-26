<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained();
            
            // Current month usage
            $table->integer('employees_used')->default(0);
            $table->integer('customers_used')->default(0);
            $table->integer('bookings_used')->default(0);
            $table->integer('quotations_used')->default(0);
            $table->integer('emails_used')->default(0);
            $table->integer('invoices_used')->default(0);
            
            // Manual override by admin (optional)
            $table->integer('override_employees')->nullable();
            $table->integer('override_customers')->nullable();
            $table->integer('override_bookings')->nullable();
            $table->integer('override_quotations')->nullable();
            $table->integer('override_emails')->nullable();
            $table->integer('override_invoices')->nullable();
            
            $table->string('current_month', 7); // e.g., 2025-11
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
        Schema::dropIfExists('company_limits');
    }
}
