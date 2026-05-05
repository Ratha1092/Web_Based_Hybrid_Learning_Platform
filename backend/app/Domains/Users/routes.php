<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\ProfileController;

Route::middleware('auth:sanctum')->prefix('users')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);
    });