<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    protected $emailService;

    public function __construct(EmailVerificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send verification email
     */
    public function send(Request $request)
    {
        $user = $request->user();

        // Check if already verified
        if ($this->emailService->isVerified($user)) {
            return ApiResponse::error('Email already verified', 400);
        }

        // Generate token
        $token = $this->emailService->generateToken($user);
        $verificationLink = $this->emailService->getVerificationLink($token->token);

        // TODO: Send email with verification link
        // \Mail::send(new VerifyEmailMailable($user, $verificationLink));

        // For now, return the link for testing
        return ApiResponse::success([
            'message' => 'Verification email sent',
            'verification_link' => $verificationLink, // Remove in production
        ], 'Verification email sent');
    }

    /**
     * Verify email with token
     */
    public function verify(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        try {
            $this->emailService->verifyEmail($request->token);

            ActivityLogService::log('email_verified', $request->user());

            return ApiResponse::success(null, 'Email verified successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Invalid or expired verification token', 400);
        }
    }

    /**
     * Check verification status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return ApiResponse::success([
            'is_verified' => $this->emailService->isVerified($user),
            'verified_at' => $user->email_verified_at,
        ], 'Verification status');
    }
}
