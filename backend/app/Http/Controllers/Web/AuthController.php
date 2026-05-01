<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use App\Domains\User\Models\User;
use App\Domains\User\Models\InstructorVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Support\ApiResponse;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:student,instructor',
            'bio' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|max:2000',
            'qualification_type' => 'nullable|in:degree,certification,professional_experience',
            'institution' => 'nullable|string|max:255',
            'completion_year' => 'nullable|integer|min:1990|max:' . date('Y'),
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'portfolio_url' => 'nullable|url|max:255',
        ]);

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'instructor_status' => $data['role'] === 'instructor' ? 'pending' : 'not_instructor',
        ]);

        // Handle instructor verification
        if ($data['role'] === 'instructor') {
            $verificationData = [
                'user_id' => $user->id,
                'bio' => $data['bio'] ?? null,
                'experience' => $data['experience'] ?? null,
                'qualification_type' => $data['qualification_type'] ?? null,
                'institution' => $data['institution'] ?? null,
                'completion_year' => $data['completion_year'] ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? null,
            ];

            // Handle certificate file upload
            if ($request->hasFile('certificate_file')) {
                $certPath = $request->file('certificate_file')->store('instructor-verification/certificates', 'public');
                $verificationData['certificate_file'] = $certPath;
            }

            // Handle identity file upload
            if ($request->hasFile('identity_file')) {
                $idPath = $request->file('identity_file')->store('instructor-verification/identity', 'public');
                $verificationData['identity_file'] = $idPath;
            }

            InstructorVerification::create($verificationData);
        }

        Auth::login($user);

        return redirect($this->redirectByRole($user));
    }

    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        return redirect($this->redirectByRole($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ✅ CENTRALIZED ROLE REDIRECT
    private function redirectByRole($user)
    {
        return match ($user->role) {
            'admin' => '/admin/dashboard',
            'instructor' => '/instructor/dashboard',
            default => '/', // student
        };
    }
}