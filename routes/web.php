<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SongPickerController;
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

// SONG
Route::get('/songs', [SongController::class, 'index'])->name('song.index');
//Route::get('/song/{id}', [SongController::class, 'detail'])->name('song.detail');
Route::post('/songs/list', [SongController::class, 'list'])->name('song.list');
Route::post('/songs/search', [SongController::class, 'search'])->name('song.search');
Route::post('/songs/random', [SongController::class, 'random'])->name('song.random');

// MAP POOLS
Route::get('/pools', [SongPickerController::class, 'index'])->name('pool.index');
Route::get('/pools/add', [SongPickerController::class, 'add'])->name('pool.add');
Route::post('/pools/{id}/edit', [SongPickerController::class, 'edit'])->name('pool.edit');
Route::get('/pools/store', [SongPickerController::class, 'store'])->name('pool.store');
Route::post('/pools/{id}/update', [SongPickerController::class, 'update'])->name('pool.update');
//Route::get('/song/{id}', [SongController::class, 'detail'])->name('song.detail');