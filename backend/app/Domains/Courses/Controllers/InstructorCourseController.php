<?php

namespace App\Domains\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Courses\Models\Course;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use App\Domains\Courses\Services\CourseService;

class InstructorCourseController extends Controller
{
    public function __construct(
        protected CourseService $courseService
    ) {}

    public function index()
    {
        $courses = Course::where('instructor_id', auth()->id())
            ->latest()
            ->get();

        return ApiResponse::success($courses, 'Courses retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'nullable|string',
            'language' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $course = $this->courseService->create($validated, auth()->id());

        return ApiResponse::success($course, 'Course created successfully', 201);
    }

    public function show($id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        return ApiResponse::success($course, 'Course retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'level' => 'nullable|string',
            'language' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $updated = $this->courseService->update($course, $validated);

        return ApiResponse::success($updated, 'Course updated successfully');
    }

    public function destroy($id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->first();

        if (!$course) {
            return ApiResponse::error('Course not found', 404);
        }

        $this->courseService->delete($course);

        return ApiResponse::success(null, 'Course deleted successfully');
    }
}