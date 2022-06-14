<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Song extends Model
{
    use HasFactory;

    public function list(Request $request)
    {
        $columns = array(
            0 => 'rec_sort',
            1 => 'cover',
            2 => 'song',
            3 => 'artist',
            4 => 'category',
            5 => 'version',
            6 => 'bpm',
        );

        //        $totalData = $totalFiltered = WorkPackage::count();

        // TO-DO: Link Site to Work Package
        $search = $request->input('search.value');
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $query = DB::table('songs')->select('*')
        			->whereNotNull('rec_sort');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('title', 'LIKE', "%{$search}%")
                    ->orWhere('songId', 'LIKE', "%{$search}%")
                    ->orWhere('artist', 'LIKE', "%{$search}%")
                    ->orWhere('version', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();
        $songs = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = array();
        if (!empty($songs)) {
            foreach ($songs as $song) {
				$nestedData['rec_sort']					= $song->rec_sort;
				$nestedData['cover']				= '<img src="https://dp4p6x0xfi5o9.cloudfront.net/maimai/img/cover/' . $song->imageName . '" height="100">';
				$nestedData['song']					= $song->title;
				$nestedData['artist']				= $song->artist;
				$nestedData['category']				= $song->category;
				$nestedData['version']				= $song->version;
				$nestedData['bpm']					= $song->bpm;

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
