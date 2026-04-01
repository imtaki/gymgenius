<?php

namespace Database\Seeders;

use App\Models\UserSettings;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();

        if ($user) {
            UserSettings::factory()->create([
                'user_id' => $user->id,
                'height' => 180.3,
                'age' => 28,
                'goal_type' => 'maintaining',
                'caloric_goal' => 2500,
                'current_weight' => 75.50,
                'target_weight' => 75.50,
            ]);
        }
    }
}
