<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnglishNameForSongs extends Migration
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
            if (!Schema::hasColumn('songs', 'title_eng')) {
                $table->string('title_eng')->nullable(true);
            }
            if (!Schema::hasColumn('songs', 'artist_eng')) {
                $table->string('artist_eng')->nullable(true);
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
            if (Schema::hasColumn('songs', 'title_eng')) {
                Schema::dropColumn('songs', 'title_eng');
            }
            if (Schema::hasColumn('songs', 'artist_eng')) {
                Schema::dropColumn('songs', 'artist_eng');
            }
        });
    }
}
