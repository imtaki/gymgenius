<?php

namespace App\Services;

use App\Data\CreateMealData;
use App\Data\UpdateMealData;
use App\Models\User;
use App\Models\Meal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class MealService 
{
    public function __construct(private readonly DailyLogService $dailyLogService) {}

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
    public function createMeal($userId, CreateMealData $data): Meal
    {
        $user = User::findOrFail($userId);
        try {
            $dailyLog = $this->dailyLogService->getTodayLog($userId);

            $meal = $user->meals()->create([
                'daily_log_id' => $dailyLog->id,
                'user_id' => $userId,
                'name' => $data->name,
                'category' => $data->category,
                'calories' => $data->calories,
                'protein' => $data->protein,
                'carbs' => $data->carbs,
                'fats' => $data->fats,
                'date' => $data->date,
            ]);

            return $meal;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create meal: {$e->getMessage()}");
        }
    }

    /**
     * Update meal - Observer clears cache automatically
     */
    public function updateMeal($mealId, UpdateMealData $data): Meal
    {
        try {
            $meal = Meal::findOrFail($mealId);
            
            $updateData = array_filter([
                'name' => $data->name,
                'category' => $data->category,
                'calories' => $data->calories,
                'protein' => $data->protein,
                'carbs' => $data->carbs,
                'fats' => $data->fats,
                'date' => $data->date,
            ], fn($value) => $value !== null);

            $meal->update($updateData);
            return $meal;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        }
    }

    /**
     * Delete meal - Observer clears cache automatically
     */
    public function deleteMeal($mealId): bool
    {
        try {
            $meal = Meal::findOrFail($mealId);
            return $meal->delete();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        }
    }
}