<?php

namespace App\Filament\Pages;

use BackedEnum;
use App\Domains\Courses\Models\Course;
use App\Domains\Courses\Models\Lesson;
use App\Domains\Courses\Models\Section;
use App\Domains\Users\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Models\Payment;
use App\Domains\Finance\Models\InstructorWallet;
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

        // Payment & Finance Metrics
        $totalOrders = Order::count();
        $paidStatuses = [PaymentStatus::Paid->value, PaymentStatus::Completed->value];
        $totalRevenue = Payment::whereIn('status', $paidStatuses)->sum('amount');
        $completedPayments = Payment::whereIn('status', $paidStatuses)->count();
        $pendingPayments = Payment::where('status', PaymentStatus::Pending->value)->count();
        $failedPayments = Payment::where('status', PaymentStatus::Failed->value)->count();

        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $revenueThisMonth = Payment::whereIn('status', $paidStatuses)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $recentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = Payment::with(['order'])
            ->latest()
            ->take(6)
            ->get();

        $totalInstructorBalance = InstructorWallet::sum('balance');
        $totalPendingBalance = InstructorWallet::sum('pending_balance');

        $revenueTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($paidStatuses) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M'),
                'revenue' => Payment::whereIn('status', $paidStatuses)
                    ->whereMonth('paid_at', $date->month)
                    ->whereYear('paid_at', $date->year)
                    ->sum('amount'),
                'orders' => Order::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        });

        $paymentStatusBreakdown = [
            'completed' => Payment::whereIn('status', $paidStatuses)->count(),
            'pending' => Payment::where('status', PaymentStatus::Pending->value)->count(),
            'failed' => Payment::where('status', PaymentStatus::Failed->value)->count(),
            'refunded' => Payment::where('status', PaymentStatus::Refunded->value)->count(),
        ];

        $paymentGatewayBreakdown = [
            'stripe' => Payment::whereIn('status', $paidStatuses)->where('payment_gateway', 'stripe')->sum('amount'),
            'khqr' => Payment::whereIn('status', $paidStatuses)->where('payment_gateway', 'khqr')->sum('amount'),
            'paypal' => Payment::whereIn('status', $paidStatuses)->where('payment_gateway', 'paypal')->sum('amount'),
        ];

        return [
            'totalStudents'           => $totalStudents,
            'totalInstructors'        => $totalInstructors,
            'totalCourses'            => $totalCourses,
            'totalSections'           => $totalSections,
            'totalLessons'            => $totalLessons,
            'pendingVerifications'    => $pendingVerifications,
            'newStudentsThisMonth'    => $newStudentsThisMonth,
            'newCoursesThisMonth'     => $newCoursesThisMonth,
            'recentCourses'           => $recentCourses,
            'recentUsers'             => $recentUsers,
            'pendingInstructors'      => $pendingInstructors,
            'studentTrend'            => $studentTrend,
            // Payment & Finance Data
            'totalOrders'             => $totalOrders,
            'totalRevenue'            => $totalRevenue,
            'completedPayments'       => $completedPayments,
            'pendingPayments'         => $pendingPayments,
            'failedPayments'          => $failedPayments,
            'ordersThisMonth'         => $ordersThisMonth,
            'revenueThisMonth'        => $revenueThisMonth,
            'recentOrders'            => $recentOrders,
            'recentPayments'          => $recentPayments,
            'totalInstructorBalance'  => $totalInstructorBalance,
            'totalPendingBalance'     => $totalPendingBalance,
            'revenueTrend'            => $revenueTrend,
            'paymentStatusBreakdown'  => $paymentStatusBreakdown,
            'paymentGatewayBreakdown' => $paymentGatewayBreakdown,
        ];
    }
}
