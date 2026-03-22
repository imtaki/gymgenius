<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MealRequest;
use App\Http\Resources\MealResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\MealService;
use App\Services\DailyLogService;

class MealController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly MealService $mealService,
        private readonly DailyLogService $dailyLogService
    ) {}

    public function index($userId): JsonResponse
    {
        Gate::authorize('viewAny', [Meal::class, $userId]);
        $meals = $this->mealService->getMealByUser($userId);
        return $this->successResponse(
            MealResource::collection($meals)
        );
    }

    public function show(Meal $meal): JsonResponse
    {
        Gate::authorize('view', $meal);
        return $this->successResponse(
            MealResource::make($meal)
        );
    }

    public function store(MealRequest $request, $userId): JsonResponse
    {
        Gate::authorize('create', [Meal::class, $userId]);
        $meal = $this->mealService->createMeal($userId, $request->toDto());
        return $this->createdResponse(
            MealResource::make($meal)
        );
    }

    public function update(MealRequest $request, Meal $meal): JsonResponse
    {
        Gate::authorize('update', $meal);
        $updated = $this->mealService->updateMeal($meal->id, $request->toDto());
        return $this->successResponse(
            MealResource::make($updated)
        );
    }

    public function destroy(Meal $meal): JsonResponse
    {
        Gate::authorize('delete', $meal);
        $meal->delete();
        return $this->deletedResponse();
    }
}