<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    //
    public function index()
    {
    	$players = Player::all();

    	$data = [
    		'players' => $players,
    	];

    	return view('player.index', $data);
    }

    public function store(Request $request)
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
