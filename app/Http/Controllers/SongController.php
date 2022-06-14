<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constant\GameConstant;
use App\Models\Song;
use App\Models\Chart;

class SongController extends Controller
{
	// List Page
    public function index(Request $request)
    {
    	$song_list_url = route('song.list', [], false);
    	$categories = GameConstant::SONG_CATEGORIES;
    	$versions = GameConstant::VERSIONS;

    	$data = [
    		'song_list_url' => $song_list_url,
    		'categories' => $categories,
    		'versions' => $versions
    	];

    	return view('song.index', $data);
    }

    // Show datatable
    public function list(Request $request)
    {
    	return Song::list($request);
    }

    // Search songs
    public function search(Request $request)
    {
    	$charts = Chart::search($request);

    	return json_encode($charts->toArray());
    }

    // Random a song
    public function random(Request $request)
    {
    	$input = $request->all();
    	$count = $input['count'] ?? 1;
    	$charts = Chart::search($request);

    	$results = array_rand($charts->toArray(), $count);
    	$response = [];
    	if($count > 1) {
    		foreach ($results as $key => $result) {
	    		$response[] = $charts[$result];
	    	}
    	} else {
    		$response[] = $charts[$results];
    	}

    	return json_encode($response);
    }
}
