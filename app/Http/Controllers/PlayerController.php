<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Tournament;

class PlayerController extends Controller
{
    //
    public function index($tourney_id)
    {
    	$players = Player::where('tourney_id', $tourney_id)->get();
        $tourney = Tournament::find($tourney_id);

    	$data = [
    		'players' => $players,
            'tourney' => $tourney
    	];

    	return view('player.index', $data);
    }

    public function store(Request $request, $tourney_id)
    {
    	$input = $request->all();
    	if($input['id']) {
    		$player = Player::find($input['id']);
    	} else {
    		$player = new Player();	
    	}
    	$player->name = $input['name'];
        $is_eliminated = 0;
        $input['is_eliminated'];
        if(isset($input['is_eliminated']) && $input['is_eliminated'] != 'false')
            $is_eliminated = 1;
    	$player->is_eliminated = $is_eliminated;
        $player->tourney_id = $tourney_id;
    	$player->save();

    	return 1;
    }

    public function delete($id, Request $request)
    {
    	$player = Player::find($id);
    	if($player) {
    		$player->delete();
    	}
    	return 1;
    }
}
