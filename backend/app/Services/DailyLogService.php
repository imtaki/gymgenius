<?php

namespace App\Services;

use App\Models\DailyLog;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserSettings;



class DailyLogService 
{
    /**
     * Get daily log for a user by date
     */
    public function getDailyLogByUserAndDate($userId, $date) 
    {
        $formattedDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $caloricGoal = UserSettings::where('user_id', $userId)->first()?->caloric_goal ?? 2000; 

        return DailyLog::firstOrCreate(
            ['user_id' => $userId, 'date' => $formattedDate],
            ['calorie_goal' => $caloricGoal] 
        );
        
    }

    /**
     * Get 7-day history of daily logs.
     * Limited by date range (7 days), safe for unbounded get().
     * Note: Maximum ~7 rows, no pagination needed.
     */
    public function getWeeklyHistory($userId) 
    {
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
        
        return DailyLog::where('user_id', $userId)
            ->where('date', '>=', $sevenDaysAgo)
            ->orderBy('date', 'desc')
            ->with('meals')
            ->get();
    }

    /**
     * Get daily logs for a date range with pagination.
     * Use this for arbitrary date ranges or UI that requires pagination.
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getDailyLogsByDateRangePaginated(int $userId, string $startDate, string $endDate, int $perPage = 30)
    {
        return DailyLog::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('meals')
            ->orderBy('date', 'desc')
            ->paginate($perPage);
    }

    public function getTodayLog($userId) 
    {
        return $this->getDailyLogByUserAndDate($userId, now()->toDateString());
    }

}