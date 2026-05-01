<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domains\User\Models\User;
use Illuminate\Http\Request;
use App\Domains\Learning\Models\LessonProgress;

class ProfileController extends Controller
{
    /**
     * Show user's own profile
     */
    public function show()
    {
        $user = auth()->user();
        return $this->displayProfile($user);
    }

    /**
     * Display a specific user's profile (by ID)
     */
    public function view($id)
    {
        $user = User::findOrFail($id);
        return $this->displayProfile($user, isOwnProfile: auth()->id() === $user->id);
    }

    /**
     * Display profile based on role
     */
    private function displayProfile(User $user, bool $isOwnProfile = true)
    {
        // Load profile data based on role
        if ($user->role === 'student') {
            // Create profile if it doesn't exist
            $profile = $user->studentProfile ?? $user->studentProfile()->create();
            $stats = $this->getStudentStats($user);
        } elseif ($user->role === 'instructor') {
            // Create profile if it doesn't exist
            $profile = $user->instructorProfile ?? $user->instructorProfile()->create();
            $stats = $this->getInstructorStats($user);
        } else {
            // Admin profile
            $profile = null;
            $stats = null;
        }

        return view('profile.show', [
            'user' => $user,
            'profile' => $profile,
            'stats' => $stats,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }

    /**
     * Get student profile stats
     */
    private function getStudentStats(User $user): array
    {
        $enrollments = $user->enrollments()->count();
        $completedCourses = \App\Domains\Learning\Models\LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
            ->join('sections', 'lessons.section_id', '=', 'sections.id')
            ->distinct('sections.course_id')
            ->count('sections.course_id');

        // Calculate average progress
        $totalProgress = $user->lessonProgress()->sum('progress_percentage');
        $totalLessons = $user->lessonProgress()->count();
        $averageProgress = $totalLessons > 0 ? round($totalProgress / $totalLessons) : 0;

        return [
            'enrolled_courses' => $enrollments,
            'completed_courses' => $completedCourses,
            'average_progress' => $averageProgress,
            'total_reviews' => $user->reviews()->count(),
        ];
    }

    /**
     * Get instructor profile stats
     */
    private function getInstructorStats(User $user): array
    {
        $courses = $user->courses()->count();
        $totalStudents = $user->courses()
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');

        $averageRating = $user->courses()
            ->where('published', true)
            ->avg('rating') ?? 0;

        // Calculate total revenue (you can adjust based on your payment structure)
        $totalRevenue = $user->revenueShares()->sum('amount') ?? 0;

        return [
            'total_courses' => $courses,
            'total_students' => $totalStudents,
            'average_rating' => round($averageRating, 1),
            'total_revenue' => $totalRevenue,
        ];
    }
}
