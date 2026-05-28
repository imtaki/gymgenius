<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CreateExerciseData;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class ExerciseService
{
    /**
     * Get all exercises for a user (cached, suitable for < 1000 exercises)
     * For users with many exercises, use getExercisesByUserPaginated() instead.
     */
    public function getExercisesByUser($userId): Collection
    {
        return Cache::remember("user_{$userId}_exercises", now()->addMinutes(30), function () use ($userId) {
            try {
                return Exercise::where('user_id', $userId)
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                throw new \Exception("Failed to retrieve exercises: {$e->getMessage()}");
            }
        });
    }

    /**
     * Get exercises for a user with pagination.
     * Use this for large result sets or when cursor is provided.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getExercisesByUserPaginated(int $userId, int $perPage = 20)
    {
        return Exercise::where('user_id', $userId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get exercises for a user using cursor pagination (keyset pagination).
     * Efficient for large datasets and consistent ordering.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\CursorPaginator
     */
    public function getExercisesByUserCursor(int $userId, int $perPage = 20)
    {
        return Exercise::where('user_id', $userId)
            ->orderBy('id')
            ->cursorPaginate($perPage);
    }

    /**
     * Get exercise by ID
     */
    public function getExerciseById($id): Exercise
    {
        return Cache::remember("exercise_{$id}", now()->addHours(1), function () use ($id) {
            try {
                return Exercise::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                throw new ModelNotFoundException("Exercise not found with ID: {$id}");
            }
        });
    }

    /**
     * Create a new exercise for a user
     */
    public function createExercise($userId, CreateExerciseData $data): Exercise
    {
        try {
            $exercise = Exercise::create([
                'user_id' => $userId,
                'name' => $data->name,
                'muscleGroup' => $data->muscleGroup,
                'description' => $data->description,
            ]);

            return $exercise;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create exercise: {$e->getMessage()}");
        }
    }

    /**
     * Update an exercise
     */
    public function updateExercise($exerciseId, CreateExerciseData $data): Exercise
    {
        try {
            $exercise = Exercise::findOrFail($exerciseId);
            $exercise->update([
                'name' => $data->name,
                'muscleGroup' => $data->muscleGroup,
                'description' => $data->description,
            ]);

            return $exercise;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Exercise not found with ID: {$exerciseId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to update exercise: {$e->getMessage()}");
        }
    }

    /**
     * Delete an exercise
     */
    public function deleteExercise($exerciseId): bool
    {
        try {
            $exercise = Exercise::findOrFail($exerciseId);
            return $exercise->delete();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Exercise not found with ID: {$exerciseId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete exercise: {$e->getMessage()}");
        }
    }

    /**
     * Get unique muscle groups for a user's exercises
     */
    public function getMuscleGroupsByUser($userId): Collection
    {
        return Cache::remember("user_{$userId}_muscle_groups", now()->addMinutes(30), function () use ($userId) {
            try {
                return Exercise::where('user_id', $userId)
                    ->distinct()
                    ->pluck('muscleGroup');
            } catch (\Exception $e) {
                throw new \Exception("Failed to retrieve muscle groups: {$e->getMessage()}");
            }
        });
    }
}