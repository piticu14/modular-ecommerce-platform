<?php

    use App\Http\Controllers\ProductController;
    use App\Http\Controllers\StockReservationController;

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/products/{product}/stock-reservations', [StockReservationController::class, 'index']);
