<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateWorkoutData;
use App\Data\UpdateWorkoutData;
use App\Models\Workout;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class WorkoutService
{
    private const CACHE_TTL = 3600;

    /**
     * Get paginated workouts for a user
     */
    public function paginate(int $userId, int $perPage = 20)
    {
        return Cache::tags(['workouts'])->remember(
            "workouts.user.$userId",
            self::CACHE_TTL,
            function () use ($userId, $perPage) {
                return Workout::where('user_id', $userId)
                    ->with(['workoutSplit', 'loggedSets'])
                    ->orderByDesc('date')
                    ->paginate($perPage);
            }
        );
    }

    /**
     * Get a single workout by ID
     */
    public function getById(int $id): Workout
    {
        return Cache::tags(['workouts'])->remember(
            "workout.$id",
            self::CACHE_TTL,
            function () use ($id) {
                return Workout::with(['workoutSplit', 'loggedSets.workoutSplitExercise.exercise'])
                    ->findOrFail($id);
            }
        );
    }

    /**
     * Create a new workout
     */
    public function create(int $userId, CreateWorkoutData $data): Workout
    {
        $workout = Workout::create([
            'user_id' => $userId,
            'workout_split_id' => $data->workout_split_id,
            'date' => $data->date,
            'started_at' => $data->started_at,
            'ended_at' => $data->ended_at,
            'notes' => $data->notes,
        ]);

        Cache::tags(['workouts'])->forget("workouts.user.$userId");

        return $workout;
    }

    /**
     * Update a workout
     */
    public function update(int $id, UpdateWorkoutData $data): Workout
    {
        $workout = Workout::findOrFail($id);
        $userId = $workout->user_id;

        $updateData = [];
        if ($data->workout_split_id !== null) {
            $updateData['workout_split_id'] = $data->workout_split_id;
        }
        if ($data->date !== null) {
            $updateData['date'] = $data->date;
        }
        if ($data->started_at !== null) {
            $updateData['started_at'] = $data->started_at;
        }
        if ($data->ended_at !== null) {
            $updateData['ended_at'] = $data->ended_at;
        }
        if ($data->notes !== null) {
            $updateData['notes'] = $data->notes;
        }

        if (!empty($updateData)) {
            $workout->update($updateData);
        }

        Cache::tags(['workouts'])->forget("workout.$id");
        Cache::tags(['workouts'])->forget("workouts.user.$userId");

        return $workout;
    }

    /**
     * Delete a workout
     */
    public function delete(int $id): bool
    {
        $workout = Workout::findOrFail($id);
        $userId = $workout->user_id;

        $result = $workout->delete();

        Cache::tags(['workouts'])->forget("workout.$id");
        Cache::tags(['workouts'])->forget("workouts.user.$userId");

        return $result;
    }
}