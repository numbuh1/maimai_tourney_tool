<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');            
            $table->timestamps();
        });

        Schema::table('players', function (Blueprint $table) {
            $table->integer('tourney_id')->default(0);
        });

        Schema::table('map_pools', function (Blueprint $table) {
            $table->integer('tourney_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');

        Schema::table('players', function (Blueprint $table) {
            Schema::dropColumn('players', 'tourney_id');
        });

        Schema::table('map_pools', function (Blueprint $table) {
            Schema::dropColumn('map_pools', 'tourney_id');
        });
    }
}
