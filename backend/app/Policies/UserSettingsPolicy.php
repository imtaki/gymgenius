<?php

namespace App\Policies;

use App\Models\User;

class UserSettingsPolicy
{
    /**
     * Create a new policy instance.
     */
   public function viewAny(User $user, int $userId)
    {
        return $user->id === $userId;
    }

    /**
     * Determine if the user can update their settings
     */
    public function update(User $user, int $userId)
    {
        return $user->id === $userId;
    }

    public function create(User $user, int $userId)
    {
        return $user->id === $userId;
    }



}
