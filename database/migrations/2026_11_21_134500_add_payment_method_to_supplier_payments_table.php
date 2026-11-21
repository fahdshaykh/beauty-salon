<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('supplier_payments')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('supplier_payments', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('amount');
                }
                if (!Schema::hasColumn('supplier_payments', 'payment_details')) {
                    $table->json('payment_details')->nullable()->after('payment_method');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('supplier_payments')) {
            Schema::table('supplier_payments', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('supplier_payments', 'payment_method')) {
                    $columns[] = 'payment_method';
                }
                if (Schema::hasColumn('supplier_payments', 'payment_details')) {
                    $columns[] = 'payment_details';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
