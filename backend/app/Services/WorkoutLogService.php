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

    /**
     * Get workout logs in a date range (unbounded, returns all matching records).
     * For large result sets, use getWorkoutLogsByDateRangePaginated() instead.
     *
     * @deprecated Use getWorkoutLogsByDateRangePaginated() for safety on large datasets.
     */
    public function getWorkoutLogsByDateRange(User $user, string $startDate, string $endDate) 
    {
        return WorkoutLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('exercise')
            ->latest('date')
            ->get();
    }

    /**
     * Get workout logs in a date range with pagination.
     * Use this for displaying results to users.
     *
     * @param User $user
     * @param string $startDate
     * @param string $endDate
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWorkoutLogsByDateRangePaginated(User $user, string $startDate, string $endDate, int $perPage = 15)
    {
        return WorkoutLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('exercise')
            ->latest('date')
            ->paginate($perPage);
    }

    /**
     * Get workout logs in a date range using cursor pagination.
     * Efficient for keyset-based pagination on large result sets.
     *
     * @param User $user
     * @param string $startDate
     * @param string $endDate
     * @param int $perPage
     * @return \Illuminate\Pagination\CursorPaginator
     */
    public function getWorkoutLogsByDateRangeCursor(User $user, string $startDate, string $endDate, int $perPage = 15)
    {
        return WorkoutLog::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('exercise')
            ->latest('date')
            ->cursorPaginate($perPage);
    }
}