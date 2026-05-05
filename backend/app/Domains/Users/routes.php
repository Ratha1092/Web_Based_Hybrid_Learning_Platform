<?php

use Illuminate\Support\Facades\Route;

// Placeholder for User routes - add controllers as needed
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    // Route::get('/{id}', [UserController::class, 'show']);
    // Route::put('/{id}', [UserController::class, 'update']);
});
