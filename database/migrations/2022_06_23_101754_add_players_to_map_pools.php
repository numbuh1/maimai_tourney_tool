<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlayersToMapPools extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('map_pools', function (Blueprint $table) {
            //
            $table->integer('player_1')->nullable(true);
            $table->integer('player_2')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('map_pools', function (Blueprint $table) {
            Schema::dropColumn('map_pools', 'player_1');
            Schema::dropColumn('map_pools', 'player_2');
        });
    }
}
