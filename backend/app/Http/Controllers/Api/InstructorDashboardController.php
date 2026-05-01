<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        return response()->json([
            'total_courses' => Course::where('instructor_id', $userId)->count(),
            'published_courses' => Course::where('instructor_id', $userId)
                ->where('is_published', true)->count(),
            'pending_courses' => Course::where('instructor_id', $userId)
                ->where('status', 'draft')->count(),
        ]);
    }
}