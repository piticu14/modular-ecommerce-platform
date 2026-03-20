<?php

use App\Http\Controllers\OrderController;

Route::prefix(config('api.version'))->group(function () {
    Route::middleware(['internal.signature', 'user.context'])->group(function () {

        Route::apiResource('orders', OrderController::class)
            ->only(['index', 'show', 'destroy']);

        Route::apiResource('orders', OrderController::class)
            ->only(['store'])
            ->middleware('idempotency');
    });

});
