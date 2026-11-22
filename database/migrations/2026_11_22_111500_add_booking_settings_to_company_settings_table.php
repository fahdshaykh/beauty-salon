<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'advance_payment_permission')) {
                $table->boolean('advance_payment_permission')->default(true)->after('email_permissions');
            }
            if (!Schema::hasColumn('company_settings', 'booking_inventory_permission')) {
                $table->boolean('booking_inventory_permission')->default(true)->after('advance_payment_permission');
            }
            if (!Schema::hasColumn('company_settings', 'complete_booking_email_permission')) {
                $table->boolean('complete_booking_email_permission')->default(true)->after('booking_inventory_permission');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'complete_booking_email_permission')) {
                $table->dropColumn('complete_booking_email_permission');
            }
            if (Schema::hasColumn('company_settings', 'booking_inventory_permission')) {
                $table->dropColumn('booking_inventory_permission');
            }
            if (Schema::hasColumn('company_settings', 'advance_payment_permission')) {
                $table->dropColumn('advance_payment_permission');
            }
        });
    }
};
