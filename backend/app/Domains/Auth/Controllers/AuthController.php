<?php

namespace App\Domains\Auth\Controllers;

use Illuminate\Routing\Controller;
use App\Domains\Auth\Services\AuthService;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Support\ApiResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return ApiResponse::success($data, 'User registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return ApiResponse::success($data, 'Login successful');
    }

    public function logout()
    {
        $this->authService->logout(request()->user());

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
