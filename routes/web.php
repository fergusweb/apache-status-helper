<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApacheStatusController;

/*
Route::get(
    '/', function () {
        return view('welcome');
    }
);
*/

Route::get('/', [ApacheStatusController::class, 'showStatus']);
