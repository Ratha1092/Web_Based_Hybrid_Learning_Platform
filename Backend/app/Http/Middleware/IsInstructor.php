<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsInstructor
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Please login first');
        }

        if (auth()->user()->role !== 'instructor') {
            return redirect('/dashboard')->with('error', 'Unauthorized access. Only instructors can access this resource');
        }

        return $next($request);
    }
}