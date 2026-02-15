<?php

namespace App\Services;
use App\Models\DailyLog;
use Carbon\Carbon;
use App\Models\User;


class DailyLogService 
{
    /**
     * Get daily log for a user by date
     */
    public function getDailyLogByUserAndDate($userId, $date) 
    {
        $date = $dateString ? Carbon::parse($dateString)->toDateString() : now()->toDateString();

        return DailyLog::firstOrCreate(
            ['user_id' => $userId, 'date' => $date],
            ['calorie_goal' => 3000] 
        );
        
    }

    // TODO: Add frontend support for daily log updates + plus history button last couple days or so

}