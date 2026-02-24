<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\WorkoutStreak;
use App\Models\User;

class WorkoutStreakPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkoutStreak $workoutStreak): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkoutStreak $workoutStreak): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkoutStreak $workoutStreak): bool
    {
        return false;
    }

}
