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
        if (!Schema::hasTable('user_settings')) {
            Schema::create('user_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            // Migrate existing JSON settings from users table into user_settings
            $usersWithSettings = DB::table('users')->whereNotNull('settings')->get();
            foreach ($usersWithSettings as $u) {
                if (!empty($u->settings)) {
                    DB::table('user_settings')->insert([
                        'user_id'    => $u->id,
                        'settings'   => is_string($u->settings) ? $u->settings : json_encode($u->settings),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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
        Schema::dropIfExists('user_settings');
    }
};
