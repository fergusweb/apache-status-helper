<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApacheStatusController;
use App\Livewire\StatusTableController;


Route::get('/', [ApacheStatusController::class, 'showStatus']);
Route::get('/live', [StatusTableController::class, 'render']);
