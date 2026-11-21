<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('inventories_purchases') && !Schema::hasColumn('inventories_purchases', 'payment_details')) {
            Schema::table('inventories_purchases', function (Blueprint $table) {
                $table->json('payment_details')->nullable()->after('payment_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventories_purchases') && Schema::hasColumn('inventories_purchases', 'payment_details')) {
            Schema::table('inventories_purchases', function (Blueprint $table) {
                $table->dropColumn('payment_details');
            });
        }
    }
};
