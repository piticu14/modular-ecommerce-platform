<?php

use App\Http\Controllers\HealthController;

/*
|--------------------------------------------------------------------------
| HEALTH
|--------------------------------------------------------------------------
*/

Route::get('/health', [HealthController::class, 'health']);
Route::get('/health/ready', [HealthController::class, 'ready']);
