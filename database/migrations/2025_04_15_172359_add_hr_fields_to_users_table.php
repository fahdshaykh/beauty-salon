<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHrFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('position')->nullable();
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('is_transport', 8, 2)->nullable();
            $table->decimal('transport_allowance', 8, 2)->default(0);
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
            $table->dropColumn([
                'designation',
                'basic_salary',
                'commission_rate',
                'joining_date',
                'is_active'
            ]);
        });
    }
}
