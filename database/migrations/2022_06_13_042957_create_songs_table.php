<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('songId')->nullable();
            $table->string('category')->nullable();
            $table->string('title')->nullable();
            $table->string('artist')->nullable();
            $table->string('imageName')->nullable();
            $table->string('version')->nullable();
            $table->string('releaseDate')->nullable();
            $table->string('isNew')->nullable();
            $table->string('isLocked')->nullable();
            $table->string('bpm')->nullable();
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
        Schema::dropIfExists('songs');
    }
}
