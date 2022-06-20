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
        # If you don't know the type of image you are using as your originals.
        // $image = imagecreatefromstring(file_get_contents('stream-layout.png'));
        // $frame = imagecreatefromstring(file_get_contents('https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/d3867397e9923cd2f91007cf9e34bded36b3ff2b61fc241df9377f525dc7e646.png'));

        # If you know your originals are of type PNG.

        $songs = Song::join('charts','charts.song_id','songs.id')
                        ->whereNotNull('sega_song_id')
                        ->groupBy('songs.id')
                        ->pluck('songs.id');
        $song_names = Song::pluck('title', 'id');
    
        $layout = 'stream-layout.png';
        $layout = imagecreatefrompng($layout);
        
        imagealphablending($layout, true);
        imagesavealpha($layout, true);

        // $song_1_file = 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $songs[89];
        // $song_1 = imagecreatefrompng($song_1_file);
        // imagecopy($layout, $song_1, 300, 800, 0, 0, 190, 190);
        $x = 100;
        $song_data = [];
        for ($i=0; $i < 5; $i++) { 
            while(true) {
                $chart = Chart::join('songs','charts.song_id','songs.id')
                            ->whereIn('difficulty', ['expert', 'master', 'remaster'])
                            ->whereNotNull('songs.sega_song_id')
                            ->where('level_value', '>=', '12')
                            ->inRandomOrder()->first();
                $song = Song::find($chart->song_id) ?? null;
                if(!$song) continue;
                $this->addImage($layout, $song, $chart, $x, 700, $song->title);
                break;
            }
            $song_data[] = $chart->song_id;
            $x+=350;
        }

        $file = 'test-image.png';
        imagepng($layout, 'test-image.png');
        return '<img src="test-image.png">';
        //dd($song_data);
        return 1;
    }

    public function addImage(&$layout, $song, $chart, $srcX, $srcY, $text) {
        if(strlen($chart->sega_song_id) > 4) {
            $chart->sega_song_id = substr($chart->sega_song_id, 1, 5);
        }
        $song_sega_id = sprintf("%06d", $chart->sega_song_id);
        //$song_file = 'https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $song->imageName;
        $song_file = 'img/song_image/UI_Jacket_' . $song_sega_id . '_s.png';
        try {
            $baseImage = imagecreatefrompng('img/song_layout/frame_' . $chart->difficulty . '.png');
            imagecopy($layout, $baseImage, $srcX, $srcY, 0, 0, 316, 288);

            $songImage = imagecreatefrompng($song_file);
            imagecopyresized($layout, $songImage, $srcX + 50, $srcY + 37, 0, 0, 217, 217, 200, 200);
            
            $typeImage = imagecreatefrompng('img/song_layout/type_' . $chart->type . '.png');
            imagecopyresized($layout, $typeImage, $srcX, $srcY - 3, 0, 0, 180, 60, 120, 40);

            $levelBaseImage = imagecreatefrompng('img/song_layout/level_base.png');
            imagecopyresized($layout, $levelBaseImage, $srcX + 180, $srcY + 220, 0, 0, 156, 92, 156, 92);

            $levelImage = imagecreatefrompng('img/song_layout/level_' . $chart->level . '.png');
            imagecopyresized($layout, $levelImage, $srcX + 180, $srcY + 220 - 5, 0, 0, 161, 80, 322, 160);

            $titleBaseImage = imagecreatefrompng('img/song_layout/title.png');
            imagecopyresized($layout, $titleBaseImage, $srcX, $srcY + 300, 0, 0, 316, 88, 300, 88);            


            $white = imagecolorallocate($layout, 255, 255, 255);
            $black = imagecolorallocate($layout, 0, 0, 0);
            $font = 'YasashisaGothicBold-V2.otf';
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
            // $this->addImage($layout, $songs[rand(77, 1121)], $srcX, $srcY);
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
