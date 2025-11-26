<?php

namespace Database\Seeders;

use App\Models\UserSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserSettings::factory()->create([
            'height' => 180.3,
            'age' => 28,
            'goal_type' => 'maintaining',
            'caloric_goal' => 2500,
            'current_weight' => 75.50,
            'target_weight' => 75.50,
        ]);
    }
}
