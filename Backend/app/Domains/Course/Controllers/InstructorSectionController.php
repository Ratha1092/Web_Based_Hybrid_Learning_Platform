<?php

namespace App\Domains\Course\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Section;
use App\Domains\Course\Models\Course;
use Illuminate\Http\Request;

class InstructorSectionController extends Controller
{
    public function index($courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        $sections = Section::where('course_id', $courseId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $sections
        ]);
    }

    public function store(Request $request, $courseId)
    {
        $course = Course::where('id', $courseId)
            ->where('instructor_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section = Section::create([
            'course_id' => $courseId,
            'title' => $validated['title'],
            'order' => Section::where('course_id', $courseId)->count() + 1,
        ]);

        return response()->json([
            'message' => 'Section created',
            'data' => $section
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        if ($section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update($validated);

        return response()->json([
            'message' => 'Section updated',
            'data' => $section
        ]);
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);

        if ($section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $section->delete();

        return response()->json([
            'message' => 'Section deleted'
        ]);
    }
}