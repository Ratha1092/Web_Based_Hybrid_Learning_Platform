<?php

namespace App\Services;

use App\Domains\User\Models\User;
use App\Models\TwoFactorCode;
use Illuminate\Support\Str;

class TwoFactorAuthService
{
    /**
     * Generate OTP code
     */
    public function generateCode(User $user): string
    {
        // Delete old codes
        $user->twoFactorCodes()->delete();

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        return $code;
    }

    /**
     * Verify code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $twoFactorCode = TwoFactorCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->first();

        if (!$twoFactorCode) {
            return false;
        }

        $twoFactorCode->update(['used' => true]);
        return true;
    }

    /**
     * Enable 2FA for user
     */
    public function enable(User $user): void
    {
        $user->update(['two_factor_enabled' => true]);
    }

    /**
     * Disable 2FA for user
     */
    public function disable(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);
        
        $user->twoFactorCodes()->delete();
    }

    /**
     * Is 2FA enabled
     */
    public function isEnabled(User $user): bool
    {
        return $user->two_factor_enabled;
    }
}
