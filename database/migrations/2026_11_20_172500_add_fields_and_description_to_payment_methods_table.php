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
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'description')) {
                $table->text('description')->nullable()->after('method');
            }
            if (!Schema::hasColumn('payment_methods', 'fields')) {
                $table->json('fields')->nullable()->after('description');
            }
            if (!Schema::hasColumn('payment_methods', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('fields');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('payment_methods', 'description')) {
                $columns[] = 'description';
            }
            if (Schema::hasColumn('payment_methods', 'fields')) {
                $columns[] = 'fields';
            }
            if (Schema::hasColumn('payment_methods', 'is_active')) {
                $columns[] = 'is_active';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
