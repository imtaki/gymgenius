<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkoutLog;

class WorkoutLogPolicy
{

    public function viewAny(User $user): bool
    {
        return false; 
    }
    public function view(User $user, WorkoutLog $workoutLog): bool
    {
        return $user->id === $workoutLog->user_id;
    }

    public function create(User $user, $userId)
    {
        return $user->id === $userId;
    }

    public function update(User $user, WorkoutLog $workoutLog): bool
    {
        return $user->id === $workoutLog->user_id;
    }

    public function delete(User $user, WorkoutLog $workoutLog): bool
    {
        return $user->id === $workoutLog->user_id;
    }
}
