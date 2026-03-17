<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseRequest;
use App\Models\Exercise;
use App\Services\ExerciseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    public function __construct(
        private readonly ExerciseService $exerciseService
    ) {}

    public function index()
    {
        $exercises = $this->exerciseService->getExercisesByUser(Auth::id());
        return response()->json($exercises);
    }

    public function show($exerciseId)
    {
        $exercise = $this->exerciseService->getExerciseById($exerciseId);
        Gate::authorize('view', $exercise);
        return response()->json($exercise);
    }

    public function store(ExerciseRequest $request)
    {
        Gate::authorize('create', Exercise::class);
        $exercise = $this->exerciseService->createExercise(Auth::id(), $request->validated());
        return response()->json([
            'message' => 'Exercise created successfully',
            'exercise' => $exercise,
        ], 201);
    }

    public function update(ExerciseRequest $request, $exerciseId)
    {
        $exercise = $this->exerciseService->getExerciseById($exerciseId);
        Gate::authorize('update', $exercise);
        $updated = $this->exerciseService->updateExercise($exerciseId, $request->validated());
        return response()->json([
            'message' => 'Exercise updated successfully',
            'exercise' => $updated,
        ]);
    }

    public function destroy($exerciseId): bool
    {
        $exercise = $this->exerciseService->getExerciseById($exerciseId);
        Gate::authorize('delete', $exercise);
        $this->exerciseService->deleteExercise($exerciseId);
        return response()->json(['message' => 'Exercise deleted successfully']);
    }

    public function muscleGroups(): JsonResponse
    {
        $muscleGroups = $this->exerciseService->getMuscleGroupsByUser(Auth::id());
        return response()->json(["muscleGroups" => $muscleGroups]);
    }
}