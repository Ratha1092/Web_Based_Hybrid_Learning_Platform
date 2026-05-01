<?php

namespace App\Domains\Admin\Services;

use App\Domains\Analytics\Models\DailyMetric;

class DashboardService
{
    public function getSummary()
    {
        return [
            'total_revenue' => DailyMetric::sum('total_revenue'),
            'total_orders' => DailyMetric::sum('total_orders'),
            'total_users' => DailyMetric::sum('total_users'),
            'total_enrollments' => DailyMetric::sum('total_enrollments'),
        ];
    }

    public function getToday()
    {
        $today = DailyMetric::where('date', now()->toDateString())->first();

        return [
            'revenue' => $today->total_revenue ?? 0,
            'orders' => $today->total_orders ?? 0,
            'enrollments' => $today->total_enrollments ?? 0,
            'new_users' => $today->new_users ?? 0,
        ];
    }

    public function getChart()
    {
        return DailyMetric::orderBy('date', 'asc')
            ->limit(30)
            ->get(['date', 'total_revenue']);
    }
}