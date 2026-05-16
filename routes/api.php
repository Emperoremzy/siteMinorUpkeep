<?php

use App\Http\Controllers\Api\MonitorController;
use Illuminate\Support\Facades\Route;

Route::post('monitors', [MonitorController::class, 'store']);
Route::get('monitors', [MonitorController::class, 'index']);
Route::get('monitors/{monitor}/history', [MonitorController::class, 'history'])
    ->whereNumber('monitor');
