<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Get user's activity history
     */
    public function history(Request $request)
    {
        $limit = $request->query('limit', 50);
        $logs = ActivityLogService::getUserHistory($request->user(), $limit);

        return ApiResponse::success([
            'logs' => $logs,
            'count' => count($logs),
        ], 'Activity history');
    }

    /**
     * Get user's recent logins
     */
    public function recentLogins(Request $request)
    {
        $limit = $request->query('limit', 10);
        $logins = ActivityLogService::getRecentLogins($request->user(), $limit);

        return ApiResponse::success([
            'logins' => $logins,
            'count' => count($logins),
        ], 'Recent logins');
    }

    /**
     * Admin: Get all activity logs
     */
    public function all(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return ApiResponse::error('Unauthorized', 403);
        }

        $limit = $request->query('limit', 100);
        $logs = ActivityLogService::getAllLogs($limit);

        return ApiResponse::success([
            'logs' => $logs,
            'count' => count($logs),
        ], 'All activity logs');
    }
}
