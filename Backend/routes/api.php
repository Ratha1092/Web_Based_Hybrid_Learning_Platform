<?php

use Illuminate\Support\Facades\Route;

// Core Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\TwoFactorAuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\OAuthController;

// Admin
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminController;

// Instructor
use App\Http\Controllers\Api\InstructorDashboardController;
use App\Http\Controllers\Api\InstructorController;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Password Reset (public)
        Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
        Route::post('/verify-reset-token', [PasswordResetController::class, 'verifyToken']);
        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

        // 2FA (public)
        Route::post('/2fa/send-code', [TwoFactorAuthController::class, 'sendCode']);
        Route::post('/2fa/verify-code', [TwoFactorAuthController::class, 'verifyCode']);

        // OAuth (public)
        Route::post('/oauth/google', [OAuthController::class, 'handleGoogleCallback']);
        Route::post('/oauth/github', [OAuthController::class, 'handleGithubCallback']);

        // Protected auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);

            // Email Verification
            Route::prefix('email')->group(function () {
                Route::post('/send-verification', [EmailVerificationController::class, 'send']);
                Route::get('/status', [EmailVerificationController::class, 'status']);
            });

            // 2FA Management
            Route::prefix('2fa')->group(function () {
                Route::post('/enable', [TwoFactorAuthController::class, 'enable']);
                Route::post('/verify-enable', [TwoFactorAuthController::class, 'verifyAndEnable']);
                Route::post('/disable', [TwoFactorAuthController::class, 'disable']);
                Route::get('/status', [TwoFactorAuthController::class, 'status']);
            });

            // OAuth Accounts
            Route::prefix('oauth')->group(function () {
                Route::get('/accounts', [OAuthController::class, 'linkedAccounts']);
                Route::post('/link', [OAuthController::class, 'linkOAuthAccount']);
                Route::delete('/{provider}', [OAuthController::class, 'unlinkOAuthAccount']);
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */
        Route::get('/me', fn() => auth()->user());

        /*
        |--------------------------------------------------------------------------
        | EMAIL VERIFICATION
        |--------------------------------------------------------------------------
        */
        Route::post('/verify-email/{token}', [EmailVerificationController::class, 'verify']);

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY & SECURITY
        |--------------------------------------------------------------------------
        */
        Route::prefix('activity')->group(function () {
            Route::get('/history', [ActivityController::class, 'history']);
            Route::get('/logins', [ActivityController::class, 'recentLogins']);
            Route::get('/all', [ActivityController::class, 'all']); // Admin only
        });

        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::get('/profile/{id}', [ProfileController::class, 'view']);
        Route::put('/profile', [ProfileController::class, 'update']);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        Route::middleware('is_admin')->prefix('admin')->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index']);
            Route::get('/users', [AdminController::class, 'users']);
            Route::get('/courses', [AdminController::class, 'courses']);
            Route::get('/enrollments', [AdminController::class, 'enrollments']);
            Route::get('/reviews', [AdminController::class, 'reviews']);
            Route::get('/orders', [AdminController::class, 'orders']);
            Route::get('/payments', [AdminController::class, 'payments']);
            Route::get('/payouts', [AdminController::class, 'payouts']);
            Route::get('/settings', [AdminController::class, 'settings']);
        });

        /*
        |--------------------------------------------------------------------------
        | INSTRUCTOR
        |--------------------------------------------------------------------------
        */
        Route::middleware('is_instructor')->prefix('instructor')->group(function () {

            Route::get('/dashboard', [InstructorDashboardController::class, 'index']);
            Route::get('/courses', [InstructorController::class, 'indexCourses']);
            Route::get('/students', [InstructorController::class, 'indexStudents']);
            Route::get('/reviews', [InstructorController::class, 'indexReviews']);
            Route::get('/earnings', [InstructorController::class, 'indexEarnings']);
            Route::get('/payouts', [InstructorController::class, 'indexPayouts']);
            Route::get('/wallet', [InstructorController::class, 'indexWallet']);
            Route::get('/profile', [InstructorController::class, 'indexProfile']);
            Route::get('/settings', [InstructorController::class, 'indexSettings']);
        });

    });
});