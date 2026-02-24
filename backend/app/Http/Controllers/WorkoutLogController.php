<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WorkoutLogService;
use App\Models\WorkoutLog;

class WorkoutLogController extends Controller
{
     protected function __construct(WorkoutLogService $workoutLogService)
    {
        $this->workoutLogService = $workoutLogService;
    }

    public function index() 
    {
        $logs = $this->workoutLogService->getAllWorkoutLogs(auth()->user());
        return response()->json($logs);
    }

    public function show(WorkoutLog $workoutLog) 
    {
        Gate::authorize('view', WorkoutLog::class);
        $log = $this->workoutLogService->getWorkoutLogById($workoutLog->id);
        return response()->json($log);
    }

    public function store(StoreWorkoutLogRequest $request) 
    {
        Gate::authorize('create', [WorkoutLog::class, $request->user()->id]);
        $log = $this->workoutLogService->createWorkoutLog(auth()->user(), $request->validated());
        return response()->json($log, 201);
    }

    public function update(UpdateWorkoutLogRequest $request, WorkoutLog $workoutLog) 
    {
        Gate::authorize('update', $workoutLog);
        $log = $this->workoutLogService->updateWorkoutLog($workoutLog, $request->validated());
        return response()->json($log);
    }

    public function destroy(WorkoutLog $workoutLog) 
    {
        Gate::authorize('delete', WorkoutLog::class);
        $this->workoutLogService->deleteWorkoutLog($workoutLog);
        return response()->json(['message' => 'Workout log deleted successfully'], 204);
    }

    public function getDataByRange($startDate, $endDate) 
    {
        $logs = $this->workoutLogService->getWorkoutDataByRange(
        auth()->user(),
         $startDate,
         $endDate
         );
        return response()->json($data);
    }
}
