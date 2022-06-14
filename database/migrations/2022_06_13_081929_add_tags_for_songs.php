<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagsForSongs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('songs', function (Blueprint $table) {
            if (!Schema::hasColumn('songs', 'tag')) {
                $table->text('tag')->nullable(true);
            }
            if (!Schema::hasColumn('songs', 'rec_sort')) {
                $table->integer('rec_sort')->nullable(true);
            }
            if (!Schema::hasColumn('songs', 'sega_song_id')) {
                $table->integer('sega_song_id')->nullable(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('songs', function (Blueprint $table) {
            if (Schema::hasColumn('songs', 'tag')) {
                Schema::dropColumn('songs', 'tag');
            }
            if (Schema::hasColumn('songs', 'rec_sort')) {
                Schema::dropColumn('songs', 'rec_sort');
            }
            if (Schema::hasColumn('songs', 'sega_song_id')) {
                Schema::dropColumn('songs', 'sega_song_id');
            }
        });
    }
}
