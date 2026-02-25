<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkoutProgramController extends Controller
{
    protected function __construct(WorkoutProgramService $workoutProgramService)
    {
        $this->workoutProgramService = $workoutProgramService;
    }

    public function index()
    {
        $workoutPrograms = $this->workoutProgramService->getAllWorkoutPrograms(auth()->user());
        return response()->json($workoutPrograms);
    }

    public function show(WorkoutProgram $workoutProgram)
    {
        Gate::authorize('view', WorkoutProgram::class);
        $workoutProgram->load('exercises');
        return response()->json($workoutProgram);
    }

    public function store(StoreWorkoutProgramRequest $request)
    {
        Gate::authorize('create', WorkoutProgram::class);
        $workoutProgram = $this->workoutProgramService->createWorkoutProgram($request->validated(), auth()->user());
        return response()->json($workoutProgram, 201);
    }

    public function update(UpdateWorkoutProgramRequest $request, WorkoutProgram $workoutProgram)
    {
        Gate::authorize('update', $workoutProgram);
        $updatedWorkoutProgram = $this->workoutProgramService->updateWorkoutProgram($workoutProgram, $request->validated());
        return response()->json($updatedWorkoutProgram);
    }

    public function destroy(WorkoutProgram $workoutProgram)
    {
        Gate::authorize('delete', $workoutProgram);
        $this->workoutProgramService->deleteWorkoutProgram($workoutProgram);
        return response()->json(['message' => 'Workout program deleted successfully'], 204);
    }
}
