<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Exercise;
use App\Services\ExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExerciseController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly ExerciseService $exerciseService
    ) {}

    public function index(): JsonResponse
    {
        $exercises = $this->exerciseService->getExercisesByUser(Auth::id());
        return $this->successResponse(
            ExerciseResource::collection($exercises)
        );
    }

    public function show(Exercise $exercise): JsonResponse
    {
        Gate::authorize('view', $exercise);
        return $this->successResponse(
            ExerciseResource::make($exercise)
        );
    }

    public function store(ExerciseRequest $request): JsonResponse
    {
        Gate::authorize('create', Exercise::class);
        $exercise = $this->exerciseService->createExercise(Auth::id(), $request->toDto());
        return $this->createdResponse(
            ExerciseResource::make($exercise),
            'Exercise created successfully'
        );
    }

    public function update(ExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        Gate::authorize('update', $exercise);
        $updated = $this->exerciseService->updateExercise($exercise->id, $request->toDto());
        return $this->successResponse(
            ExerciseResource::make($updated)
        );
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        Gate::authorize('delete', $exercise);
        $this->exerciseService->deleteExercise($exercise->id);
        return $this->deletedResponse();
    }

    public function muscleGroups(): JsonResponse
    {
        $muscleGroups = $this->exerciseService->getMuscleGroupsByUser(Auth::id());
        return $this->successResponse([
            'muscleGroups' => $muscleGroups
        ]);
    }
}