<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LoggedSet;

class LoggedSetPolicy
{
    /**
     * Determine if the user can view all logged sets.
     * Logged sets should only be viewed through specific workout relationship.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can view a specific logged set
     */
    public function view(User $user, LoggedSet $loggedSet): bool
    {
        return $user->id === $loggedSet->workout->user_id;
    }

    /**
     * Determine if the user can create a logged set
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update a logged set
     */
    public function update(User $user, LoggedSet $loggedSet): bool
    {
        return $user->id === $loggedSet->workout->user_id;
    }

    /**
     * Determine if the user can delete a logged set
     */
    public function delete(User $user, LoggedSet $loggedSet): bool
    {
        return $user->id === $loggedSet->workout->user_id;
    }
}
