<?php 

namespace App\Services;

use App\Models\User;
use App\Models\Meal;
use App\Models\WorkoutLog;
use Illuminate\Support\Facades\Cache;

class UserService 
{
    /**
     * Get all user data for admin dashboard, Cached for 1 hour, User count + Meal logs count
     */
    public function getUserDataCount(): array
    {

       return Cache::remember('user_dashboard_stats', 3600, function () {
            return [
                'user_count' => (int) User::count(),
                'meal_logs_count' => (int) Meal::count(),
                // 'workout_logs_count' => (int) WorkoutLog::count(), // Future implementation in future when workout logs are added
            ];
        });

    }
    /**
     * TODO: For admin dashboard - get subscription count + other stats, future implementation
     */
    public function getUserSubscriptionsCount(): int {
        return 0;
    }

    /**
     * Get all user workout count for admin dashboard, TODO: implement in future
     */
    public function getUserWorkoutCount(): int {
        return 0;
    }

    /**
     * Get Recent users
     */
    public function getRecentUsers(): array 
    {
    return Cache::remember('recent_users_list', 3600, function () {
        return User::orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(['id', 'name', 'email', 'created_at'])
                    ->toArray();
            });
    }

}