<?php

    use App\Http\Controllers\Product\ProductController;
    use App\Http\Controllers\Stock\StockReservationController;

    Route::prefix('v1')->group(function () {
        Route::middleware(['internal.signature', 'user.context'])->group(function () {
            Route::get('/products/by-uuid', [ProductController::class, 'indexByUuid']);
            Route::apiResource('products', ProductController::class)
                ->only(['index', 'show', 'store', 'destroy']);
            Route::get('/products/{product}/stock-reservations', [StockReservationController::class, 'index']);

        });
    });
