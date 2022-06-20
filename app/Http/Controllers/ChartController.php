<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Chart;

class ChartController extends Controller
{
    // Show chart data
    public function showChart($chartId)
    {
    	$chart = Chart::find($chartId);
    	$song = Song::find($chart->song_id);

    	$data = [
    		'chart' => $chart,
    		'song' => $song
    	];
    	
    	return view('chart.detail', $data);
    }

    // Show chart detail
    public function detail($chartId)
    {
        $chart = Chart::find($chartId);
        $song = Song::find($chart->song_id);

        $data = [
            'chart' => $chart,
            'song' => $song
        ];
        
        return json_encode($data);
    }
}
