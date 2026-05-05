<?php

namespace App\Domains\Auth\Services;

use App\Domains\Users\Models\User;
use App\Domains\Auth\Models\EmailVerificationToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $plainToken = Str::random(64);

        return EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Hash::make($plainToken), // 🔐 hashed
            'expires_at' => now()->addHours(24),
        ]);
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): bool
    {
        $verification = EmailVerificationToken::where('expires_at', '>', now())->get()
            ->first(function ($record) use ($token) {
                return Hash::check($token, $record->token);
            });

        if (!$verification) {
            throw new \RuntimeException('Invalid or expired verification token');
        }

        return DB::transaction(function () use ($verification) {
            // Mark as used if column exists
            if (isset($verification->used)) {
                $verification->update(['used' => true]);
            }

            $verification->user->update([
                'email_verified_at' => now()
            ]);

            return true;
        });
    }

    public function isVerified(User $user): bool
    {
        return !is_null($user->email_verified_at);
    }

    public function getVerificationLink(string $token): string
    {
        $frontendUrl = config('app.frontend_url');

        return "{$frontendUrl}/verify-email/{$token}";
    }
}