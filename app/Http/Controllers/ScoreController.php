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
        $items = MapPoolItem::where('map_pool_id', $poolId)
                    ->where('is_banned', 0)
                    ->orderBy('order')->get();
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
    
        // return $this->drawScore($items, 1, 350, 'background.png', 'test-score-image.png', $players, $player_names, $pool);


        $layout = imagecreatefrompng('background.png');
        $layout= imagescale ( $layout, 1920 , 1080);
        imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);

        $song_count = count($items);
        $currentY = 300;

        if($song_count == 4) {
            $currentY = 190;
        } else if($song_count == 3) {
            $currentY = 290;
        }
        $originalY = $currentY;

        // $currentY = 300;
        $scale = 1;
        $player_scores = [];
        $player_ids = [];

        foreach ($items as $key => $item) {
            $chart = Chart::find($item->chart_id);
            $song = Song::find($chart->song_id);
            $currentX = 300;
            $index = 1;
            foreach ($players as $player) {
                $player_ids[$index] = $player->id;
                $index++;
                if(!isset($player_scores[$player->id])) {
                    $player_scores[$player->id] = [];
                    $player_scores[$player->id]['achievement_score'] = 0;
                    $player_scores[$player->id]['dx_score'] = 0;
                }
                $score = Score::where('map_pool_item_id', $item->id)
                                ->where('player_id', $player->id)
                                ->first();
                $win = Score::where('map_pool_item_id', $item->id)
                                ->orderBy('achievement_score', 'desc')
                                ->orderBy('dx_score', 'desc')
                                ->first();
                $firstRank = true;
                if(isset($score->achievement_score)) {                    
                    if(isset($win->achievement_score)) {
                        $firstRank = $score->achievement_score >= $win->achievement_score;
                    }
                } else {
                    $firstRank = !isset($win->achievement_score);
                }

                $score_image = $this->drawScoreItem($song, $chart, 0, $currentY, $score, $firstRank);
                imagecopyresized($layout, $score_image, $currentX, $currentY, 0, 0, 640 * $scale, 140 * $scale, 640, 140);

                $currentX += 700;
                $player_scores[$player->id]['achievement_score'] += $score->achievement_score ?? 0;
                $player_scores[$player->id]['dx_score'] += $score->dx_score ?? 0;
            }
            $song_data[] = $chart->song_id;
            $currentY += 145;
        }

        $achievement_score_1 = $player_scores[$player_ids[1]]['achievement_score'];
        $achievement_score_2 = $player_scores[$player_ids[2]]['achievement_score'];
        $dx_score_1 = $player_scores[$player_ids[1]]['dx_score'];
        $dx_score_2 = $player_scores[$player_ids[2]]['dx_score'];

        $total_first = $player_ids[2];
        if($achievement_score_1 == $achievement_score_2) {
            if($dx_score_1 > $dx_score_2) {
                $total_first = $player_ids[1];
            }
        } else if($achievement_score_1 > $achievement_score_2) {
            $total_first = $player_ids[1];
        } else {
            $total_first = $player_ids[2];
        }

        $currentX = 370;
        foreach ($players as $player) {

            $player_image = imagecreatefrompng('img/score_layout/name_base.png');
            imagecopyresized($layout, $player_image, $currentX + 25, $originalY - 110, 0, 0, 308 * $scale * 1.5, 60 * $scale * 1.5, 308, 60);

            // Name
            $blue = imagecolorallocate($layout, 16, 57, 123);
            $font = 'font/YasashisaGothicBold-V2.otf';
            $size = 30;

            $box = imagettfbbox($size, 0, $font, $player->name);
            $text_width = abs($box[2]) - abs($box[0]);
            $x = $currentX + 10 + (496 * $scale - $text_width) / 2;
            imagettftext($layout, $size, 0, $x, $originalY - 50, $blue, $font, $player->name);

            $total_image = $this->drawScoreTotal($pool, $player, $player_scores, $player->id == $total_first);
            imagecopyresized($layout, $total_image, $currentX, $currentY, 0, 0, 496 * $scale, 324 * $scale, 496, 324);
            $currentX += 700;
        }

        $file_name = 'test-score-image.png';
        $file = $file_name;
        imagepng($layout, $file_name);
        return '<img src="/' . $file_name . '">';
        //dd($song_data);
        return 1;
    }

    public function drawScoreItem($song, $chart, $baseX, $baseY, $score, $firstRank)
    {
        $layout = imagecreatefrompng('img/score_layout/scorebase_' . $chart->difficulty . '.png');
        
        // Song Image
        if(strlen($song->sega_song_id) > 4) {
            $song->sega_song_id = substr($song->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $song->sega_song_id);
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        $songImage = imagecreatefrompng($song_file);
        imagecopyresized($layout, $songImage, 12, 12, 0, 0, 117, 117, 200, 200);

        // Song title
        $white = imagecolorallocate($layout, 255, 255, 255);
        $black = imagecolorallocate($layout, 0, 0, 0);
        $font = 'font/YasashisaGothicBold-V2.otf';
        $size = 10;
        imagettftext($layout, $size, 0, 330, 27, $white, $font, $song->title);

        // Chart type
        $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
        imagecopyresized($layout, $typeImage, 245, 12, 0, 0, 120 * 0.6, 40 * 0.6, 120, 40);

        // Level (60x48)
        $lvScale = 0.7;
        $lvImage = imagecreatefrompng('img/song_layout/lv_' . $chart->difficulty . '.png');
        $coorLv = [
            'x' => 2,
            'y' => 3
        ];
        imagecopyresized($layout, $lvImage, 543, 35, 48 * $coorLv['x'], 60 * $coorLv['y'], 48 * $lvScale, 60 * $lvScale, 48, 60);
        $lvChar = str_split($chart->level, 1);
        $currLvX = 40 * $lvScale * 0.8;
        foreach ($lvChar as $char) {
            $coorLv = $this->getLvCoor($char);
            imagecopyresized($layout, $lvImage, 543 + $currLvX, 35, 48 * $coorLv['x'], 60 * $coorLv['y'], 48 * $lvScale, 60 * $lvScale, 48, 60);
            $currLvX += 48 * $lvScale * 0.6;
        }

        if($score) {
            // Score (achievement)  (74x98)
            if($score->achievement_score >= 97) {
                $score_color = 'gold';
            } else if($score->achievement_score > 80) {
                $score_color = 'red';
            } else {
                $score_color = 'blue';
            }            
            $scoreImage = imagecreatefrompng('img/score_layout/score_' . $score_color . '.png');
            
            $scoreParts = explode('.', $score->achievement_score);
            
            // $startX = 110;
            // $startY = -25;
            $startX = 0;
            $startY = 0;

            $scoreScale = 0.7;
            $scoreChar = str_split($scoreParts[0], 1);
            $currScoreX = 40 * $scoreScale * 0.8;
            $currScoreX += 98 * $scoreScale * 0.6 * (3 - count($scoreChar));
            foreach ($scoreChar as $char) {
                $coorLv = $this->getLvCoor($char);
                imagecopyresized($layout, $scoreImage, 123 + $currScoreX + $startX, 65 + $startY, 74 * $coorLv['x'], 98 * $coorLv['y'], 74 * $scoreScale, 98 * $scoreScale, 74, 98);
                $currScoreX += 98 * $scoreScale * 0.6;
            }

            $scoreScale = 0.5;
            if(!isset($scoreParts[1])) {
                $scoreParts[1] = 0;
            }
            $scoreParts[1] = str_pad($scoreParts[1], 4, "0");
            $scoreChar = str_split('.' . $scoreParts[1], 1);
            $currScoreX -= 25;
            foreach ($scoreChar as $char) {
                if($char == '.') {
                    $currScoreY = 87;
                } else {
                    $currScoreY = 83;
                }
                $coorLv = $this->getLvCoor($char);
                imagecopyresized($layout, $scoreImage, 143 + $currScoreX + $startX, $currScoreY + $startY, 74 * $coorLv['x'], 98 * $coorLv['y'], 74 * $scoreScale, 98 * $scoreScale, 74, 98);

                if($char == '.') {                    
                    $currScoreX += 90 * $scoreScale * 0.6;
                } else {                    
                    $currScoreX += 102 * $scoreScale * 0.6;
                }
            }

            // Score (deluxe)
            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            $font = 'font/YasashisaGothicBold-V2.otf';
            $size = 10;
            imagettftext($layout, $size, 0, 590, 126, $black, $font, $score->dx_score ?? '');

            // Score rank
            if($firstRank) {
                $resultImage = @imagecreatefrompng('img/score_layout/vs_win.png');
            } else {
                $resultImage = @imagecreatefrompng('img/score_layout/vs_lose.png');
            }
            $resultScale = 0.5;

            imagecopyresized($layout, $resultImage, 410, 35, 0, 0, 254 * $resultScale, 123 * $resultScale, 254, 123);
        }

        return $layout;

        $file = $file_name;
        imagepng($layout, $file_name);
        return '<img src="/' . $file_name . '">';
        //dd($song_data);
        return 1;
    }

    public function drawScoreTotal($pool, $player, $player_scores, $firstRank)
    {
        $layout = imagecreatefrompng('img/score_layout/scorebase_total.png');

        // Score (achievement)  (74x98)
        if($player_scores[$player->id]['achievement_score'] >= 97) {
            $score_color = 'gold';
        } else if($player_scores[$player->id]['achievement_score'] > 80) {
            $score_color = 'red';
        } else {
            $score_color = 'blue';
        }            
        $scoreImage = imagecreatefrompng('img/score_layout/score_' . $score_color . '.png');
        
        $scoreParts = explode('.', $player_scores[$player->id]['achievement_score']);
        
        // $startX = 110;
        // $startY = -25;
        $startX = -30;
        $startY = 40;

        $scoreScale = 0.7;
        $scoreChar = str_split($scoreParts[0], 1);
        $currScoreX = 40 * $scoreScale * 0.8;
        $currScoreX += 98 * $scoreScale * 0.6 * (3 - count($scoreChar));
        foreach ($scoreChar as $char) {
            $coorLv = $this->getLvCoor($char);
            imagecopyresized($layout, $scoreImage, 123 + $currScoreX + $startX, 65 + $startY, 74 * $coorLv['x'], 98 * $coorLv['y'], 74 * $scoreScale, 98 * $scoreScale, 74, 98);
            $currScoreX += 98 * $scoreScale * 0.6;
        }

        $scoreScale = 0.5;
        if(!isset($scoreParts[1])) {
            $scoreParts[1] = 0;
        }
        $scoreParts[1] = str_pad($scoreParts[1], 4, "0");
        $scoreChar = str_split('.' . $scoreParts[1], 1);
        $currScoreX -= 25;
        foreach ($scoreChar as $char) {
            if($char == '.') {
                $currScoreY = 87;
            } else {
                $currScoreY = 83;
            }
            $coorLv = $this->getLvCoor($char);
            imagecopyresized($layout, $scoreImage, 143 + $currScoreX + $startX, $currScoreY + $startY, 74 * $coorLv['x'], 98 * $coorLv['y'], 74 * $scoreScale, 98 * $scoreScale, 74, 98);

            if($char == '.') {                    
                $currScoreX += 90 * $scoreScale * 0.6;
            } else {                    
                $currScoreX += 102 * $scoreScale * 0.6;
            }
        }

        // Score (deluxe)
        $white = imagecolorallocate($layout, 255, 255, 255);
        $black = imagecolorallocate($layout, 0, 0, 0);
        $font = 'font/YasashisaGothicBold-V2.otf';
        $size = 10;
        imagettftext($layout, $size, 0, 250, 200, $black, $font, 'でらっくスコア: ' . $player_scores[$player->id]['dx_score']);

        $font = 'font/nikumaru.otf';
        $size = 20;
        imagettftext($layout, $size, 0, 210, 70, $white, $font, 'TOTAL');

        // Score rank        
        $resultScale = 1.5;
        $rankX = 90;
        $rankY = 150;
        // $rankBaseImage = imagecreatefrompng('img/score_layout/score_rankbase.png');
        // imagecopyresized($layout, $rankBaseImage, $rankX, $rankY, 0, 0, 128 * $resultScale, 108 * $resultScale, 128, 108);

        if($firstRank) {
            $resultImage = imagecreatefrompng('img/score_layout/score_1st.png');
        } else {
            $resultImage = imagecreatefrompng('img/score_layout/score_2nd.png');
        }

        imagecopyresized($layout, $resultImage, $rankX + 30, $rankY + 30, 0, 0, 66 * $resultScale, 46 * $resultScale, 66, 46);

        return $layout;
    }

    public function getLvCoor($char)
    {
        switch ($char) {
            case '0':
                $x = 0;
                $y = 0;
                break;
            case '1':
                $x = 1;
                $y = 0;
                break;
            case '2':
                $x = 2;
                $y = 0;
                break;
            case '3':
                $x = 3;
                $y = 0;
                break;
            case '4':
                $x = 0;
                $y = 1;
                break;
            case '5':
                $x = 1;
                $y = 1;
                break;
            case '6':
                $x = 2;
                $y = 1;
                break;
            case '7':
                $x = 3;
                $y = 1;
                break;
            case '8':
                $x = 0;
                $y = 2;
                break;
            case '9':
                $x = 1;
                $y = 2;
                break;
            case '+':
                $x = 2;
                $y = 2;
                break;
            case '-':
                $x = 3;
                $y = 2;
                break;            
            case ',':
                $x = 0;
                $y = 3;
                break;
            case '.':
                $x = 1;
                $y = 3;
                break;
            case 'lv':
                $x = 2;
                $y = 3;
                break;
            default:
                $x = -1;
                $y = -1;
                break;
        }
        return [
            'x' => $x,
            'y' => $y
        ];
    }

    public function drawScore($items, $scale, $y, $background, $file_name, $players, $player_names, $pool)
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
        $size = 43;

        imagettftext($layout, $size, 0, 515, 375, $white, $font, "Song");

        $box = imagettfbbox($size, 0, $font, $player_names[0]);
        $text_width = abs($box[2]) - abs($box[0]);
        $x = (350 - $text_width) / 2;
        imagettftext($layout, $size, 0, 840 + $x, 375, $white, $font, $player_names[0]);

        $box = imagettfbbox($size, 0, $font, $player_names[1]);
        $text_width = abs($box[2]) - abs($box[0]);
        $x = (350 - $text_width) / 2;
        imagettftext($layout, $size, 0, 1240 + $x, 375, $white, $font, $player_names[1]);


        $score_item = imagecreatefrompng('img/score_layout/score_item.png');

        $currentY = 0;
        foreach ($items as $key => $item) {
            imagecopy($layout, $score_item, 0, $currentY, 0, 0, 1920, 1080);
            $chart = Chart::find($item->chart_id);
            $song = Song::find($chart->song_id);
            $this->addSongScoreImage($layout, $song, $chart, 0, $currentY, $song->short_name ?? $song->title);
            //imagecopyresized($layout, $song_image, $x, $y, 0, 0, $song_width * $scale, 500 * $scale, $song_width, 500);
            $song_data[] = $chart->song_id;
            $currentY += 145;
        }

        // $this->makeTextbox($layout, 43, 0, 515, 375, $white, $white, $font, "Song", 0);
        // $this->makeTextbox($layout, 43, 0, 905, 375, $white, $white, $font, $player_names[0], 0);
        // $this->makeTextbox($layout, 43, 0, 1345, 375, $white, $white, $font, $player_names[1], 0);

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

    public function addSongScoreImage($layout, $song, $chart, $currentX, $currentY, $song_title) {
        if(strlen($song->sega_song_id) > 4) {
            $song->sega_song_id = substr($song->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $song->sega_song_id);
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        $songImage = imagecreatefrompng($song_file);
        imagecopyresized($layout, $songImage, $currentX + 352, $currentY + 395, 0, 0, 135, 135, 200, 200);
        $white = imagecolorallocate($layout, 255, 255, 255);
        $font = 'font/nikumaru.otf';
        imagettftext($layout, 15, 0, 525, $currentY + 455, $white, $font, $song_title);
    }

    public function makeTextbox(&$image, $size, $angle, $x1, $y1, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
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
