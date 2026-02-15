<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyLog;
use App\Services\DailyLogService;

class DailyLogController extends Controller
{
    public function __construct( DailyLogService $dailyLogService)
    {
        $this->dailyLogService = $dailyLogService;
    }

    public function show($date) {
        $userId = auth()->id();
        try {
            $dailyLog = $this->dailyLogService->getDailyLogByUserAndDate($userId, $date);
            Gate::authorize('view', $dailyLog);
            $dailyLog->load('meals');
            return response()->json([
                'status' => 'success',
                'data' => $dailyLog,
                'meta' => [
                    'total_calories' => $dailyLog->meals->sum('calories'),
                    'remaining_calories' => $dailyLog->calorie_goal - $dailyLog->meals->sum('calories'),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
