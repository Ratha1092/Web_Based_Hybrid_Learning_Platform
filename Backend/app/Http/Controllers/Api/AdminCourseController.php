<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;

class AdminCourseController extends Controller
{
    public function pending()
    {
        $courses = Course::where('status', 'draft')->latest()->get();

        return response()->json([
            'data' => $courses
        ]);
    }

    public function approve($id)
    {
        $course = Course::findOrFail($id);

        $course->update([
            'status' => 'published',
            'is_published' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Course approved successfully',
            'data' => $course
        ]);
    }

    public function reject($id)
    {
        $course = Course::findOrFail($id);

        $course->update([
            'status' => 'rejected',
            'is_published' => false,
        ]);

        return response()->json([
            'message' => 'Course rejected',
            'data' => $course
        ]);
    }
}