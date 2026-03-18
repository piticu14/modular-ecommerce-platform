<?php

use App\Http\Controllers\OrderController;

Route::prefix('v1')->group(function () {
    Route::middleware(['internal.signature', 'user.context'])->group(function () {
        Route::apiResource('orders', OrderController::class)
            ->only(['index', 'show', 'store', 'destroy']);

    });
});
