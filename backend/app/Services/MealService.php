<?php

namespace App\Services;

use App\Models\User;
use App\Models\Meal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class MealService 
{
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
        try {
            $user = User::findOrFail($userId);
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