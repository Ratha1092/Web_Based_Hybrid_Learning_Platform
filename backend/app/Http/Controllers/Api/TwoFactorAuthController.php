<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorAuthService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Enable 2FA and generate first code
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($this->twoFactorService->isEnabled($user)) {
            return ApiResponse::error('2FA is already enabled', 400);
        }

        // Generate OTP
        $code = $this->twoFactorService->generateCode($user);

        // TODO: Send OTP via email
        // \Mail::send(new SendTwoFactorCodeMailable($user, $code));

        ActivityLogService::log('2fa_enable_requested', $user);

        return ApiResponse::success([
            'code' => $code, // Return for testing (remove in production)
            'message' => '6-digit code sent to your email',
        ], '2FA code sent');
    }

    /**
     * Verify 2FA code and enable 2FA
     */
    public function verifyAndEnable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();

        if (!$this->twoFactorService->verifyCode($user, $request->code)) {
            return ApiResponse::error('Invalid or expired code', 400);
        }

        $this->twoFactorService->enable($user);

        ActivityLogService::log('2fa_enabled', $user);

        return ApiResponse::success(null, '2FA enabled successfully');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);

        $user = $request->user();

        // Verify password
        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return ApiResponse::error('Invalid password', 401);
        }

        $this->twoFactorService->disable($user);

        ActivityLogService::log('2fa_disabled', $user);

        return ApiResponse::success(null, '2FA disabled successfully');
    }

    /**
     * Get 2FA status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        return ApiResponse::success([
            'two_factor_enabled' => $this->twoFactorService->isEnabled($user),
        ], '2FA status');
    }

    /**
     * Send 2FA code (during login if 2FA enabled)
     */
    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = \App\Domains\User\Models\User::where('email', strtolower($request->email))->first();

        if (!$user || !$this->twoFactorService->isEnabled($user)) {
            return ApiResponse::error('2FA not enabled for this account', 400);
        }

        $code = $this->twoFactorService->generateCode($user);

        // TODO: Send OTP via email
        // \Mail::send(new SendTwoFactorCodeMailable($user, $code));

        return ApiResponse::success([
            'code' => $code, // Return for testing (remove in production)
            'message' => '6-digit code sent to your email',
        ], '2FA code sent');
    }

    /**
     * Verify 2FA code (during login)
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = \App\Domains\User\Models\User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }

        if (!$this->twoFactorService->verifyCode($user, $request->code)) {
            return ApiResponse::error('Invalid or expired code', 400);
        }

        // Generate login token
        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLogService::log('login', $user);

        return ApiResponse::success([
            'token' => $token,
            'user' => $this->transformUser($user),
        ], 'Login successful');
    }

    private function transformUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'student',
        ];
    }
}
