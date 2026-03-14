<?php

    use App\Http\Controllers\HealthController;

    /*
    |--------------------------------------------------------------------------
    | HEALTH
    |--------------------------------------------------------------------------
    */


    Route::get('/health', [HealthController::class, 'index']);
