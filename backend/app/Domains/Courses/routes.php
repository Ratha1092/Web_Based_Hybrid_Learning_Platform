<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Courses\Controllers\CourseController;
use App\Domains\Courses\Controllers\InstructorCourseController;
use App\Domains\Courses\Controllers\InstructorSectionController;
use App\Domains\Courses\Controllers\InstructorLessonController;

// Public course routes
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('/{slug}', [CourseController::class, 'show']);
});

// Instructor-only course management
Route::middleware('auth:sanctum')->prefix('instructor/courses')->group(function () {
    Route::post('/', [InstructorCourseController::class, 'store']);
    Route::put('/{id}', [InstructorCourseController::class, 'update']);
    Route::delete('/{id}', [InstructorCourseController::class, 'destroy']);
    Route::get('/{id}', [InstructorCourseController::class, 'show']);

    // Sections
    Route::prefix('{courseId}/sections')->group(function () {
        Route::post('/', [InstructorSectionController::class, 'store']);
        Route::put('{sectionId}', [InstructorSectionController::class, 'update']);
        Route::delete('{sectionId}', [InstructorSectionController::class, 'destroy']);
    });

    // Lessons
    Route::prefix('{courseId}/lessons')->group(function () {
        Route::post('/', [InstructorLessonController::class, 'store']);
        Route::put('{lessonId}', [InstructorLessonController::class, 'update']);
        Route::delete('{lessonId}', [InstructorLessonController::class, 'destroy']);
    });
});
