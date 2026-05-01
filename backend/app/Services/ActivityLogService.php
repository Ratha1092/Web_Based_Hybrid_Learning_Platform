<?php

namespace App\Services;

use App\Domains\User\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log user activity
     */
    public static function log(string $action, ?User $user = null, ?Request $request = null, ?array $data = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'ip_address' => $request?->ip() ?? request()->ip(),
            'user_agent' => $request?->userAgent() ?? request()->userAgent(),
            'data' => $data,
        ]);
    }

    /**
     * Get user activity history
     */
    public static function getUserHistory(User $user, int $limit = 50)
    {
        return ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all activity logs (admin)
     */
    public static function getAllLogs(int $limit = 100)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent logins for user
     */
    public static function getRecentLogins(User $user, int $limit = 10)
    {
        return ActivityLog::where('user_id', $user->id)
            ->where('action', 'login')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
