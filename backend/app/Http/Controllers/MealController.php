<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function getMealByUser($id) {
        try {
            $user = User::findorFail($id);
            return response()->json($user->meals, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function getMealById($id) {
        try {
            $meal = Meal::findOrFail($id);
            return response()->json($meal, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($e->getMessage(), 404);
        }
    }

    public function createMeal(Request $request, $userId) {

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'calories' => 'required|integer',
            'protein' => 'required|integer',
            'carbs' => 'required|integer',
            'fats' => 'required|integer'
        ]);
        try {
            $meal = $user->meals()->create($validated);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return response()->json([
            'message' => 'Meal registered successfully',
            'meal' => $meal,
        ], 201);
    }

    public function deleteMeal($id) {
        try {
            $meal = Meal::findOrFail($id);
            $meal->delete();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return response()->json("Meal deleted successfully", 204);
    }
}
