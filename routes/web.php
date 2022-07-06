<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\MapPoolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// TEST
Route::get('/test', [DashboardController::class, 'test'])->name('test');

// SONG
Route::get('/songs', [SongController::class, 'index'])->name('song.index');
//Route::get('/song/{id}', [SongController::class, 'detail'])->name('song.detail');
Route::post('/songs/list', [SongController::class, 'list'])->name('song.list');
Route::post('/songs/search', [SongController::class, 'search'])->name('song.search');
Route::post('/songs/random', [SongController::class, 'random'])->name('song.random');

// CHART
Route::post('/chart/{id}', [ChartController::class, 'detail'])->name('chart.detail');

// MAP POOLS
Route::get('/pools', [MapPoolController::class, 'index'])->name('pool.index');
Route::get('/pools/add', [MapPoolController::class, 'add'])->name('pool.add');
Route::get('/pools/{id}/edit', [MapPoolController::class, 'edit'])->name('pool.edit');
Route::get('/pools/{id}/show', [MapPoolController::class, 'show'])->name('pool.show');
Route::get('/pools/{id}/showLayout/{showPlayer}', [MapPoolController::class, 'showLayout'])->name('pool.showLayout');

Route::get('/pools/store', [MapPoolController::class, 'store'])->name('pool.store');
Route::post('/pools/{id}/random', [MapPoolController::class, 'random'])->name('pool.random');
Route::post('/pools/{id}/lock', [MapPoolController::class, 'lock'])->name('pool.lock');
Route::post('/pools/{id}/update', [MapPoolController::class, 'update'])->name('pool.update');
Route::post('/pools/{id}/items', [MapPoolController::class, 'getItems'])->name('pool.items');
Route::post('/pools/{id}/item/add', [MapPoolController::class, 'storeItem'])->name('pool.storeItems');

// MAP POOL ITEMS
Route::post('/pool-item/{id}/ban/{ban}', [MapPoolController::class, 'banItem'])->name('pool.item.ban');
Route::post('/pool-item/{id}/select/{select}', [MapPoolController::class, 'selectItem'])->name('pool.item.select');
Route::post('/pool-item/{id}/remove', [MapPoolController::class, 'removeItem'])->name('pool.item.remove');
Route::get('/pools-item/{id}/roulette', [MapPoolController::class, 'roulette'])->name('pool.item.roulette');
//Route::get('/song/{id}', [SongController::class, 'detail'])->name('song.detail');

// MAP POOL SCORES
Route::post('/pools/{id}/scores', [ScoreController::class, 'getScores'])->name('pool.scores');
Route::get('/pools/{id}/showScores', [ScoreController::class, 'showScores'])->name('pool.showScores');
Route::get('/score/edit/{item_id}/{player_id}', [ScoreController::class, 'edit'])->name('score.edit');
Route::post('/score/store/{item_id}/{player_id}', [ScoreController::class, 'store'])->name('score.store');
Route::post('/score/update/{item_id}/{player_id}/{score_id}', [ScoreController::class, 'update'])->name('score.update');