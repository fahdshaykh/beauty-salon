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
        if (Schema::hasTable('expenses') && !Schema::hasColumn('expenses', 'payment_file')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('payment_file')->nullable()->after('payment_details');
            });
        }

        if (Schema::hasTable('inventories_purchases') && !Schema::hasColumn('inventories_purchases', 'payment_file')) {
            Schema::table('inventories_purchases', function (Blueprint $table) {
                $table->string('payment_file')->nullable()->after('payment_details');
            });
        }

        if (Schema::hasTable('supplier_payments') && !Schema::hasColumn('supplier_payments', 'payment_file')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->string('payment_file')->nullable()->after('payment_details');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'payment_file')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('payment_file');
            });
        }

        if (Schema::hasTable('inventories_purchases') && Schema::hasColumn('inventories_purchases', 'payment_file')) {
            Schema::table('inventories_purchases', function (Blueprint $table) {
                $table->dropColumn('payment_file');
            });
        }

        if (Schema::hasTable('supplier_payments') && Schema::hasColumn('supplier_payments', 'payment_file')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $table->dropColumn('payment_file');
            });
        }
    }
};
