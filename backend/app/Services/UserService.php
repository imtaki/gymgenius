<?php 

namespace App\Services;

class UserService 
{
    /**
     * Get all user count for admin dashboard
     */
    public function getUserCount(): int
    {

        return Cache::remember("user_count", 3600, function () {
            return User::count();
        });
    }
    /**
     * TODO: For admin dashboard - get subscription count + other stats, future implementation
     */
    public function getUserSubscriptionsCount(): int {
    }

    /**
     * Get all user workout count for admin dashboard, TODO: implement in future
     */
    public function getUserWorkoutCount(): int {
    }

    /**
     * Get all user meal logs count for admin dashboard
     */
    public function getUserMealLogsCount(): int {
        return Cache::remember("user_meal_logs_count", 3600, function () {
            return MealLog::count();
        });
    }




}