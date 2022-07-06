<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Chart;
use File;

use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $this->updateConstant();

        return view('dashboard.index');
    }

    public function sortSongData()
    {        
        $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'MusicSort.txt';

        $data = File::get($filename);
        $songs = explode('||', $data);
        $sort = 1;
        foreach ($songs as $key => $song) {
            $song_data = explode('|', $song);
            $song_item = Song::where('title', htmlspecialchars_decode($song_data[1]))->where('category', '!=', '宴会場')->first();
            if($song_item) {
                $song_item->rec_sort = $sort;
                $song_item->sega_song_id = $song_data[0];
                $song_item->save();
                $sort++;
            } else {
                dd(htmlspecialchars_decode($song));
            }           
        }
    }

    public function rerateSongData()
    {
        $charts = Chart::whereNull('internalLevel')->get();
        foreach ($charts as $key => $chart) {
            $chart->internalLevel = $chart->level_value;
            $chart->save();
        }
    }

    public function updateConstant()
    {
        $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR  . 'song_list_with_chart_constant' . DIRECTORY_SEPARATOR . '14.4_14.9(66 songs).json';

        $json = File::get($filename);
        $data = explode("\n", str_replace("\r", '', $json));
        foreach ($data as $key => $song) {
            $song_data = explode('|', $song);
            $song_name = $song_data[0];
            $song_type = $song_data[1];
            $song_diff = $song_data[2];
            $song_constant_new = $song_data[3];

            $chart_item = Chart::select('*', 'songs.id as song_id', 'charts.id as id')
                    ->join('songs', 'charts.song_id', 'songs.id')
                    ->where('songs.title', htmlspecialchars_decode($song_name))
                    ->where('charts.type', $song_type)
                    ->where('charts.difficulty', $song_diff)
                    ->first();

            if($chart_item) {
                $song_constant_old = $chart_item->internalLevel;
                $chart_item->internalLevel = $song_constant_new;
                $constant_string = (string) $song_constant_old;
                $constant_values = explode('.', $constant_string);
                $constant_mark = $constant_values[1] ?? 0;
                if($constant_mark == '7' || $constant_mark == '8' || $constant_mark == '9') {
                    $chart_item->level = $constant_values[0] . '+';
                    $chart_item->level_value = $constant_values[0] . '.5';
                } else {
                    $chart_item->level = $constant_values[0];
                    $chart_item->level_value = $constant_values[0];
                }
                $chart_item->save();
                if($song_constant_new != $song_constant_old) {                    
                    Log::info(htmlspecialchars_decode($song_name) . ' changed : ' . $song_constant_old . ' => ' . $song_constant_new);
                } else {
                    Log::info(htmlspecialchars_decode($song_name) . ' no change.');
                }
            } else {
                Log::info(htmlspecialchars_decode($song_name) . ' not found.');
            }
        }
    }

    public function correctDiff()
    {
        $songs = Song::join('charts', 'charts.song_id', 'songs.id')
                    ->pluck('songs.id', 'songId')->toArray();

        $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data.json';

        $json = File::get($filename);
        $data = json_decode($json);

        $original_songs = $data->songs;
        foreach ($original_songs as $key => $original_song) {
            if(in_array($original_song->songId, array_keys($songs))) {
                $currSong = Song::where('id', $songs[$original_song->songId])->first();                
                $charts = $original_song->sheets;
                foreach ($charts as $key => $chart) {
                    if($chart->type != 'utage' && $chart->regions->intl) {
                        $currChart = Chart::where('song_id', $currSong->id)
                                    ->where('type', $chart->type)
                                    ->where('difficulty', $chart->difficulty)
                                    ->first();
                        if(!$currChart) {
                            $currChart = new Chart();
                            $currChart->song_id = $currSong->id;
                            $currChart->type = $chart->type;
                            $currChart->difficulty = $chart->difficulty;
                        }

                        Log::info($original_song->songId . ' : changed ' . $chart->difficulty . ' chart ( ' . $currChart->level . ' => ' . $chart->level . ' )');

                        $currChart->level           = $chart->level;
                        $currChart->level_value     = str_replace('+', '.5', $chart->level);
                        $currChart->version         = $chart->version ?? '';
                        $currChart->noteDesigner    = $chart->noteDesigner;
                        $currChart->internalLevel   = $chart->internalLevel ?? str_replace('+', '.7', $chart->level);

                        $currChart->save();
                    }
                }
            }
        }
    }

    public function correctAllDiff()
    {
        $charts = Chart::where('difficulty', 'expert')->get();
        $songs = Song::pluck('title', 'id');

        // $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data.json';

        // $json = File::get($filename);
        // $data = json_decode($json);

        // $original_songs = $data->songs;
        foreach ($charts as $key => $chart) {
            $mark = false;
            $constant = $chart->internalLevel;
            $constants = explode('.', $constant);

            if($chart->internalLevel == '13.7'){
                $mark = true;
                //dd($constant[1]);
            }

            if(isset($constants[1]) && ($constants[1] == '7' || $constants[1] == '8' || $constants[1] == '9')) {
                $chart_level = $constants[0] . '+';
                $chart_level_value = $constants[0] . '.5';
            } else {
                $chart_level = $constants[0];
                $chart_level_value = $constants[0];
            }

            if($chart->level != $chart_level || $chart->level_value != $chart_level_value) {

                Log::info($songs[$chart->song_id] . ' : changed ' . $chart->difficulty . ' chart ( ' . $chart->level . ' => ' . $chart_level . ' )');
                $chart->level = $chart_level;
                $chart->level_value = $chart_level_value;
                $chart->save();
            }
        }
    }

    // TEST
    public function test()
    {
        return $this->drawMapPool([3569, 3581, 3585, 3589], 1, 250, 'background.png', 'test-image.png', false);

        # If you don't know the type of image you are using as your originals.
        // $image = imagecreatefromstring(file_get_contents('stream-layout.png'));
        // $frame = imagecreatefromstring(file_get_contents('https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/d3867397e9923cd2f91007cf9e34bded36b3ff2b61fc241df9377f525dc7e646.png'));

        # If you know your originals are of type PNG.

        $songs = Song::join('charts','charts.song_id','songs.id')
                        ->whereNotNull('sega_song_id')
                        ->groupBy('songs.id')
                        ->pluck('songs.id');
        $song_names = Song::pluck('title', 'id');
    
        //$layout = 'stream-layout.png';
        $layout = 'test-background.png';
        $layout = imagecreatefrompng($layout);
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        // $song_1_file = 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $songs[89];
        // $song_1 = imagecreatefrompng($song_1_file);
        // imagecopy($layout, $song_1, 300, 800, 0, 0, 190, 190);
        $x = 100;
        $song_data = [];
        $select1 = 0;
        $ban1 = 1;
        $ban2 = 2;
        $select = 0;
        $lock = 0;
        // for ($i=0; $i < 5; $i++) { 
        //     while(true) {
        //         $select = 0;
        //         $lock = 0;
        //         $chart = Chart::join('songs','charts.song_id','songs.id')
        //                     ->whereIn('difficulty', ['expert', 'master'])
        //                     ->whereNotNull('songs.sega_song_id')
        //                     ->where('level_value', '>=', '12')
        //                     ->inRandomOrder()->first();
        //         $song = Song::find($chart->song_id) ?? null;
        //         if(!$song) continue;
        //         if($i == $select1) $select = 1;
        //         if($i == $ban1 || $i == $ban2) $lock = 1;
        //         $this->addBountySong($layout, $song, $chart, $x, 350, $song->short_name ?? $song->title, $song->artist, $lock, $select);
        //         break;
        //     }
        //     $song_data[] = $chart->song_id;
        //     $x+=350;
        // }

        $charts = Chart::join('songs','charts.song_id','songs.id')
                        ->whereIn('charts.id', [3569, 3581, 3585, 3589])->get();
        foreach ($charts as $key => $chart) {
            $song = Song::find($chart->song_id) ?? null;
            $this->addBountySong($layout, $song, $chart, $x, 250, $song->short_name ?? $song->title, $song->artist, $lock, $select);
            $x+=350;
        }

        $file = 'test-image.png';
        imagepng($layout, 'test-image.png');
        return '<img src="test-image.png">';
        //dd($song_data);
        return 1;
    }

    public function drawMapPool($items, $scale, $y, $background, $file_name, $showPlayer)
    {
        $song_width = 325;
        $song_height = 500;

        $max_scale = 2;
        $min_scale = 0.1;

        $padding = 0;
        $min_padding = 100;
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
        $y =  (1080 - $song_height) / 2.5;

        $layout = $background;
        $layout = imagecreatefrompng($layout);
        $layout= imagescale ( $layout, 1920 , 1080);
        imagefilter($layout, IMG_FILTER_BRIGHTNESS, -50);
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        $x = $padding / 2;
        $song_data = [];

        $charts = Chart::join('songs','charts.song_id','songs.id')
                        ->whereIn('charts.id', [3573,3577,3593,3597])->get();
        foreach ($charts as $key => $item) {
            $chart = $item;
            $song = Song::find($item->song_id) ?? null;
            $song_image = $this->addBountySong($layout, $song, $chart, $x, $y, $song->title_eng ?? $song->title, $song->artist_eng ?? $song->artist, 0, 0, $item, 0);
            // imagefilter($song_image, IMG_FILTER_SMOOTH, 1000);
            // imagefilter($song_image, IMG_FILTER_SMOOTH);
            imagecopyresized($layout, $song_image, $x, $y, 0, 0, $song_width * $scale, 550 * $scale, $song_width, 550);
            $x += $song_width * $scale;
        }

        $file = $file_name;
        imagepng($layout, $file_name);
        return '<img src="/' . $file_name . '">';
        //dd($song_data);
        return 1;
    }

    public function addTourneySong(&$layout, $song, $chart, $srcX, $srcY, $text, $lock, $select) {
        if(strlen($chart->sega_song_id) > 4) {
            $chart->sega_song_id = substr($chart->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $chart->sega_song_id);
        //$song_file = 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $song->imageName;
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
            // $textBgImage = imagecreatefrompng('img/song_layout/text_bg.png');
            // $textBgImage = imagecreatefrompng('img/song_layout/text_bg_2.png');

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

                // $lockImage = imagecreatefrompng('img/song_layout/lock.png');
                // imagecopyresized($layout, $lockImage, $srcX + 108, $srcY + 75, 0, 0, 101, 128, 52, 60);

                $happyImage = imagecreatefrompng('img/song_layout/happy.png');
                imagecopyresized($layout, $happyImage, $srcX + 70, $srcY + 85, 0, 0, 175, 170, 70, 70);

                // $happyMainImage = imagecreatefrompng('img/song_layout/happy_main.png');
                // imagecopyresized($layout, $happyMainImage, $srcX + 80, $srcY + 104, 0, 0, 163, 150, 652, 590);
                
                // $happyShadowImage = imagecreatefrompng('img/song_layout/happy_shadow.png');
                // imagecopyresized($layout, $happyShadowImage, $srcX + 80, $srcY + 104, 0, 0, 163, 150, 652, 590);

                // $happyLipImage = imagecreatefrompng('img/song_layout/happy_lip.png');
                // imagecopyresized($layout, $happyLipImage, $srcX + 147, $srcY + 144, 0, 0, 29, 17, 116, 68);

                // $happyMouthImage = imagecreatefrompng('img/song_layout/happy_mouth.png');
                // imagecopyresized($layout, $happyMouthImage, $srcX + 153, $srcY + 158, 0, 0, 17, 23, 68, 92);

                // $zannenImage = imagecreatefrompng('img/song_layout/zannen.png');
                // imagecopyresized($layout, $zannenImage, $srcX + 60, $srcY + 50, 0, 0, 192, 180, 512, 480);

                // $kumaDedImage = imagecreatefrompng('img/song_layout/kuma_ded.png');
                // imagecopyresized($layout, $kumaDedImage, $srcX + 58, $srcY + 95, 0, 0, 192, 127, 383, 253);
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

                // $selectImage = imagecreatefrompng('img/song_layout/select.png');
                // imagecopyresized($layout, $selectImage, $srcX + 50, $srcY - 65, 0, 0, 217, 80, 524, 192);
            }

            imagecopyresized($layout, $levelBaseImage, $srcX + 180, $srcY + 220, 0, 0, 156, 92, 156, 92);
            imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);
            imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);
            //imagecopyresized($layout, $textBgImage, $srcX, $srcY + 300, 0, 0, 316, 88, 168, 64);
            // imagecopyresized($layout, $textBgImage, $srcX, $srcY + 300, 0, 0, 316, 88, 540, 68);

            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            $font = 'font/nikumaru.otf';
            $size = 15;

            //$text = $this->makeTextBlock($text, $font, 20, 190);
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
            throw $e;
            // $songs = Song::pluck('imageName', 'id');
            // $this->addTourneySong($layout, $songs[rand(77, 1121)], $srcX, $srcY);
        }
    }

    public function addBountySong($layout, $song, $chart, $srcX, $srcY, $title, $artist, $lock, $select, $item, $showPlayer) {

        $layout = imagecreatefrompng('song-layout.png');
        $layout= imagescale ( $layout, 355 , 550);
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
            $baseImage = imagecreatefrompng('img/song_layout/base_' . $chart->difficulty . '.png');
            $songImage = imagecreatefrompng($song_file);
            $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
            $levelBaseImage = imagecreatefrompng('img/song_layout/base_' . $chart->difficulty . '_lower.png');
            // $levelImage = imagecreatefrompng('img/song_layout/level_' . $chart->level . '.png');
            // $titleBaseImage = imagecreatefrompng('img/song_layout/title.png');

            imagecopyresized($layout, $baseImage, $srcX, $srcY-47, 0, 0, 316, 516, 284, 464);
            imagecopyresized($layout, $songImage, $srcX + 47, $srcY + 37, 0, 0, 221, 220, 200, 200);
            imagecopyresized($layout, $typeImage, $srcX, $srcY - 3, 0, 0, 180, 60, 120, 40);

            imagecopyresized($layout, $levelBaseImage, $srcX + 171, $srcY + 220, 0, 0, 124, 76, 124, 76);
            // imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);
            // imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);

            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            
            $font_niku = 'font/nikumaru.otf';
            $font = 'font/YasashisaGothicBold-V2.otf';
            $size = 14;

            // if(strlen($text) > 18) {
            //     $stringCut = substr($text, 0, 18);
            //     $endPoint = strrpos($stringCut, ' ');

            //     $text = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            //     $text .= '...';
            // }

            $box = imagettfbbox(20, 0, $font, $title);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;

            $this->imagettfstroketext($layout, 20, 0, $srcX + 216, $srcY + 280, $white, $black, $font, $chart->level, 2);

            $box = imagettfbbox($size, 0, $font, $title);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;

            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 318, $white, $black, $font, $title, 1);

            $box = imagettfbbox($size, 0, $font, $artist);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;
            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 347, $white, $black, $font, $artist, 1);

            $bounties = $this->getBounty($chart->level);
            // dd($bounties);

            foreach ($bounties as $key => $bounty) {
                switch ($key) {
                    case 'grade':
                        $grade_y = 366;
                        foreach ($bounty as $bounty_key => $bounty_value) {                            
                            $gradeImage = imagecreatefrompng('img/song_layout/grade_' . $bounty_key . '.png');
                            imagecopyresized($layout, $gradeImage, $srcX + 47, $srcY + $grade_y, 0, 0, 33, 33, 52, 52);

                            $box = imagettfbbox(13, 0, $font, $bounty_value);
                            $this->imagettfstroketext($layout, 13, 0, $srcX + 90, $srcY + $grade_y + 20, $black, $white, $font, $bounty_value, 1);

                            $grade_y += 40;
                        }
                        break;
                    case 'rank':
                        $rank_y = 368;
                        foreach ($bounty as $bounty_key => $bounty_value) {                            
                            $rankImage = imagecreatefrompng('img/song_layout/grade_' . $bounty_key . '.png');
                            imagecopyresized($layout, $rankImage, $srcX + 147, $srcY + $rank_y, 0, 0, 60, 24, 60, 24);

                            $box = imagettfbbox(13, 0, $font, $bounty_value);
                            $this->imagettfstroketext($layout, 13, 0, $srcX + 220, $srcY + $rank_y + 17, $black, $white, $font, $bounty_value, 1);

                            $rank_y += 40;
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }

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

    public function getBounty($level) {
        $result = [
            'grade' => [],
            'rank' => [],
        ];
        switch ($level) {
            case '12':
                $result['rank']['sss+'] = '5k';
                $result['grade']['ap'] = '20k';
                $result['grade']['fc+'] = '10k';
                break;
            case '12+':
                $result['rank']['sss+'] = '10k';
                $result['grade']['ap'] = '25k';
                $result['grade']['fc+'] = '15k';
                break;
            case '13':
                $result['rank']['sss+'] = '15k';
                $result['rank']['sss'] = '5k';
                $result['grade']['ap'] = '35k';
                $result['grade']['fc+'] = '20k';
                break;
            case '13+':
                $result['rank']['sss+'] = '25k';
                $result['rank']['sss'] = '15k';
                $result['grade']['ap'] = '50k';
                $result['grade']['fc'] = '20k';
                break;
            case '14+':
                $result['rank']['sss'] = '100k';
                $result['rank']['ss'] = '25k';
                $result['grade']['fc+'] = '150k';
                $result['grade']['fc'] = '75k';
                break;
            default:
                # code...
                break;
        }
        return $result;
    }

    public function addBountySong_bk(&$layout, $song, $chart, $srcX, $srcY, $title, $artist, $lock, $select) {
        if(strlen($chart->sega_song_id) > 4) {
            $chart->sega_song_id = substr($chart->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $chart->sega_song_id);
        //$song_file = 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $song->imageName;
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        try {

            $baseImage = imagecreatefrompng('img/song_layout/base_' . $chart->difficulty . '.png');
            $songImage = imagecreatefrompng($song_file);
            $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
            // $levelBaseImage = imagecreatefrompng('img/song_layout/level_base.png');
            // $levelImage = imagecreatefrompng('img/song_layout/level_' . $chart->level . '.png');
            // $titleBaseImage = imagecreatefrompng('img/song_layout/title.png');
            
            imagecopyresized($layout, $baseImage, $srcX, $srcY-47, 0, 0, 316, 516, 284, 464);
            imagecopyresized($layout, $songImage, $srcX + 47, $srcY + 37, 0, 0, 221, 220, 200, 200);
            imagecopyresized($layout, $typeImage, $srcX, $srcY - 3, 0, 0, 180, 60, 120, 40);

            // imagecopyresized($layout, $levelBaseImage, $srcX + 180, $srcY + 220, 0, 0, 156, 92, 156, 92);
            // imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);
            // imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);
            //imagecopyresized($layout, $textBgImage, $srcX, $srcY + 300, 0, 0, 316, 88, 168, 64);
            // imagecopyresized($layout, $textBgImage, $srcX, $srcY + 300, 0, 0, 316, 88, 540, 68);

            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            //$font = 'font/nikumaru.otf';
            $font = 'font/YasashisaGothicBold-V2.otf';
            $size = 12;

            //$text = $this->makeTextBlock($text, $font, 20, 190);
            // if(strlen($text) > 18) {
            //     $stringCut = substr($text, 0, 18);
            //     $endPoint = strrpos($stringCut, ' ');

            //     $text = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            //     $text .= '...';
            // }

            $box = imagettfbbox($size, 0, $font, $title);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;

            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 318, $white, $black, $font, $title, 1);

            $box = imagettfbbox($size, 0, $font, $artist);
            $text_width = abs($box[2]) - abs($box[0]);
            $text_height = abs($box[5]) - abs($box[3]);
            $x = (316 - $text_width) / 2;
            $this->imagettfstroketext($layout, $size, 0, $srcX + $x, $srcY + 347, $white, $black, $font, $artist, 1);
            // imagettftext($layout, $size, 0, $srcX + $x, $srcY + 300, $black, $font, $text);
            return $layout;
        } catch (\Exception $e) {
            throw $e;
            // $songs = Song::pluck('imageName', 'id');
            // $this->addTourneySong($layout, $songs[rand(77, 1121)], $srcX, $srcY);
        }
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

    function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {

        for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
            for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
                $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

       return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
    }
}
