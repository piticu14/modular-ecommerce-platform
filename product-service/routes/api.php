<?php

    use App\Http\Controllers\Product\ProductController;
    use App\Http\Controllers\Stock\StockReservationController;

    Route::apiResource('products', ProductController::class)
        ->only([
            'index',
            'show',
            'store',
            'destroy',
        ]);
    Route::get('/products/{product}/stock-reservations', [StockReservationController::class, 'index']);
