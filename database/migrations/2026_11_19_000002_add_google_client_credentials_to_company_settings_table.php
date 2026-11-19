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
        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'google_client_id')) {
                $table->text('google_client_id')->nullable()->after('google_calendar_id');
            }
            if (!Schema::hasColumn('company_settings', 'google_client_secret')) {
                $table->text('google_client_secret')->nullable()->after('google_client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('company_settings', 'google_client_id')) {
                $columnsToDrop[] = 'google_client_id';
            }
            if (Schema::hasColumn('company_settings', 'google_client_secret')) {
                $columnsToDrop[] = 'google_client_secret';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
