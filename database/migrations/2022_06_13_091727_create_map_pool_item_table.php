<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapPoolItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_pool_items', function (Blueprint $table) {
            $table->id();
            $table->integer('map_pool_id')->nullable();
            $table->integer('song_id')->nullable();
            $table->integer('chart_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('is_banned')->nullable();
            $table->integer('is_selected')->nullable();
            $table->integer('order')->nullable();
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
        Schema::dropIfExists('map_pool_items');
    }
}
