<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // In Filament v5, $view is a non-static property on the parent — do NOT use static here
    protected string $view = 'filament.pages.dashboard';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -2;

    public function getViewData(): array
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalCourses = Course::count();
        $totalSections = Section::count();
        $totalLessons = Lesson::count();

        $pendingVerifications = User::where('role', 'instructor')
            ->where('is_verified', false)
            ->count();

        $newStudentsThisMonth = User::where('role', 'student')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $newCoursesThisMonth = Course::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $recentCourses = Course::with(['instructor', 'category'])
            ->latest()
            ->take(6)
            ->get();

        $recentUsers = User::latest()
            ->take(6)
            ->get();

        $pendingInstructors = User::where('role', 'instructor')
            ->where('is_verified', false)
            ->latest()
            ->take(5)
            ->get();

        $studentTrend = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M'),
                'count' => User::where('role', 'student')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        });

        return [
            'totalStudents'        => $totalStudents,
            'totalInstructors'     => $totalInstructors,
            'totalCourses'         => $totalCourses,
            'totalSections'        => $totalSections,
            'totalLessons'         => $totalLessons,
            'pendingVerifications' => $pendingVerifications,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'newCoursesThisMonth'  => $newCoursesThisMonth,
            'recentCourses'        => $recentCourses,
            'recentUsers'          => $recentUsers,
            'pendingInstructors'   => $pendingInstructors,
            'studentTrend'         => $studentTrend,
        ];
    }
}