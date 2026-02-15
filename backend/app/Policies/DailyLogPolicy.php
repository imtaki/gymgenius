<?php

namespace App\Policies;

use App\Models\User;

class DailyLogPolicy
{
   public function viewAny(User $user, DailyLog $dailyLog): bool
    {
        return $user->id === $dailyLog->user_id;
    }
}
