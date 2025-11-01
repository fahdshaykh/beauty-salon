<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('description', 255)->nullable();
            $table->double('quantity', 10, 2);
            $table->double('price', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->decimal('amount_pending', 10, 2)->default(0.00);
            $table->string('payment_type', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            // Foreign key constraint (optional if you already manage manually)
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_purchases');
    }
}
