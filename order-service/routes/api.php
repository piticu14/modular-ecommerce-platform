<?php

    use App\Http\Controllers\OrderController;

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order:uuid}', [OrderController::class, 'show']);
