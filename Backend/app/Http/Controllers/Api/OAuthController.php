<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OAuthAccount;
use App\Domains\User\Models\User;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string', // Google ID token
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        return $this->handleOAuthLogin('google', $request->all());
    }

    /**
     * GitHub OAuth callback
     */
    public function handleGithubCallback(Request $request)
    {
        $request->validate([
            'code' => 'required|string', // GitHub authorization code
            'state' => 'required|string',
        ]);

        // TODO: Exchange code for access token
        // Then fetch user data from GitHub API

        return ApiResponse::error('GitHub OAuth not fully implemented', 501);
    }

    /**
     * Handle OAuth login/registration
     */
    private function handleOAuthLogin(string $provider, array $data)
    {
        // Check if OAuth account exists
        $oauthAccount = OAuthAccount::where('provider', $provider)
            ->where('provider_id', $data['id'] ?? $data['email'])
            ->first();

        if ($oauthAccount) {
            // Login existing user
            $user = $oauthAccount->user;
            $token = $user->createToken('api-token')->plainTextToken;

            ActivityLogService::log('login', $user);

            return ApiResponse::success([
                'token' => $token,
                'user' => $this->transformUser($user),
            ], 'Login successful');
        }

        // Check if email already exists
        $user = User::where('email', strtolower($data['email']))->first();

        if (!$user) {
            // Create new user
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'password' => Hash::make(Str::random(32)), // Random password
                'email_verified_at' => now(), // OAuth emails are already verified
                'role' => 'student',
            ]);
        }

        // Create OAuth account link
        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $data['id'] ?? $data['email'],
            'email' => $data['email'],
            'name' => $data['name'],
            'avatar' => $data['avatar'] ?? null,
            'data' => $data,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        ActivityLogService::log('login', $user);
        ActivityLogService::log('oauth_signup', $user, data: ['provider' => $provider]);

        return ApiResponse::success([
            'token' => $token,
            'user' => $this->transformUser($user),
            'is_new_user' => true,
        ], 'Login successful');
    }

    /**
     * Link OAuth account to existing user
     */
    public function linkOAuthAccount(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:google,github',
            'provider_id' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $user = $request->user();

        // Check if already linked
        if ($user->oauthAccounts()->where('provider', $request->provider)->exists()) {
            return ApiResponse::error('OAuth account already linked', 400);
        }

        OAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $request->provider,
            'provider_id' => $request->provider_id,
            'email' => $request->email,
            'name' => $request->name,
        ]);

        ActivityLogService::log('oauth_linked', $user, data: ['provider' => $request->provider]);

        return ApiResponse::success(null, 'OAuth account linked successfully');
    }

    /**
     * Unlink OAuth account
     */
    public function unlinkOAuthAccount(Request $request, string $provider)
    {
        $user = $request->user();

        // Must have email/password as backup
        if (!$user->password) {
            return ApiResponse::error('Cannot unlink - set a password first', 400);
        }

        $user->oauthAccounts()->where('provider', $provider)->delete();

        ActivityLogService::log('oauth_unlinked', $user, data: ['provider' => $provider]);

        return ApiResponse::success(null, 'OAuth account unlinked');
    }

    /**
     * Get linked OAuth accounts
     */
    public function linkedAccounts(Request $request)
    {
        $accounts = $request->user()
            ->oauthAccounts()
            ->select('id', 'provider', 'email', 'avatar', 'created_at')
            ->get();

        return ApiResponse::success($accounts, 'Linked OAuth accounts');
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
