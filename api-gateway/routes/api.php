<?php

    use App\Http\Controllers\HealthController;
    use App\Http\Controllers\ProxyController;


    /*
    |--------------------------------------------------------------------------
    | HEALTH
    |--------------------------------------------------------------------------
    */
    Route::get('/health', [HealthController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    Route::post('/auth/login', fn () =>
        app(ProxyController::class)->forward(request(), 'auth', 'login')
    );

    Route::post('/auth/register', fn () =>
     app(ProxyController::class)->forward(request(), 'auth', 'register')
    );


    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware(['jwt', 'throttle:api'])->group(function () {

        Route::get('/auth/me', fn () =>
        app(ProxyController::class)->forward(request(), 'auth', 'me')
        );

        Route::any('/orders/{path?}', fn ($path = '') =>
            app(ProxyController::class)->forward(request(), 'orders', $path)
        )->where('path', '.*');

        Route::any('/products/{path?}', fn ($path = '') =>
            app(ProxyController::class)->forward(request(), 'products', $path)
        )->where('path', '.*');

    });


