<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\Course\Models\Course;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Learning\Models\Review;
use App\Domains\Finance\Models\InstructorWallet;
use App\Domains\Finance\Models\PayoutRequest;
use App\Domains\Finance\Models\RevenueShare;
use App\Domains\Finance\Models\WalletTransaction;

class InstructorController extends Controller
{
    public function dashboard(Request $request)
    {
        $instructor = Auth::user();

        if ($instructor->role !== 'instructor') {
            return redirect('/dashboard')->with('error', 'Unauthorized access');
        }

        $instructorId = $instructor->id;

        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $instructorId],
            ['balance' => 0, 'pending_balance' => 0, 'currency' => 'USD']
        );

        $totalEarned = RevenueShare::where('instructor_id', $instructorId)
            ->sum('instructor_amount');

        $pendingPayout = PayoutRequest::where('instructor_id', $instructorId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $earningsThisMonth = RevenueShare::where('instructor_id', $instructorId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('instructor_amount');

        $earningsLastMonth = RevenueShare::where('instructor_id', $instructorId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('instructor_amount');

        $enrollThisMonth = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->whereMonth('enrolled_at', now()->month)
            ->whereYear('enrolled_at', now()->year)
            ->count();

        $enrollLastMonth = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->whereMonth('enrolled_at', now()->subMonth()->month)
            ->whereYear('enrolled_at', now()->subMonth()->year)
            ->count();

        $enrollTrendPct = $enrollLastMonth > 0
            ? round((($enrollThisMonth - $enrollLastMonth) / $enrollLastMonth) * 100, 1)
            : 0;

        $totalCourses = Course::where('instructor_id', $instructorId)->count();
        $publishedCourses = Course::where('instructor_id', $instructorId)
            ->where('is_published', true)
            ->count();
        $draftCourses = Course::where('instructor_id', $instructorId)
            ->where('is_published', false)
            ->count();

        $totalStudents = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->distinct('user_id')
            ->count('user_id');

        $activeStudents = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->where('status', 'active')
            ->count();

        $completedStudents = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->whereNotNull('completed_at')
            ->count();

        $reviewStats = Review::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        $earningsTrend = RevenueShare::where('instructor_id', $instructorId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m'))
            ->map(function ($group, $month) {
                return (object) [
                    'month' => $month,
                    'total' => $group->sum('instructor_amount'),
                ];
            })
            ->sortBy('month')
            ->values();

        $myCourses = Course::where('instructor_id', $instructorId)
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->with('category')
            ->orderByDesc('is_published')
            ->orderByDesc('created_at')
            ->get();

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->whereHas('course', fn($q) =>
                $q->where('instructor_id', $instructorId)
            )
            ->latest('enrolled_at')
            ->limit(6)
            ->get();

        $recentReviews = Review::with(['user', 'course'])
            ->whereHas('course', fn($q) =>
                $q->where('instructor_id', $instructorId)
            )
            ->where('is_approved', true)
            ->latest()
            ->limit(5)
            ->get();

        $recentTransactions = WalletTransaction::where('instructor_id', $instructorId)
            ->latest()
            ->limit(5)
            ->get();

        $myEnrollTotal = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )->count();

        $myEnrollCompleted = Enrollment::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->whereNotNull('completed_at')
            ->count();

        $completionRate = $myEnrollTotal > 0
            ? round($myEnrollCompleted / $myEnrollTotal * 100, 1)
            : 0;

        $ratingBreakdown = Review::whereHas('course', fn($q) =>
            $q->where('instructor_id', $instructorId)
        )
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->pluck('count', 'rating');

        return view('instructor.dashboard', [
            'instructor' => $instructor,
            'wallet' => $wallet,
            'totalEarned' => $totalEarned,
            'pendingPayout' => $pendingPayout,
            'earningsThisMonth' => $earningsThisMonth,
            'enrollTrendPct' => $enrollTrendPct,
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'draftCourses' => $draftCourses,
            'totalStudents' => $totalStudents,
            'activeStudents' => $activeStudents,
            'completedStudents' => $completedStudents,
            'reviewStats' => $reviewStats,
            'earningsTrend' => $earningsTrend,
            'myCourses' => $myCourses,
            'recentEnrollments' => $recentEnrollments,
            'recentReviews' => $recentReviews,
            'recentTransactions' => $recentTransactions,
            'pendingPayout' => $pendingPayout,
            'completionRate' => $completionRate,
            'ratingBreakdown' => $ratingBreakdown,
        ]);
    }

    /**
     * Show instructor courses list
     */
    public function indexCourses(Request $request)
    {
        $instructor = Auth::user();

        if ($instructor->role !== 'instructor') {
            return redirect('/dashboard')->with('error', 'Unauthorized access');
        }

        $courses = $instructor->courses()->paginate(15);

        return view('instructor.courses', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show instructor students list
     */
    public function indexStudents(Request $request)
    {
        $instructor = Auth::user();

        if ($instructor->role !== 'instructor') {
            return redirect('/dashboard')->with('error', 'Unauthorized access');
        }

        // Get all unique students enrolled in this instructor's courses
        $students = Enrollment::whereIn('course_id', $instructor->courses()->pluck('id'))
            ->with('user')
            ->distinct('user_id')
            ->paginate(20);

        return view('instructor.students', [
            'students' => $students,
        ]);
    }

    /**
     * Show instructor earnings/revenue
     */
    public function indexEarnings(Request $request)
    {
        $instructor = Auth::user();

        if ($instructor->role !== 'instructor') {
            return redirect('/dashboard')->with('error', 'Unauthorized access');
        }

        $earnings = $instructor->revenueShares()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalEarnings = $instructor->wallet()
            ->first()?->balance ?? 0;

        return view('instructor.earnings', [
            'earnings' => $earnings,
            'totalEarnings' => $totalEarnings,
        ]);
    }

    public function createCourse(Request $request)
    {
        return $this->placeholderPage('Create Course', 'Course creation is not ready yet.');
    }

    public function indexReviews(Request $request)
    {
        return $this->placeholderPage('Reviews', 'Review management is coming soon.');
    }

    public function indexPayouts(Request $request)
    {
        return $this->placeholderPage('Payouts', 'Payout management is not available yet.');
    }

    public function indexWallet(Request $request)
    {
        return $this->placeholderPage('Wallet', 'Wallet details will be available soon.');
    }

    public function indexProfile(Request $request)
    {
        return $this->placeholderPage('Profile', 'Instructor profile settings are coming soon.');
    }

    public function indexSettings(Request $request)
    {
        return $this->placeholderPage('Settings', 'Instructor account settings are not ready yet.');
    }

    protected function placeholderPage(string $title, string $description)
    {
        return view('instructor.placeholder', compact('title', 'description'));
    }
}
