<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;


    Route::prefix('auth')->controller(AuthController::class)->group(function () {

        Route::post('/register', 'register');
        Route::post('/login', 'login');

        Route::middleware('auth:api')->group(function () {

            Route::post('/logout', 'logout');
            Route::post('/refresh', 'refresh');
            Route::get('/me',  'me');

        });

    });
