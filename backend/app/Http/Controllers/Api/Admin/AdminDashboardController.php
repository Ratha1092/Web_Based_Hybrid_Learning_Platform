<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Analytics\Models\DailyMetric;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $todayDate = now()->toDateString();

        // 📅 Today metrics
        $today = DailyMetric::where('date', $todayDate)->first();

        // 📊 Summary (ALL TIME)
        $summary = [
            'total_revenue' => DailyMetric::sum('total_revenue'),
            'total_orders' => DailyMetric::sum('total_orders'),
            'total_users' => DailyMetric::sum('total_users'),
            'total_enrollments' => DailyMetric::sum('total_enrollments'),
        ];

        // 📅 Today data
        $todayData = [
            'revenue' => $today->total_revenue ?? 0,
            'orders' => $today->total_orders ?? 0,
            'enrollments' => $today->total_enrollments ?? 0,
            'new_users' => $today->new_users ?? 0,
        ];

        // 📈 Chart (last 30 days)
        $chart = DailyMetric::orderBy('date', 'asc')
            ->limit(30)
            ->get(['date', 'total_revenue']);

        return response()->json([
            'summary' => $summary,
            'today' => $todayData,
            'chart' => $chart,
        ]);
    }
}