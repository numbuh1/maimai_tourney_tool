<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Player;

class MapPoolItem extends Model
{
    use HasFactory;

    public static function list(Request $request)
    {
        $columns = array(
            0 => 'order',
            1 => 'song',
            2 => 'songAction',
            3 => 'slotAction',
        );

        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('map_pool_items')
        			->select(
        				'*',
        				'map_pool_items.type as item_type',
        				'map_pool_items.id as id'
        			)
        			->join('songs', 'songs.id', 'map_pool_items.song_id')
        			->join('charts', 'charts.id', 'map_pool_items.chart_id');

        $totalFiltered = $query->count();
        $items = $query->orderBy($order, $dir)->get();

        $players = Player::pluck('name', 'id');
        $pool_id = null;

        $data = array();
        if (!empty($items)) {
            foreach ($items as $item) {

                if(!$pool_id) {
                    $pool_id = $item->map_pool_id;
                    $pool = MapPool::find($pool_id);
                    $player_1 = $players[$pool->player_1] ?? 'Player 1';
                    $player_2 = $players[$pool->player_2] ?? 'Player 2';
                }

            	if($item->is_banned) {
            		$banButton = '<a href="#" class="btn m-1 btn-danger btn-ban-song" data-id="' . $item->id . '" data-action="' . route('pool.item.ban', ['id' => $item->id, 'ban' => 0]) . '">Unban Song</a>';
            	} else {
            		$banButton = '<a href="#" class="btn m-1 btn-danger btn-ban-song" data-id="' . $item->id . '" data-action="' . route('pool.item.ban', ['id' => $item->id, 'ban' => 1]) . '">Ban Song</a>';
            	}

            	if($item->is_selected) {
            		$selectButton = '<a href="#" class="btn m-1 btn-info btn-select-song" data-id="' . $item->id . '" data-action="' . route('pool.item.select', ['id' => $item->id, 'select' => 0]) . '">De-select Song</a>';
            	} else {
            		$selectButton = '<a href="#" class="btn m-1 btn-info btn-select-song" data-id="' . $item->id . '" data-action="' . route('pool.item.select', ['id' => $item->id, 'select' => 1]) . '">Select Song</a>';
            	}

            	$removeButton = '<a href="#" class="btn m-1 btn-secondary btn-remove-song" data-id="' . $item->id . '" data-action="' . route('pool.item.remove', ['id' => $item->id]) . '">Remove Song</a>';

                switch ($item->item_type) {
                    case 'Player 1':
                        $item->item_type = $player_1;
                        break;
                    case 'Player 2':
                        $item->item_type = $player_2;
                        break;
                    default:
                        break;
                }


				$nestedData['order'] = $item->order;
				$nestedData['song'] = '<img width="100" class="chart-thumbnail chart-' . $item->difficulty . '" src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $item->imageName . '" alt="Card image cap">';
				$nestedData['songAction'] = $item->item_type . ' picks';
				$nestedData['slotAction'] = $selectButton . $banButton . $removeButton;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalFiltered),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return response()->json($json_data);
    }
}
