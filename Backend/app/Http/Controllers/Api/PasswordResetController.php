<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use App\Domains\User\Models\User;

class PasswordResetController extends Controller
{
    protected $resetService;

    public function __construct(PasswordResetService $resetService)
    {
        $this->resetService = $resetService;
    }

    /**
     * Request password reset (forgot password)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            // Don't reveal if email exists (security best practice)
            return ApiResponse::success(null, 'If email exists, reset link has been sent');
        }

        // Generate reset token
        $token = $this->resetService->generateToken($user);
        $resetLink = $this->resetService->getResetLink($token->token);

        // TODO: Send email with reset link
        // \Mail::send(new ResetPasswordMailable($user, $resetLink));

        ActivityLogService::log('password_reset_requested', $user);

        // For testing, return the link
        return ApiResponse::success([
            'reset_link' => $resetLink,
            'message' => 'Password reset link sent to email',
        ], 'Reset link sent');
    }

    /**
     * Verify reset token
     */
    public function verifyToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $isValid = $this->resetService->isTokenValid($request->token);

        if (!$isValid) {
            return ApiResponse::error('Invalid or expired reset token', 400);
        }

        return ApiResponse::success(null, 'Token is valid');
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/', // lowercase
                'regex:/[A-Z]/', // uppercase
                'regex:/[0-9]/', // numbers
                'regex:/[@$!%*?&]/', // symbols
            ],
        ]);

        try {
            $this->resetService->resetPassword($request->token, $request->password);

            ActivityLogService::log('password_changed');

            return ApiResponse::success(null, 'Password reset successfully. Please login with your new password.');
        } catch (\Exception $e) {
            return ApiResponse::error('Invalid or expired reset token', 400);
        }
    }
}
