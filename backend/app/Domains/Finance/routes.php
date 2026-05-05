<?php

use Illuminate\Support\Facades\Route;

// Placeholder for Finance routes - add controllers as needed
Route::middleware(['auth:sanctum', 'is_instructor'])->prefix('finance')->group(function () {
    // Route::get('/wallet', [FinanceController::class, 'wallet']);
    // Route::get('/earnings', [FinanceController::class, 'earnings']);
});
