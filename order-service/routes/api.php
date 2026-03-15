<?php

    use App\Http\Controllers\OrderController;

    Route::middleware(['internal.signature', 'user.context'])->group(function () {
        Route::apiResource('orders', OrderController::class)
            ->only([
                'index',
                'show',
                'store',
                'destroy',
            ]);

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{order:uuid}', [OrderController::class, 'show']);
    });
