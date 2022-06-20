<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;
use App\Models\MapPoolItem;
use App\Models\Song;
use App\Models\Chart;

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

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => array(),
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

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => $map_pool_items,
    	];

    	return view('pool.edit', $data);
    }

    // Update Map Pool
    public function update(Request $request, $id)
    {
    	return view('pool.edit');
    }

    // Display Map Pool
    public function display($id)
    {
    	$map_pool = MapPool::find($id);
    	$map_pool_items = MapPoolItem::where('map_pool_id', $id)->get();

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
}
