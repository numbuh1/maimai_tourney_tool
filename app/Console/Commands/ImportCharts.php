<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;
use App\Models\Chart;
use File;

class ImportCharts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:charts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Songs & Charts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = database_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data-uni.json';

        $json = File::get($filename);
        $data = json_decode($json);

        $songs = $data->songs;

        $songs_created = 0;
        $songs_updated = 0;

        foreach ($songs as $key => $song) {
            $currSong = Song::where('songId', $song->songId)->first();
            if(!$currSong) {
                $currSong = new Song();
                $currSong->songId = $song->songId;
                $songs_created++;
            } else {
                $songs_updated++;
            }

            $currSong->songId       = $song->songId;
            $currSong->category     = $song->category;
            $currSong->title        = $song->title;
            $currSong->artist       = $song->artist;
            $currSong->imageName    = $song->imageName;
            $currSong->version      = $song->version;
            $currSong->bpm          = $song->bpm;
            $currSong->save();

            $charts = $song->sheets;
            foreach ($charts as $key => $chart) {
                if($chart->type != 'utage' && $chart->regions->intl) {
                    $currChart = Chart::where('song_id', $currSong->id)->where('type', $chart->type)->where('difficulty', $chart->difficulty)->first();
                    if(!$currChart) {
                        $currChart = new Chart();
                        $currChart->song_id = $currSong->id;
                        $currChart->type = $chart->type;
                        $currChart->difficulty = $chart->difficulty;
                    }
                    $currChart->level           = $chart->level;
                    $currChart->version         = $chart->version ?? '';
                    $currChart->noteDesigner    = $chart->noteDesigner;
                    $currChart->internalLevel   = $chart->internalLevel ?? str_replace('+', '.7', $chart->level);
                    $currChart->level_value     = str_replace('+', '.5', $chart->level);
                    $currChart->save();
                }
            }
        }

        $this->info("Songs created: " . $songs_created);
        $this->info("Songs updated: " . $songs_updated);

        return 0;
    }
}
