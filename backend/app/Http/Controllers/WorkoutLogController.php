<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutLogRequest;
use App\Http\Requests\UpdateWorkoutLogRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\WorkoutLog;
use App\Services\WorkoutLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class WorkoutLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly WorkoutLogService $workoutLogService)
    {
    }

    public function index(): JsonResponse
    {
        $logs = $this->workoutLogService->getAllWorkoutLogs(Auth::user());
        return $this->successResponse($logs);
    }

    public function show(WorkoutLog $workoutLog): JsonResponse
    {
        Gate::authorize('view', $workoutLog);
        $log = $this->workoutLogService->getWorkoutLogById($workoutLog->id);
        return $this->successResponse($log);
    }

    public function store(StoreWorkoutLogRequest $request): JsonResponse
    {
        Gate::authorize('create', [WorkoutLog::class, Auth::id()]);
        $log = $this->workoutLogService->createWorkoutLog(Auth::user(), $request->validated());
        return $this->createdResponse($log);
    }

    public function update(UpdateWorkoutLogRequest $request, WorkoutLog $workoutLog): JsonResponse
    {
        Gate::authorize('update', $workoutLog);
        $log = $this->workoutLogService->updateWorkoutLog($workoutLog, $request->validated());
        return $this->successResponse($log);
    }

    public function destroy(WorkoutLog $workoutLog): JsonResponse
    {
        Gate::authorize('delete', $workoutLog);
        $this->workoutLogService->deleteWorkoutLog($workoutLog);
        return $this->deletedResponse();
    }

    public function getDataByRange($startDate, $endDate): JsonResponse
    {
        $logs = $this->workoutLogService->getWorkoutLogsByDateRange(
            auth()->user(),
            $startDate,
            $endDate
        );
        return $this->successResponse($logs);
    }
}
