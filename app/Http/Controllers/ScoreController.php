<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\MapPoolItem;

class ScoreController extends Controller
{
    //
    public function add()
    {
    	$mapPools = MapPool::pluck('name', 'id');
    	$players = Player::where('is_eliminated', 0)->pluck('name', 'id');
    	$data = [
    		'pools' => $mapPools
    	];
    	return view('score.add', $data);
    }
}
