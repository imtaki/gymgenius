<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutSplit;

class WorkoutSplitPolicy
{
    /**
     * Determine if the user can view all workout splits
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a specific workout split
     */
    public function view(User $user, WorkoutSplit $workoutSplit): bool
    {
        return $user->id === $workoutSplit->user_id;
    }

    /**
     * Determine if the user can create a workout split
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update a workout split
     */
    public function update(User $user, WorkoutSplit $workoutSplit): bool
    {
        return $user->id === $workoutSplit->user_id;
    }

    /**
     * Determine if the user can delete a workout split
     */
    public function delete(User $user, WorkoutSplit $workoutSplit): bool
    {
        return $user->id === $workoutSplit->user_id;
    }
}
