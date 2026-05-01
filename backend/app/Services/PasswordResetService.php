<?php

namespace App\Services;

use App\Domains\User\Models\User;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetService
{
    /**
     * Generate password reset token
     */
    public function generateToken(User $user): PasswordResetToken
    {
        // Delete old tokens
        $user->passwordResetTokens()->delete();

        $token = PasswordResetToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(1),
        ]);

        return $token;
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $reset = PasswordResetToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        // Mark as used
        $reset->update(['used' => true]);

        // Update user password
        $reset->user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Revoke all tokens for security
        $reset->user->tokens()->delete();

        return true;
    }

    /**
     * Get reset link
     */
    public function getResetLink(string $token): string
    {
        $frontendUrl = config('app.frontend_url');
        return "{$frontendUrl}/reset-password/{$token}";
    }

    /**
     * Verify token exists and is valid
     */
    public function isTokenValid(string $token): bool
    {
        return PasswordResetToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->exists();
    }
}
