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

    public function getWeeklyHistory($userId) 
    {
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
        
        return DailyLog::where('user_id', $userId)
            ->where('date', '>=', $sevenDaysAgo)
            ->orderBy('date', 'desc')
            ->with('meals')
            ->get();
    }

    public function getTodayLog($userId) 
    {
        return $this->getDailyLogByUserAndDate($userId, now()->toDateString());
    }

}