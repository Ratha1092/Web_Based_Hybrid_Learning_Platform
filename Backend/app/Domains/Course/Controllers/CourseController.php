<?php

namespace App\Domains\Course\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)->latest()->get();

        return response()->json([
            'data' => $courses
        ]);
    }

    public function show($slug)
    {
        return Course::where('slug', $slug)
            ->with('sections.lessons')
            ->firstOrFail();
    }
}