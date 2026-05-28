<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateWorkoutSplitExerciseData;
use App\Data\UpdateWorkoutSplitExerciseData;
use App\Models\WorkoutSplitExercise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class WorkoutSplitExerciseService
{
    private const CACHE_TTL = 3600;

    /**
     * Get exercises for a workout split (cached, returns all).
     * Safe: typically < 50 exercises per split. Uses cache tags for invalidation.
     *
     * @param int $workoutSplitId
     * @return Collection All exercises in the split
     */
    public function getByWorkoutSplit(int $workoutSplitId): Collection
    {
        return Cache::tags(['split_exercises'])->remember(
            "split.$workoutSplitId.exercises",
            self::CACHE_TTL,
            function () use ($workoutSplitId) {
                return WorkoutSplitExercise::where('workout_split_id', $workoutSplitId)
                    ->with('exercise')
                    ->orderBy('order')
                    ->get();
            }
        );
    }

    /**
     * Get exercises for a workout split with pagination.
     * Bypass cache; use for paginated UI or data export.
     *
     * @param int $workoutSplitId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByWorkoutSplitPaginated(int $workoutSplitId, int $perPage = 50): \Illuminate\Pagination\LengthAwarePaginator
    {
        return WorkoutSplitExercise::where('workout_split_id', $workoutSplitId)
            ->with('exercise')
            ->orderBy('order')
            ->paginate($perPage);
    }

    /**
     * Get a single workout split exercise
     */
    public function getById(int $id): WorkoutSplitExercise
    {
        return Cache::tags(['split_exercises'])->remember(
            "split_exercise.$id",
            self::CACHE_TTL,
            function () use ($id) {
                return WorkoutSplitExercise::with('exercise')
                    ->findOrFail($id);
            }
        );
    }

    /**
     * Create a new exercise in a workout split
     */
    public function create(int $workoutSplitId, CreateWorkoutSplitExerciseData $data): WorkoutSplitExercise
    {
        $exercise = WorkoutSplitExercise::create([
            'workout_split_id' => $workoutSplitId,
            'exercise_id' => $data->exercise_id,
            'order' => $data->order,
            'target_sets' => $data->target_sets,
            'target_reps' => $data->target_reps,
            'notes' => $data->notes,
        ]);

        Cache::tags(['split_exercises'])->forget("split.$workoutSplitId.exercises");

        return $exercise;
    }

    /**
     * Update a workout split exercise
     */
    public function update(int $id, UpdateWorkoutSplitExerciseData $data): WorkoutSplitExercise
    {
        $exercise = WorkoutSplitExercise::findOrFail($id);
        $workoutSplitId = $exercise->workout_split_id;

        $updateData = [];
        if ($data->exercise_id !== null) {
            $updateData['exercise_id'] = $data->exercise_id;
        }
        if ($data->order !== null) {
            $updateData['order'] = $data->order;
        }
        if ($data->target_sets !== null) {
            $updateData['target_sets'] = $data->target_sets;
        }
        if ($data->target_reps !== null) {
            $updateData['target_reps'] = $data->target_reps;
        }
        if ($data->notes !== null) {
            $updateData['notes'] = $data->notes;
        }

        if (!empty($updateData)) {
            $exercise->update($updateData);
        }

        Cache::tags(['split_exercises'])->forget("split_exercise.$id");
        Cache::tags(['split_exercises'])->forget("split.$workoutSplitId.exercises");

        return $exercise;
    }

    /**
     * Delete an exercise from a workout split
     */
    public function delete(int $id): bool
    {
        $exercise = WorkoutSplitExercise::findOrFail($id);
        $workoutSplitId = $exercise->workout_split_id;

        $result = $exercise->delete();

        Cache::tags(['split_exercises'])->forget("split_exercise.$id");
        Cache::tags(['split_exercises'])->forget("split.$workoutSplitId.exercises");

        return $result;
    }
}