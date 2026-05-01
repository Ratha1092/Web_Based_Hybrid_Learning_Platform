<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to provider (Google/GitHub)
     */
    public function redirect($provider)
    {
        // Validate provider
        if (!in_array($provider, ['google', 'github'])) {
            return redirect()->route('login')->with('error', 'Invalid provider');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from provider
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if user already exists with this OAuth ID
            $user = User::where('oauth_provider', $provider)
                        ->where('oauth_id', $socialUser->getId())
                        ->first();

            if (!$user) {
                // Check if user exists by email (but only for non-admin users to prevent accidental linking)
                $user = User::where('email', $socialUser->getEmail())->first();

                if (!$user) {
                    // Create new user
                    $user = User::create([
                        'name' => $socialUser->getName(),
                        'email' => $socialUser->getEmail(),
                        'oauth_provider' => $provider,
                        'oauth_id' => $socialUser->getId(),
                        'oauth_avatar' => $socialUser->getAvatar(),
                        'password' => bcrypt(uniqid()), // Random password for OAuth users
                        'role' => 'student', // Default role for new OAuth users
                        'status' => 'active', // Explicit status
                    ]);

                    // Create initial student profile
                    $user->studentProfile()->create([
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                } else {
                    // Only link OAuth to existing user if they are student or instructor (not admin)
                    // This prevents accidentally linking OAuth to admin accounts
                    if (in_array($user->role, ['student', 'instructor'])) {
                        $user->update([
                            'oauth_provider' => $provider,
                            'oauth_id' => $socialUser->getId(),
                            'oauth_avatar' => $socialUser->getAvatar(),
                        ]);
                    } else {
                        // If trying to link to admin account, create a new student account instead
                        return redirect()->route('login')->with('error', 'An account with this email already exists. Please login with your password or use a different email.');
                    }
                }
            }

            // Update avatar if available
            if ($socialUser->getAvatar() && !$user->avatar) {
                $user->update(['oauth_avatar' => $socialUser->getAvatar()]);
            }

            // Login the user
            Auth::login($user, remember: true);

            // Redirect based on role
            return $this->redirectByRole($user);

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Unable to authenticate with ' . ucfirst($provider) . '. Please try again.');
        }
    }

    /**
     * Redirect user based on their role
     */
    private function redirectByRole($user)
    {
        return match ($user->role) {
            'instructor' => redirect()->route('instructor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }
}
