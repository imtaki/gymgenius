<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutProgramRequest;
use App\Http\Requests\UpdateWorkoutProgramRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\WorkoutProgram;
use App\Services\WorkoutProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WorkoutProgramController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly WorkoutProgramService $workoutProgramService)
    {
    }

    public function index(): JsonResponse
    {
        $workoutPrograms = $this->workoutProgramService->getAllWorkoutPrograms(Auth::user());
        return $this->successResponse($workoutPrograms);
    }

    public function show(WorkoutProgram $workoutProgram): JsonResponse
    {
        Gate::authorize('view', $workoutProgram);
        $workoutProgram->load('exercises');
        return $this->successResponse($workoutProgram);
    }

    public function store(StoreWorkoutProgramRequest $request): JsonResponse
    {
        Gate::authorize('create', WorkoutProgram::class);
        $workoutProgram = $this->workoutProgramService->createWorkoutProgram($request->validated(), Auth::user());
        return $this->createdResponse($workoutProgram);
    }

    public function update(UpdateWorkoutProgramRequest $request, WorkoutProgram $workoutProgram): JsonResponse
    {
        Gate::authorize('update', $workoutProgram);
        $updatedWorkoutProgram = $this->workoutProgramService->updateWorkoutProgram($workoutProgram, $request->validated());
        return $this->successResponse($updatedWorkoutProgram);
    }

    public function destroy(WorkoutProgram $workoutProgram): JsonResponse
    {
        Gate::authorize('delete', $workoutProgram);
        $this->workoutProgramService->deleteWorkoutProgram($workoutProgram);
        return $this->deletedResponse();
    }
}
