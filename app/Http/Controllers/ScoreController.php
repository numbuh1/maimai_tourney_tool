<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\MapPoolItem;
use App\Models\Player;
use App\Models\Score;
use App\Models\Song;
use App\Models\Chart;
use Illuminate\Support\Facades\Storage;

class ScoreController extends Controller
{
    //
    public function edit($map_pool_item_id, $player_id)
    {
        $poolItem = MapPoolItem::find($map_pool_item_id);
    	$pool = MapPool::where('id', $poolItem->map_pool_id)->first();
        $player = Player::find($player_id);
        $score = Score::where('map_pool_item_id', $map_pool_item_id)
                        ->where('player_id', $player_id)
                        ->first();

        $chart = Chart::find($poolItem->chart_id);
        $song = Song::find($chart->song_id);
    	// $players = Player::where('is_eliminated', 0)->pluck('name', 'id');
    	$data = [
    		'pool' => $pool,
            'poolItem' => $poolItem,
            'song' => $song,
            'chart' => $chart,
            'score' => $score,
            'player' => $player,
    	];
    	return view('pool.score_submit', $data);
    }

    // Store Map Pool
    public function store(Request $request, $map_pool_item_id, $player_id)
    {
        return $this->save($request, $map_pool_item_id, $player_id);
    }

    // Update Map Pool
    public function update(Request $request, $map_pool_item_id, $player_id, $score_id)
    {
        return $this->save($request, $map_pool_item_id, $player_id, $score_id);
    }

    private function save(Request $request, $map_pool_item_id, $player_id, $score_id = null)
    {
        $poolItem = MapPoolItem::find($map_pool_item_id);
        $pool = MapPool::where('id', $poolItem->map_pool_id)->first();
        $player = Player::find($player_id);
        $score = Score::where('map_pool_item_id', $map_pool_item_id)
                        ->where('player_id', $player_id)
                        ->first();
        $chart = Chart::find($poolItem->chart_id);
        $song = Song::find($chart->song_id);

        $input = $request->all();

        $score = null;
        if($score_id) {
            $score = Score::find($score_id);
        }

        if(!$score) {
            $score = new Score();
        }

        $score->map_pool_item_id = $map_pool_item_id;
        $score->player_id = $player_id;
        $score->achievement_score = $input['achievement_score'];
        $score->dx_score = $input['dx_score'];

        if ($request->hasFile('photo_path')) {
            $file = $request->file('photo_path');
            if ($file->isValid()) {
                $filename = 'scores/' . $pool->id . '/' . $pool->name . '_' . $player->id . '_' . str_replace(' ', '_', $song->title) . '_' . $chart->difficulty . '.' . $file->getClientOriginalExtension();
                Storage::disk('uploads')->put($filename, file_get_contents($file));
            }

            $score->photo_path = $filename ?? '';
        }

        $score->save();

        return redirect()
                ->route('pool.edit', ['id' => $pool->id]);
    }
}
