<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Orders\Controllers\OrderController;

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
});
