<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutProgram;

class WorkoutProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }
   public function view(User $user, WorkoutProgram $workoutProgram): bool
    {
        return $user->id === $workoutProgram->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, WorkoutProgram $workoutProgram): bool
    {
        return $user->id === $workoutProgram->user_id;
    }

    public function delete(User $user, WorkoutProgram $workoutProgram): bool
    {
        return $user->id === $workoutProgram->user_id;
    }
}
