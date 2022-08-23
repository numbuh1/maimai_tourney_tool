<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\Tournament;

class TournamentController extends Controller
{
    // List Tournament
    public function index()
    {
        $tourneys = Tournament::All();
        $data = [
            'tourneys' => $tourneys,
        ];
        return view('tournament.index', $data);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $tourney = new Tournament(); 
        $tourney->name = $input['name'];
        $tourney->save();

        return 1;
    }

    public function delete($id, Request $request)
    {
        $tourney = Tournament::find($id);
        if($tourney) {
            $tourney->delete();
        }
        return 1;
    }
}
