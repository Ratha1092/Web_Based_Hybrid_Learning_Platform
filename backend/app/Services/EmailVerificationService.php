<?php

namespace App\Services;

use App\Domains\User\Models\User;
use App\Models\EmailVerificationToken;
use Illuminate\Support\Str;

class EmailVerificationService
{
    /**
     * Generate verification token
     */
    public function generateToken(User $user): EmailVerificationToken
    {
        // Delete old tokens
        $user->emailVerificationTokens()->delete();

        $token = EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        return $token;
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): bool
    {
        $verification = EmailVerificationToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        // Mark as used
        $verification->update(['used' => true]);

        // Update user
        $verification->user->update(['email_verified_at' => now()]);

        return true;
    }

    /**
     * Check if email is verified
     */
    public function isVerified(User $user): bool
    {
        return $user->email_verified_at !== null;
    }

    /**
     * Get verification link
     */
    public function getVerificationLink(string $token): string
    {
        $frontendUrl = config('app.frontend_url');
        return "{$frontendUrl}/verify-email/{$token}";
    }
}
