<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Chart extends Model
{
    use HasFactory;

    // Get charts by filter
    // Search songs
    public static function search(Request $request)
    {
    	$input = $request->all();

    	$category = $input['category'] ?? '';
    	$version = $input['version'] ?? '';
    	$levelMax = $input['levelMax'] ?? '';
    	$levelMin = $input['levelMin'] ?? '';
    	$difficulty = $input['difficulty'] ?? '';
    	$banned = $input['banned'] ?? [];

    	$query = Song::select(
                    'charts.internalLevel',
    				'songs.id',
    				'songs.imageName',
    				'charts.type',
    				'charts.difficulty',
    				'charts.level',
    				'charts.id as chart_id',
    			)
    			->leftJoin('charts', 'charts.song_id', 'songs.id')
    			->whereNotNull('charts.level');

    	if($category) {
    		$query->whereIn('category', $category);
    	}
    	if($version) {
    		$query->whereIn('charts.version', $version);
    	}
    	if($difficulty) {
    		$query->whereIn('charts.difficulty', $difficulty);
    	}
    	if($levelMin != $levelMax) {
    		if($levelMin) {
	    		$query->where('internalLevel', '>=', $levelMin - 0.001);
	    	}
	    	if($levelMax) {
	    		$query->where('internalLevel', '<=', $levelMax + 0.001);
	    	}	
    	} else {
    		if($levelMin && $levelMax) {
    			$query->whereRaw('abs(internalLevel - ' . $levelMin .') < 0.001');
    		}
    	}
    	if($banned) {
    		$query->whereNotIn('charts.id', $banned);
    	}

    	$charts = $query->orderBy('rec_sort', 'asc')->get();

    	return $charts;
    }
}
