<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\MapPoolItem;
use App\Models\Player;
use App\Models\Score;
use App\Models\Song;
use App\Models\Chart;
use App\Models\PlayersInMapPools;
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

    public function showScores($poolId)
    {
        $pool = MapPool::find($poolId);
        $items = MapPoolItem::where('map_pool_id', $poolId)->orderBy('order')->get();
        // $pool_players = PlayersInMapPools::where('map_pool_id', $poolId)->pluck('player_id');
        $players = Player::select('players.*')
                        ->join('player_in_map_pool', 'player_in_map_pool.player_id', 'players.id')
                        ->where('map_pool_id', $poolId)
                        ->get();
        $player_names = $players->pluck('name');

        $songs = Song::join('charts','charts.song_id','songs.id')
                        ->whereNotNull('sega_song_id')
                        ->groupBy('songs.id')
                        ->pluck('songs.id');
        $song_names = Song::pluck('title', 'id');
    
        return $this->drawScore($items, 1, 350, 'background.png', 'test-score-image.png', $players, $player_names);
    }

    public function drawScore($items, $scale, $y, $background, $file_name, $players, $player_names)
    {
        $layout = $background;
        $layout = imagecreatefrompng($layout);
        $layout= imagescale ( $layout, 1920 , 1080);
        imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        // Header
        $score_header = imagecreatefrompng('img/score_layout/score_header.png');
        imagecopy($layout, $score_header, 0, 0, 0, 0, 1920, 1080);

        $white = imagecolorallocate($layout, 255, 255, 255);
        $black = imagecolorallocate($layout, 0, 0, 0);
        $font = 'font/nikumaru.otf';
        $size = 45;

        $text = "Song";

        $this->makeTextbox($layout, 43, 0, 515, 375, $white, $white, $font, "Song", 0);
        $this->makeTextbox($layout, 43, 0, 905, 375, $white, $white, $font, $player_names[0], 0);
        $this->makeTextbox($layout, 43, 0, 1345, 375, $white, $white, $font, $player_names[1], 0);

        // $x = $padding / 2;
        // $song_data = [];
        // foreach ($items as $key => $item) {            
        //     $chart = Chart::find($item->chart_id);
        //     $song = Song::find($chart->song_id);
        //     $select = $item->is_selected ? 1 : 0;
        //     $lock = $item->is_banned ? 1 : 0;
        //     $song_image = $this->addImage($layout, $song, $chart, $x, $y, $song->short_name ?? $song->title, $lock, $select, $item);
        //     imagefilter($song_image, IMG_FILTER_SMOOTH, 100);
        //     imagecopyresized($layout, $song_image, $x, $y, 0, 0, $song_width * $scale, 500 * $scale, $song_width, 500);
        //     $song_data[] = $chart->song_id;
        //     $x += $song_width * $scale;
        // }

        $file = $file_name;
        imagepng($layout, $file_name);
        return '<img src="/' . $file_name . '">';
        //dd($song_data);
        return 1;
    }

    public function makeTextbox(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
        $box = imagettfbbox($size, 0, $fontfile, $text);
        $text_width = abs($box[2]) - abs($box[0]);
        $text_height = abs($box[5]) - abs($box[3]);

        $this->imagettfstroketext($image, $size, $angle, $x, $y, $textcolor, $strokecolor, $fontfile, $text, $px);
    }

    public function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {

        for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
            for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

       return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }
}
