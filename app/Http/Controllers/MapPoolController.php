<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\MapPoolItem;
use App\Models\Song;
use App\Models\Chart;
use App\Models\Player;

class MapPoolController extends Controller
{
    // List Map Pool
    public function index()
    {
    	$data = [
    		'pools' => MapPool::all()
    	];
    	return view('pool.index', $data);
    }

    // Add Map Pool
    public function add()
    {
    	$map_pool = new MapPool();
        $map_pool->name = 'New Map Pool';
        $map_pool->save();
        $players = Player::where('is_eliminated', 0)->get();

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => array(),
            'players' => $players
    	];

        return redirect()->route('pool.edit', ['id' => $map_pool->id]);
    }

    // Store Map Pool
    public function store(Request $request)
    {
    	return view('pool.edit');
    }

    // Edit Map Pool
    public function edit($id)
    {
    	$map_pool = MapPool::find($id);
    	$map_pool_items = MapPoolItem::where('map_pool_id', $id)->get();
        $players = Player::where('is_eliminated', 0)->get();

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => $map_pool_items,
            'players' => $players
    	];

    	return view('pool.edit', $data);
    }

    // Update Map Pool
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $key = $input['key'];
        $value = $input['value'];
        $map_pool = MapPool::find($id);
        $map_pool->$key = $value;
        $map_pool->save();

    	return 1;
    }

    // Display Map Pool
    public function display($id)
    {
    	$map_pool = MapPool::find($id);
    	$map_pool_items = MapPoolItem::where('map_pool_id', $id)->orderBy('order')->get();

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => $map_pool_items,
    	];

    	return view('pool.view', $data);
    }

    // Show datatable
    public function getItems(Request $request)
    {
        return MapPoolItem::list($request);
    }

    // Store Map Pool Item
    public function storeItem(Request $request)
    {
        $input = $request->all();

        $count = MapPoolItem::where('map_pool_id', $input['mapPoolId'])->count();

        $item = new MapPoolItem();
        $item->map_pool_id = $input['mapPoolId'];
        $item->chart_id = $input['chartId'];
        $item->type = $input['type'];
        $item->is_banned = 0;
        $item->is_selected = 0;
        $chart = Chart::find($item->chart_id);
        $item->song_id = $chart->song_id;
        $item->order = $count + 1;

        $item->save();

        return 1;
    }

    // Select Map Pool Item
    public function selectItem(Request $request, $id, $select)
    {
        $item = MapPoolItem::find($id);
        $item->is_selected = $select;
        $item->save();

        return 1;
    }

    // Ban Map Pool Item
    public function banItem(Request $request, $id, $ban)
    {
        $item = MapPoolItem::find($id);
        $item->is_banned = $ban;
        $item->save();

        return 1;
    }

    // Remove Map Pool Item
    public function removeItem(Request $request, $id)
    {
        $item = MapPoolItem::find($id);
        $item->delete();

        return 1;
    }

    // Random Map Pool Item
    public function random(Request $request, $poolId)
    {
        $items = MapPoolItem::where('map_pool_id', $poolId)->inRandomOrder()->get();
        foreach ($items as $key => $item) {
            $item->order = $key + 1;
            $item->save();
        }        

        return 1;
    }

    // Show roulette detail
    public function roulette($itemId)
    {
        $item = MapPoolItem::find($itemId);
        $chart = Chart::find($item->chart_id);
        $song = Song::find($chart->song_id);
        $songs = Song::whereNotNull('sega_song_id')->inRandomOrder()->limit(20)->pluck('sega_song_id', 'id');
        $key_id = 0;

        foreach ($songs as $key => $value) {
            if(strlen($value) > 4) {
                $value = substr($value, 1, 5);
            }
            if($value == '000000' || $value == '000854' || $value == '001443' || $value == '001401') {
                unset($songs[$key]);
                continue;
            }

            $value = sprintf("%06d", $value);
            $songs[$key] = 'img/song_image/UI_Jacket_' . $value . '_s.png';
            if($key == $chart->song_id) {
                $key_id = $key;
            }
        }

        $data = [
            'songs' => $songs,
            'song' => $song,
            'key_id' => $key_id
        ];

        return view('pool.roulette', $data);
    }

    public function show($poolId, $showPlayer = null)
    {
        $pool = MapPool::find($poolId);
        $items = MapPoolItem::where('map_pool_id', $poolId)->orderBy('order')->get();

        $songs = Song::join('charts','charts.song_id','songs.id')
                        ->whereNotNull('sega_song_id')
                        ->groupBy('songs.id')
                        ->pluck('songs.id');
        $song_names = Song::pluck('title', 'id');
    
        return $this->drawMapPool($items, 1, 350, 'background.png', 'test-pool-image.png', $showPlayer);

        // $layout = 'background.png';
        // $layout = imagecreatefrompng($layout);
        // $layout= imagescale ( $layout, 1920 , 1080);
        // imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);
        
        // imagealphablending($layout, true);
        // imagesavealpha($layout, true);

        // $x = 100;
        // $song_data = [];
        // foreach ($items as $key => $item) {            
        //     $chart = Chart::find($item->chart_id);
        //     $song = Song::find($chart->song_id);
        //     $select = $item->is_selected ? 1 : 0;
        //     $lock = $item->is_banned ? 1 : 0;
        //     $this->addImage($layout, $song, $chart, $x, 350, $song->short_name ?? $song->title, $lock, $select, $item, $showPlayer);
        //     $song_data[] = $chart->song_id;
        //     $x+=350;
        // }

        // $file = 'test-pool-image.png';
        // imagepng($layout, 'test-pool-image.png');
        // return '<img src="/test-pool-image.png">';
        // //dd($song_data);
        // return 1;
    }

    public function drawMapPool($items, $scale, $y, $background, $file_name, $showPlayer)
    {
        $song_width = 355;
        $song_height = 500;

        $max_scale = 2;
        $min_scale = 0.1;

        $padding = 0;
        $min_padding = 300;
        $max_padding = 2000;

        while(true) {
            $padding = 1920 - ($song_width * $scale * count($items));
            if($padding > $max_padding) {
                $scale -= 0.005;
            }
            if($padding < $max_padding) {
                $scale += 0.005;
            }
            $check_padding = 1920 - ($song_width * $scale * count($items));
            if($scale >= $max_scale || $scale <= $min_scale || $check_padding < $min_padding || $check_padding > $max_padding) {
                break;
            }
        };

        $song_height *= $scale;
        $y =  (1080 - $song_height) / 2;

        $layout = $background;
        $layout = imagecreatefrompng($layout);
        $layout= imagescale ( $layout, 1920 , 1080);
        imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        $x = $padding / 2;
        $song_data = [];
        foreach ($items as $key => $item) {            
            $chart = Chart::find($item->chart_id);
            $song = Song::find($chart->song_id);
            $select = $item->is_selected ? 1 : 0;
            $lock = $item->is_banned ? 1 : 0;
            $song_image = $this->addImage($layout, $song, $chart, $x, $y, $song->short_name ?? $song->title, $lock, $select, $item, $showPlayer);
            imagefilter($song_image, IMG_FILTER_SMOOTH, 100);
            imagecopyresized($layout, $song_image, $x, $y, 0, 0, $song_width * $scale, 500 * $scale, $song_width, 500);
            $song_data[] = $chart->song_id;
            $x += $song_width * $scale;
        }

        $file = $file_name;
        imagepng($layout, $file_name);
        return '<img src="/' . $file_name . '">';
        //dd($song_data);
        return 1;
    }

    public function addImag_bk(&$layout, $song, $chart, $srcX, $srcY, $text, $lock, $select, $item, $showPlayer) {
        if(strlen($song->sega_song_id) > 4) {
            $song->sega_song_id = substr($song->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $song->sega_song_id);
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        try {
            if($select) {
                $selectBgImage = imagecreatefrompng('img/song_layout/select_background.png');
                imagecopyresized($layout, $selectBgImage, $srcX - 65, $srcY - 90, 0, 0, 450, 450, 896, 896);

                $selectRainbowImage = imagecreatefrompng('img/song_layout/select_rainbow.png');
                imagecopyresized($layout, $selectRainbowImage, $srcX + 10, $srcY - 65, 0, 0, 294, 96, 512, 128);

                $selectImage = imagecreatefrompng('img/song_layout/select.png');
                imagecopyresized($layout, $selectImage, $srcX + 50, $srcY - 65, 0, 0, 217, 80, 524, 192);
            }

            $baseImage = imagecreatefrompng('img/song_layout/frame_' . $chart->difficulty . '.png');
            $songImage = imagecreatefrompng($song_file);
            $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
            $levelBaseImage = imagecreatefrompng('img/song_layout/level_base.png');
            $levelImage = imagecreatefrompng('img/song_layout/level_' . $chart->level . '.png');
            $titleBaseImage = imagecreatefrompng('img/song_layout/title.png');

            if($lock) {                
                imagefilter($baseImage, IMG_FILTER_GRAYSCALE);
                imagefilter($songImage, IMG_FILTER_GRAYSCALE);
                imagefilter($typeImage, IMG_FILTER_GRAYSCALE);
                imagefilter($levelBaseImage, IMG_FILTER_GRAYSCALE);
                imagefilter($levelImage, IMG_FILTER_GRAYSCALE);
                imagefilter($titleBaseImage, IMG_FILTER_GRAYSCALE);
            } else {
                imagefilter($titleBaseImage, IMG_FILTER_BRIGHTNESS, -50);
            }

            imagecopy($layout, $baseImage, $srcX, $srcY, 0, 0, 316, 288);
            imagecopyresized($layout, $songImage, $srcX + 50, $srcY + 37, 0, 0, 217, 217, 200, 200);
            imagecopyresized($layout, $typeImage, $srcX, $srcY - 3, 0, 0, 180, 60, 120, 40);

            if($lock) {  
                $cross1Image = imagecreatefrompng('img/song_layout/cross_1.png');
                imagecopyresized($layout, $cross1Image, $srcX + 35, $srcY + 25, 0, 0, 250, 250, 164, 164);

                $cross2Image = imagecreatefrompng('img/song_layout/cross_2.png');
                imagecopyresized($layout, $cross2Image, $srcX + 35, $srcY + 25, 0, 0, 250, 250, 164, 164);

                $happyImage = imagecreatefrompng('img/song_layout/happy.png');
                imagecopyresized($layout, $happyImage, $srcX + 70, $srcY + 85, 0, 0, 175, 170, 70, 70);
            }

            if($select) {
                $select01Image = imagecreatefrompng('img/song_layout/select_01.png');
                imagecopyresized($layout, $select01Image, $srcX + 240, $srcY + 25, 0, 0, 84, 68, 84, 68);

                $select04Image = imagecreatefrompng('img/song_layout/select_04.png');
                imagecopyresized($layout, $select04Image, $srcX - 20, $srcY + 65, 0, 0, 112, 72, 112, 72);

                $select05Image = imagecreatefrompng('img/song_layout/select_05.png');
                imagecopyresized($layout, $select05Image, $srcX + 250, $srcY + 145, 0, 0, 64, 84, 64, 84);

                $select02Image = imagecreatefrompng('img/song_layout/select_02.png');
                imagecopyresized($layout, $select02Image, $srcX + 20, $srcY + 185, 0, 0, 64, 84, 64, 84);
            }

            imagecopyresized($layout, $levelBaseImage, $srcX + 180, $srcY + 220, 0, 0, 156, 92, 156, 92);
            imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);
            imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);

            if(!$showPlayer) {
                if($item->type != 'Random') {
                    $bubbleTextImage = imagecreatefrompng('img/song_layout/player_textbox.png');
                    imagecopyresized($layout, $bubbleTextImage, $srcX + 80, $srcY - 95, 0, 0, 156, 92, 156, 92);    
                }
                
                if($item->type == 'Player 1') {
                    $player1Image = imagecreatefrompng('img/song_layout/player_1.png');
                    imagecopyresized($layout, $player1Image, $srcX + 125, $srcY - 85, 0, 0, 72, 56, 36, 28);
                }
                if($item->type == 'Player 2') {
                    $player2Image = imagecreatefrompng('img/song_layout/player_2.png');
                    imagecopyresized($layout, $player2Image, $srcX + 125, $srcY - 85, 0, 0, 72, 56, 36, 28);
                }             
            }

            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            $font = 'font/nikumaru.otf';
            $size = 15;

            if(strlen($text) > 18) {
                $stringCut = substr($text, 0, 18);
                $endPoint = strrpos($stringCut, ' ');

                $text = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                $text .= '...';
            }

            $box = imagettfbbox($size, 0, $font, $text);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;

            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 350, $white, $black, $font, $text, 1);
        } catch (\Exception $e) {
            //dd($song_sega_id);
            throw $e;
            // $songs = Song::pluck('imageName', 'id');
            // $this->addImage($layout, $songs[rand(77, 1121)], $srcX, $srcY);
        }
    }

    public function addImage($layout, $song, $chart, $srcX, $srcY, $text, $lock, $select, $item, $showPlayer) {

        $layout = imagecreatefrompng('song-layout.png');
        $layout= imagescale ( $layout, 355 , 500);
        // imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);
        $srcY = 100;
        $srcX = 20;
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        if(strlen($song->sega_song_id) > 4) {
            $song->sega_song_id = substr($song->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $song->sega_song_id);
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        try {
            if($select) {
                // $selectBgImage = imagecreatefrompng('img/song_layout/select_background.png');
                // imagecopyresized($layout, $selectBgImage, $srcX - 65, $srcY - 90, 0, 0, 450, 450, 896, 896);

                $selectRainbowImage = imagecreatefrompng('img/song_layout/select_rainbow.png');
                imagecopyresized($layout, $selectRainbowImage, $srcX + 10, $srcY - 65, 0, 0, 294, 96, 512, 128);

                $selectImage = imagecreatefrompng('img/song_layout/select.png');
                imagecopyresized($layout, $selectImage, $srcX + 50, $srcY - 65, 0, 0, 217, 80, 524, 192);
            }

            $baseImage = imagecreatefrompng('img/song_layout/frame_' . $chart->difficulty . '.png');
            $songImage = imagecreatefrompng($song_file);
            $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
            $levelBaseImage = imagecreatefrompng('img/song_layout/level_base.png');
            $levelImage = imagecreatefrompng('img/song_layout/level_' . $chart->level . '.png');
            $titleBaseImage = imagecreatefrompng('img/song_layout/title.png');

            if($lock) {                
                imagefilter($baseImage, IMG_FILTER_GRAYSCALE);
                imagefilter($songImage, IMG_FILTER_GRAYSCALE);
                imagefilter($typeImage, IMG_FILTER_GRAYSCALE);
                imagefilter($levelBaseImage, IMG_FILTER_GRAYSCALE);
                imagefilter($levelImage, IMG_FILTER_GRAYSCALE);
                imagefilter($titleBaseImage, IMG_FILTER_GRAYSCALE);
            } else {
                // imagefilter($titleBaseImage, IMG_FILTER_BRIGHTNESS, -50);
            }

            imagecopy($layout, $baseImage, $srcX, $srcY, 0, 0, 316, 288);
            imagecopyresized($layout, $songImage, $srcX + 50, $srcY + 37, 0, 0, 217, 217, 200, 200);
            imagecopyresized($layout, $typeImage, $srcX, $srcY - 3, 0, 0, 180, 60, 120, 40);

            if($lock) {  
                $cross1Image = imagecreatefrompng('img/song_layout/cross_1.png');
                imagecopyresized($layout, $cross1Image, $srcX + 35, $srcY + 25, 0, 0, 250, 250, 164, 164);

                $cross2Image = imagecreatefrompng('img/song_layout/cross_2.png');
                imagecopyresized($layout, $cross2Image, $srcX + 35, $srcY + 25, 0, 0, 250, 250, 164, 164);

                $happyImage = imagecreatefrompng('img/song_layout/happy.png');
                imagecopyresized($layout, $happyImage, $srcX + 70, $srcY + 85, 0, 0, 175, 170, 70, 70);
            }

            if($select) {
                $select01Image = imagecreatefrompng('img/song_layout/select_01.png');
                imagecopyresized($layout, $select01Image, $srcX + 240, $srcY + 25, 0, 0, 84, 68, 84, 68);

                $select04Image = imagecreatefrompng('img/song_layout/select_04.png');
                imagecopyresized($layout, $select04Image, $srcX - 20, $srcY + 65, 0, 0, 112, 72, 112, 72);

                $select05Image = imagecreatefrompng('img/song_layout/select_05.png');
                imagecopyresized($layout, $select05Image, $srcX + 250, $srcY + 145, 0, 0, 64, 84, 64, 84);

                $select02Image = imagecreatefrompng('img/song_layout/select_02.png');
                imagecopyresized($layout, $select02Image, $srcX + 20, $srcY + 185, 0, 0, 64, 84, 64, 84);
            }

            imagecopyresized($layout, $levelBaseImage, $srcX + 180, $srcY + 220, 0, 0, 156, 92, 156, 92);
            imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);
            imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);

            if(!$showPlayer) {
                if($item->type != 'Random') {
                    $bubbleTextImage = imagecreatefrompng('img/song_layout/player_textbox.png');
                    imagecopyresized($layout, $bubbleTextImage, $srcX + 80, $srcY - 95, 0, 0, 156, 92, 156, 92);    
                }
                
                if($item->type == 'Player 1') {
                    $player1Image = imagecreatefrompng('img/song_layout/player_1.png');
                    imagecopyresized($layout, $player1Image, $srcX + 125, $srcY - 85, 0, 0, 72, 56, 36, 28);
                }
                if($item->type == 'Player 2') {
                    $player2Image = imagecreatefrompng('img/song_layout/player_2.png');
                    imagecopyresized($layout, $player2Image, $srcX + 125, $srcY - 85, 0, 0, 72, 56, 36, 28);
                }             
            }

            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            $font = 'font/nikumaru.otf';
            $size = 15;

            if(strlen($text) > 18) {
                $stringCut = substr($text, 0, 18);
                $endPoint = strrpos($stringCut, ' ');

                $text = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                $text .= '...';
            }

            $box = imagettfbbox($size, 0, $font, $text);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;

            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 350, $white, $black, $font, $text, 1);

            return $layout;
        } catch (\Exception $e) {
            //dd($song_sega_id);
            throw $e;
            // $songs = Song::pluck('imageName', 'id');
            // $this->addImage($layout, $songs[rand(77, 1121)], $srcX, $srcY);
        }

        // $file = 'test_song.png';
        // imagepng($layout, 'test_song.png');
        // return '<img src="/' . 'test_song.png' . '">';
    }

    public function makeTextBlock($text, $fontfile, $fontsize, $width)
    {   
        $words = explode(' ', $text);
        $lines = array($words[0]);
        $currentLine = 0;
        for($i = 1; $i < count($words); $i++)
        {
            $lineSize = imagettfbbox($fontsize, 0, $fontfile, $lines[$currentLine] . ' ' . $words[$i]);
            if($lineSize[2] - $lineSize[0] < $width)
            {
                $lines[$currentLine] .= ' ' . $words[$i];
            }
            else
            {
                $currentLine++;
                $lines[$currentLine] = $words[$i];
            }
        }
       
        return implode("\n", $lines);
    }

    public function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {

        for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
            for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

       return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }
}