<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class ExerciseService
{
    /**
     * Get all exercises for a user
     */
    public function getExercisesByUser($userId)
    {
        return Cache::remember("user_{$userId}_exercises", now()->addMinutes(30), function () use ($userId) {
        try {
            return Exercise::where('user_id', $userId)->get();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve exercises: {$e->getMessage()}");
        }
        });
    }

    /**
     * Get exercise by ID
     */
    public function getExerciseById($id)
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
    public function createExercise($userId, $data): Exercise
    {
        try {
            $data['user_id'] = $userId;
            $exercise = Exercise::create($data);

            Cache::forget("user_{$userId}_exercises");

            return $exercise;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create exercise: {$e->getMessage()}");
        }
    }

    /**
     * Update an exercise
     */
    public function updateExercise($exerciseId, $data)
    {
        try {
            $exercise = Exercise::findOrFail($exerciseId);
            $exercise->update($data);

            Cache::forget("exercise_{$exerciseId}");
            Cache::forget("user_{$exercise->user_id}_exercises");

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
    public function deleteExercise($exerciseId)
    {
        try {
            $exercise = Exercise::findOrFail($exerciseId);
            $userId = $exercise->user_id;
            
            $deleted = $exercise->delete();

            if ($deleted) {
                Cache::forget("exercise_{$exerciseId}");
                Cache::forget("user_{$userId}_exercises");
            }

            return $deleted;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("Exercise not found with ID: {$exerciseId}");
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete exercise: {$e->getMessage()}");
        }
    }
}