<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapPoolScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_pool_scores', function (Blueprint $table) {
            $table->id();
            $table->integer('map_pool_item_id');
            $table->integer('player_id');
            $table->double('achievement_score', 8, 4)->nullable();
            $table->integer('dx_score')->nullable();
            $table->string('photo_path')->nullable();
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
        Schema::dropIfExists('map_pool_scores');
    }
}
