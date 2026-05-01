<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Support\ApiResponse;
use App\Services\ActivityLogService;

class AuthController extends Controller
{
    /**
     * Register a new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'email.unique' => 'This email is already registered',
            'password.confirmed' => 'Passwords do not match',
            'password.regex' => 'Password must contain uppercase, lowercase, numbers and symbols',
            'name.regex' => 'Name can only contain letters and spaces',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => strtolower($request->email),
                'password' => Hash::make($request->password),
                'role' => 'student', // Default role
            ]);

            // Log registration
            ActivityLogService::log('registration', $user, $request);

            // Generate API token
            $token = $user->createToken('api-token')->plainTextToken;

            return ApiResponse::success([
                'token' => $token,
                'user' => $this->transformUser($user)
            ], 'User registered successfully', 201);

        } catch (\Exception $e) {
            return ApiResponse::error('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login user with email and password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors(), 422);
        }

        // Case-insensitive email lookup
        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            if ($user) {
                ActivityLogService::log('failed_login', $user, $request);
            }
            // Don't reveal if email exists (security best practice)
            return ApiResponse::error('Invalid credentials', 401);
        }

        // Check if email is verified (optional - uncomment to enforce)
        // if (!$user->email_verified_at) {
        //     return ApiResponse::error('Please verify your email first', 403);
        // }

        // Revoke all existing tokens for this user (optional but recommended)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        // Log successful login
        ActivityLogService::log('login', $user, $request);

        return ApiResponse::success([
            'token' => $token,
            'user' => $this->transformUser($user)
        ], 'Login successful');
    }

    /**
     * Logout the current user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke the current token
            $request->user()->currentAccessToken()->delete();

            return ApiResponse::success(null, 'Logged out successfully');

        } catch (\Exception $e) {
            return ApiResponse::error('Logout failed', 500);
        }
    }

    /**
     * Transform user data - prevent leaking sensitive fields
     * @param User $user
     * @return array
     */
    private function transformUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role ?? 'student',
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}