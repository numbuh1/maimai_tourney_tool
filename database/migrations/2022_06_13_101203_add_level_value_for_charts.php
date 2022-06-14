<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Chart;

class AddLevelValueForCharts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('charts', function (Blueprint $table) {
            if (!Schema::hasColumn('charts', 'level_value')) {
                $table->float('level_value')->nullable(true);
            }
        });

        $charts = Chart::whereNull('level_value')->get();
        foreach ($charts as $key => $chart) {
            if(strpos($chart->level, '+')) {
                $chart->level_value = explode('+', $chart->level)[0] . '.5';
            } else {
                $chart->level_value = $chart->level;
            }
            $chart->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('charts', function (Blueprint $table) {
            if (Schema::hasColumn('charts', 'level_value')) {
                Schema::dropColumn('charts', 'level_value');
            }
        });
    }
}
