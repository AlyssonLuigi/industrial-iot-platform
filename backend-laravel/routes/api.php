<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MachineEventController;

Route::get('/machine-events', [MachineEventController::class, 'index']);
Route::get('/machine-events/latest', [MachineEventController::class, 'latest']);
Route::get('/machine-events/oee', [MachineEventController::class, 'oee']);
