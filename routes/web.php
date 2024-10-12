<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApacheStatusController;
use App\Livewire\StatusTable;


Route::get('/', [ApacheStatusController::class, 'showStatus']);
// Route::get('/live', [StatusTable::class, 'render']);
