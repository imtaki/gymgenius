<?php

namespace App\Services;
use App\Models\User;
use App\Models\Meal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MealService {

    public function getMealByUser($id) {
        try {
            $user = User::FindOrFail($id);
            return $user->meals;
        } catch (ModelNotFoundException $e) {
            return response()->json($e->getMessage(), 404);
        }
    }

     public function getMealById($id) {
        try {
            return Meal::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function createMeal($userId, $data): Meal
    {
        try {
            $user = User::findOrFail($userId);
            return $user->meals()->create($data);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("User not found with ID: {$userId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to create meal: {$e->getMessage()}");
        }
    }

    public function updateMeal($mealId, $data) {
        try {
            $meal = Meal::findOrFail($mealId);
            $meal->update($data);
            return $meal;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to update meal: {$e->getMessage()}");
        }
    }

    public function deleteMeal($mealId): bool
    {
        try {
            $meal = Meal::findOrFail($mealId);
            return $meal->delete();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Meal not found with ID: {$mealId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete meal: {$e->getMessage()}");
        }
    }

}