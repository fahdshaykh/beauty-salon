<?php



use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;



class CreateServicesTable extends Migration

{

    /**

     * Run the migrations.

     *

     * @return void

     */

    public function up()

    {

        Schema::create('services', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('name')->nullable();
            $table->string('price_type')->default('modify_able');
            $table->string('service_color')->nullable();
            $table->string('risk_status')->default('not_risky');

            $table->float('price', 5, 2)->nullable();

            $table->integer('company_id')->nullable();

            $table->integer('is_active')->default(1);

            $table->text('description')->nullable();

            $table->integer('is_deleted')->default(0);

            $table->timestamps();

        });

    }



    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        Schema::dropIfExists('services');

    }

}

