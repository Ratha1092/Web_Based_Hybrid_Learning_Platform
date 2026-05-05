<?php

namespace App\Domain\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ApiResponse;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('profile');

        return ApiResponse::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'profile' => $user->profile
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bio' => 'nullable|string',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $profile = auth()->user()->profile;

        $profile->update($data);

        return ApiResponse::success($profile, 'Profile updated');
    }
}