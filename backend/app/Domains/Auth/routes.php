<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Auth\Controllers\AuthController;
use App\Domains\Auth\Controllers\EmailVerificationController;
use App\Domains\Auth\Controllers\PasswordResetController;
use App\Domains\Auth\Controllers\TwoFactorAuthController;
use App\Domains\Auth\Controllers\OAuthController;

Route::prefix('auth')->group(function () {

    // Public
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Password Reset
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('throttle:5,1');
    Route::post('/reset-password/verify', [PasswordResetController::class, 'verifyToken']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // Email Verification (public)
    Route::post('/email/verify', [EmailVerificationController::class, 'verify']);

    // 2FA (public)
    Route::post('/2fa/code', [TwoFactorAuthController::class, 'sendCode'])->middleware('throttle:5,1');
    Route::post('/2fa/verify', [TwoFactorAuthController::class, 'verifyCode']);

    // OAuth
    Route::post('/oauth/google', [OAuthController::class, 'handleGoogleCallback']);
    Route::post('/oauth/github', [OAuthController::class, 'handleGithubCallback']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::prefix('email')->group(function () {
            Route::post('/send', [EmailVerificationController::class, 'send']);
            Route::get('/status', [EmailVerificationController::class, 'status']);
        });

        Route::prefix('2fa')->group(function () {
            Route::post('/enable', [TwoFactorAuthController::class, 'enable']);
            Route::post('/verify-enable', [TwoFactorAuthController::class, 'verifyAndEnable']);
            Route::post('/disable', [TwoFactorAuthController::class, 'disable']);
            Route::get('/status', [TwoFactorAuthController::class, 'status']);
        });

        Route::prefix('oauth')->group(function () {
            Route::get('/accounts', [OAuthController::class, 'linkedAccounts']);
            Route::post('/link', [OAuthController::class, 'linkOAuthAccount']);
            Route::delete('/{provider}', [OAuthController::class, 'unlinkOAuthAccount']);
        });
    });
});
