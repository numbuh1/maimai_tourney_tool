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
        $this->correctDiff();

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
        $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'song_list_with_chart_constant' . DIRECTORY_SEPARATOR . '13(205 songs).json';

        $json = File::get($filename);
        $data = explode("\n", str_replace("\r", '', $json));
        foreach ($data as $key => $song) {
            $song_data = explode('|', $song);
            $song_name = $song_data[0];
            $diff_data = explode('-', $song_data[1]);
            $song_type = $diff_data[0];
            $song_diff = $diff_data[1];
            $song_constant_new = $song_data[2];

            $chart_item = Chart::select('*', 'songs.id as song_id', 'charts.id as id')
                    ->join('songs', 'charts.song_id', 'songs.id')
                    ->where('songs.title', htmlspecialchars_decode($song_name))
                    ->where('charts.type', $song_type)
                    ->where('charts.difficulty', $song_diff)
                    ->first();

            if($chart_item) {
                $song_constant_old = $chart_item->internalLevel;
                if($song_constant_new != $song_constant_old) {
                    $chart_item->internalLevel = $song_constant_new;
                    $constant_string = (string) $song_constant_old;
                    $constant_values = explode('.', $constant_string);
                    $constant_mark = $constant_values[1] ?? 0;
                    if($constant_mark >= 7) {
                        $chart_item->level = $constant_values[0];
                        $chart_item->level_value = $constant_values[0];
                    } else {
                        $chart_item->level = $constant_values[0] . '+';
                        $chart_item->level_value = $constant_values[0] . '.5';
                    }
                    Log::info(htmlspecialchars_decode($song_name) . ' changed : ' . $song_constant_old . ' => ' . $song_constant_new);
                    $chart_item->save();
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
}
