<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DailyLog;

class DailyLogPolicy
{
    public function view(User $user, string $userId): bool
    {
        return (int) $user->id === (int) $userId;
    }
}
