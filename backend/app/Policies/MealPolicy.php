<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Meal;

class MealPolicy
{
    /**
     * Determine if the user can view meals for a specific user
     */

    public function viewAny(User $user, int $userId): bool
    {
        return $user->id === $userId;
    }


    /**
     * Determine if the user can view a specific meal
     */

    
    public function view(User $user, Meal $meal): bool
    {
        return $user->id === $meal->user_id;
    }
    /**
     * Determine if the user can create a meal for a specific user
     */
    public function create(User $user, $userId): bool
    {
        return $user->id == $userId;
    }


    /**
     * Determine if the user can update a meal
     */

    public function update(User $user, Meal $meal): bool
    {
        return $user->id === $meal->user_id;
    }

    /**
     * Determine if the user can delete a meal
     */
    
    public function delete(User $user, Meal $meal): bool
    {
        return $user->id === $meal->user_id;
    }

}