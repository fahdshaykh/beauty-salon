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
        Schema::table('inventories_purchases', function (Blueprint $table) {
            $table->index(['company_id', 'supplier_id', 'purchase_date'], 'idx_inv_purchases_company_supplier_date');
        });

        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->index(['company_id', 'supplier_id', 'created_at'], 'idx_supplier_payments_company_supplier_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_purchases', function (Blueprint $table) {
            $table->dropIndex('idx_inv_purchases_company_supplier_date');
        });

        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropIndex('idx_supplier_payments_company_supplier_created');
        });
    }
};
