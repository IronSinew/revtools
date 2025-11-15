<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MobController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\SearchSimpleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::prefix('/items')->name('item.')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/{item}', [ItemController::class, 'show'])->name('show');
});
Route::resource('quest', QuestController::class)->only(['index', 'show']);

Route::post('/search-simple', SearchSimpleController::class)->name('search.simple');

Route::prefix('/mobs')->name('mob.')->group(function () {
    Route::get('/', [MobController::class, 'index'])->name('index');
    Route::get('/{mob}', [MobController::class, 'show'])->name('show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {});
