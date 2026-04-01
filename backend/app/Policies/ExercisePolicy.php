<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exercise;

class ExercisePolicy
{
    /**
     * Determine if the user can view all exercises
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine if the user can view a specific exercise
     */
    public function view(User $user, Exercise $exercise)
    {
        return $user->id === $exercise->user_id;
    }

    /**
     * Determine if the user can create an exercise
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine if the user can update an exercise
     */
    public function update(User $user, Exercise $exercise)
    {
        return $user->id === $exercise->user_id;
    }

    /**
     * Determine if the user can delete an exercise
     */
    public function delete(User $user, Exercise $exercise)
    {
        return $user->id === $exercise->user_id;
    }
}
