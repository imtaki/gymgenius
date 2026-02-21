<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MealRequest;
use App\Services\MealService;
use App\Services\DailyLogService;
use Illuminate\Support\Facades\Gate;


class MealController extends Controller
{
    public function __construct(MealService $mealService, DailyLogService $dailyLogService) {
        $this->mealService = $mealService;
        $this->dailyLogService = $dailyLogService;
    }



    public function index(Request $request, $userId)
    {
        $user = Auth::user();
        
        if ($user->id != $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $meals = $this->mealService->getMealByUser($userId);
        return response()->json($meals, 200);
    }

    public function show($mealId) {
        $meal = $this->mealService->getMealById($id);
        return response()->json($meal, 200);
    }

    public function store(MealRequest $request, $userId) {
        Gate::authorize('create', [Meal::class, $userId]);
        $meal = $this->mealService->createMeal($userId, $request->validated());
        return response()->json($meal, 201);
    }

    public function update(MealRequest $request, $mealId) {
        Gate::authorize('update', Meal::class);
        $meal = $this->mealService->updateMeal($mealId, $request);
        if (!$data["success"]) {
            return response()->json(['error' => $data["message"]], 409);
        }
        return response()->json($meal, 200);
    }



    public function destroy($mealId) {
        $meal = Meal::findOrFail($mealId);
        Gate::authorize('delete', $meal);
        $meal->delete();
        return response()->json(["message" => "Meal deleted successfully"], 200);
    }

}