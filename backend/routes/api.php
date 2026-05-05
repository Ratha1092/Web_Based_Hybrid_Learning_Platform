<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Auth\Resources\UserResource;
use App\Support\ApiResponse;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
| Domain-based routing system with versioning
*/

Route::prefix('v1')->group(function () {

    /**
     * Load all domain routes in a predictable order
     */
    $routes = glob(app_path('Domains/*/routes.php'));
    sort($routes);

    foreach ($routes as $route) {
        require $route;
    }

    /**
     * Global authenticated endpoints
     */
    Route::middleware('auth:sanctum')->group(function () {

        // Get current authenticated user
        Route::get('/me', function () {
            $user = auth()->user();

            if (!$user) {
                return ApiResponse::error('Unauthenticated', 401);
            }

            return ApiResponse::success(
                new UserResource($user),
                'User retrieved successfully'
            );
        });

    });
});