<?php

    use App\Http\Controllers\ProxyController;


        /*
        |--------------------------------------------------------------------------
        | AUTH (public)
        |--------------------------------------------------------------------------
        */

        Route::post('/auth/login', fn() => app(ProxyController::class)->forward(request(), 'auth'));

        Route::post('/auth/register', fn() => app(ProxyController::class)->forward(request(), 'auth'));


        /*
        |--------------------------------------------------------------------------
        | PROTECTED ROUTES
        |--------------------------------------------------------------------------
        */

        Route::middleware(['jwt', 'throttle:api'])->group(function () {

            Route::prefix('auth')->any('/{path?}', fn () =>
                app(ProxyController::class)->forward(request(), 'auth')
            )->where('path', '.*');

            Route::any('/orders/{path?}', fn() =>
                app(ProxyController::class)->forward(request(), 'orders')
            )->where('path', '.*');

            Route::any('/products/{path?}', fn() =>
                app(ProxyController::class)->forward(request(), 'products')
            )->where('path', '.*');

        });
