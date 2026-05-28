<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workout;

class WorkoutPolicy
{
    /**
     * Determine if the user can view all workouts
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific workout
     */
    public function view(User $user, Workout $workout): bool
    {
        return $user->id === $workout->user_id;
    }

    /**
     * Determine if the user can create a workout
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update a workout
     */
    public function update(User $user, Workout $workout): bool
    {
        return $user->id === $workout->user_id;
    }

    /**
     * Determine if the user can delete a workout
     */
    public function delete(User $user, Workout $workout): bool
    {
        return $user->id === $workout->user_id;
    }
}
