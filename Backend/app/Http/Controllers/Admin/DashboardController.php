<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Course\Models\Course;
use App\Domains\Analytics\Models\DailyMetric;
use App\Domains\Learning\Models\Enrollment;
use App\Domains\Order\Models\Order;
use App\Domains\Payment\Models\Payment;
use App\Domains\Learning\Models\Review;
use App\Domains\User\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── ALL-TIME SUMMARY ──────────────────────────────────────────────
        // Revenue = sum of final_amount on paid orders only
        $totalRevenue = Order::where('payment_status', 'paid')->sum('final_amount');
        $totalOrders  = Order::where('payment_status', 'paid')->count();
        $totalUsers   = User::where('role', 'student')->count();
        $totalEnrollments = Enrollment::count();

        $summary = compact('totalRevenue', 'totalOrders', 'totalUsers', 'totalEnrollments');

        // ── TREND BADGES (this month vs last month) ───────────────────────
        $thisMonth = now()->month;
        $lastMonth = now()->subMonth()->month;
        $thisYear  = now()->year;
        $lastYear  = now()->subMonth()->year;

        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('paid_at', $thisMonth)->whereYear('paid_at', $thisYear)
            ->sum('final_amount');
        $revenueLastMonth = Order::where('payment_status', 'paid')
            ->whereMonth('paid_at', $lastMonth)->whereYear('paid_at', $lastYear)
            ->sum('final_amount');
        $revenueTrend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        $ordersThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('paid_at', $thisMonth)->whereYear('paid_at', $thisYear)->count();
        $ordersLastMonth = Order::where('payment_status', 'paid')
            ->whereMonth('paid_at', $lastMonth)->whereYear('paid_at', $lastYear)->count();
        $ordersTrend = $ordersLastMonth > 0
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : 0;

        $usersThisMonth = User::where('role', 'student')
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->count();
        $usersLastMonth = User::where('role', 'student')
            ->whereMonth('created_at', $lastMonth)->whereYear('created_at', $lastYear)->count();
        $usersTrend = $usersLastMonth > 0
            ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100, 1)
            : 0;

        $enrollThisMonth = Enrollment::whereMonth('enrolled_at', $thisMonth)
            ->whereYear('enrolled_at', $thisYear)->count();
        $enrollLastMonth = Enrollment::whereMonth('enrolled_at', $lastMonth)
            ->whereYear('enrolled_at', $lastYear)->count();
        $enrollTrend = $enrollLastMonth > 0
            ? round((($enrollThisMonth - $enrollLastMonth) / $enrollLastMonth) * 100, 1)
            : 0;

        $trends = compact('revenueTrend', 'ordersTrend', 'usersTrend', 'enrollTrend');

        // ── TODAY ─────────────────────────────────────────────────────────
        $todayData = [
            'revenue'     => Order::where('payment_status', 'paid')
                                ->whereDate('paid_at', today())
                                ->sum('final_amount'),
            'orders'      => Order::where('payment_status', 'paid')
                                ->whereDate('paid_at', today())
                                ->count(),
            'enrollments' => Enrollment::whereDate('enrolled_at', today())->count(),
            'new_users'   => User::whereDate('created_at', today())->count(),
        ];

        // ── REVENUE CHART (last 30 days from daily_metrics) ───────────────
        // Prefer daily_metrics if populated, otherwise fall back to orders
        $hasDailyMetrics = DailyMetric::where('date', '>=', now()->subDays(30))->exists();

        if ($hasDailyMetrics) {
            $chart = DailyMetric::where('date', '>=', now()->subDays(30))
                ->orderBy('date')
                ->select('date', 'total_revenue', 'total_orders', 'new_users', 'total_enrollments')
                ->get();
        } else {
            // Fallback: aggregate from orders table
            $chart = Order::where('payment_status', 'paid')
                ->where('paid_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(paid_at) as date, SUM(final_amount) as total_revenue, COUNT(*) as total_orders, 0 as new_users, 0 as total_enrollments')
                ->groupByRaw('DATE(paid_at)')
                ->orderBy('date')
                ->get();
        }

        // ── TOP COURSES (by enrollment count) ─────────────────────────────
        $topCourses = Course::withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->with('category')
            ->where('is_published', true)
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get();

        // ── ENROLLMENT BY COURSE LEVEL (for donut) ────────────────────────
        $enrollmentByLevel = Enrollment::join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->selectRaw('courses.level, COUNT(*) as total')
            ->whereNull('enrollments.deleted_at')
            ->groupBy('courses.level')
            ->pluck('total', 'level');

        // ── REVENUE SHARE BREAKDOWN (platform vs instructors) ─────────────
        $revenueBreakdown = DB::table('revenue_shares')
            ->selectRaw('
                SUM(platform_amount)    as platform_total,
                SUM(instructor_amount)  as instructor_total,
                SUM(total_amount)       as gross_total
            ')
            ->first() ?? (object)['platform_total' => 0, 'instructor_total' => 0, 'gross_total' => 0];

        // ── TOP INSTRUCTORS by earnings ────────────────────────────────────
        $topInstructors = User::where('role', 'instructor')
            ->join('instructor_wallets', 'users.id', '=', 'instructor_wallets.instructor_id')
            ->select('users.id', 'users.name', 'users.avatar', 'instructor_wallets.balance')
            ->orderByDesc('instructor_wallets.balance')
            ->limit(5)
            ->get();

        // ── RECENT ORDERS ─────────────────────────────────────────────────
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(6)
            ->get();

        // ── RECENT ENROLLMENTS (for activity feed) ────────────────────────
        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest('enrolled_at')
            ->limit(6)
            ->get();

        // ── PLATFORM REVENUE STATS ────────────────────────────────────────
        $pendingPayouts = DB::table('payout_requests')
            ->where('status', 'pending')
            ->sum('amount');

        $avgRating = Review::where('is_approved', true)->avg('rating');

        $completedEnrollments = Enrollment::whereNotNull('completed_at')->count();
        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 1)
            : 0;

        return view('admin.dashboard', compact(
            'summary',
            'trends',
            'todayData',
            'chart',
            'topCourses',
            'enrollmentByLevel',
            'revenueBreakdown',
            'topInstructors',
            'recentOrders',
            'recentEnrollments',
            'pendingPayouts',
            'avgRating',
            'completionRate',
        ));
    }
}