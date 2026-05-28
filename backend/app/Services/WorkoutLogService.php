<?php   

namespace App\Services;

use App\Models\WorkoutLog;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class WorkoutLogService
{
    public function getAllWorkoutLogs(User $user, $perPage = 15) 
    {
        return WorkoutLog::where('user_id', $user->id)
        ->with('exercise')
        ->latest('date')
        ->paginate($perPage);
    }

    public function getWorkoutLogById(int $id) 
    {
        return WorkoutLog::with('exercise')->findOrFail($id);
    }

    public function createWorkoutLog(User $user, array $data) 
    {

        return WorkoutLog::create([
            'user_id' => $user->id,
            'exercise_id' => $data['exercise_id'],
            'date' => $data['date'],
            'duration_minutes' => $data['duration_minutes'],
            'calories_burned' => $data['calories_burned'] ?? null
        ]);

    }

    public function updateWorkoutLog(WorkoutLog $workoutLog, array $data) 
    {
        $workoutLog->update($data);
        return $workoutLog->fresh();
    }

    public function deleteWorkoutLog(WorkoutLog $workoutLog) 
    {
        $workoutLog->delete();
    }

    public function getWorkoutLogsByDateRange(User $user, string $startDate, string $endDate) 
    {
        return WorkoutLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('exercise')
            ->latest('date')
            ->get();
    }
}