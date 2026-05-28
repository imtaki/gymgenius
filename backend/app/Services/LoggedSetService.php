<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateLoggedSetData;
use App\Models\LoggedSet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class LoggedSetService
{
    private const CACHE_TTL = 3600;

    /**
     * Get logged sets for a workout
     */
    public function getByWorkout(int $workoutId): Collection
    {
        return Cache::tags(['logged_sets'])->remember(
            "workout.$workoutId.sets",
            self::CACHE_TTL,
            function () use ($workoutId) {
                return LoggedSet::where('workout_id', $workoutId)
                    ->with('workoutSplitExercise.exercise')
                    ->orderBy('workout_split_exercise_id')
                    ->orderBy('set_number')
                    ->get();
            }
        );
    }

    /**
     * Get a single logged set by ID
     */
    public function getById(int $id): LoggedSet
    {
        return Cache::tags(['logged_sets'])->remember(
            "logged_set.$id",
            self::CACHE_TTL,
            function () use ($id) {
                return LoggedSet::with('workoutSplitExercise.exercise')
                    ->findOrFail($id);
            }
        );
    }

    /**
     * Create a new logged set
     */
    public function create(int $workoutId, CreateLoggedSetData $data): LoggedSet
    {
        $set = LoggedSet::create([
            'workout_id' => $workoutId,
            'workout_split_exercise_id' => $data->workout_split_exercise_id,
            'set_number' => $data->set_number,
            'reps' => $data->reps,
            'weight' => $data->weight,
            'rpe' => $data->rpe,
        ]);

        Cache::tags(['logged_sets'])->forget("workout.$workoutId.sets");

        return $set;
    }

    /**
     * Update a logged set
     */
    public function update(int $id, array $data): LoggedSet
    {
        $set = LoggedSet::findOrFail($id);
        $workoutId = $set->workout_id;

        $updateData = [];
        if (isset($data['reps'])) {
            $updateData['reps'] = $data['reps'];
        }
        if (isset($data['weight'])) {
            $updateData['weight'] = $data['weight'];
        }
        if (isset($data['rpe'])) {
            $updateData['rpe'] = $data['rpe'];
        }

        if (!empty($updateData)) {
            $set->update($updateData);
        }

        Cache::tags(['logged_sets'])->forget("logged_set.$id");
        Cache::tags(['logged_sets'])->forget("workout.$workoutId.sets");

        return $set;
    }

    /**
     * Delete a logged set
     */
    public function delete(int $id): bool
    {
        $set = LoggedSet::findOrFail($id);
        $workoutId = $set->workout_id;

        $result = $set->delete();

        Cache::tags(['logged_sets'])->forget("logged_set.$id");
        Cache::tags(['logged_sets'])->forget("workout.$workoutId.sets");

        return $result;
    }
}