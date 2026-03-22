<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\DailyLogResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\DailyLog;
use App\Services\DailyLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class DailyLogController extends Controller
{
    use ApiResponseTrait;

    protected $dailyLogService;

    public function __construct(DailyLogService $dailyLogService)
    {
        $this->dailyLogService = $dailyLogService;
    }

    /**
     * Get today's daily log
     */
    public function today($userId): JsonResponse
    {
        Gate::authorize('view', [DailyLog::class, $userId]);
        $log = $this->dailyLogService->getTodayLog($userId);
        return $this->successResponse(
            DailyLogResource::make($log->load('meals'))
        );
    }

    /**
     * Get weekly history (last 7 days)
     */
    public function weekly($userId): JsonResponse
    {
        Gate::authorize('view', [DailyLog::class, $userId]);
        $logs = $this->dailyLogService->getWeeklyHistory($userId);
        return $this->successResponse(
            DailyLogResource::collection($logs)
        );
    }

    /**
     * Get specific date log
     */
    public function byDate($userId, $date): JsonResponse
    {
        Gate::authorize('view', [DailyLog::class, $userId]);
        $log = $this->dailyLogService->getDailyLogByUserAndDate($userId, $date);
        return $this->successResponse(
            DailyLogResource::make($log->load('meals'))
        );
    }
}