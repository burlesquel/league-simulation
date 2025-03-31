<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TournamentController;

Route::get('/', function () {
    return view('main');
});


Route::get('/tournament/create', [TournamentController::class, 'create']);
Route::get('/tournament/{id}/simulate', [TournamentController::class, 'simulate']);
Route::get('/tournament/{id}/standings', [TournamentController::class, 'standings']);
Route::get('/tournaments', [TournamentController::class, 'tournaments']);
Route::get('/tournament/{id}', [TournamentController::class, 'tournament']);