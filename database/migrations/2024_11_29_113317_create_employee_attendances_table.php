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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('status');
            $table->string('leave_type')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('is_late')->default(false);
            $table->string('hours_worked')->nullable();
            $table->integer('overtime_minutes')->default(0);
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();
            $table->string('transport_assistance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
