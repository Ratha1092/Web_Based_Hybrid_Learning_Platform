<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)
            ->latest()
            ->take(12)
            ->get();

        return view('courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->with(['category', 'instructor', 'reviews'])
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->firstOrFail();

        return view('courses.show', [
            'course' => $course,
        ]);
    }
}
