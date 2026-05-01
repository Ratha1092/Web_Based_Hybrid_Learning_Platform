<?php

namespace App\Domains\Course\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;
use Illuminate\Http\Request;
use App\Domains\Course\Services\CourseService;

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

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Course created successfully',
            'data' => $course
        ], 201);
    }

    public function show($id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'level' => 'nullable|string',
            'language' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $updated = $this->courseService->update($course, $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Course updated successfully',
            'data' => $updated
        ]);
    }

    public function destroy($id)
    {
        $course = Course::where('id', $id)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        $this->courseService->delete($course);

        return response()->json([
            'status' => 'success',
            'message' => 'Course deleted successfully'
        ]);
    }
}