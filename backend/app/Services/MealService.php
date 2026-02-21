<?php

namespace App\Services;

use App\Models\User;
use App\Models\Meal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\DailyLogService;
use Illuminate\Support\Facades\Cache;

class MealService 
{
    protected $dailyLogService;

    public function __construct(DailyLogService $dailyLogService)
    {
        $this->dailyLogService = $dailyLogService;
    }

    /**
     * Get all meals for a user 
     */
    public function getMealByUser($id) 
    {
        return Cache::remember("user_{$id}_meals", 1800, function () use ($id) {
            $user = User::findOrFail($id);
            return $user->meals; 
        });
    }

    /**
     * Get specific meal by ID 
     */
    public function getMealById($id) 
    {
        return Cache::remember("meal_{$id}", 3600, function () use ($id) {
            return Meal::findOrFail($id);
        });
    }

    /**
     * Create meal - Observer clears cache automatically
     */
    public function createMeal($userId, $data): Meal
    {
        $user = User::findOrFail($userId);
        try {
            $dailyLog = $this->dailyLogService->getTodayLog($userId);

            $data['daily_log_id'] = $dailyLog->id;
            $data['user_id'] = $userId;

            return $user->meals()->create($data);

        } catch (\Exception $e) {
            throw new \Exception("Failed to create meal: {$e->getMessage()}");
        }
    }

    /**
     * Update meal - Observer clears cache automatically
     */
    public function updateMeal($mealId, $data) 
    {
        try {
            $meal = Meal::findOrFail($mealId);
            $meal->update($data);
            return $meal;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        }
    }

    /**
     * Delete meal - Observer clears cache automatically
     */
    public function deleteMeal($mealId)
    {
        try {
            $meal = Meal::findOrFail($mealId);
            return $meal->delete();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        }
    }
}