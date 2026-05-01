<?php

namespace App\Domains\Course\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Lesson;
use App\Domains\Course\Models\Section;
use Illuminate\Http\Request;

class InstructorLessonController extends Controller
{
    public function index($sectionId)
    {
        $section = Section::findOrFail($sectionId);

        if ($section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lessons = Lesson::where('section_id', $sectionId)
            ->orderBy('order')
            ->get();

        return response()->json([
            'data' => $lessons
        ]);
    }

    public function store(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);

        if ($section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,article,quiz',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson = Lesson::create([
            'section_id' => $sectionId,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'is_preview' => $validated['is_preview'] ?? false,
            'order' => Lesson::where('section_id', $sectionId)->count() + 1,
        ]);

        return response()->json([
            'message' => 'Lesson created',
            'data' => $lesson
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'nullable|in:video,article,quiz',
            'video_url' => 'nullable|url',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        $lesson->update($validated);

        return response()->json([
            'message' => 'Lesson updated',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        if ($lesson->section->course->instructor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lesson->delete();

        return response()->json([
            'message' => 'Lesson deleted'
        ]);
    }
}