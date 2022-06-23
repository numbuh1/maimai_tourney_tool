<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShortNameForSongs extends Migration
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
            if (!Schema::hasColumn('songs', 'short_name')) {
                $table->string('short_name')->nullable(true);
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
            if (Schema::hasColumn('songs', 'short_name')) {
                Schema::dropColumn('songs', 'short_name');
            }
        });
    }
}
