<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserStatusesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->after('id');
            $table->string('role')->nullable()->after('id');
            $table->integer('company_id')->nullable()->after('role_id');
            $table->integer('is_active')->default(1)->after('image');
            $table->integer('is_deleted')->default(0)->after('is_active');
            $table->string('last_login_ip')->nullable()->after('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_id')->nullable()->after('id');
            $table->integer('is_active')->default(1)->after('image');
            $table->integer('is_deleted')->default(0)->after('is_active');
        });
    }
}
