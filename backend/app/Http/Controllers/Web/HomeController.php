<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;
use App\Domains\Course\Models\Category;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\User\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => User::where('role', 'student')->count(),
            'courses' => Course::where('is_published', true)->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'enrollments' => Enrollment::count(),
        ];

        $categories = Category::withCount('courses')
            ->orderByDesc('courses_count')
            ->take(8)
            ->get();

        $featuredCourses = Course::with(['category', 'instructor'])
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->where('is_published', true)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $testimonials = Review::with(['user', 'course'])
            ->whereNotNull('comment')
            ->latest()
            ->take(6)
            ->get();

        $topInstructors = User::where('role', 'instructor')
            ->withCount('courses')
            ->orderByDesc('courses_count')
            ->take(4)
            ->get();

        return view('home', compact(
            'stats',
            'categories',
            'featuredCourses',
            'testimonials',
            'topInstructors'
        ));
    }
}
