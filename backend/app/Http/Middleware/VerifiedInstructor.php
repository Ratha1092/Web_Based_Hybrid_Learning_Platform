<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedInstructor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Check if user is instructor and is verified
        if ($user && $user->role === 'instructor') {
            if (!$user->isVerifiedInstructor()) {
                return redirect()->route('instructor.dashboard')
                    ->with('error', 'You need to be verified as an instructor to access this feature.');
            }
        }

        return $next($request);
    }
}
