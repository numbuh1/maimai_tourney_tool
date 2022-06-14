<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MapPool;

class SongPickerController extends Controller
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

    	$data = [
    		'pool' => $map_pool,
    		'pool_items' => array(),
    	];

    	return view('pool.edit', $data);
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
}
