<?php

namespace App\Http\Controllers;

use App\Services\DailyLogService;
use Illuminate\Http\Request;
use App\Models\DailyLog;

class DailyLogController extends Controller
{
    protected $dailyLogService;

    public function __construct(DailyLogService $dailyLogService)
    {
        $this->dailyLogService = $dailyLogService;
    }

    /**
     * Get today's daily log
     */
    public function today($userId)
    {
        Gate::authorize('view', DailyLog::class);
        $log = $this->dailyLogService->getTodayLog($userId);
        return response()->json($log->load('meals'));
    }

    /**
     * Get weekly history (last 7 days)
     */
    public function weekly($userId)
    {
        Gate::authorize('view', DailyLog::class);
        $logs = $this->dailyLogService->getWeeklyHistory($userId);
        return response()->json($logs);
    }

    /**
     * Get specific date log
     */
    public function byDate($userId, $date)
    {
        $log = $this->dailyLogService->getDailyLogByUserAndDate($userId, $date);
        return response()->json($log->load('meals'));
    }
}