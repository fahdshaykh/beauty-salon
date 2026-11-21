<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add payment tracking columns to service_bookings
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->default(0.00)->after('vat');
            }
            if (!Schema::hasColumn('service_bookings', 'amount_pending')) {
                $table->decimal('amount_pending', 10, 2)->default(0.00)->after('amount_paid');
            }
            if (!Schema::hasColumn('service_bookings', 'payment_status')) {
                $table->string('payment_status', 50)->default('unpaid')->after('amount_pending');
            }
        });

        // 2. Create booking_payments table
        if (!Schema::hasTable('booking_payments')) {
            Schema::create('booking_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('booking_id')->index();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method', 100)->nullable();
                $table->text('payment_details')->nullable();
                $table->string('payment_file', 255)->nullable();
                $table->date('payment_date');
                $table->string('payment_type', 50)->default('final'); // advance, final, installment
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('booking_id')
                      ->references('id')
                      ->on('service_bookings')
                      ->onDelete('cascade');
            });
        }

        // 3. Backfill historical bookings
        $bookings = DB::table('service_bookings')->get();
        foreach ($bookings as $b) {
            $total = (float)($b->total_price ?? 0) + (float)($b->vat ?? 0);
            if ((int)$b->service_status === 3) {
                // Completed booking: fully paid
                DB::table('service_bookings')->where('id', $b->id)->update([
                    'amount_paid'    => $total,
                    'amount_pending' => 0.00,
                    'payment_status' => 'paid',
                ]);

                // Create transaction entry if not already present
                $exists = DB::table('booking_payments')->where('booking_id', $b->id)->exists();
                if (!$exists && $total > 0) {
                    $pDate = !empty($b->due_date) ? date('Y-m-d', strtotime($b->due_date)) : date('Y-m-d');
                    DB::table('booking_payments')->insert([
                        'company_id'      => $b->company_id,
                        'booking_id'      => $b->id,
                        'amount'          => $total,
                        'payment_method'  => $b->payment_instrument ?: 'cash',
                        'payment_details' => $b->payment_details,
                        'payment_file'    => null,
                        'payment_date'    => $pDate,
                        'payment_type'    => 'final',
                        'notes'           => 'Migrated completed booking payment',
                        'created_by'      => $b->user_id,
                        'created_at'      => $b->updated_at ?? now(),
                        'updated_at'      => $b->updated_at ?? now(),
                    ]);
                }
            } else {
                // Non-completed: unpaid
                DB::table('service_bookings')->where('id', $b->id)->update([
                    'amount_paid'    => 0.00,
                    'amount_pending' => $total,
                    'payment_status' => 'unpaid',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_payments');

        Schema::table('service_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('service_bookings', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }
            if (Schema::hasColumn('service_bookings', 'amount_pending')) {
                $table->dropColumn('amount_pending');
            }
            if (Schema::hasColumn('service_bookings', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
