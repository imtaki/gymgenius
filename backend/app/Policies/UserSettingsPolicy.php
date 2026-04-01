<?php

namespace App\Policies;

use App\Models\User;

class UserSettingsPolicy
{
    /**
     * Determine if the user can view settings
     */
    public function view(User $user, string $userId): bool
    {
        return (int) $user->id === (int) $userId;
    }

    /**
     * Determine if the user can update settings
     */
    public function update(User $user, string $userId): bool
    {
        return (int) $user->id === (int) $userId;
    }

    /**
     * Determine if the user can create settings
     */
    public function create(User $user, string $userId): bool
    {
        return (int) $user->id === (int) $userId;
    }



}
