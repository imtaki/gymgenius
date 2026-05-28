<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutSplitExerciseRequest;
use App\Http\Requests\UpdateWorkoutSplitExerciseRequest;
use App\Http\Resources\WorkoutSplitExerciseResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\WorkoutSplit;
use App\Models\WorkoutSplitExercise;
use App\Services\WorkoutSplitExerciseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorkoutSplitExerciseController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private readonly WorkoutSplitExerciseService $service
    ) {}

    /**
     * Get exercises for a specific workout split
     */
    public function index(WorkoutSplit $split): JsonResponse
    {
        $this->authorize('view', $split);
        $exercises = $this->service->getByWorkoutSplit($split->id);
        return $this->successResponse(
            WorkoutSplitExerciseResource::collection($exercises)
        );
    }

    /**
     * Add an exercise to a workout split
     */
    public function store(WorkoutSplit $split, StoreWorkoutSplitExerciseRequest $request): JsonResponse
    {
        $this->authorize('update', $split);
        $exercise = $this->service->create($split->id, $request->toDto());
        return $this->createdResponse(
            WorkoutSplitExerciseResource::make($exercise),
            'Exercise added to workout split successfully'
        );
    }

    /**
     * Get a specific exercise from a workout split
     */
    public function show(WorkoutSplit $split, WorkoutSplitExercise $exercise): JsonResponse
    {
        $this->authorize('view', $split);
        if ($exercise->workout_split_id !== $split->id) {
            return $this->errorResponse('Exercise not found in this workout split', 404);
        }
        $exercise = $this->service->getById($exercise->id);
        return $this->successResponse(
            WorkoutSplitExerciseResource::make($exercise)
        );
    }

    /**
     * Update an exercise in a workout split
     */
    public function update(WorkoutSplit $split, WorkoutSplitExercise $exercise, UpdateWorkoutSplitExerciseRequest $request): JsonResponse
    {
        $this->authorize('update', $split);
        if ($exercise->workout_split_id !== $split->id) {
            return $this->errorResponse('Exercise not found in this workout split', 404);
        }
        $updated = $this->service->update($exercise->id, $request->toDto());
        return $this->successResponse(
            WorkoutSplitExerciseResource::make($updated),
            'Exercise updated successfully'
        );
    }

    /**
     * Remove an exercise from a workout split
     */
    public function destroy(WorkoutSplit $split, WorkoutSplitExercise $exercise): JsonResponse
    {
        $this->authorize('update', $split);
        if ($exercise->workout_split_id !== $split->id) {
            return $this->errorResponse('Exercise not found in this workout split', 404);
        }
        $this->service->delete($exercise->id);
        return $this->deletedResponse();
    }
}
