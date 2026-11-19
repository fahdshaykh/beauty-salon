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
            if (!Schema::hasColumn('company_settings', 'google_access_token')) {
                $table->longText('google_access_token')->nullable()->after('google_calendar_id');
            }
            if (!Schema::hasColumn('company_settings', 'google_account_email')) {
                $table->string('google_account_email', 255)->nullable()->after('google_access_token');
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
            if (Schema::hasColumn('company_settings', 'google_account_email')) {
                $table->dropColumn('google_account_email');
            }
            if (Schema::hasColumn('company_settings', 'google_access_token')) {
                $table->dropColumn('google_access_token');
            }
        });
    }
};
